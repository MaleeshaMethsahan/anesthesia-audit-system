<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ProfileSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $doctor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@hospital.test'],
            [
                'name' => 'System Admin',
                'password' => bcrypt('password'),
                'role' => 'system_admin',
            ]
        );

        $this->doctor = User::factory()->create([
            'name' => 'Dr. Lisa Cuddy',
            'email' => 'cuddy@hospital.test',
            'password' => bcrypt('password123'),
            'role' => 'doctor',
        ]);
    }

    public function test_profile_page_is_displayed_with_new_ui(): void
    {
        $response = $this->actingAs($this->doctor)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('Profile & Settings');
        $response->assertSee('Dr. Lisa Cuddy');
        $response->assertSee('cuddy@hospital.test');
        $response->assertSee('Doctor');
        $response->assertSee('Profile Information');
        $response->assertSee('Update Password');
        $response->assertSee('Delete Account');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $response = $this->actingAs($this->doctor)->patch(route('profile.update'), [
            'name' => 'Dr. Lisa M. Cuddy',
            'email' => 'cuddy.new@hospital.test',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('profile.edit'));

        $this->doctor->refresh();

        $this->assertSame('Dr. Lisa M. Cuddy', $this->doctor->name);
        $this->assertSame('cuddy.new@hospital.test', $this->doctor->email);
        $this->assertNull($this->doctor->email_verified_at);

        // Staff record for doctor must be synchronized
        $this->assertTrue(Staff::where('name', 'Dr. Lisa M. Cuddy')->exists());
    }

    public function test_password_can_be_updated(): void
    {
        $response = $this->from(route('profile.edit'))->actingAs($this->doctor)->put(route('password.update'), [
            'current_password' => 'password123',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('profile.edit'));

        $this->assertTrue(Hash::check('new-secure-password', $this->doctor->refresh()->password));
    }

    public function test_sole_system_admin_cannot_delete_their_own_account(): void
    {
        // Only 1 admin in the system
        User::where('role', 'system_admin')->where('id', '!=', $this->admin->id)->delete();

        $response = $this->actingAs($this->admin)->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

        $response->assertSessionHasErrorsIn('userDeletion', 'password');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_doctor_can_delete_their_account(): void
    {
        $response = $this->actingAs($this->doctor)->delete(route('profile.destroy'), [
            'password' => 'password123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $this->doctor->id]);
    }
}
