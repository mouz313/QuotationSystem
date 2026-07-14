<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
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

        return redirect('/admin/settings?tab=email')->with('success', 'Email settings updated.');
    }
}
