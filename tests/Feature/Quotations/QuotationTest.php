<?php

namespace Tests\Feature\Quotations;

use App\Models\Client;
use App\Models\Company;
use App\Models\CompanyPackage;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Package;
use App\Models\Tax;
use App\Models\User;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
        $package = Package::factory()->create([
            'max_quotations' => 1000,
            'max_clients'     => 1000,
            'max_users'       => 100,
            'duration_days'   => 365,
        ]);
        CompanyPackage::factory()->create([
            'company_id'  => $this->company->id,
            'package_id'  => $package->id,
            'start_date'  => now()->toDateString(),
            'end_date'    => now()->addYear()->toDateString(),
            'status'      => 'active',
        ]);
        $this->user = User::factory()->companyAdmin()->create([
            'company_id' => $this->company->id,
        ]);
        Currency::factory()->default()->create();
    }

    public function test_quotation_index_is_displayed(): void
    {
        $response = $this->actingAs($this->user)->get('/quotations');
        $response->assertStatus(200);
        $response->assertSee('Quotations');
    }

    public function test_company_user_can_create_quotation(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);
        $currency = Currency::first();

        $response = $this->actingAs($this->user)->post('/quotations', [
            'client_id'        => $client->id,
            'currency_id'      => $currency->id,
            'type'             => 'simple',
            'issue_date'       => now()->toDateString(),
            'expiry_date'      => now()->addDays(30)->toDateString(),
            'discount_amount'  => 0,
            'tax_percentage'   => 0,
            'terms_conditions' => 'Standard terms',
            'items'            => [
                ['item_title' => 'Service A', 'quantity' => 1, 'unit_price' => 1000],
                ['item_title' => 'Service B', 'quantity' => 2, 'unit_price' => 500],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quotations', [
            'user_id'   => $this->user->id,
            'client_id' => $client->id,
            'type'      => 'simple',
            'status'    => 'draft',
        ]);
    }

    public function test_quotation_requires_client(): void
    {
        $currency = Currency::first();

        $response = $this->actingAs($this->user)->post('/quotations', [
            'currency_id'      => $currency->id,
            'type'             => 'simple',
            'issue_date'       => now()->toDateString(),
            'discount_amount'  => 0,
            'tax_percentage'   => 0,
            'items'            => [
                ['item_title' => 'Service', 'quantity' => 1, 'unit_price' => 100],
            ],
        ]);

        $response->assertSessionHasErrors('client_id');
    }

    public function test_quotation_requires_items(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);
        $currency = Currency::first();

        $response = $this->actingAs($this->user)->post('/quotations', [
            'client_id'        => $client->id,
            'currency_id'      => $currency->id,
            'type'             => 'simple',
            'issue_date'       => now()->toDateString(),
            'discount_amount'  => 0,
            'tax_percentage'   => 0,
            'items'            => [],
        ]);

        $response->assertSessionHasErrors('items');
    }

    public function test_user_can_view_quotation(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);
        $currency = Currency::first();
        $quotation = Quotation::factory()->create([
            'user_id'     => $this->user->id,
            'client_id'   => $client->id,
            'currency_id' => $currency->id,
        ]);

        $response = $this->actingAs($this->user)->get("/quotations/{$quotation->id}");
        $response->assertStatus(200);
        $response->assertSee($quotation->quote_number);
    }

    public function test_user_cannot_view_other_users_quotation(): void
    {
        $otherUser = User::factory()->create(['company_id' => $this->company->id]);
        $client = Client::factory()->create(['user_id' => $otherUser->id]);
        $currency = Currency::first();
        $quotation = Quotation::factory()->create([
            'user_id'     => $otherUser->id,
            'client_id'   => $client->id,
            'currency_id' => $currency->id,
        ]);

        $response = $this->actingAs($this->user)->get("/quotations/{$quotation->id}");
        $response->assertStatus(403);
    }

    public function test_user_can_update_quotation(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);
        $currency = Currency::first();
        $quotation = Quotation::factory()->create([
            'user_id'     => $this->user->id,
            'client_id'   => $client->id,
            'currency_id' => $currency->id,
            'type'        => 'simple',
            'status'      => 'draft',
        ]);

        $response = $this->actingAs($this->user)->put("/quotations/{$quotation->id}", [
            'client_id'        => $client->id,
            'currency_id'      => $currency->id,
            'type'             => 'simple',
            'issue_date'       => now()->toDateString(),
            'discount_amount'  => 0,
            'tax_percentage'   => 0,
            'terms_conditions' => 'Updated terms',
            'items'            => [
                ['item_title' => 'Updated Service', 'quantity' => 1, 'unit_price' => 2000],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quotation_items', [
            'item_title' => 'Updated Service',
            'unit_price' => 2000,
        ]);
    }

    public function test_user_can_delete_quotation(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);
        $currency = Currency::first();
        $quotation = Quotation::factory()->create([
            'user_id'     => $this->user->id,
            'client_id'   => $client->id,
            'currency_id' => $currency->id,
        ]);

        $response = $this->actingAs($this->user)->delete("/quotations/{$quotation->id}");
        $response->assertRedirect('/quotations');
        $this->assertSoftDeleted('quotations', ['id' => $quotation->id]);
    }

    public function test_user_can_clone_quotation(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);
        $currency = Currency::first();
        $quotation = Quotation::factory()->create([
            'user_id'     => $this->user->id,
            'client_id'   => $client->id,
            'currency_id' => $currency->id,
            'status'      => 'draft',
        ]);
        QuotationItem::factory()->create(['quotation_id' => $quotation->id]);

        $response = $this->actingAs($this->user)->post("/quotations/{$quotation->id}/clone");
        $response->assertRedirect();

        $this->assertDatabaseHas('quotations', [
            'user_id' => $this->user->id,
            'status'  => 'draft',
        ]);
        $this->assertEquals(2, Quotation::where('user_id', $this->user->id)->count());
    }

    public function test_user_can_update_quotation_status(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);
        $currency = Currency::first();
        $quotation = Quotation::factory()->create([
            'user_id'     => $this->user->id,
            'client_id'   => $client->id,
            'currency_id' => $currency->id,
            'status'      => 'draft',
        ]);

        $response = $this->actingAs($this->user)->patch("/quotations/{$quotation->id}/status", [
            'status' => 'sent',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quotations', ['id' => $quotation->id, 'status' => 'sent']);
    }

    public function test_bulk_delete_quotations(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);
        $currency = Currency::first();
        $q1 = Quotation::factory()->create(['user_id' => $this->user->id, 'client_id' => $client->id, 'currency_id' => $currency->id]);
        $q2 = Quotation::factory()->create(['user_id' => $this->user->id, 'client_id' => $client->id, 'currency_id' => $currency->id]);

        $response = $this->actingAs($this->user)->post('/quotations/bulk-delete', [
            'ids' => [$q1->id, $q2->id],
        ]);

        $response->assertRedirect('/quotations');
        $this->assertSoftDeleted('quotations', ['id' => $q1->id]);
        $this->assertSoftDeleted('quotations', ['id' => $q2->id]);
    }
}
