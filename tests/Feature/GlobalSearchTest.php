<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Surgery;
use App\Models\Anaesthesia;
use App\Models\Staff;
use App\Models\MedicalSystem;
use App\Models\MedicalCondition;
use App\Models\MonitoringType;
use App\Models\Analgesic;
use App\Models\AdverseEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    protected User $doctor;
    protected User $dataEntry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = User::factory()->create([
            'role' => 'doctor',
        ]);

        $this->dataEntry = User::factory()->create([
            'role' => 'data_entry',
        ]);
    }

    public function test_guest_cannot_access_global_search(): void
    {
        $response = $this->get(route('global-search.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_doctor_and_data_entry_can_access_global_search(): void
    {
        $responseDoctor = $this->actingAs($this->doctor)->get(route('global-search.index'));
        $responseDoctor->assertOk();
        $responseDoctor->assertSee('Global Clinical Audit Search');

        $responseDE = $this->actingAs($this->dataEntry)->get(route('global-search.index'));
        $responseDE->assertOk();
        $responseDE->assertSee('Global Clinical Audit Search');
    }

    public function test_global_search_by_keyword_matches_operation_phn_and_encrypted_name(): void
    {
        $patientA = Patient::create([
            'name' => 'Perera Silva',
            'phn'  => 'PHN-7771',
            'age'  => 45,
            'sex'  => 'M',
            'bht'  => 'BHT-101',
        ]);

        $surgeryA = Surgery::create([
            'patient_id'         => $patientA->patient_id,
            'operation_name'     => 'Laparoscopic Cholecystectomy',
            'surgical_specialty' => 'Gen. Surg',
            'surgery_type'       => 'Elect',
            'date_of_surgery'    => '2026-05-10',
            'asa_grade'          => 'II',
            'post_op_admission'  => 'Ward',
        ]);

        $patientB = Patient::create([
            'name' => 'Kumara Fernando',
            'phn'  => 'PHN-8882',
            'age'  => 62,
            'sex'  => 'F',
            'bht'  => 'BHT-202',
        ]);

        $surgeryB = Surgery::create([
            'patient_id'         => $patientB->patient_id,
            'operation_name'     => 'Emergency Craniotomy',
            'surgical_specialty' => 'Neuro',
            'surgery_type'       => 'Em',
            'date_of_surgery'    => '2026-05-11',
            'asa_grade'          => 'IV',
            'post_op_admission'  => 'ICU',
        ]);

        // Search by operation name
        $response = $this->actingAs($this->doctor)->get(route('global-search.index', ['q' => 'Cholecystectomy']));
        $response->assertOk();
        $response->assertSee('Laparoscopic Cholecystectomy');
        $response->assertDontSee('Emergency Craniotomy');

        // Search by PHN
        $response = $this->actingAs($this->doctor)->get(route('global-search.index', ['q' => '8882']));
        $response->assertOk();
        $response->assertSee('Emergency Craniotomy');
        $response->assertDontSee('Laparoscopic Cholecystectomy');

        // Search by decrypted patient name
        $response = $this->actingAs($this->doctor)->get(route('global-search.index', ['q' => 'Perera']));
        $response->assertOk();
        $response->assertSee('Laparoscopic Cholecystectomy');
        $response->assertDontSee('Emergency Craniotomy');
    }

    public function test_global_search_filters_by_specialty_asa_and_surgery_type(): void
    {
        $patient = Patient::create([
            'name' => 'John Doe',
            'phn'  => 'PHN-1234',
            'age'  => 30,
            'sex'  => 'M',
        ]);

        $surgElective = Surgery::create([
            'patient_id'         => $patient->patient_id,
            'operation_name'     => 'Total Knee Replacement',
            'surgical_specialty' => 'Ortho',
            'surgery_type'       => 'Elect',
            'asa_grade'          => 'I',
            'date_of_surgery'    => '2026-06-01',
        ]);

        $surgEmergency = Surgery::create([
            'patient_id'         => $patient->patient_id,
            'operation_name'     => 'Exploratory Laparotomy',
            'surgical_specialty' => 'Gen. Surg',
            'surgery_type'       => 'Em',
            'asa_grade'          => 'III',
            'date_of_surgery'    => '2026-06-02',
        ]);

        // Filter by Surgery Type = Em
        $resType = $this->actingAs($this->doctor)->get(route('global-search.index', ['surgery_type' => 'Em']));
        $resType->assertOk();
        $resType->assertSee('Exploratory Laparotomy');
        $resType->assertDontSee('Total Knee Replacement');

        // Filter by ASA Grade = I
        $resAsa = $this->actingAs($this->doctor)->get(route('global-search.index', ['asa_grades' => ['I']]));
        $resAsa->assertOk();
        $resAsa->assertSee('Total Knee Replacement');
        $resAsa->assertDontSee('Exploratory Laparotomy');

        // Filter by Specialty = Ortho
        $resSpec = $this->actingAs($this->doctor)->get(route('global-search.index', ['specialties' => ['Ortho']]));
        $resSpec->assertOk();
        $resSpec->assertSee('Total Knee Replacement');
        $resSpec->assertDontSee('Exploratory Laparotomy');
    }

    public function test_global_search_filters_by_clinical_team(): void
    {
        $surgeon1 = Staff::create(['name' => 'Dr. A. Surgeon']);
        $surgeon2 = Staff::create(['name' => 'Dr. B. Surgeon']);

        $patient = Patient::create(['name' => 'Jane Patient', 'phn' => 'PHN-999', 'sex' => 'F']);

        $surgery1 = Surgery::create([
            'patient_id'     => $patient->patient_id,
            'operation_name' => 'Thyroidectomy',
            'surgery_type'   => 'Elect',
        ]);
        $surgery1->staff()->attach($surgeon1->staff_id, ['role_in_case' => 'Consultant Surgeon']);

        $surgery2 = Surgery::create([
            'patient_id'     => $patient->patient_id,
            'operation_name' => 'Appendicectomy',
            'surgery_type'   => 'Em',
        ]);
        $surgery2->staff()->attach($surgeon2->staff_id, ['role_in_case' => 'Consultant Surgeon']);

        // Filter by Surgeon 1
        $res = $this->actingAs($this->doctor)->get(route('global-search.index', ['surgeon_id' => $surgeon1->staff_id]));
        $res->assertOk();
        $res->assertSee('Thyroidectomy');
        $res->assertDontSee('Appendicectomy');
    }

    public function test_global_search_filters_by_medical_conditions(): void
    {
        $system = MedicalSystem::create([
            'system_code' => 'RESP',
            'system_name' => 'Respiratory System',
        ]);

        $condAsthma = MedicalCondition::create([
            'system_id'      => $system->system_id,
            'condition_name' => 'Bronchial Asthma',
            'patient_type'   => 'Adult',
        ]);

        $patientAsthma = Patient::create(['name' => 'Patient Asthma', 'phn' => 'PHN-ASTHMA', 'sex' => 'M']);
        $patientAsthma->conditions()->attach($condAsthma->condition_id, ['recorded_at' => now()]);

        $patientClean = Patient::create(['name' => 'Patient Healthy', 'phn' => 'PHN-HEALTHY', 'sex' => 'F']);

        $surg1 = Surgery::create(['patient_id' => $patientAsthma->patient_id, 'operation_name' => 'Hernia Repair', 'surgery_type' => 'Elect']);
        $surg2 = Surgery::create(['patient_id' => $patientClean->patient_id, 'operation_name' => 'Tonsillectomy', 'surgery_type' => 'Elect']);

        // Filter by condition
        $res = $this->actingAs($this->doctor)->get(route('global-search.index', ['conditions' => [$condAsthma->condition_id]]));
        $res->assertOk();
        $res->assertSee('Hernia Repair');
        $res->assertDontSee('Tonsillectomy');
    }

    public function test_global_search_filters_by_anaesthesia_mode_and_adverse_events(): void
    {
        $patient = Patient::create(['name' => 'Patient GA', 'phn' => 'PHN-GA', 'sex' => 'M']);

        $surgGA = Surgery::create(['patient_id' => $patient->patient_id, 'operation_name' => 'GA Surgery', 'surgery_type' => 'Elect']);
        Anaesthesia::create([
            'surgery_id' => $surgGA->surgery_id,
            'mode'       => 'GA',
        ]);

        $surgRegional = Surgery::create(['patient_id' => $patient->patient_id, 'operation_name' => 'Spinal Surgery', 'surgery_type' => 'Elect']);
        Anaesthesia::create([
            'surgery_id' => $surgRegional->surgery_id,
            'mode'       => 'Regional',
        ]);

        $event = AdverseEvent::create(['event_name' => 'Severe Bronchospasm']);
        $surgGA->adverseEvents()->attach($event->event_id);

        // Filter by Mode = GA
        $resMode = $this->actingAs($this->doctor)->get(route('global-search.index', ['anaesthesia_mode' => 'GA']));
        $resMode->assertOk();
        $resMode->assertSee('GA Surgery');
        $resMode->assertDontSee('Spinal Surgery');

        // Filter by has_adverse_events = yes
        $resEvents = $this->actingAs($this->doctor)->get(route('global-search.index', ['has_adverse_events' => 'yes']));
        $resEvents->assertOk();
        $resEvents->assertSee('GA Surgery');
        $resEvents->assertDontSee('Spinal Surgery');

        // Filter by has_adverse_events = no
        $resNoEvents = $this->actingAs($this->doctor)->get(route('global-search.index', ['has_adverse_events' => 'no']));
        $resNoEvents->assertOk();
        $resNoEvents->assertSee('Spinal Surgery');
        $resNoEvents->assertDontSee('GA Surgery');
    }
}
