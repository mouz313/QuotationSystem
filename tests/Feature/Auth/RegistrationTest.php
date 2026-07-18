<?php

namespace Tests\Feature\Auth;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_is_displayed(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_user_can_register_with_company(): void
    {
        $response = $this->post('/register', [
            'name'              => 'Test User',
            'email'             => 'test@example.com',
            'password'          => 'password123',
            'password_confirmation' => 'password123',
            'company_name'      => 'Test Company',
            'company_email'     => 'test@company.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'company_admin']);
        $this->assertDatabaseHas('companies', ['name' => 'Test Company']);
    }

    public function test_registration_requires_company_name(): void
    {
        $response = $this->post('/register', [
            'name'              => 'Test User',
            'email'             => 'test@example.com',
            'password'          => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('company_name');
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name'              => 'Test User',
            'email'             => 'existing@example.com',
            'password'          => 'password123',
            'password_confirmation' => 'password123',
            'company_name'      => 'Test Company',
            'company_email'     => 'test@company.com',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
