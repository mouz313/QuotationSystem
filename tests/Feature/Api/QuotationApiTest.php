<?php

namespace Tests\Feature\Api;

use App\Models\Client;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use App\Models\Quotation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class QuotationApiTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;
    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
        $this->user = User::factory()->companyAdmin()->create([
            'company_id' => $this->company->id,
        ]);
        $this->currency = Currency::factory()->default()->create();
    }

    public function test_api_create_quotation(): void
    {
        Sanctum::actingAs($this->user);

        $client = Client::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson('/api/v1/quotations', [
            'client_id'        => $client->id,
            'currency_id'      => $this->currency->id,
            'type'             => 'simple',
            'issue_date'       => now()->toDateString(),
            'discount_amount'  => 0,
            'tax_percentage'   => 0,
            'items'            => [
                ['item_title' => 'Service', 'quantity' => 1, 'unit_price' => 1000],
            ],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('quotations', [
            'user_id'   => $this->user->id,
            'client_id' => $client->id,
        ]);
    }

    public function test_api_unauthenticated_access_rejected(): void
    {
        $response = $this->postJson('/api/v1/quotations', []);
        $response->assertStatus(401);
    }
}
