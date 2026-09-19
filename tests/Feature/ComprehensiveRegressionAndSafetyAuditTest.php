<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Surgery;
use App\Models\Anaesthesia;
use App\Models\MedicalSystem;
use App\Models\MedicalCondition;
use App\Models\Staff;
use App\Models\AdverseEvent;
use App\Models\Analgesic;
use App\Models\MonitoringType;
use App\Models\InductionAgent;
use App\Models\MaintenanceAgent;
use App\Models\RegionalType;
use App\Models\Relaxant;
use App\Models\IntubationMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComprehensiveRegressionAndSafetyAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $doctor;
    protected User $dataEntry;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = User::factory()->create(['role' => 'doctor']);
        $this->dataEntry = User::factory()->create(['role' => 'data_entry']);
        $this->admin = User::factory()->create(['role' => 'system_admin']);
    }

    /**
     * Section 3: Route Regression Test
     */
    public function test_all_routes_are_registered_and_no_shadowing_exists(): void
    {
        // 1. Verify doctor patient routes
        $this->assertTrue(Route::has('doctor.patients.index'));
        $this->assertTrue(Route::has('doctor.patients.suggestions'));
        $this->assertTrue(Route::has('doctor.patients.show'));

        // 2. Verify existing doctor clinical routes
        $this->assertTrue(Route::has('doctor.dashboard'));
        $this->assertTrue(Route::has('doctor.records.index'));
        $this->assertTrue(Route::has('doctor.records.show'));
        $this->assertTrue(Route::has('doctor.records.edit'));
        $this->assertTrue(Route::has('doctor.records.update'));
        $this->assertTrue(Route::has('doctor.records.destroy'));
        $this->assertTrue(Route::has('doctor.audit-form.create'));
        $this->assertTrue(Route::has('doctor.audit-form.phn-suggestions'));
        $this->assertTrue(Route::has('doctor.audit-form.store'));

        // 3. Verify data entry routes
        $this->assertTrue(Route::has('data_entry.dashboard'));
        $this->assertTrue(Route::has('data_entry.records.index'));
        $this->assertTrue(Route::has('data_entry.records.show'));
        $this->assertTrue(Route::has('data_entry.records.edit'));
        $this->assertTrue(Route::has('data_entry.records.update'));
        $this->assertTrue(Route::has('data_entry.records.destroy'));
        $this->assertTrue(Route::has('data_entry.audit-form.create'));
        $this->assertTrue(Route::has('data_entry.audit-form.store'));

        // 4. Verify admin routes
        $this->assertTrue(Route::has('admin.dashboard'));
        $this->assertTrue(Route::has('admin.users.create'));
        $this->assertTrue(Route::has('admin.users.store'));
        $this->assertTrue(Route::has('admin.logs.index'));
        $this->assertTrue(Route::has('admin.reference-data.index'));
        $this->assertTrue(Route::has('admin.reference-data.store'));
        $this->assertTrue(Route::has('admin.reference-data.update'));
        $this->assertTrue(Route::has('admin.reference-data.destroy'));

        // 5. Verify suggestions route does not conflict with show route
        $response = $this->actingAs($this->doctor)->get(route('doctor.patients.suggestions', ['query' => 'test']));
        $response->assertOk();
        $response->assertJson([]);
    }

    /**
     * Section 4 & 26: Database Safety Test & Data Integrity Check
     */
    public function test_viewing_directory_and_profile_does_not_mutate_database(): void
    {
        $patient = Patient::create([
            'name' => 'INTEGRITY PATIENT',
            'phn' => '00987',
            'age' => 45,
            'sex' => 'M',
            'bht' => 'BHT-101',
        ]);

        $surgery = Surgery::create([
            'patient_id' => $patient->patient_id,
            'operation_name' => 'Cholecystectomy',
            'surgical_specialty' => 'General Surgery',
            'surgery_type' => 'Elect',
            'date_of_surgery' => '2026-07-20',
            'asa_grade' => 'I',
            'post_op_admission' => 'Ward',
        ]);

        $initialPatientHash = md5(json_encode(DB::table('patients')->where('patient_id', $patient->patient_id)->first()));
        $initialSurgeryHash = md5(json_encode(DB::table('surgeries')->where('surgery_id', $surgery->surgery_id)->first()));

        // View Directory
        $resIndex = $this->actingAs($this->doctor)->get(route('doctor.patients.index'));
        $resIndex->assertOk();

        // View Profile
        $resShow = $this->actingAs($this->doctor)->get(route('doctor.patients.show', $patient->patient_id));
        $resShow->assertOk();

        // View Suggestions
        $resSug = $this->actingAs($this->doctor)->get(route('doctor.patients.suggestions', ['query' => '00987']));
        $resSug->assertOk();

        $afterPatientHash = md5(json_encode(DB::table('patients')->where('patient_id', $patient->patient_id)->first()));
        $afterSurgeryHash = md5(json_encode(DB::table('surgeries')->where('surgery_id', $surgery->surgery_id)->first()));

        $this->assertEquals($initialPatientHash, $afterPatientHash, 'Patient record was mutated during read actions!');
        $this->assertEquals($initialSurgeryHash, $afterSurgeryHash, 'Surgery record was mutated during read actions!');
    }

    /**
     * Section 5, 6, 7, 8: Patient Directory Display, Leading Zero PHN, and Audit Counts
     */
    public function test_patient_directory_displays_leading_zeros_and_exact_audit_counts(): void
    {
        // Patient with leading zeros in PHN and 0 audits
        $p0 = Patient::create(['name' => 'ZERO AUDIT PATIENT', 'phn' => '0001', 'age' => 50, 'sex' => 'M', 'bht' => 'BHT-001']);
        
        // Patient with 1 audit
        $p1 = Patient::create(['name' => 'ONE AUDIT PATIENT', 'phn' => '0002', 'age' => 30, 'sex' => 'F', 'bht' => 'BHT-002']);
        Surgery::create(['patient_id' => $p1->patient_id, 'operation_name' => 'Surgery 1', 'surgery_type' => 'Elect', 'date_of_surgery' => '2026-07-01']);

        // Patient with 3 audits
        $p3 = Patient::create(['name' => 'THREE AUDIT PATIENT', 'phn' => '0003', 'age' => 60, 'sex' => 'M', 'bht' => 'BHT-003']);
        Surgery::create(['patient_id' => $p3->patient_id, 'operation_name' => 'Surgery 2', 'surgery_type' => 'Elect', 'date_of_surgery' => '2026-05-01']);
        Surgery::create(['patient_id' => $p3->patient_id, 'operation_name' => 'Surgery 3', 'surgery_type' => 'Em', 'date_of_surgery' => '2026-06-01']);
        Surgery::create(['patient_id' => $p3->patient_id, 'operation_name' => 'Surgery 4', 'surgery_type' => 'Elect', 'date_of_surgery' => '2026-08-01']);

        $response = $this->actingAs($this->doctor)->get(route('doctor.patients.index'));
        $response->assertOk();

        // Check PHN leading zeros preservation
        $response->assertSee('0001');
        $response->assertSee('0002');
        $response->assertSee('0003');

        // Check Audit count badges
        $response->assertSee('0 Audits');
        $response->assertSee('1 Audit');
        $response->assertSee('3 Audits');

        // Check names
        $response->assertSee('ZERO AUDIT PATIENT');
        $response->assertSee('ONE AUDIT PATIENT');
        $response->assertSee('THREE AUDIT PATIENT');

        // Check total count
        $response->assertSee('3 Registered Patients');
    }

    /**
     * Section 9, 10, 11, 12: Search, Autocomplete, Sorting and Combinations
     */
    public function test_search_and_all_sorting_combinations(): void
    {
        $p1 = Patient::create(['name' => 'CHARLIE BROWN', 'phn' => '5001', 'age' => 20, 'sex' => 'M']);
        $p2 = Patient::create(['name' => 'ALICE WONDER', 'phn' => '1001', 'age' => 25, 'sex' => 'F']);
        $p3 = Patient::create(['name' => 'BOB BUILDER', 'phn' => '3001', 'age' => 30, 'sex' => 'M']);

        Surgery::create(['patient_id' => $p1->patient_id, 'operation_name' => 'Procedure A', 'surgery_type' => 'Elect', 'date_of_surgery' => '2026-01-01']);
        Surgery::create(['patient_id' => $p2->patient_id, 'operation_name' => 'Procedure B', 'surgery_type' => 'Em', 'date_of_surgery' => '2026-09-01']);
        // p3 has 0 surgeries

        // 1. Autocomplete Search
        $resAutocomp = $this->actingAs($this->doctor)->getJson(route('doctor.patients.suggestions', ['query' => 'ali']));
        $resAutocomp->assertOk();
        $resAutocomp->assertJsonFragment(['name' => 'ALICE WONDER', 'phn' => '1001']);
        $resAutocomp->assertJsonMissing(['name' => 'BOB BUILDER']);

        // 2. Search + Sort Combination: search 'b' (CHARLIE BROWN, BOB BUILDER) + sort 'name_asc'
        $resSearchSort = $this->actingAs($this->doctor)->get(route('doctor.patients.index', [
            'search' => 'b',
            'sort' => 'name_asc',
        ]));
        $resSearchSort->assertOk();
        $resSearchSort->assertSeeInOrder(['BOB BUILDER', 'CHARLIE BROWN']);
        $resSearchSort->assertDontSee('ALICE WONDER');

        // 3. Sort 'oldest_registered'
        $resOldest = $this->actingAs($this->doctor)->get(route('doctor.patients.index', ['sort' => 'oldest_registered']));
        $resOldest->assertOk();
        $resOldest->assertSeeInOrder(['CHARLIE BROWN', 'ALICE WONDER', 'BOB BUILDER']);

        // 4. Sort 'surgery_asc' (CHARLIE (Jan), ALICE (Sep), BOB (null/0 audits))
        $resSurgAsc = $this->actingAs($this->doctor)->get(route('doctor.patients.index', ['sort' => 'surgery_asc']));
        $resSurgAsc->assertOk();
        $resSurgAsc->assertSeeInOrder(['CHARLIE BROWN', 'ALICE WONDER', 'BOB BUILDER']);

        // 5. Sort 'surgery_desc' (ALICE (Sep), CHARLIE (Jan), BOB (null/0 audits))
        $resSurgDesc = $this->actingAs($this->doctor)->get(route('doctor.patients.index', ['sort' => 'surgery_desc']));
        $resSurgDesc->assertOk();
        $resSurgDesc->assertSeeInOrder(['ALICE WONDER', 'CHARLIE BROWN', 'BOB BUILDER']);
    }

    /**
     * Section 14, 15: Patient Profile and View Record Navigation Integrity
     */
    public function test_patient_profile_and_view_record_link_integrity(): void
    {
        $patient = Patient::create([
            'name' => 'SARAH CONNOR',
            'phn' => '778899',
            'age' => 38,
            'sex' => 'F',
            'bht' => 'BHT-999',
        ]);

        $system = MedicalSystem::create(['system_code' => 'RESP', 'system_name' => 'Respiratory']);
        $condition = MedicalCondition::create(['system_id' => $system->system_id, 'condition_name' => 'Bronchial Asthma', 'patient_type' => 'Adult']);
        $patient->conditions()->attach($condition->condition_id);

        $surgery = Surgery::create([
            'patient_id' => $patient->patient_id,
            'operation_name' => 'Thyroidectomy',
            'surgical_specialty' => 'ENT',
            'surgery_type' => 'Elect',
            'date_of_surgery' => '2026-08-15',
            'asa_grade' => 'II',
            'post_op_admission' => 'ICU',
        ]);

        $anaesthesia = Anaesthesia::create([
            'surgery_id' => $surgery->surgery_id,
            'mode' => 'GA',
            'induction_type' => 'IV',
            'maintenance_route' => 'Gas',
            'relaxant_used' => true,
            'difficult_intubation' => false,
        ]);

        // 1. Check Patient Profile
        $resProfile = $this->actingAs($this->doctor)->get(route('doctor.patients.show', $patient->patient_id));
        $resProfile->assertOk();
        $resProfile->assertSee('SARAH CONNOR');
        $resProfile->assertSee('778899');
        $resProfile->assertSee('Bronchial Asthma');
        $resProfile->assertSee('Thyroidectomy');
        $resProfile->assertSee('15-Aug-2026');
        $resProfile->assertSee('1 Record');
        $resProfile->assertSee(route('doctor.records.show', $surgery->surgery_id));

        // 2. Open Record View through the link
        $resRecord = $this->actingAs($this->doctor)->get(route('doctor.records.show', $surgery->surgery_id));
        $resRecord->assertOk();
        $resRecord->assertSee('Record #' . $surgery->surgery_id);
        $resRecord->assertSee('SARAH CONNOR');
        $resRecord->assertSee('778899');
        $resRecord->assertSee('Thyroidectomy');
        $resRecord->assertSee('General Anaesthesia');
    }

    /**
     * Section 16, 17, 18, 19: View Database, Add Record, Edit Record, Delete Record Regression
     */
    public function test_existing_records_crud_and_database_view_remain_functional(): void
    {
        // 1. Create Patient & Surgery via AuditFormController store
        $postData = [
            'patient' => [
                'name' => 'NEW AUDIT PATIENT',
                'phn' => 'AUD1234',
                'age' => 55,
                'sex' => 'M',
                'bht' => 'BHT-888',
            ],
            'surgery' => [
                'operation_name' => 'Mastectomy',
                'surgical_specialty' => 'Onco-Surgery',
                'surgery_type' => 'Elect',
                'date_of_surgery' => '2026-08-20',
                'asa_grade' => 'II',
                'post_op_admission' => 'Ward',
            ],
            'anaesthesia' => [
                'mode' => 'GA',
                'induction_type' => 'IV',
                'maintenance_route' => 'Gas',
            ],
        ];

        $resStore = $this->actingAs($this->doctor)->post(route('doctor.audit-form.store'), $postData);
        $resStore->assertRedirect();
        $resStore->assertSessionHas('success');

        $createdPatient = Patient::where('phn', 'AUD1234')->first();
        $this->assertNotNull($createdPatient);
        $this->assertEquals('NEW AUDIT PATIENT', $createdPatient->name);

        $createdSurgery = Surgery::where('patient_id', $createdPatient->patient_id)->first();
        $this->assertNotNull($createdSurgery);
        $this->assertEquals('Mastectomy', $createdSurgery->operation_name);

        // 2. View Database shows the created record
        $resIndex = $this->actingAs($this->doctor)->get(route('doctor.records.index'));
        $resIndex->assertOk();
        $resIndex->assertSee('NEW AUDIT PATIENT');
        $resIndex->assertSee('Mastectomy');

        // 3. Edit Record View
        $resEdit = $this->actingAs($this->doctor)->get(route('doctor.records.edit', $createdSurgery->surgery_id));
        $resEdit->assertOk();
        $resEdit->assertSee('NEW AUDIT PATIENT');

        // 4. Update Record
        $updateData = $postData;
        $updateData['surgery']['operation_name'] = 'Mastectomy (Updated)';
        $resUpdate = $this->actingAs($this->doctor)->put(route('doctor.records.update', $createdSurgery->surgery_id), $updateData);
        $resUpdate->assertRedirect(route('doctor.records.index'));
        $resUpdate->assertSessionHas('success');

        $this->assertDatabaseHas('surgeries', [
            'surgery_id' => $createdSurgery->surgery_id,
            'operation_name' => 'Mastectomy (Updated)',
        ]);

        // 5. Delete Record
        $resDelete = $this->actingAs($this->doctor)->delete(route('doctor.records.destroy', $createdSurgery->surgery_id));
        $resDelete->assertRedirect();
        $this->assertDatabaseMissing('surgeries', [
            'surgery_id' => $createdSurgery->surgery_id,
        ]);
    }

    /**
     * Section 24: Authentication & Authorization Security Test
     */
    public function test_unauthenticated_and_unauthorized_users_are_rejected(): void
    {
        $patient = Patient::create(['name' => 'SECURE PATIENT', 'phn' => 'SEC001', 'age' => 33, 'sex' => 'F']);

        // 1. Guest user cannot access doctor routes
        $this->get(route('doctor.patients.index'))->assertRedirect(route('login'));
        $this->get(route('doctor.patients.suggestions', ['query' => 'SEC']))->assertRedirect(route('login'));
        $this->get(route('doctor.patients.show', $patient->patient_id))->assertRedirect(route('login'));

        // 2. Data Entry user cannot access doctor-only patient routes
        $this->actingAs($this->dataEntry)->get(route('doctor.patients.index'))->assertForbidden();
        $this->actingAs($this->dataEntry)->get(route('doctor.patients.suggestions', ['query' => 'SEC']))->assertForbidden();
        $this->actingAs($this->dataEntry)->get(route('doctor.patients.show', $patient->patient_id))->assertForbidden();
    }
}
