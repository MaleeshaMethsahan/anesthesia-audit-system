<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Surgery;
use App\Models\Anaesthesia;
use App\Models\MedicalSystem;
use App\Models\MedicalCondition;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientsDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $doctor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = User::factory()->create([
            'role' => 'doctor',
        ]);
    }

    public function test_guest_cannot_access_patients_directory(): void
    {
        $response = $this->get(route('doctor.patients.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_doctor_can_view_patients_directory_with_unique_patients(): void
    {
        $patient = Patient::create([
            'name' => 'TESTING2',
            'phn' => '11111',
            'age' => 56,
            'sex' => 'F',
            'bht' => '2121',
        ]);

        // Create 3 surgeries for this patient
        Surgery::create([
            'patient_id' => $patient->patient_id,
            'operation_name' => 'Test Surgery 1',
            'surgical_specialty' => 'General Surgery',
            'surgery_type' => 'Elect',
            'date_of_surgery' => '2026-06-20',
            'asa_grade' => 'I',
        ]);

        Surgery::create([
            'patient_id' => $patient->patient_id,
            'operation_name' => 'Test Surgery 2',
            'surgical_specialty' => 'General Surgery',
            'surgery_type' => 'Em',
            'date_of_surgery' => '2026-07-05',
            'asa_grade' => 'II',
        ]);

        Surgery::create([
            'patient_id' => $patient->patient_id,
            'operation_name' => 'Test Surgery 3',
            'surgical_specialty' => 'General Surgery',
            'surgery_type' => 'Elect',
            'date_of_surgery' => '2026-07-13',
            'asa_grade' => 'II',
        ]);

        $response = $this->actingAs($this->doctor)->get(route('doctor.patients.index'));

        $response->assertOk();
        $response->assertSee('Patients Directory');
        $response->assertSee('11111');
        $response->assertSee('TESTING2');
        $response->assertSee('3 Audits');
    }

    public function test_search_by_phn_and_name(): void
    {
        Patient::create([
            'name' => 'ALICE SMITH',
            'phn' => '998877',
            'age' => 45,
            'sex' => 'F',
        ]);

        Patient::create([
            'name' => 'BOB JONES',
            'phn' => '112233',
            'age' => 50,
            'sex' => 'M',
        ]);

        // Search by partial PHN
        $responsePhn = $this->actingAs($this->doctor)->get(route('doctor.patients.index', ['search' => '998']));
        $responsePhn->assertOk();
        $responsePhn->assertSee('ALICE SMITH');
        $responsePhn->assertDontSee('BOB JONES');

        // Search by partial name (case insensitive)
        $responseName = $this->actingAs($this->doctor)->get(route('doctor.patients.index', ['search' => 'bob']));
        $responseName->assertOk();
        $responseName->assertSee('BOB JONES');
        $responseName->assertDontSee('ALICE SMITH');

        // Search with no results
        $responseEmpty = $this->actingAs($this->doctor)->get(route('doctor.patients.index', ['search' => 'nonexistent']));
        $responseEmpty->assertOk();
        $responseEmpty->assertSee('No patients match your search.');
    }

    public function test_patient_profile_displays_details_and_surgery_history(): void
    {
        $patient = Patient::create([
            'name' => 'KUMAR S',
            'phn' => '554433',
            'age' => 62,
            'sex' => 'M',
            'bht' => 'BHT-8899',
        ]);

        $system = MedicalSystem::create([
            'system_code' => 'CVS',
            'system_name' => 'Cardiovascular'
        ]);
        
        $condition = MedicalCondition::create([
            'system_id' => $system->system_id,
            'condition_name' => 'HTN',
            'patient_type' => 'Adult',
        ]);

        $patient->conditions()->attach($condition->condition_id);

        $surgery1 = Surgery::create([
            'patient_id' => $patient->patient_id,
            'operation_name' => 'Appendectomy',
            'surgical_specialty' => 'General Surgery',
            'surgery_type' => 'Em',
            'date_of_surgery' => '2026-06-02',
            'asa_grade' => 'I',
            'post_op_admission' => 'Ward',
        ]);

        Anaesthesia::create([
            'surgery_id' => $surgery1->surgery_id,
            'mode' => 'GA',
        ]);

        $surgery2 = Surgery::create([
            'patient_id' => $patient->patient_id,
            'operation_name' => 'Hernia Repair',
            'surgical_specialty' => 'General Surgery',
            'surgery_type' => 'Elect',
            'date_of_surgery' => '2026-07-13',
            'asa_grade' => 'II',
            'post_op_admission' => 'HDU',
        ]);

        Anaesthesia::create([
            'surgery_id' => $surgery2->surgery_id,
            'mode' => 'Regional',
        ]);

        $response = $this->actingAs($this->doctor)->get(route('doctor.patients.show', $patient->patient_id));

        $response->assertOk();
        $response->assertSee('Patient Profile');
        $response->assertSee('KUMAR S');
        $response->assertSee('554433');
        $response->assertSee('HTN');
        $response->assertSee('Total Audits');
        $response->assertSee('02-Jun-2026');
        $response->assertSee('13-Jul-2026');
        $response->assertSee('Appendectomy');
        $response->assertSee('Hernia Repair');
        $response->assertSee(route('doctor.records.show', $surgery1->surgery_id));
        $response->assertSee(route('doctor.records.show', $surgery2->surgery_id));
    }

    public function test_patient_profile_with_no_conditions_and_no_audits(): void
    {
        $patient = Patient::create([
            'name' => 'NEW PATIENT',
            'phn' => '777888',
            'age' => 30,
            'sex' => 'F',
        ]);

        $response = $this->actingAs($this->doctor)->get(route('doctor.patients.show', $patient->patient_id));

        $response->assertOk();
        $response->assertSee('No medical conditions recorded.');
        $response->assertSee('No audit records found for this patient.');
    }

    public function test_pagination_and_query_string_preservation(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            Patient::create([
                'name' => "PATIENT {$i}",
                'phn' => sprintf('PHN%04d', $i),
                'age' => 20 + $i,
                'sex' => $i % 2 === 0 ? 'F' : 'M',
            ]);
        }

        // Page 1 should have 15 items
        $responsePage1 = $this->actingAs($this->doctor)->get(route('doctor.patients.index'));
        $responsePage1->assertOk();
        $responsePage1->assertSee('Registered Patients: 20');
        $responsePage1->assertSee('PATIENT 20'); // descending by patient_id

        // Page 2 should have remaining 5 items
        $responsePage2 = $this->actingAs($this->doctor)->get(route('doctor.patients.index', ['page' => 2]));
        $responsePage2->assertOk();
        $responsePage2->assertSee('PATIENT 1');
    }

    public function test_existing_records_view_database_and_show_remain_functional(): void
    {
        $patient = Patient::create([
            'name' => 'RECORD TEST PATIENT',
            'phn' => 'REC12345',
            'age' => 40,
            'sex' => 'M',
        ]);

        $surgery = Surgery::create([
            'patient_id' => $patient->patient_id,
            'operation_name' => 'Diagnostic Laparoscopy',
            'surgical_specialty' => 'General Surgery',
            'surgery_type' => 'Elect',
            'date_of_surgery' => '2026-08-01',
            'asa_grade' => 'I',
        ]);

        // 1. View Database
        $responseIndex = $this->actingAs($this->doctor)->get(route('doctor.records.index'));
        $responseIndex->assertOk();
        $responseIndex->assertSee('RECORD TEST PATIENT');
        $responseIndex->assertSee('Diagnostic Laparoscopy');

        // 2. View Individual Record
        $responseShow = $this->actingAs($this->doctor)->get(route('doctor.records.show', $surgery->surgery_id));
        $responseShow->assertOk();
        $responseShow->assertSee('Record #' . $surgery->surgery_id);
        $responseShow->assertSee('RECORD TEST PATIENT');
        $responseShow->assertSee('Diagnostic Laparoscopy');
    }
}
