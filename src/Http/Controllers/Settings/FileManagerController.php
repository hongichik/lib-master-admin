<?php

namespace Hongdev\MasterAdmin\Http\Controllers\Settings;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManagerController extends Controller
{
    private $allowedDisks = ['public', 'storage', 'local'];
    
    /**
     * Show file manager index
     */
    public function index(Request $request)
    {
        $disk = $request->get('disk', 'public');
        $path = $request->get('path', '');
        
        if (!in_array($disk, $this->allowedDisks)) {
            $disk = 'public';
        }
        
        try {
            // Sử dụng Laravel Storage thay vì File facade
            $storage = Storage::disk($disk);
            
            // Kiểm tra path có tồn tại không
            if ($path && !$storage->exists($path)) {
                $path = '';
            }
            
            $directories = $storage->directories($path);
            $files = $storage->files($path);
            
            $items = [];
            
            // Thêm directories
            foreach ($directories as $dir) {
                $items[] = [
                    'name' => basename($dir),
                    'path' => $dir,
                    'type' => 'directory',
                    'size' => null,
                    'modified' => null,
                    'url' => null
                ];
            }
            
            // Thêm files
            foreach ($files as $file) {
                $items[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'type' => 'file',
                    'size' => $storage->size($file),
                    'modified' => $storage->lastModified($file),
                    'url' => $disk === 'public' ? Storage::url($file) : null,
                    'extension' => pathinfo($file, PATHINFO_EXTENSION)
                ];
            }
            
            $breadcrumbs = $this->buildBreadcrumbs($path);
            
            return view('master-admin::master-admin.page.settings.file-manager.index', [
                'items' => $items,
                'currentPath' => $path,
                'disk' => $disk,
                'breadcrumbs' => $breadcrumbs,
                'diskInfo' => $this->getDiskInfo($disk)
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('master-admin.settings.file-manager.index')
                           ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Create directory
     */
    public function createDirectory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9_\-\s]+$/',
            'path' => 'nullable|string',
            'disk' => 'required|in:public,storage,local'
        ]);

        try {
            $storage = Storage::disk($request->disk);
            $newPath = $request->path ? $request->path . '/' . $request->name : $request->name;
            
            if ($storage->exists($newPath)) {
                return back()->with('error', 'Directory already exists');
            }
            
            $storage->makeDirectory($newPath);
            
            return back()->with('success', 'Directory created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Upload files
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:20480', // 20MB
            'path' => 'nullable|string',
            'disk' => 'required|in:public,storage,local'
        ]);

        try {
            $storage = Storage::disk($request->disk);
            $uploaded = 0;
            
            foreach ($request->file('files') as $file) {
                $filename = $file->getClientOriginalName();
                $path = $request->path ? $request->path . '/' . $filename : $filename;
                
                // Tránh ghi đè file
                $counter = 1;
                $originalPath = $path;
                while ($storage->exists($path)) {
                    $info = pathinfo($originalPath);
                    $path = ($request->path ? $request->path . '/' : '') . 
                           $info['filename'] . '_' . $counter . '.' . $info['extension'];
                    $counter++;
                }
                
                $storage->putFileAs(
                    $request->path ?: '', 
                    $file, 
                    basename($path)
                );
                $uploaded++;
            }
            
            return back()->with('success', "Uploaded {$uploaded} file(s)");
        } catch (\Exception $e) {
            return back()->with('error', 'Upload error: ' . $e->getMessage());
        }
    }

    /**
     * Delete file or directory
     */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'disk' => 'required|in:public,storage,local'
        ]);

        try {
            $storage = Storage::disk($request->disk);
            
            if (!$storage->exists($request->path)) {
                return back()->with('error', 'Item not found');
            }
            
            // Kiểm tra nếu là directory
            if ($storage->directories($request->path)) {
                $storage->deleteDirectory($request->path);
                $message = 'Directory deleted';
            } else {
                $storage->delete($request->path);
                $message = 'File deleted';
            }
            
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Delete error: ' . $e->getMessage());
        }
    }

    /**
     * Download file
     */
    public function download(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'disk' => 'required|in:public,storage,local'
        ]);

        try {
            $storage = Storage::disk($request->disk);
            
            if (!$storage->exists($request->path)) {
                return back()->with('error', 'File not found');
            }
            
            return $storage->download($request->path);
        } catch (\Exception $e) {
            return back()->with('error', 'Download error: ' . $e->getMessage());
        }
    }

    /**
     * View file content
     */
    public function view(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'disk' => 'required|in:public,storage,local'
        ]);

        try {
            $storage = Storage::disk($request->disk);
            
            if (!$storage->exists($request->path)) {
                return back()->with('error', 'File not found');
            }
            
            $content = null;
            $fileSize = $storage->size($request->path);
            $isTextFile = $this->isTextFile($request->path);
            
            // Chỉ đọc file text nhỏ hơn 1MB
            if ($isTextFile && $fileSize < 1048576) {
                $content = $storage->get($request->path);
            }
            
            $fileInfo = [
                'name' => basename($request->path),
                'size' => $fileSize,
                'modified' => $storage->lastModified($request->path),
                'mimeType' => $storage->mimeType($request->path),
                'extension' => pathinfo($request->path, PATHINFO_EXTENSION)
            ];
            
            return view('master-admin::master-admin.page.settings.file-manager.view', [
                'fileInfo' => $fileInfo,
                'content' => $content,
                'filePath' => $request->path,
                'disk' => $request->disk,
                'isTextFile' => $isTextFile
            ]);
            
        } catch (\Exception $e) {
            return back()->with('error', 'View error: ' . $e->getMessage());
        }
    }

    /**
     * Edit file content
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('GET')) {
            return $this->showEditor($request);
        }
        
        return $this->updateFile($request);
    }

    private function showEditor(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'disk' => 'required|in:public,storage,local'
        ]);

        try {
            $storage = Storage::disk($request->disk);
            
            if (!$storage->exists($request->path) || !$this->isTextFile($request->path)) {
                return back()->with('error', 'File cannot be edited');
            }
            
            $content = $storage->get($request->path);
            
            return view('master-admin::master-admin.page.settings.file-manager.edit', [
                'content' => $content,
                'filePath' => $request->path,
                'disk' => $request->disk,
                'fileName' => basename($request->path)
            ]);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Edit error: ' . $e->getMessage());
        }
    }

    private function updateFile(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'disk' => 'required|in:public,storage,local',
            'content' => 'required|string'
        ]);

        try {
            $storage = Storage::disk($request->disk);
            $storage->put($request->path, $request->content);
            
            return back()->with('success', 'File updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Update error: ' . $e->getMessage());
        }
    }

    /**
     * Build breadcrumb navigation
     */
    private function buildBreadcrumbs($path)
    {
        $breadcrumbs = [['name' => 'Root', 'path' => '']];
        
        if ($path) {
            $parts = explode('/', $path);
            $currentPath = '';
            
            foreach ($parts as $part) {
                $currentPath .= ($currentPath ? '/' : '') . $part;
                $breadcrumbs[] = ['name' => $part, 'path' => $currentPath];
            }
        }
        
        return $breadcrumbs;
    }

    /**
     * Get disk information
     */
    private function getDiskInfo($disk)
    {
        $info = [];
        
        try {
            switch ($disk) {
                case 'public':
                    $info = [
                        'name' => 'Public Folder',
                        'path' => public_path(),
                        'url' => url('/'),
                        'description' => 'Web accessible files (CSS, JS, Images, etc.)'
                    ];
                    break;
                case 'storage':
                    $info = [
                        'name' => 'Storage Public',
                        'path' => storage_path('app/public'),
                        'url' => Storage::url(''),
                        'description' => 'Laravel storage disk (linked to public/storage)'
                    ];
                    break;
                case 'local':
                    $info = [
                        'name' => 'Storage Local',
                        'path' => storage_path('app'),
                        'url' => null,
                        'description' => 'Private Laravel files (not web accessible)'
                    ];
                    break;
            }
        } catch (\Exception $e) {
            $info['error'] = $e->getMessage();
        }
        
        return $info;
    }

    /**
     * Check if file is editable text file
     */
    private function isTextFile($path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $textExtensions = [
            'txt', 'log', 'md', 'php', 'js', 'css', 'html', 'htm', 'xml', 
            'json', 'yml', 'yaml', 'env', 'ini', 'conf', 'sql', 'py', 'rb', 'go'
        ];
        
        return in_array($extension, $textExtensions);
    }
}
