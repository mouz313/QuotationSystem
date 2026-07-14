<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\AdminRole;
use App\Models\Client;
use App\Models\Company;
use App\Models\CompanyPackage;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Package;
use App\Models\Page;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Super Admin ──
        $admin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@quotation.test',
            'password' => Hash::make('password'),
            'role'     => 'super_admin',
        ]);

        // ── Admin Roles ──
        $managerRole = AdminRole::create([
            'name'        => 'Manager',
            'permissions' => ['companies.manage', 'quotations.view', 'reports.view', 'activity.view'],
            'is_default'  => true,
        ]);
        AdminRole::create([
            'name'        => 'Viewer',
            'permissions' => ['quotations.view', 'reports.view'],
            'is_default'  => false,
        ]);

        // ── Default Pages ──
        Page::create(['slug' => 'terms', 'title' => 'Terms & Conditions', 'content' => '<h2>Terms and Conditions</h2><p>These Terms and Conditions govern your use of the QuotationSystem platform. By accessing or using our service, you agree to be bound by these terms.</p><h3>1. Acceptance of Terms</h3><p>By creating an account and using our platform, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</p><h3>2. Service Description</h3><p>QuotationSystem is a SaaS platform that allows businesses to create, manage, and track quotations for their clients.</p>', 'is_published' => true]);
        Page::create(['slug' => 'privacy', 'title' => 'Privacy Policy', 'content' => '<h2>Privacy Policy</h2><p>Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your personal information.</p><h3>Information We Collect</h3><p>We collect information you provide directly, including name, email address, company details, and quotation data.</p>', 'is_published' => true]);
        Page::create(['slug' => 'about', 'title' => 'About Us', 'content' => '<h2>About QuotationSystem</h2><p>QuotationSystem is a modern SaaS platform designed to streamline the quotation management process for businesses of all sizes.</p>', 'is_published' => true]);

        // ── Default Currencies ──
        Currency::create(['code' => 'USD', 'name' => 'US Dollar',       'symbol' => '$',  'is_default' => true]);
        Currency::create(['code' => 'EUR', 'name' => 'Euro',            'symbol' => '€',  'is_default' => false]);
        Currency::create(['code' => 'GBP', 'name' => 'British Pound',   'symbol' => '£',  'is_default' => false]);
        Currency::create(['code' => 'JPY', 'name' => 'Japanese Yen',    'symbol' => '¥',  'is_default' => false]);
        Currency::create(['code' => 'INR', 'name' => 'Indian Rupee',    'symbol' => '₹',  'is_default' => false]);
        Currency::create(['code' => 'AUD', 'name' => 'Australian Dollar','symbol' => 'A$', 'is_default' => false]);
        Currency::create(['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'is_default' => false]);
        Currency::create(['code' => 'SGD', 'name' => 'Singapore Dollar','symbol' => 'S$', 'is_default' => false]);

        // ── Default Taxes ──
        Tax::create(['name' => 'VAT',   'percentage' => 10.00, 'is_default' => true]);
        Tax::create(['name' => 'GST',   'percentage' => 5.00,  'is_default' => false]);
        Tax::create(['name' => 'Sales Tax', 'percentage' => 8.25, 'is_default' => false]);
        Tax::create(['name' => 'Service Tax', 'percentage' => 15.00, 'is_default' => false]);

        // ── Default Packages ──
        $starter = Package::create([
            'name' => 'Starter', 'description' => 'Perfect for small businesses.', 'price' => 9.99,
            'duration_days' => 30, 'max_users' => 2, 'max_clients' => 20, 'max_quotations' => 50,
        ]);
        $pro = Package::create([
            'name' => 'Professional', 'description' => 'For growing teams.', 'price' => 29.99,
            'duration_days' => 30, 'max_users' => 5, 'max_clients' => 100, 'max_quotations' => 500,
        ]);
        $enterprise = Package::create([
            'name' => 'Enterprise', 'description' => 'Large organizations.', 'price' => 79.99,
            'duration_days' => 30, 'max_users' => 50, 'max_clients' => 1000, 'max_quotations' => 10000,
        ]);

        // ── Company 1: TechCorp (Active + Pro) ──
        $techcorp = Company::create(['name' => 'TechCorp Solutions', 'email' => 'info@techcorp.com', 'phone' => '+1-555-0101', 'address' => '123 Silicon Ave, San Francisco, CA', 'status' => 'active']);
        CompanyPackage::create(['company_id' => $techcorp->id, 'package_id' => $pro->id, 'start_date' => now(), 'end_date' => now()->addDays(30), 'status' => 'active']);

        $tc_admin = User::create(['name' => 'John Founder', 'email' => 'john@techcorp.com', 'password' => Hash::make('password'), 'company_id' => $techcorp->id, 'role' => 'company_admin']);
        User::create(['name' => 'Sarah Dev', 'email' => 'sarah@techcorp.com', 'password' => Hash::make('password'), 'company_id' => $techcorp->id, 'role' => 'staff']);

        $tc_client1 = Client::create(['user_id' => $tc_admin->id, 'name' => 'Alice Johnson', 'email' => 'alice@globex.com', 'phone' => '+1-555-0201', 'address' => '456 Oak St, Portland, OR']);
        $tc_client2 = Client::create(['user_id' => $tc_admin->id, 'name' => 'Bob Martinez', 'email' => 'bob@initech.com', 'phone' => '+1-555-0202', 'address' => '789 Pine Rd, Austin, TX']);
        $tc_client3 = Client::create(['user_id' => $tc_admin->id, 'name' => 'Carol Williams', 'email' => 'carol@umbrella.co', 'phone' => '+1-555-0203']);

        Item::create(['user_id' => $tc_admin->id, 'title' => 'Web Application Development', 'description' => 'Full-stack web app development', 'unit_price' => 5000.00]);
        Item::create(['user_id' => $tc_admin->id, 'title' => 'UI/UX Design', 'description' => 'Complete interface design package', 'unit_price' => 2500.00]);
        Item::create(['user_id' => $tc_admin->id, 'title' => 'Cloud Hosting (Monthly)', 'description' => 'AWS cloud hosting per month', 'unit_price' => 299.00]);
        Item::create(['user_id' => $tc_admin->id, 'title' => 'API Integration', 'description' => 'Third-party API integration service', 'unit_price' => 1200.00]);

        $q1 = Quotation::create(['user_id' => $tc_admin->id, 'client_id' => $tc_client1->id, 'quote_number' => 'QT-20260714-0001', 'issue_date' => '2026-07-01', 'expiry_date' => '2026-07-31', 'discount_amount' => 200.00, 'tax_percentage' => 5.00, 'grand_total' => 7647.50, 'status' => 'accepted', 'terms_conditions' => 'Payment due within 30 days of acceptance.']);
        QuotationItem::create(['quotation_id' => $q1->id, 'item_title' => 'Web Application Development', 'item_description' => 'Full-stack web app development', 'quantity' => 1, 'unit_price' => 5000.00, 'subtotal' => 5000.00]);
        QuotationItem::create(['quotation_id' => $q1->id, 'item_title' => 'UI/UX Design', 'item_description' => 'Complete interface design package', 'quantity' => 1, 'unit_price' => 2500.00, 'subtotal' => 2500.00]);

        $q2 = Quotation::create(['user_id' => $tc_admin->id, 'client_id' => $tc_client2->id, 'quote_number' => 'QT-20260714-0002', 'issue_date' => '2026-07-10', 'expiry_date' => '2026-08-10', 'discount_amount' => 0.00, 'tax_percentage' => 5.00, 'grand_total' => 314.85, 'status' => 'sent']);
        QuotationItem::create(['quotation_id' => $q2->id, 'item_title' => 'Cloud Hosting (Monthly)', 'item_description' => 'AWS cloud hosting per month', 'quantity' => 1, 'unit_price' => 299.00, 'subtotal' => 299.00]);

        $q3 = Quotation::create(['user_id' => $tc_admin->id, 'client_id' => $tc_client3->id, 'quote_number' => 'QT-20260714-0003', 'issue_date' => '2026-07-14', 'expiry_date' => '2026-07-28', 'discount_amount' => 100.00, 'tax_percentage' => 5.00, 'grand_total' => 1365.00, 'status' => 'draft', 'terms_conditions' => 'Payment due within 14 days of acceptance.']);
        QuotationItem::create(['quotation_id' => $q3->id, 'item_title' => 'API Integration', 'item_description' => 'Third-party API integration service', 'quantity' => 1, 'unit_price' => 1200.00, 'subtotal' => 1200.00]);

        // ── Company 2: DesignStudio (Active + Starter) ──
        $design = Company::create(['name' => 'Creative Design Studio', 'email' => 'hello@designstudio.io', 'phone' => '+1-555-0301', 'address' => '321 Art Blvd, New York, NY', 'status' => 'active']);
        CompanyPackage::create(['company_id' => $design->id, 'package_id' => $starter->id, 'start_date' => now(), 'end_date' => now()->addDays(30), 'status' => 'active']);

        $ds_admin = User::create(['name' => 'Emma Creative', 'email' => 'emma@designstudio.io', 'password' => Hash::make('password'), 'company_id' => $design->id, 'role' => 'company_admin']);

        $ds_client1 = Client::create(['user_id' => $ds_admin->id, 'name' => 'Frank Bakery', 'email' => 'frank@sweetbites.com', 'phone' => '+1-555-0401']);
        $ds_client2 = Client::create(['user_id' => $ds_admin->id, 'name' => 'Grace Fitness', 'email' => 'grace@fitlife.com', 'phone' => '+1-555-0402']);

        Item::create(['user_id' => $ds_admin->id, 'title' => 'Logo Design', 'description' => 'Custom brand logo with 3 revisions', 'unit_price' => 800.00]);
        Item::create(['user_id' => $ds_admin->id, 'title' => 'Business Card Design', 'description' => 'Double-sided business card', 'unit_price' => 150.00]);
        Item::create(['user_id' => $ds_admin->id, 'title' => 'Social Media Kit', 'description' => 'Templates for Instagram, Facebook, Twitter', 'unit_price' => 400.00]);

        $q4 = Quotation::create(['user_id' => $ds_admin->id, 'client_id' => $ds_client1->id, 'quote_number' => 'QT-20260714-0004', 'issue_date' => '2026-07-12', 'expiry_date' => '2026-07-26', 'discount_amount' => 50.00, 'tax_percentage' => 0.00, 'grand_total' => 1300.00, 'status' => 'accepted']);
        QuotationItem::create(['quotation_id' => $q4->id, 'item_title' => 'Logo Design', 'item_description' => 'Custom brand logo with 3 revisions', 'quantity' => 1, 'unit_price' => 800.00, 'subtotal' => 800.00]);
        QuotationItem::create(['quotation_id' => $q4->id, 'item_title' => 'Business Card Design', 'item_description' => 'Double-sided business card', 'quantity' => 2, 'unit_price' => 150.00, 'subtotal' => 300.00]);
        QuotationItem::create(['quotation_id' => $q4->id, 'item_title' => 'Social Media Kit', 'item_description' => 'Templates for Instagram, Facebook, Twitter', 'quantity' => 1, 'unit_price' => 400.00, 'subtotal' => 400.00]);

        $q5 = Quotation::create(['user_id' => $ds_admin->id, 'client_id' => $ds_client2->id, 'quote_number' => 'QT-20260714-0005', 'issue_date' => '2026-07-14', 'expiry_date' => '2026-08-14', 'discount_amount' => 0.00, 'tax_percentage' => 8.00, 'grand_total' => 432.00, 'status' => 'sent']);
        QuotationItem::create(['quotation_id' => $q5->id, 'item_title' => 'Social Media Kit', 'item_description' => 'Templates for Instagram, Facebook, Twitter', 'quantity' => 1, 'unit_price' => 400.00, 'subtotal' => 400.00]);

        // ── Company 3: BuildRight (Inactive, no package) ──
        $build = Company::create(['name' => 'BuildRight Construction', 'email' => 'contact@buildright.com', 'phone' => '+1-555-0501', 'address' => '555 Builder Ln, Chicago, IL', 'status' => 'inactive']);
        User::create(['name' => 'Mike Builder', 'email' => 'mike@buildright.com', 'password' => Hash::make('password'), 'company_id' => $build->id, 'role' => 'company_admin']);

        // ── Company 4: DataFlow (Blocked) ──
        $dataflow = Company::create(['name' => 'DataFlow Analytics', 'email' => 'support@dataflow.io', 'phone' => '+1-555-0601', 'address' => '888 Data Dr, Seattle, WA', 'status' => 'blocked']);
        CompanyPackage::create(['company_id' => $dataflow->id, 'package_id' => $enterprise->id, 'start_date' => now()->subDays(15), 'end_date' => now()->addDays(15), 'status' => 'active']);
        User::create(['name' => 'Lisa Analyst', 'email' => 'lisa@dataflow.io', 'password' => Hash::make('password'), 'company_id' => $dataflow->id, 'role' => 'company_admin']);

        // ── Seed Activity Log ──
        ActivityLog::create(['user_id' => $admin->id, 'action' => 'login', 'description' => 'Admin logged in', 'ip_address' => '127.0.0.1', 'created_at' => now()->subDays(5)]);
        ActivityLog::create(['user_id' => $admin->id, 'action' => 'created', 'subject_type' => Company::class, 'subject_id' => $techcorp->id, 'description' => 'Created company TechCorp Solutions', 'ip_address' => '127.0.0.1', 'created_at' => now()->subDays(4)]);
        ActivityLog::create(['user_id' => $admin->id, 'action' => 'created', 'subject_type' => Company::class, 'subject_id' => $design->id, 'description' => 'Created company Creative Design Studio', 'ip_address' => '127.0.0.1', 'created_at' => now()->subDays(3)]);
        ActivityLog::create(['user_id' => $admin->id, 'action' => 'package_assigned', 'subject_type' => Company::class, 'subject_id' => $techcorp->id, 'description' => 'Assigned Professional package to TechCorp Solutions', 'ip_address' => '127.0.0.1', 'created_at' => now()->subDays(2)]);
        ActivityLog::create(['user_id' => $admin->id, 'action' => 'status_changed', 'subject_type' => Company::class, 'subject_id' => $dataflow->id, 'description' => 'Blocked company DataFlow Analytics', 'ip_address' => '127.0.0.1', 'created_at' => now()->subDay()]);
    }
}
