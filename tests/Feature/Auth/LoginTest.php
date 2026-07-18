<?php

namespace Tests\Feature\Auth;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_admin_can_login(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->companyAdmin()->create([
            'company_id' => $company->id,
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_staff_can_login(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->staff()->create([
            'company_id' => $company->id,
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_rejected(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->companyAdmin()->create([
            'company_id' => $company->id,
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_blocked_company_user_cannot_access_dashboard(): void
    {
        $company = Company::factory()->blocked()->create();
        $user = User::factory()->companyAdmin()->create([
            'company_id' => $company->id,
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_super_admin_can_login_to_admin_panel(): void
    {
        $user = User::factory()->superAdmin()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_logout(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->companyAdmin()->create([
            'company_id' => $company->id,
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
