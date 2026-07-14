<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class WebEmailTemplateController extends Controller
{
    private array $templates = [
        'welcome_company'   => 'Welcome Company',
        'package_assigned'  => 'Package Assigned',
        'quotation_notification' => 'Quotation Notification',
        'password_reset'    => 'Password Reset',
    ];

    public function index()
    {
        $templates = [];
        foreach ($this->templates as $key => $label) {
            $data = Setting::getGroup('email_template_' . $key);
            $templates[$key] = [
                'label'      => $label,
                'subject'    => $data['subject'] ?? '',
                'body'       => $data['body'] ?? '',
                'is_enabled' => ($data['is_enabled'] ?? '1') === '1',
            ];
        }
        return view('admin.email-templates.index', compact('templates'));
    }

    public function edit(string $template)
    {
        if (!isset($this->templates[$template])) abort(404);

        $data = Setting::getGroup('email_template_' . $template);
        $label = $this->templates[$template];

        return view('admin.email-templates.edit', compact('template', 'label', 'data'));
    }

    public function update(Request $request, string $template)
    {
        if (!isset($this->templates[$template])) abort(404);

        $validated = $request->validate([
            'subject'     => 'required|string|max:255',
            'body'        => 'required|string',
            'is_enabled'  => 'sometimes|boolean',
        ]);

        $validated['is_enabled'] = $request->boolean('is_enabled') ? '1' : '0';

        Setting::setGroup('email_template_' . $template, $validated);
        ActivityLog::log('updated', null, 'Updated email template ' . $template);

        return redirect('/admin/email-templates')->with('success', 'Template updated.');
    }
}
