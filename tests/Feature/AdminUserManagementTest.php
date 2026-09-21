<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Surgery;
use App\Models\Anaesthesia;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $doctor;
    protected User $dataEntry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Super Administrator',
            'email' => 'admin@hospital.test',
            'role' => 'system_admin',
        ]);

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

    public function test_admin_can_view_users_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertOk();
        $response->assertSee('User Management');
        $response->assertSee('Super Administrator');
        $response->assertSee('Dr. Alexander Fleming');
        $response->assertSee('Jane Data');
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        // Doctor is forbidden
        $responseDoctor = $this->actingAs($this->doctor)->get(route('admin.users.index'));
        $responseDoctor->assertForbidden();

        // Data Entry is forbidden
        $responseDE = $this->actingAs($this->dataEntry)->get(route('admin.users.index'));
        $responseDE->assertForbidden();

        // Guest is also forbidden / unauthorized
        $responseGuest = $this->get(route('admin.users.index'));
        $responseGuest->assertForbidden();
    }

    public function test_admin_can_create_new_doctor_user_and_staff_record_is_created(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Dr. Gregory House',
            'email' => 'house@hospital.test',
            'password' => 'Diagnostician123!',
            'password_confirmation' => 'Diagnostician123!',
            'role' => 'doctor',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'name' => 'Dr. Gregory House',
            'email' => 'house@hospital.test',
            'role' => 'doctor',
        ]);

        // Staff record for clinical audits must exist
        $this->assertDatabaseHas('staff', [
            'name' => 'Dr. Gregory House',
        ]);
    }

    public function test_admin_can_edit_user_role_email_and_password(): void
    {
        $targetUser = User::factory()->create([
            'name' => 'Temporary Nurse',
            'email' => 'temp@hospital.test',
            'role' => 'data_entry',
        ]);

        $responseView = $this->actingAs($this->admin)->get(route('admin.users.edit', $targetUser));
        $responseView->assertOk();
        $responseView->assertSee('Edit User Account');
        $responseView->assertSee('Temporary Nurse');

        $responseUpdate = $this->actingAs($this->admin)->put(route('admin.users.update', $targetUser), [
            'name' => 'Permanent Operator',
            'email' => 'perm@hospital.test',
            'role' => 'data_entry',
            'password' => 'NewStrongPass123!',
            'password_confirmation' => 'NewStrongPass123!',
        ]);

        $responseUpdate->assertRedirect(route('admin.users.index'));
        $responseUpdate->assertSessionHas('status');

        $targetUser->refresh();
        $this->assertEquals('Permanent Operator', $targetUser->name);
        $this->assertEquals('perm@hospital.test', $targetUser->email);
        $this->assertTrue(Hash::check('NewStrongPass123!', $targetUser->password));
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));
        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
            'email' => 'admin@hospital.test',
        ]);
    }

    public function test_deleting_doctor_account_preserves_all_audit_and_surgery_and_patient_records(): void
    {
        // 1. Create a doctor account
        $doctorUser = User::factory()->create([
            'name' => 'Dr. John Watson',
            'email' => 'watson@hospital.test',
            'role' => 'doctor',
        ]);

        $doctorStaff = Staff::firstOrCreate(['name' => 'Dr. John Watson']);

        // 2. Doctor creates an audit record (surgery + patient + anaesthesia + surgery_staff)
        $patient = Patient::create([
            'name' => 'Sherlock Holmes',
            'phn' => 'PHN-99901',
            'age' => 40,
            'sex' => 'M',
            'bht' => 'BHT-221B',
        ]);

        $surgery = Surgery::create([
            'patient_id' => $patient->patient_id,
            'operation_name' => 'Appendectomy',
            'surgical_specialty' => 'Gen. Surg',
            'surgery_type' => 'Em',
            'date_of_surgery' => '2026-08-15',
            'asa_grade' => 'I',
            'post_op_admission' => 'Ward',
        ]);

        $anaesthesia = Anaesthesia::create([
            'surgery_id' => $surgery->surgery_id,
            'mode' => 'GA',
            'induction_type' => 'IV',
        ]);

        // Link doctor as Consultant Anaesthetist
        $surgery->staff()->attach($doctorStaff->staff_id, [
            'role_in_case' => 'Consultant Anaesthetist',
        ]);

        // Verify initial setup
        $this->assertDatabaseHas('surgeries', ['surgery_id' => $surgery->surgery_id]);
        $this->assertDatabaseHas('patients', ['patient_id' => $patient->patient_id]);
        $this->assertDatabaseHas('surgery_staff', [
            'surgery_id' => $surgery->surgery_id,
            'staff_id' => $doctorStaff->staff_id,
            'role_in_case' => 'Consultant Anaesthetist',
        ]);

        // 3. System Admin deletes Dr. John Watson's user account
        $responseDelete = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $doctorUser));
        $responseDelete->assertRedirect(route('admin.users.index'));
        $responseDelete->assertSessionHas('status');

        // 4. Assert: Dr. Watson's USER account is deleted
        $this->assertDatabaseMissing('users', [
            'id' => $doctorUser->id,
            'email' => 'watson@hospital.test',
        ]);

        // 5. Assert: ALL CLINICAL RECORDS REMAIN COMPLETELY INTACT
        $this->assertDatabaseHas('surgeries', ['surgery_id' => $surgery->surgery_id]);
        $this->assertDatabaseHas('patients', ['patient_id' => $patient->patient_id]);
        $this->assertDatabaseHas('anaesthesia', ['surgery_id' => $surgery->surgery_id]);
        $this->assertDatabaseHas('staff', ['staff_id' => $doctorStaff->staff_id, 'name' => 'Dr. John Watson']);
        $this->assertDatabaseHas('surgery_staff', [
            'surgery_id' => $surgery->surgery_id,
            'staff_id' => $doctorStaff->staff_id,
            'role_in_case' => 'Consultant Anaesthetist',
        ]);

        // 6. Assert: Historical views still display Dr. John Watson as the doctor
        $responseShow = $this->actingAs($this->dataEntry)->get(route('data_entry.records.show', $surgery->surgery_id));
        $responseShow->assertOk();
        $responseShow->assertSee('Dr. John Watson');
        $responseShow->assertSee('Consultant Anaesthetist');

        $responseIndex = $this->actingAs($this->dataEntry)->get(route('data_entry.records.index'));
        $responseIndex->assertOk();
        $responseIndex->assertSee('Dr. John Watson');
    }

    public function test_editing_doctor_name_preserves_historical_records_with_old_name_while_new_records_use_new_name(): void
    {
        // 1. Doctor originally named Dr. Robert Bruce
        $doctorUser = User::factory()->create([
            'name' => 'Dr. Robert Bruce',
            'email' => 'bruce@hospital.test',
            'role' => 'doctor',
        ]);
        $oldStaff = Staff::firstOrCreate(['name' => 'Dr. Robert Bruce']);

        $patient1 = Patient::create([
            'name' => 'Historical Patient One',
            'phn' => 'PHN-11101',
            'age' => 50,
            'sex' => 'F',
        ]);

        $surgery1 = Surgery::create([
            'patient_id' => $patient1->patient_id,
            'operation_name' => 'Cholecystectomy',
            'surgery_type' => 'Elect',
            'date_of_surgery' => '2026-01-10',
        ]);
        $surgery1->staff()->attach($oldStaff->staff_id, ['role_in_case' => 'Consultant Anaesthetist']);

        // 2. Admin edits the doctor's name to Dr. Robert Banner
        $responseUpdate = $this->actingAs($this->admin)->put(route('admin.users.update', $doctorUser), [
            'name' => 'Dr. Robert Banner',
            'email' => 'bruce@hospital.test',
            'role' => 'doctor',
        ]);
        $responseUpdate->assertRedirect(route('admin.users.index'));

        // 3. Historical Record 1 MUST STILL SHOW "Dr. Robert Bruce"
        $surgery1->refresh();
        $this->assertEquals('Dr. Robert Bruce', $surgery1->staff()->first()->name);

        $responseShowOld = $this->actingAs($this->dataEntry)->get(route('data_entry.records.show', $surgery1->surgery_id));
        $responseShowOld->assertOk();
        $responseShowOld->assertSee('Dr. Robert Bruce');

        // 4. Doctor creates a new audit under their new name "Dr. Robert Banner"
        $doctorUser->refresh();
        $this->actingAs($doctorUser)->post(route('doctor.audit-form.store'), [
            'patient' => [
                'name' => 'New Patient Two',
                'phn' => 'PHN-22202',
                'sex' => 'M',
                'age' => 30,
            ],
            'surgery' => [
                'operation_name' => 'Hernia Repair',
                'surgery_type' => 'Elect',
                'surgical_specialty' => 'Gen. Surg',
                'date_of_surgery' => '2026-09-01',
            ],
            'anaesthesia' => [
                'mode' => 'GA',
            ],
        ]);

        $surgery2 = Surgery::where('operation_name', 'Hernia Repair')->first();
        $this->assertNotNull($surgery2);

        // Surgery 2 must have "Dr. Robert Banner"
        $this->assertEquals('Dr. Robert Banner', $surgery2->staff()->wherePivot('role_in_case', 'Consultant Anaesthetist')->first()->name);

        // Old surgery 1 must STILL have "Dr. Robert Bruce"
        $surgery1Fresh = Surgery::find($surgery1->surgery_id);
        $this->assertEquals('Dr. Robert Bruce', $surgery1Fresh->staff()->wherePivot('role_in_case', 'Consultant Anaesthetist')->first()->name);
    }

    public function test_doctor_creating_audit_is_auto_identified_without_manual_entry(): void
    {
        $doctorUser = User::factory()->create([
            'name' => 'Dr. Jonas Salk',
            'email' => 'salk@hospital.test',
            'role' => 'doctor',
        ]);

        // 1. Doctor visits the audit form
        $responseCreate = $this->actingAs($doctorUser)->get(route('doctor.audit-form.create'));
        $responseCreate->assertOk();
        $responseCreate->assertSee('Dr. Jonas Salk');
        $responseCreate->assertSee('Auto-identified');

        // 2. Doctor submits form WITHOUT specifying anaesthetist_id
        $responseStore = $this->actingAs($doctorUser)->post(route('doctor.audit-form.store'), [
            'patient' => [
                'name' => 'Polio Vaccine Trial Patient',
                'phn' => 'PHN-55501',
                'sex' => 'M',
                'age' => 12,
            ],
            'surgery' => [
                'operation_name' => 'Tonsillectomy',
                'surgery_type' => 'Elect',
                'surgical_specialty' => 'ENT',
                'date_of_surgery' => '2026-09-10',
            ],
            'anaesthesia' => [
                'mode' => 'GA',
            ],
        ]);

        $responseStore->assertRedirect();
        $responseStore->assertSessionHas('success');

        $surgery = Surgery::where('operation_name', 'Tonsillectomy')->first();
        $this->assertNotNull($surgery);

        // Consultant Anaesthetist must be automatically attached as Dr. Jonas Salk
        $anaesthetistStaff = $surgery->staff()->wherePivot('role_in_case', 'Consultant Anaesthetist')->first();
        $this->assertNotNull($anaesthetistStaff);
        $this->assertEquals('Dr. Jonas Salk', $anaesthetistStaff->name);
    }
}
