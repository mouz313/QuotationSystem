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

Route::get('/', fn () => view('welcome'));

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
    // Dashboard
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Companies
    Route::get('/companies', [AdminCompany::class, 'index'])->name('companies.index');
    Route::get('/companies/{company}', [AdminCompany::class, 'show'])->name('companies.show');
    Route::patch('/companies/{company}/status', [AdminCompany::class, 'updateStatus'])->name('companies.status');
    Route::post('/companies/{company}/assign-package', [AdminCompany::class, 'assignPackage'])->name('companies.assign');
    Route::delete('/companies/{company}', [AdminCompany::class, 'destroy'])->name('companies.destroy');

    // Packages
    Route::get('/packages', [AdminPackage::class, 'index'])->name('packages.index');
    Route::get('/packages/create', [AdminPackage::class, 'create'])->name('packages.create');
    Route::post('/packages', [AdminPackage::class, 'store'])->name('packages.store');
    Route::get('/packages/{package}/edit', [AdminPackage::class, 'edit'])->name('packages.edit');
    Route::put('/packages/{package}', [AdminPackage::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [AdminPackage::class, 'destroy'])->name('packages.destroy');

    // Currencies
    Route::get('/currencies', [AdminCurrency::class, 'index'])->name('currencies.index');
    Route::get('/currencies/create', [AdminCurrency::class, 'create'])->name('currencies.create');
    Route::post('/currencies', [AdminCurrency::class, 'store'])->name('currencies.store');
    Route::get('/currencies/{currency}/edit', [AdminCurrency::class, 'edit'])->name('currencies.edit');
    Route::put('/currencies/{currency}', [AdminCurrency::class, 'update'])->name('currencies.update');
    Route::delete('/currencies/{currency}', [AdminCurrency::class, 'destroy'])->name('currencies.destroy');

    // Taxes
    Route::get('/taxes', [AdminTax::class, 'index'])->name('taxes.index');
    Route::get('/taxes/create', [AdminTax::class, 'create'])->name('taxes.create');
    Route::post('/taxes', [AdminTax::class, 'store'])->name('taxes.store');
    Route::get('/taxes/{tax}/edit', [AdminTax::class, 'edit'])->name('taxes.edit');
    Route::put('/taxes/{tax}', [AdminTax::class, 'update'])->name('taxes.update');
    Route::delete('/taxes/{tax}', [AdminTax::class, 'destroy'])->name('taxes.destroy');

    // Quotation Oversight
    Route::get('/quotations', [AdminQuotation::class, 'index'])->name('quotations.index');
    Route::get('/quotations/{quotation}', [AdminQuotation::class, 'show'])->name('quotations.show');

    // Admin Users
    Route::get('/users', [AdminUser::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUser::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUser::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUser::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUser::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUser::class, 'destroy'])->name('users.destroy');

    // Reports & Exports
    Route::get('/reports', [AdminReport::class, 'index'])->name('reports.index');
    Route::get('/reports/{type}/export', [AdminReport::class, 'export'])->name('reports.export');

    // Activity Log
    Route::get('/activity-log', [AdminActivity::class, 'index'])->name('activity-log.index');

    // System Health
    Route::get('/health', [AdminHealth::class, 'index'])->name('health.index');

    // Pages CMS
    Route::get('/pages', [AdminPage::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [AdminPage::class, 'create'])->name('pages.create');
    Route::post('/pages', [AdminPage::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}/edit', [AdminPage::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [AdminPage::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [AdminPage::class, 'destroy'])->name('pages.destroy');

    // Email Templates
    Route::get('/email-templates', [AdminEmailTemplate::class, 'index'])->name('email-templates.index');
    Route::get('/email-templates/{template}/edit', [AdminEmailTemplate::class, 'edit'])->name('email-templates.edit');
    Route::put('/email-templates/{template}', [AdminEmailTemplate::class, 'update'])->name('email-templates.update');

    // Settings
    Route::get('/settings', [AdminSettings::class, 'index'])->name('settings.index');
    Route::put('/settings/general', [AdminSettings::class, 'updateGeneral'])->name('settings.general');
    Route::put('/settings/social', [AdminSettings::class, 'updateSocial'])->name('settings.social');
    Route::put('/settings/pusher', [AdminSettings::class, 'updatePusher'])->name('settings.pusher');
    Route::put('/settings/email', [AdminSettings::class, 'updateEmail'])->name('settings.email');
});

// ── Public Pages ──
Route::get('/pages/{page}', function (\App\Models\Page $page) {
    if (!$page->is_published) abort(404);
    return view('pages.show', compact('page'));
})->name('pages.show');

// ── Company Panel (NOT admin) ──
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isSuperAdmin()) {
            return redirect('/admin/dashboard');
        }
        return app(CompanyDashboard::class)->index(request());
    })->name('dashboard');

    // ── Profile Settings (all users) ──
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
        Route::get('/quotations/{quotation}', [CompanyQuotation::class, 'show'])->name('quotations.show');
        Route::patch('/quotations/{quotation}/status', [CompanyQuotation::class, 'updateStatus'])->name('quotations.status');

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
