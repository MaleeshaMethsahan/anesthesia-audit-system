<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsernameAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Username or Email');
    }

    public function test_users_can_authenticate_using_the_login_screen_with_email(): void
    {
        $user = User::factory()->create([
            'email' => 'doctor1@hospital.test',
            'username' => 'doc_smith',
            'password' => bcrypt('password123'),
            'role' => 'doctor',
        ]);

        $response = $this->post('/login', [
            'login' => 'doctor1@hospital.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/doctor/dashboard');
    }

    public function test_users_can_authenticate_using_the_login_screen_with_username(): void
    {
        $user = User::factory()->create([
            'email' => 'doctor2@hospital.test',
            'username' => 'dr_watson',
            'password' => 'secretPass!',
            'role' => 'doctor',
        ]);

        $response = $this->post('/login', [
            'login' => 'dr_watson',
            'password' => 'secretPass!',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/doctor/dashboard');
    }

    public function test_admin_can_authenticate_using_username(): void
    {
        $admin = User::where('email', 'admindoa@jaffna.org')->first();
        if ($admin) {
            $admin->update([
                'password' => bcrypt('admin123@doa'),
                'username' => 'admindoa',
            ]);
        } else {
            $admin = User::factory()->create([
                'email' => 'admindoa@jaffna.org',
                'username' => 'admindoa',
                'password' => 'admin123@doa',
                'role' => 'system_admin',
            ]);
        }

        $response = $this->post('/login', [
            'login' => 'admindoa',
            'password' => 'admin123@doa',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'username' => 'nurse_jackie',
            'password' => 'correct-password',
        ]);

        $this->post('/login', [
            'login' => 'nurse_jackie',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_admin_can_create_user_with_username(): void
    {
        $admin = User::factory()->create(['role' => 'system_admin']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Dr. Jane Foster',
            'username' => 'jfoster',
            'email' => 'foster@hospital.test',
            'role' => 'doctor',
            'password' => 'ValidPass123!',
            'password_confirmation' => 'ValidPass123!',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'jfoster',
            'email' => 'foster@hospital.test',
        ]);
    }

    public function test_admin_cannot_create_user_with_duplicate_username(): void
    {
        $admin = User::factory()->create(['role' => 'system_admin']);
        User::factory()->create(['username' => 'existing_user']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Another User',
            'username' => 'existing_user',
            'email' => 'another@hospital.test',
            'role' => 'doctor',
            'password' => 'ValidPass123!',
            'password_confirmation' => 'ValidPass123!',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    public function test_admin_can_search_user_by_username(): void
    {
        $admin = User::factory()->create(['role' => 'system_admin']);
        $targetUser = User::factory()->create([
            'name' => 'Gregory House',
            'username' => 'dr_house',
            'email' => 'house@hospital.test',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'dr_house']));

        $response->assertStatus(200);
        $response->assertSee('Gregory House');
        $response->assertSee('@dr_house');
    }
}
