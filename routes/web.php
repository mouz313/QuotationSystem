<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Admin\SystemHealthController as AdminHealth;
use App\Http\Controllers\Admin\WebActivityLogController as AdminActivity;
use App\Http\Controllers\Admin\WebAdminUserController as AdminUser;
use App\Http\Controllers\Admin\WebAdminQuotationController as AdminQuotation;
use App\Http\Controllers\Admin\WebCompanyController as AdminCompany;
use App\Http\Controllers\Admin\WebCurrencyController as AdminCurrency;
use App\Http\Controllers\Admin\WebEmailTemplateController as AdminEmailTemplate;
use App\Http\Controllers\Admin\WebPackageController as AdminPackage;
use App\Http\Controllers\Admin\WebPageController as AdminPage;
use App\Http\Controllers\Admin\WebTaxController as AdminTax;
use App\Http\Controllers\Admin\SettingsController as AdminSettings;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Company\DashboardController as CompanyDashboard;
use App\Http\Controllers\Company\WebClientController as CompanyClient;
use App\Http\Controllers\Company\WebItemController as CompanyItem;
use App\Http\Controllers\Company\WebQuotationController as CompanyQuotation;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\Company\UserController as CompanyUser;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $packages = \App\Models\Package::where('is_active', true)->orderBy('price')->get();
    return view('welcome', compact('packages'));
});

// ── Guest Auth ──
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'webLogin']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'webRegister']);
});
Route::post('/logout', [AuthController::class, 'webLogout'])->middleware('auth')->name('logout');

// ── Admin Panel ──
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard (always accessible to all admins)
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Companies
    Route::middleware('permission:companies.manage')->group(function () {
        Route::get('/companies', [AdminCompany::class, 'index'])->name('companies.index');
        Route::get('/companies/create', [AdminCompany::class, 'create'])->name('companies.create');
        Route::post('/companies', [AdminCompany::class, 'store'])->name('companies.store');
        Route::get('/companies/{company}', [AdminCompany::class, 'show'])->name('companies.show');
        Route::get('/companies/{company}/edit', [AdminCompany::class, 'edit'])->name('companies.edit');
        Route::put('/companies/{company}', [AdminCompany::class, 'update'])->name('companies.update');
        Route::patch('/companies/{company}/status', [AdminCompany::class, 'updateStatus'])->name('companies.status');
        Route::post('/companies/{company}/assign-package', [AdminCompany::class, 'assignPackage'])->name('companies.assign');
        Route::delete('/companies/{company}', [AdminCompany::class, 'destroy'])->name('companies.destroy');
    });

    // Packages
    Route::middleware('permission:packages.manage')->group(function () {
        Route::get('/packages', [AdminPackage::class, 'index'])->name('packages.index');
        Route::get('/packages/create', [AdminPackage::class, 'create'])->name('packages.create');
        Route::post('/packages', [AdminPackage::class, 'store'])->name('packages.store');
        Route::get('/packages/{package}/edit', [AdminPackage::class, 'edit'])->name('packages.edit');
        Route::put('/packages/{package}', [AdminPackage::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}', [AdminPackage::class, 'destroy'])->name('packages.destroy');
    });

    // Currencies
    Route::middleware('permission:currencies.manage')->group(function () {
        Route::get('/currencies', [AdminCurrency::class, 'index'])->name('currencies.index');
        Route::get('/currencies/create', [AdminCurrency::class, 'create'])->name('currencies.create');
        Route::post('/currencies', [AdminCurrency::class, 'store'])->name('currencies.store');
        Route::get('/currencies/{currency}/edit', [AdminCurrency::class, 'edit'])->name('currencies.edit');
        Route::put('/currencies/{currency}', [AdminCurrency::class, 'update'])->name('currencies.update');
        Route::delete('/currencies/{currency}', [AdminCurrency::class, 'destroy'])->name('currencies.destroy');
    });

    // Taxes
    Route::middleware('permission:taxes.manage')->group(function () {
        Route::get('/taxes', [AdminTax::class, 'index'])->name('taxes.index');
        Route::get('/taxes/create', [AdminTax::class, 'create'])->name('taxes.create');
        Route::post('/taxes', [AdminTax::class, 'store'])->name('taxes.store');
        Route::get('/taxes/{tax}/edit', [AdminTax::class, 'edit'])->name('taxes.edit');
        Route::put('/taxes/{tax}', [AdminTax::class, 'update'])->name('taxes.update');
        Route::delete('/taxes/{tax}', [AdminTax::class, 'destroy'])->name('taxes.destroy');
    });

    // Quotation Oversight
    Route::middleware('permission:quotations.view')->group(function () {
        Route::get('/quotations', [AdminQuotation::class, 'index'])->name('quotations.index');
        Route::get('/quotations/{quotation}', [AdminQuotation::class, 'show'])->name('quotations.show');
        Route::patch('/quotations/{quotation}/status', [AdminQuotation::class, 'updateStatus'])->name('quotations.status');
        Route::delete('/quotations/{quotation}', [AdminQuotation::class, 'destroy'])->name('quotations.destroy');
        Route::get('/quotations/{quotation}/pdf', [AdminQuotation::class, 'pdf'])->name('quotations.pdf');
    });

    // Admin Users
    Route::middleware('permission:users.manage')->group(function () {
        Route::get('/users', [AdminUser::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUser::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUser::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminUser::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUser::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUser::class, 'destroy'])->name('users.destroy');
    });

    // Company Users (admin view of all company staff)
    Route::middleware('permission:companies.manage')->prefix('company-users')->name('company-users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WebCompanyUserController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\WebCompanyUserController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\WebCompanyUserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\WebCompanyUserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\Admin\WebCompanyUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\Admin\WebCompanyUserController::class, 'destroy'])->name('destroy');
    });

    // Reports & Exports
    Route::middleware('permission:reports.view')->group(function () {
        Route::get('/reports', [AdminReport::class, 'index'])->name('reports.index');
        Route::get('/reports/{type}/export', [AdminReport::class, 'export'])->name('reports.export');
    });

    // Activity Log
    Route::middleware('permission:activity.view')->group(function () {
        Route::get('/activity-log', [AdminActivity::class, 'index'])->name('activity-log.index');
    });

    // System Health
    Route::middleware('permission:health.view')->group(function () {
        Route::get('/health', [AdminHealth::class, 'index'])->name('health.index');
    });

    // Pages CMS
    Route::middleware('permission:pages.manage')->group(function () {
        Route::get('/pages', [AdminPage::class, 'index'])->name('pages.index');
        Route::get('/pages/create', [AdminPage::class, 'create'])->name('pages.create');
        Route::post('/pages', [AdminPage::class, 'store'])->name('pages.store');
        Route::get('/pages/{page}/edit', [AdminPage::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [AdminPage::class, 'update'])->name('pages.update');
        Route::delete('/pages/{page}', [AdminPage::class, 'destroy'])->name('pages.destroy');
    });

    // Email Templates
    Route::middleware('permission:email_templates.manage')->group(function () {
        Route::get('/email-templates', [AdminEmailTemplate::class, 'index'])->name('email-templates.index');
        Route::get('/email-templates/{template}/edit', [AdminEmailTemplate::class, 'edit'])->name('email-templates.edit');
        Route::put('/email-templates/{template}', [AdminEmailTemplate::class, 'update'])->name('email-templates.update');
    });

    // Settings
    Route::middleware('permission:settings.manage')->group(function () {
        Route::get('/settings', [AdminSettings::class, 'index'])->name('settings.index');
        Route::put('/settings/general', [AdminSettings::class, 'updateGeneral'])->name('settings.general');
        Route::put('/settings/social', [AdminSettings::class, 'updateSocial'])->name('settings.social');
        Route::put('/settings/pusher', [AdminSettings::class, 'updatePusher'])->name('settings.pusher');
        Route::put('/settings/email', [AdminSettings::class, 'updateEmail'])->name('settings.email');
        Route::post('/settings/email/test', [AdminSettings::class, 'sendTestEmail'])->name('settings.email.test');
    });
});

// Public Pages
Route::get('/pages/{page}', function (\App\Models\Page $page) {
    if (!$page->is_published) abort(404);
    return view('pages.show', compact('page'));
})->name('pages.show');

// Company Panel (NOT admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isSuperAdmin()) {
            return redirect('/admin/dashboard');
        }
        return app(CompanyDashboard::class)->index(request());
    })->name('dashboard');

    // Profile Settings (all users)
    Route::get('/settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/settings/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Company-only routes (admin blocked)
    Route::middleware('not.admin')->group(function () {
        // Client routes
        Route::get('/clients', [CompanyClient::class, 'index'])->name('clients.index');
        Route::get('/clients/create', [CompanyClient::class, 'create'])->name('clients.create');
        Route::post('/clients', [CompanyClient::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}/edit', [CompanyClient::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [CompanyClient::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [CompanyClient::class, 'destroy'])->name('clients.destroy');
        Route::get('/clients/export', [CompanyClient::class, 'exportCsv'])->name('clients.export');

        // Item routes
        Route::get('/items', [CompanyItem::class, 'index'])->name('items.index');
        Route::get('/items/create', [CompanyItem::class, 'create'])->name('items.create');
        Route::post('/items', [CompanyItem::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [CompanyItem::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [CompanyItem::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [CompanyItem::class, 'destroy'])->name('items.destroy');

        // Quotation routes
        Route::get('/quotations', [CompanyQuotation::class, 'index'])->name('quotations.index');
        Route::get('/quotations/create', [CompanyQuotation::class, 'create'])->name('quotations.create');
        Route::post('/quotations', [CompanyQuotation::class, 'store'])->name('quotations.store');
        Route::get('/quotations/export', [CompanyQuotation::class, 'exportCsv'])->name('quotations.export');
        Route::post('/quotations/bulk-delete', [CompanyQuotation::class, 'bulkDelete'])->name('quotations.bulk-delete');
        Route::get('/quotations/{quotation}', [CompanyQuotation::class, 'show'])->name('quotations.show');
        Route::get('/quotations/{quotation}/pdf', [CompanyQuotation::class, 'pdf'])->name('quotations.pdf');
        Route::get('/quotations/{quotation}/preview', [CompanyQuotation::class, 'preview'])->name('quotations.preview');
        Route::post('/quotations/{quotation}/clone', [CompanyQuotation::class, 'clone'])->name('quotations.clone');
        Route::post('/quotations/{quotation}/send-email', [CompanyQuotation::class, 'sendEmail'])->name('quotations.send-email');
        Route::patch('/quotations/{quotation}/status', [CompanyQuotation::class, 'updateStatus'])->name('quotations.status');
        Route::patch('/quotations/{quotation}/payment', [CompanyQuotation::class, 'updatePayment'])->name('quotations.payment');
        Route::post('/quotations/{quotation}/notes', [CompanyQuotation::class, 'addNote'])->name('quotations.notes');

        // Company user management (company_admin+)
        Route::middleware('company.admin')->prefix('company')->name('company.')->group(function () {
            Route::get('/users', [CompanyUser::class, 'index'])->name('users.index');
            Route::get('/users/create', [CompanyUser::class, 'create'])->name('users.create');
            Route::post('/users', [CompanyUser::class, 'store'])->name('users.store');
            Route::delete('/users/{user}', [CompanyUser::class, 'destroy'])->name('users.destroy');

            // Company settings
            Route::get('/settings', [CompanySettingsController::class, 'edit'])->name('settings.edit');
            Route::put('/settings', [CompanySettingsController::class, 'update'])->name('settings.update');
        });
    });
});
