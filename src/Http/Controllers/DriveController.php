<?php

namespace Hongdev\MasterAdmin\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DriveController extends Controller
{
    /**
     * Show Google Drive configuration form
     *
     * @return \Illuminate\View\View
     */
    public function showConfig()
    {
        $config = [
            'client_id' => env('GOOGLE_DRIVE_CLIENT_ID', ''),
            'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET') ? '••••••••' : '',
            'refresh_token' => env('GOOGLE_DRIVE_REFRESH_TOKEN') ? '••••••••' : '',
            'access_token' => env('GOOGLE_DRIVE_ACCESS_TOKEN') ? '••••••••' : '',
            'folder' => env('GOOGLE_DRIVE_FOLDER', ''),
        ];
        
        return view('master-admin::master-admin.page.drive-config', [
            'config' => $config
        ]);
    }
    
    /**
     * Update Google Drive configuration
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateConfig(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'filesystem_cloud' => 'nullable',
            'folder' => 'nullable',
            'access_token' => 'nullable',
            // Remove required validation for these fields to allow placeholders
            'client_secret' => 'nullable',
            'refresh_token' => 'nullable',
        ]);
        
        try {
            $envPath = base_path('.env');
            $envContent = File::get($envPath);
            
            // Update Google Drive settings
            $updates = [
                'GOOGLE_DRIVE_CLIENT_ID' => $request->client_id,
                'FILESYSTEM_CLOUD' => $request->filesystem_cloud ?: 'google',
                'GOOGLE_DRIVE_FOLDER' => $request->folder ?: '',
                'GOOGLE_DRIVE_ACCESS_TOKEN' => $request->access_token ?: '',
            ];
            
            // Only update client_secret if it's not the placeholder
            if ($request->client_secret && $request->client_secret !== '••••••••') {
                $updates['GOOGLE_DRIVE_CLIENT_SECRET'] = $request->client_secret;
            }
            
            // Only update refresh_token if it's not the placeholder
            if ($request->refresh_token && $request->refresh_token !== '••••••••') {
                $updates['GOOGLE_DRIVE_REFRESH_TOKEN'] = $request->refresh_token;
            }
            
            // Apply updates to .env file
            foreach ($updates as $key => $value) {
                if (strpos($envContent, $key . '=') !== false) {
                    $envContent = preg_replace('/^' . $key . '=.*$/m', $key . '=' . $value, $envContent);
                } else {
                    $envContent .= "\n" . $key . '=' . $value;
                }
            }
            
            File::put($envPath, $envContent);
            
            // Clear config cache
            Artisan::call('config:clear');
            
            return redirect()->route('master-admin.drive.config')
                ->with('success', 'Google Drive configuration updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Google Drive configuration: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Test Google Drive connection
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function testConnection()
    {
        try {
            $disk = Storage::disk('google');
            
            // Try to list contents to test connection
            $contents = $disk->files('/');
            
            return response()->json([
                'success' => true,
                'message' => 'Google Drive connection successful!',
                'files_count' => count($contents)
            ]);
        } catch (\Exception $e) {
            Log::error('Google Drive test failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ]);
        }
    }

    protected function updateEnvFile($data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envFile, $envContent);
    }
}
