<?php

namespace Hongdev\MasterAdmin\Http\Controllers\Settings;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class DatabaseController extends Controller
{
    /**
     * Show database configuration form
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $dbConfig = [
            'connection' => config('database.default'),
            'host' => config('database.connections.' . config('database.default') . '.host'),
            'port' => config('database.connections.' . config('database.default') . '.port'),
            'database' => config('database.connections.' . config('database.default') . '.database'),
            'username' => config('database.connections.' . config('database.default') . '.username'),
            'password' => '********', // Don't expose the actual password
        ];
        
        $drivers = ['mysql', 'pgsql', 'sqlite', 'sqlsrv'];
        
        return view('master-admin::master-admin.page.settings.database.index', [
            'config' => $dbConfig,
            'drivers' => $drivers
        ]);
    }
    
    /**
     * Update database configuration
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'connection' => 'required|in:mysql,pgsql,sqlite,sqlsrv',
            'host' => 'required_unless:connection,sqlite',
            'port' => 'required_unless:connection,sqlite',
            'database' => 'required',
            'username' => 'required_unless:connection,sqlite',
        ]);
        
        try {
            // Get .env file contents
            $envPath = base_path('.env');
            $envContent = File::get($envPath);
            
            // Update database settings
            $updates = [
                'DB_CONNECTION' => $request->connection,
                'DB_HOST' => $request->host,
                'DB_PORT' => $request->port,
                'DB_DATABASE' => $request->database,
                'DB_USERNAME' => $request->username,
            ];
            
            // Only update password if provided
            if ($request->filled('password')) {
                $updates['DB_PASSWORD'] = $request->password;
            }
            
            // Apply updates to .env file
            foreach ($updates as $key => $value) {
                $envContent = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $envContent);
            }
            
            // Save updated .env file
            File::put($envPath, $envContent);
            
            // Clear config cache
            Artisan::call('config:clear');
            
            return redirect()->route('master-admin.settings.database.index')
                           ->with('success', 'Database configuration updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error updating database configuration: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Test the database connection
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function test()
    {
        try {
            $connection = DB::connection()->getPdo();
            $databaseName = DB::connection()->getDatabaseName();
            
            return redirect()->back()->with('success', "Successfully connected to database: {$databaseName}");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Database connection error: " . $e->getMessage());
        }
    }
}
