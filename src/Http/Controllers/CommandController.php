<?php

namespace Hongdev\MasterAdmin\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class CommandController extends Controller
{
    /**
     * Execute an Artisan command and return to dashboard
     *
     * @param Request $request
     * @param string $command
     * @return \Illuminate\Http\RedirectResponse
     */
    public function executeCommand(Request $request, $command)
    {
        $result = '';
        $success = true;
        
        try {
            switch ($command) {
                // Cache Commands
                case 'cache-clear':
                    Artisan::call('cache:clear');
                    $result = 'Application cache cleared successfully';
                    break;
                    
                case 'config-clear':
                    Artisan::call('config:clear');
                    $result = 'Configuration cache cleared successfully';
                    break;
                    
                case 'config-cache':
                    Artisan::call('config:cache');
                    $result = 'Configuration cached successfully';
                    break;
                    
                case 'route-clear':
                    Artisan::call('route:clear');
                    $result = 'Route cache cleared successfully';
                    break;
                    
                case 'route-cache':
                    Artisan::call('route:cache');
                    $result = 'Routes cached successfully';
                    break;
                    
                case 'view-clear':
                    Artisan::call('view:clear');
                    $result = 'View cache cleared successfully';
                    break;
                    
                case 'optimize-clear':
                    Artisan::call('optimize:clear');
                    $result = 'All caches cleared successfully';
                    break;
                    
                case 'optimize':
                    Artisan::call('optimize');
                    $result = 'Application optimized successfully';
                    break;
                
                // Database Commands
                case 'migrate':
                    Artisan::call('migrate', ['--force' => true]);
                    $result = 'Database migrations executed successfully';
                    break;
                    
                case 'migrate-fresh':
                    Artisan::call('migrate:fresh', ['--force' => true]);
                    $result = 'Database dropped and re-migrated successfully';
                    break;
                    
                case 'migrate-status':
                    $output = Artisan::call('migrate:status');
                    $result = 'Migration status checked. See logs for details.';
                    \Log::info(Artisan::output());
                    break;
                    
                case 'db-seed':
                    Artisan::call('db:seed', ['--force' => true]);
                    $result = 'Database seeded successfully';
                    break;
                
                case 'db-wipe':
                    Artisan::call('db:wipe', ['--force' => true]);
                    $result = 'Database wiped successfully';
                    break;
                
                // System Commands
                case 'storage-link':
                    Artisan::call('storage:link');
                    $result = 'Storage link created successfully';
                    break;
                    
                case 'key-generate':
                    Artisan::call('key:generate', ['--force' => true]);
                    $result = 'Application key generated successfully';
                    break;
                    
                case 'down':
                    Artisan::call('down');
                    $result = 'Application is now in maintenance mode';
                    break;
                    
                case 'up':
                    Artisan::call('up');
                    $result = 'Application is now live';
                    break;
                
                // Queue & Schedule Commands
                case 'queue-work':
                    // Queue work needs to be run in background, so we log instead
                    $result = 'Queue worker should be started with: php artisan queue:work';
                    \Log::info('Queue worker command requested');
                    break;
                    
                case 'queue-restart':
                    Artisan::call('queue:restart');
                    $result = 'Queue workers restarted successfully';
                    break;
                    
                case 'queue-retry-all':
                    Artisan::call('queue:retry', ['--all' => true]);
                    $result = 'All failed jobs have been pushed back onto the queue';
                    break;
                    
                case 'queue-clear':
                    Artisan::call('queue:clear', ['--all' => true]);
                    $result = 'Failed job queue cleared successfully';
                    break;
                
                case 'schedule-list':
                    $output = Artisan::call('schedule:list');
                    $result = 'Schedule list retrieved. See logs for details.';
                    \Log::info(Artisan::output());
                    break;
                    
                case 'schedule-run':
                    Artisan::call('schedule:run');
                    $result = 'Scheduled tasks run successfully';
                    break;
                
                // Package Management Commands
                case 'package-discover':
                    Artisan::call('package:discover');
                    $result = 'Packages discovered successfully';
                    break;
                    
                case 'vendor-publish-all':
                    Artisan::call('vendor:publish', ['--all' => true, '--force' => true]);
                    $result = 'All vendor assets published successfully';
                    break;
                    
                case 'composer-update':
                    // This should actually be executed through proper Process handling
                    $result = 'Composer update should be run manually from the command line';
                    break;
                    
                case 'composer-dump-autoload':
                    if (function_exists('exec')) {
                        exec('composer dump-autoload -o');
                        $result = 'Composer autoload dumped successfully';
                    } else {
                        $result = 'Cannot execute composer dump-autoload (exec function disabled)';
                    }
                    break;
                    
                case 'npm-install':
                    $result = 'NPM install should be run manually from the command line';
                    break;
                    
                case 'npm-run-dev':
                    $result = 'NPM run dev should be run manually from the command line';
                    break;
                
                // Route Commands
                case 'route-list':
                    $output = Artisan::call('route:list');
                    $result = 'Route list retrieved. See logs for details.';
                    \Log::info(Artisan::output());
                    break;
                
                // Make Commands
                case 'make-controller':
                    $result = 'Please use Artisan command to create a controller: php artisan make:controller YourController';
                    break;
                    
                case 'make-model':
                    $result = 'Please use Artisan command to create a model: php artisan make:model YourModel';
                    break;
                    
                case 'make-migration':
                    $result = 'Please use Artisan command to create a migration: php artisan make:migration create_your_table';
                    break;
                    
                case 'make-seeder':
                    $result = 'Please use Artisan command to create a seeder: php artisan make:seeder YourSeeder';
                    break;
                    
                case 'make-middleware':
                    $result = 'Please use Artisan command to create middleware: php artisan make:middleware YourMiddleware';
                    break;
                    
                default:
                    $result = 'Unknown command';
                    $success = false;
            }
        } catch (\Exception $e) {
            $result = 'Error: ' . $e->getMessage();
            $success = false;
        }
        
        // Use session()->flash() instead of with() to ensure session persistence
        if ($success) {
            session()->flash('success', $result);
        } else {
            session()->flash('error', $result);
        }
        
        // Add a test info message to verify session flashing works        
        return redirect()->back();
    }
}
