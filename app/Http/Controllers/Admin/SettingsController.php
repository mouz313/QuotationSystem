<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'general'  => Setting::getGroup('general'),
            'social'   => Setting::getGroup('social'),
            'pusher'   => Setting::getGroup('pusher'),
            'email'    => Setting::getGroup('email'),
            'platform' => Setting::getGroup('platform'),
        ];

        $tab = request('tab', 'general');

        return view('admin.settings.index', compact('settings', 'tab'));
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'app_name'        => 'required|string|max:255',
            'app_title'       => 'required|string|max:255',
            'app_description' => 'nullable|string|max:500',
            'footer_text'     => 'nullable|string|max:500',
            'logo'            => 'nullable|image|mimes:png,jpg,svg|max:2048',
            'favicon'         => 'nullable|image|mimes:png,ico,svg|max:1024',
        ]);

        if ($request->hasFile('logo')) {
            if (Setting::get('logo')) Storage::disk('public')->delete(Setting::get('logo'));
            $validated['logo'] = $request->file('logo')->store('settings', 'public');
        } else {
            unset($validated['logo']);
        }

        if ($request->hasFile('favicon')) {
            if (Setting::get('favicon')) Storage::disk('public')->delete(Setting::get('favicon'));
            $validated['favicon'] = $request->file('favicon')->store('settings', 'public');
        } else {
            unset($validated['favicon']);
        }

        Setting::setGroup('general', $validated);
        ActivityLog::log('updated', null, 'Updated general settings');

        return redirect('/admin/settings?tab=general')->with('success', 'General settings updated.');
    }

    public function updateSocial(Request $request)
    {
        $validated = $request->validate([
            'facebook'  => 'nullable|url',
            'twitter'   => 'nullable|url',
            'linkedin'  => 'nullable|url',
            'instagram' => 'nullable|url',
            'youtube'   => 'nullable|url',
            'github'    => 'nullable|url',
        ]);

        Setting::setGroup('social', $validated);
        ActivityLog::log('updated', null, 'Updated social media settings');

        return redirect('/admin/settings?tab=social')->with('success', 'Social media links updated.');
    }

    public function updatePusher(Request $request)
    {
        $validated = $request->validate([
            'pusher_app_id'     => 'nullable|string|max:255',
            'pusher_app_key'    => 'nullable|string|max:255',
            'pusher_app_secret' => 'nullable|string|max:255',
            'pusher_app_cluster' => 'nullable|string|max:50',
            'pusher_enabled'    => 'nullable|boolean',
        ]);

        $validated['pusher_enabled'] = $request->boolean('pusher_enabled') ? '1' : '0';

        Setting::setGroup('pusher', $validated);
        ActivityLog::log('updated', null, 'Updated Pusher settings');

        return redirect('/admin/settings?tab=pusher')->with('success', 'Pusher settings updated.');
    }

    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'mail_driver'   => 'required|in:smtp,sendmail,mailgun,ses,log',
            'mail_host'     => 'nullable|string|max:255',
            'mail_port'     => 'nullable|integer',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'nullable|email',
            'mail_from_name'    => 'nullable|string|max:255',
        ]);

        Setting::setGroup('email', $validated);
        ActivityLog::log('updated', null, 'Updated email settings');

        return redirect('/admin/settings?tab=email')->with('success', 'Email settings updated.');
    }

    public function updatePlatform(Request $request)
    {
        $validated = $request->validate([
            'pagination_per_page'          => 'required|integer|min:5|max:100',
            'pagination_client'            => 'required|integer|min:5|max:100',
            'pagination_activity'          => 'required|integer|min:5|max:100',
            'dashboard_limit'              => 'required|integer|min:3|max:50',
            'notification_limit'           => 'required|integer|min:5|max:50',
            'max_file_size_attachments'    => 'required|integer|min:1024|max:51200',
            'max_file_size_payment_proof'  => 'required|integer|min:512|max:20480',
            'max_file_size_logo'           => 'required|integer|min:256|max:10240',
            'max_attachments_per_quotation' => 'required|integer|min:1|max:20',
        ]);

        Setting::setGroup('platform', $validated);
        ActivityLog::log('updated', null, 'Updated platform settings');

        return redirect('/admin/settings?tab=platform')->with('success', 'Platform settings updated.');
    }

    public function sendTestEmail(Request $request)
    {
        set_time_limit(15);
        ini_set('default_socket_timeout', 5);

        $validated = $request->validate([
            'test_email' => 'required|email',
        ]);

        $emailSettings = Setting::getGroup('email');
        $driver = $emailSettings['mail_driver'] ?? 'log';

        config([
            'mail.default'                 => $driver,
            'mail.mailers.smtp.host'       => $emailSettings['mail_host'] ?? config('mail.mailers.smtp.host'),
            'mail.mailers.smtp.port'       => $emailSettings['mail_port'] ?? config('mail.mailers.smtp.port'),
            'mail.mailers.smtp.username'   => $emailSettings['mail_username'] ?? config('mail.mailers.smtp.username'),
            'mail.mailers.smtp.password'   => $emailSettings['mail_password'] ?? config('mail.mailers.smtp.password'),
            'mail.mailers.smtp.encryption' => $emailSettings['mail_encryption'] ?? config('mail.mailers.smtp.encryption'),
            'mail.mailers.smtp.timeout'    => 10,
            'mail.from.address'           => $emailSettings['mail_from_address'] ?? config('mail.from.address'),
            'mail.from.name'              => $emailSettings['mail_from_name'] ?? config('mail.from.name'),
        ]);

        $email = $validated['test_email'];

        try {
            $data = [
                'appName'     => config('app.name'),
                'fromAddress' => config('mail.from.address'),
                'toAddress'   => $email,
                'driver'      => $driver,
                'host'        => $emailSettings['mail_host'] ?? 'N/A',
                'port'        => $emailSettings['mail_port'] ?? 'N/A',
                'encryption'  => $emailSettings['mail_encryption'] ?? 'None',
                'sentAt'      => now()->format('F j, Y \a\t g:i A T'),
            ];

            Mail::send('emails.test', $data, function ($message) use ($email) {
                $message->to($email)
                    ->subject('Test Email - ' . config('app.name'));
            });

            ActivityLog::log('email_test', null, "Sent test email to {$email}");
            return back()->with('success', "Test email sent successfully to {$email}");
        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            return back()->with('error', 'SMTP connection failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}
