<?php

namespace Hongdev\MasterAdmin\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function index()
    {
        // Lấy danh sách file backup từ Google Drive, nhóm theo năm/ngày
        $disk = Storage::disk('google');
        $rootFolder = 'backup';
        $years = $disk->directories($rootFolder);

        $backups = [];

        foreach ($years as $yearFolder) {
            $year = basename($yearFolder);
            $days = $disk->directories($yearFolder);
            foreach ($days as $dayFolder) {
                $date = basename($dayFolder); // mm-dd
                $files = collect($disk->files($dayFolder))
                    ->filter(function($file) {
                        return preg_match('/backup_(db|storage|full)_.*\.(sql|zip)$/', basename($file));
                    })
                    ->sortByDesc(function($file) use ($disk) {
                        return $disk->lastModified($file);
                    })
                    ->map(function($file) use ($disk) {
                        return [
                            'name' => basename($file),
                            'download_url' => method_exists($disk, 'url') ? $disk->url($file) : '#',
                        ];
                    })->toArray();

                if (!empty($files)) {
                    $backups[$year][$date] = $files;
                }
            }
        }

        // Sắp xếp năm giảm dần, ngày giảm dần
        krsort($backups);
        foreach ($backups as &$days) {
            krsort($days);
        }

        return view('master-admin::master-admin.page.backup', [
            'backups' => $backups
        ]);
    }

    protected function uploadAndRotate($localPath, $type)
    {
        $disk = Storage::disk('google');
        
        $filename = basename($localPath);
        $now = now();
        $folder = 'backup/' . $now->year . '/' . $now->format('m-d');
        $remotePath = $folder . '/' . $filename;

        // Upload file to Google Drive using File::get
        $disk->put($remotePath, File::get($localPath));

        // Xoá file cũ nếu vượt quá 3 bản backup cùng loại trong folder này
        $files = collect($disk->files($folder))
            ->filter(function($file) use ($type) {
                if ($type === 'database') return str_contains($file, 'backup_db_');
                if ($type === 'storage') return str_contains($file, 'backup_storage_');
                if ($type === 'full') return str_contains($file, 'backup_full_');
                return false;
            })
            ->sortByDesc(function($file) use ($disk) {
                return $disk->lastModified($file);
            })
            ->values();

        if ($files->count() > 3) {
            $toDelete = $files->slice(3);
            foreach ($toDelete as $delFile) {
                $disk->delete($delFile);
            }
        }

        return $remotePath;
    }

    public function backupDatabase(Request $request)
    {
        ini_set('memory_limit', '99024M');
        $db = config('database.connections.' . config('database.default'));
        $filename = 'backup_db_' . date('Ymd_His') . '.sql';
        $tmpPath = storage_path('app/tmp_' . $filename);

        if (!is_dir(dirname($tmpPath))) {
            @mkdir(dirname($tmpPath), 0777, true);
        }

        $command = sprintf(
            'mysqldump -u%s -p%s -h%s %s > %s',
            escapeshellarg($db['username']),
            escapeshellarg($db['password']),
            escapeshellarg($db['host']),
            escapeshellarg($db['database']),
            escapeshellarg($tmpPath)
        );

        $result = null;
        $output = null;
        @exec($command, $output, $result);

        if ($result === 0 && file_exists($tmpPath)) {
            $this->uploadAndRotate($tmpPath, 'database');
            @unlink($tmpPath);
            return response()->json(['success' => true, 'filename' => $filename]);
        }
        @unlink($tmpPath);
        return response()->json(['success' => false, 'message' => 'Backup failed. Check database credentials and mysqldump command.']);
    }

    public function backupStorage(Request $request)
    {
        ini_set('memory_limit', '99024M');
        $filename = 'backup_storage_' . date('Ymd_His') . '.zip';
        $tmpPath = storage_path('app/tmp_' . $filename);

        if (!is_dir(dirname($tmpPath))) {
            @mkdir(dirname($tmpPath), 0777, true);
        }

        $storagePath = storage_path('app/public');
        $command = sprintf(
            'cd %s && zip -r %s .',
            escapeshellarg($storagePath),
            escapeshellarg($tmpPath)
        );

        $result = null;
        $output = null;
        @exec($command, $output, $result);

        if ($result === 0 && file_exists($tmpPath)) {
            $this->uploadAndRotate($tmpPath, 'storage');
            @unlink($tmpPath);
            return response()->json(['success' => true, 'filename' => $filename]);
        }
        @unlink($tmpPath);
        return response()->json(['success' => false, 'message' => 'Backup storage failed.']);
    }

    public function backupFull(Request $request)
    {
        ini_set('memory_limit', '99024M');
        $filename = 'backup_full_' . date('Ymd_His') . '.zip';
        $tmpPath = storage_path('app/tmp_' . $filename);

        if (!is_dir(dirname($tmpPath))) {
            @mkdir(dirname($tmpPath), 0777, true);
        }

        $exclude = [
            'vendor',
            'node_modules',
            'storage/app/public/backup',
            '.git',
        ];

        $excludeArgs = '';
        foreach ($exclude as $ex) {
            $excludeArgs .= ' --exclude="' . $ex . '"';
        }

        $projectRoot = base_path();
        $command = sprintf(
            'cd %s && zip -r %s . %s',
            escapeshellarg($projectRoot),
            escapeshellarg($tmpPath),
            $excludeArgs
        );

        $result = null;
        $output = null;
        @exec($command, $output, $result);

        if ($result === 0 && file_exists($tmpPath)) {
            $this->uploadAndRotate($tmpPath, 'full');
            @unlink($tmpPath);
            return response()->json(['success' => true, 'filename' => $filename]);
        }
        @unlink($tmpPath);
        return response()->json(['success' => false, 'message' => 'Backup failed. Check zip command and permissions.']);
    }

    public function backupAll(Request $request)
    {
        ini_set('memory_limit', '99024M');
        $results = [];
        $dbRes = $this->backupDatabase($request);
        $dbData = $dbRes->getData(true);
        $results['database'] = $dbData;

        $storageRes = $this->backupStorage($request);
        $storageData = $storageRes->getData(true);
        $results['storage'] = $storageData;

        $fullRes = $this->backupFull($request);
        $fullData = $fullRes->getData(true);
        $results['full'] = $fullData;

        $success = $dbData['success'] && $storageData['success'] && $fullData['success'];
        return response()->json([
            'success' => $success,
            'results' => $results,
            'message' => $success ? 'All backups completed.' : 'Some backups failed.'
        ]);
    }

    public function uploadToDrive(Request $request)
    {
        // Không còn ý nghĩa vì backup đã lưu trực tiếp lên Drive
        return response()->json(['success' => false, 'message' => 'Backups are already stored on Google Drive.']);
    }
}
