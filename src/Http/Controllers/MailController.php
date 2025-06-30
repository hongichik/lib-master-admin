<?php

namespace Hongdev\MasterAdmin\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MailController extends Controller
{
    /**
     * Show Gmail configuration form
     *
     * @return \Illuminate\View\View
     */
    public function showConfig()
    {
        $config = [
            'driver' => config('mail.mailer'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => '••••••••', // Don't expose the actual password
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];
        
        return view('master-admin::master-admin.page.mail-config', [
            'config' => $config
        ]);
    }
    
    /**
     * Update Gmail configuration
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateConfig(Request $request)
    {
        $request->validate([
            'driver' => 'required',
            'host' => 'required',
            'port' => 'required|numeric',
            'username' => 'required|email',
            'from_address' => 'required|email',
            'from_name' => 'required',
            'encryption' => 'required',
        ]);
        
        try {
            $envPath = base_path('.env');
            $envContent = File::get($envPath);
            
            // Update mail settings
            $updates = [
                'MAIL_MAILER' => $request->driver,
                'MAIL_HOST' => $request->host,
                'MAIL_PORT' => $request->port,
                'MAIL_USERNAME' => $request->username,
                'MAIL_FROM_ADDRESS' => $request->from_address,
                'MAIL_FROM_NAME' => '"' . $request->from_name . '"',
                'MAIL_ENCRYPTION' => $request->encryption,
            ];
            
            // Only update password if provided
            if ($request->filled('password')) {
                $updates['MAIL_PASSWORD'] = $request->password;
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
            
            return redirect()->route('master-admin.mail.config')
                ->with('success', 'Gmail configuration updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Gmail configuration: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Send a test email
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function testMail()
    {
        try {
            $toEmail = config('mail.from.address');
            
            Mail::raw('This is a test email from Master Admin.', function ($message) use ($toEmail) {
                $message->to($toEmail)
                    ->subject('Test Email from Master Admin');
            });
            
            return redirect()->back()
                ->with('success', 'Test email sent successfully to ' . $toEmail);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}
