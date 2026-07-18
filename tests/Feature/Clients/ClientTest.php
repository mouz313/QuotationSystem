<?php

namespace Tests\Feature\Clients;

use App\Models\Client;
use App\Models\Company;
use App\Models\CompanyPackage;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user = User::factory()->companyAdmin()->create([
            'company_id' => $this->company->id,
        ]);

        $package = Package::factory()->professional()->create();
        CompanyPackage::factory()->create([
            'company_id' => $this->company->id,
            'package_id' => $package->id,
            'status'     => 'active',
            'start_date' => now()->subDay(),
            'end_date'   => now()->addDays(30),
        ]);
    }

    public function test_client_index_is_displayed(): void
    {
        Client::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get('/clients');
        $response->assertStatus(200);
    }

    public function test_user_can_create_client(): void
    {
        $response = $this->actingAs($this->user)->post('/clients', [
            'name'    => 'New Client',
            'email'   => 'newclient@test.com',
            'phone'   => '1234567890',
            'address' => '123 Main St',
        ]);

        $response->assertRedirect('/clients');
        $this->assertDatabaseHas('clients', [
            'user_id' => $this->user->id,
            'name'    => 'New Client',
            'email'   => 'newclient@test.com',
        ]);
    }

    public function test_user_can_update_client(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->put("/clients/{$client->id}", [
            'name'  => 'Updated Name',
            'email' => $client->email,
        ]);

        $response->assertRedirect('/clients');
        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'Updated Name']);
    }

    public function test_user_can_delete_client(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete("/clients/{$client->id}");
        $response->assertRedirect('/clients');
        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_user_cannot_update_other_users_client(): void
    {
        $otherUser = User::factory()->create(['company_id' => $this->company->id]);
        $client = Client::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->put("/clients/{$client->id}", [
            'name'  => 'Hacked',
            'email' => $client->email,
        ]);

        $response->assertStatus(403);
    }
}
