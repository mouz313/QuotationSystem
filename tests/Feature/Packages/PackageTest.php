<?php

namespace Tests\Feature\Packages;

use App\Models\Company;
use App\Models\CompanyPackage;
use App\Models\Package;
use App\Models\PackageOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    private User $companyAdmin;
    private Company $company;
    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
        $this->companyAdmin = User::factory()->companyAdmin()->create([
            'company_id' => $this->company->id,
        ]);
        $this->superAdmin = User::factory()->superAdmin()->create();
    }

    public function test_company_can_browse_packages(): void
    {
        Package::factory()->free()->create();
        Package::factory()->professional()->create();

        $response = $this->actingAs($this->companyAdmin)->get('/packages');
        $response->assertStatus(200);
        $response->assertSee('Free');
        $response->assertSee('Professional');
    }

    public function test_company_can_purchase_package(): void
    {
        $package = Package::factory()->professional()->create();

        $response = $this->actingAs($this->companyAdmin)->post("/packages/{$package->id}/purchase", [
            'payment_method' => 'bank_transfer',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('package_orders', [
            'company_id' => $this->company->id,
            'package_id' => $package->id,
            'status'     => 'pending',
        ]);
    }

    public function test_company_can_view_subscription(): void
    {
        $package = Package::factory()->professional()->create();
        CompanyPackage::factory()->create([
            'company_id' => $this->company->id,
            'package_id' => $package->id,
            'status'     => 'active',
        ]);

        $response = $this->actingAs($this->companyAdmin)->get('/packages/subscription');
        $response->assertStatus(200);
    }

    public function test_company_can_view_order_history(): void
    {
        PackageOrder::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'pending',
        ]);

        $response = $this->actingAs($this->companyAdmin)->get('/packages/orders');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_package_orders(): void
    {
        PackageOrder::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->superAdmin)->get('/admin/package-orders');
        $response->assertStatus(200);
    }

    public function test_admin_can_approve_package_order(): void
    {
        $package = Package::factory()->professional()->create();
        $order = PackageOrder::factory()->create([
            'company_id' => $this->company->id,
            'package_id' => $package->id,
            'status'     => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)->post("/admin/package-orders/{$order->id}/approve");
        $response->assertRedirect();

        $this->assertDatabaseHas('package_orders', ['id' => $order->id, 'status' => 'paid']);
        $this->assertDatabaseHas('company_packages', [
            'company_id' => $this->company->id,
            'package_id' => $package->id,
            'status'     => 'active',
        ]);
    }

    public function test_admin_can_reject_package_order(): void
    {
        $order = PackageOrder::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)->post("/admin/package-orders/{$order->id}/reject");
        $response->assertRedirect();

        $this->assertDatabaseHas('package_orders', ['id' => $order->id, 'status' => 'failed']);
    }

    public function test_company_user_cannot_approve_order(): void
    {
        $order = PackageOrder::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->companyAdmin)->post("/admin/package-orders/{$order->id}/approve");
        $response->assertStatus(403);
    }
}
