<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Staff;
use App\Models\Patient;
use App\Models\Surgery;
use App\Models\Anaesthesia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuditFormSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $doctor;
    protected User $dataEntry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@hospital.test'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'),
                'role' => 'system_admin',
            ]
        );
        User::where('role', 'system_admin')->where('id', '!=', $this->admin->id)->delete();

        $this->doctor = User::factory()->create([
            'name' => 'Dr. Alexander Fleming',
            'email' => 'fleming@hospital.test',
            'role' => 'doctor',
        ]);

        $this->dataEntry = User::factory()->create([
            'name' => 'Jane Data',
            'email' => 'jane@hospital.test',
            'role' => 'data_entry',
        ]);
    }

    public function test_admin_can_view_audit_form_settings_hub(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.audit-form-settings.index'));
        $response->assertOk();
        $response->assertSee('Audit Form Settings');
        $response->assertSee('Doctors in Audit Form');
        $response->assertSee('Manage Clinical Master Data');
    }

    public function test_non_admin_cannot_access_audit_form_settings(): void
    {
        $this->actingAs($this->doctor)->get(route('admin.audit-form-settings.index'))->assertForbidden();
        $this->actingAs($this->dataEntry)->get(route('admin.audit-form-settings.index'))->assertForbidden();
        $this->get(route('admin.audit-form-settings.index'))->assertForbidden();

        $this->actingAs($this->doctor)->get(route('admin.audit-form-settings.doctors'))->assertForbidden();
    }

    public function test_admin_can_view_doctors_list(): void
    {
        $staff1 = Staff::create(['name' => 'Dr. Gregory House', 'is_active' => true]);
        $staff2 = Staff::create(['name' => 'Dr. James Wilson', 'is_active' => false]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-form-settings.doctors'));
        $response->assertOk();
        $response->assertSee('Doctors in Audit Form');
        $response->assertSee('Dr. Gregory House');
        $response->assertSee('Dr. James Wilson');
        $response->assertSee('Appears in Audit Form');
        $response->assertSee('Hidden from Form');
    }

    public function test_admin_can_add_new_doctor_to_roster(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.audit-form-settings.doctors.store'), [
            'name' => 'Dr. Allison Cameron',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.audit-form-settings.doctors'));
        $this->assertDatabaseHas('staff', [
            'name' => 'Dr. Allison Cameron',
            'is_active' => 1,
        ]);
    }

    public function test_admin_can_toggle_doctor_visibility_in_audit_form(): void
    {
        $doctorStaff = Staff::create(['name' => 'Dr. Eric Foreman', 'is_active' => true]);

        // Toggle to hidden
        $response = $this->actingAs($this->admin)->patch(route('admin.audit-form-settings.doctors.toggle', $doctorStaff));
        $response->assertRedirect();

        $doctorStaff->refresh();
        $this->assertFalse($doctorStaff->is_active);

        // Doctor should NOT appear in the audit form options for surgeons/anaesthetists
        $formResponse = $this->actingAs($this->doctor)->get(route('doctor.audit-form.create'));
        $formResponse->assertOk();
        $formResponse->assertDontSee('Dr. Eric Foreman');

        // Toggle back to visible
        $this->actingAs($this->admin)->patch(route('admin.audit-form-settings.doctors.toggle', $doctorStaff));
        $doctorStaff->refresh();
        $this->assertTrue($doctorStaff->is_active);

        // Doctor should now appear in the audit form
        $formResponse2 = $this->actingAs($this->doctor)->get(route('doctor.audit-form.create'));
        $formResponse2->assertOk();
        $formResponse2->assertSee('Dr. Eric Foreman');
    }

    public function test_admin_can_update_doctor_details(): void
    {
        $doctorStaff = Staff::create(['name' => 'Dr. Robert Chase', 'is_active' => true]);

        $response = $this->actingAs($this->admin)->put(route('admin.audit-form-settings.doctors.update', $doctorStaff), [
            'name' => 'Dr. Robert Chase Snr',
            'is_active' => 0,
        ]);

        $response->assertRedirect(route('admin.audit-form-settings.doctors'));
        $doctorStaff->refresh();
        $this->assertEquals('Dr. Robert Chase Snr', $doctorStaff->name);
        $this->assertFalse($doctorStaff->is_active);
    }

    public function test_admin_cannot_delete_doctor_with_historical_audits(): void
    {
        $doctorStaff = Staff::create(['name' => 'Dr. Lisa Cuddy', 'is_active' => true]);

        $patient = Patient::create([
            'name' => 'Historical Patient',
            'phn' => 'PHN-TEST01',
            'age' => 45,
            'sex' => 'F',
        ]);

        $surgery = Surgery::create([
            'patient_id' => $patient->patient_id,
            'operation_name' => 'Thyroidectomy',
            'surgery_type' => 'Elect',
            'date_of_surgery' => '2026-02-01',
        ]);

        $surgery->staff()->attach($doctorStaff->staff_id, ['role_in_case' => 'Consultant Anaesthetist']);

        // Check page shows "Has Records" disabled state
        $pageResponse = $this->actingAs($this->admin)->get(route('admin.audit-form-settings.doctors'));
        $pageResponse->assertOk();
        $pageResponse->assertSee('Has Records');

        // Attempt delete
        $response = $this->actingAs($this->admin)->delete(route('admin.audit-form-settings.doctors.destroy', $doctorStaff));
        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('staff', ['staff_id' => $doctorStaff->staff_id]);
    }

    public function test_admin_can_delete_doctor_with_zero_surgeries(): void
    {
        $doctorStaff = Staff::create(['name' => 'Dr. Unlinked Practitioner', 'is_active' => false]);

        // Check page shows active Delete button
        $pageResponse = $this->actingAs($this->admin)->get(route('admin.audit-form-settings.doctors'));
        $pageResponse->assertOk();
        $pageResponse->assertSee('Dr. Unlinked Practitioner');
        $pageResponse->assertSee('Delete');

        $response = $this->actingAs($this->admin)->delete(route('admin.audit-form-settings.doctors.destroy', $doctorStaff));
        $response->assertSessionHas('status');
        $this->assertDatabaseMissing('staff', ['staff_id' => $doctorStaff->staff_id]);

        // Consume flash message then ensure subsequent view does not contain deleted doctor
        $this->actingAs($this->admin)->get(route('admin.audit-form-settings.doctors'));
        $followUp = $this->actingAs($this->admin)->get(route('admin.audit-form-settings.doctors'));
        $followUp->assertDontSee('Dr. Unlinked Practitioner');
    }

    public function test_admin_dashboard_shows_audit_form_settings_section(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Audit Form Settings');
        $response->assertSee('Doctors in Audit Form');
        $response->assertSee('Manage Master Data');
    }

    public function test_sidebar_has_audit_form_settings_link(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        // The sidebar navigation has "Audit Form Settings" link pointing to the hub
        $response->assertSee(route('admin.audit-form-settings.index'));
        $response->assertSee('Audit Form Settings');
    }
}

