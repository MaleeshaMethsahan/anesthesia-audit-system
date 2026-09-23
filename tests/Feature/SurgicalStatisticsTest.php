<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Surgery;
use App\Models\MedicalSystem;
use App\Models\MedicalCondition;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SurgicalStatisticsTest extends TestCase
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

    public function test_guest_cannot_access_surgical_statistics(): void
    {
        $response = $this->get(route('doctor.surgical-statistics.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_doctor_can_access_surgical_statistics(): void
    {
        $response = $this->actingAs($this->doctor)->get(route('doctor.surgical-statistics.index'));
        $response->assertOk();
        $response->assertSee('Surgical Statistics');
        $response->assertSee('Filter Statistics');
    }

    public function test_data_entry_cannot_access_surgical_statistics(): void
    {
        $response = $this->actingAs($this->dataEntry)->get(route('doctor.surgical-statistics.index'));
        $response->assertForbidden();
    }

    public function test_surgical_statistics_with_data_and_filters(): void
    {
        $patient = Patient::create([
            'name' => 'Test Patient',
            'phn'  => 'PHN-0001',
            'age'  => 50,
            'sex'  => 'Male',
        ]);

        $surgery = Surgery::create([
            'patient_id'        => $patient->patient_id,
            'operation_name'    => 'Appendectomy',
            'surgical_specialty'=> 'General Surgery',
            'surgery_type'      => 'Em',
            'date_of_surgery'   => now()->subDays(5)->toDateString(),
            'asa_grade'         => 'II',
        ]);

        // Test with different filter combinations
        $response = $this->actingAs($this->doctor)->get(route('doctor.surgical-statistics.index', [
            'date_from' => now()->subDays(30)->toDateString(),
            'date_to'   => now()->toDateString(),
            'specialty' => 'General Surgery',
            'case_type' => 'Em',
            'asa_grade' => 'II',
        ]));

        $response->assertOk();
        $response->assertSee('Appendectomy');
        $response->assertSee('Most Common Procedures');
    }

    public function test_period_presets_like_30d_and_90d(): void
    {
        $patient = Patient::create([
            'name' => 'Preset Patient',
            'phn'  => 'PHN-0002',
            'age'  => 40,
            'sex'  => 'Female',
        ]);

        Surgery::create([
            'patient_id'        => $patient->patient_id,
            'operation_name'    => 'Cholecystectomy',
            'surgical_specialty'=> 'General Surgery',
            'surgery_type'      => 'Elect',
            'date_of_surgery'   => now()->subDays(10)->toDateString(),
            'asa_grade'         => 'I',
        ]);

        $response30d = $this->actingAs($this->doctor)->get(route('doctor.surgical-statistics.index', ['period' => '30d']));
        $response30d->assertOk();
        $response30d->assertSee('Cholecystectomy');

        $response90d = $this->actingAs($this->doctor)->get(route('doctor.surgical-statistics.index', ['period' => '90d']));
        $response90d->assertOk();
    }

    public function test_defensive_date_swap_when_from_after_to(): void
    {
        $response = $this->actingAs($this->doctor)->get(route('doctor.surgical-statistics.index', [
            'date_from' => now()->toDateString(),
            'date_to'   => now()->subMonths(2)->toDateString(),
        ]));

        $response->assertOk();
    }

    public function test_forecast_requires_minimum_months(): void
    {
        $patient = Patient::create([
            'name' => 'Single Month Patient',
            'phn'  => 'PHN-0003',
            'age'  => 30,
            'sex'  => 'Male',
        ]);

        // Only 1 surgery in 1 month
        Surgery::create([
            'patient_id'        => $patient->patient_id,
            'operation_name'    => 'Herniorrhaphy',
            'surgical_specialty'=> 'General Surgery',
            'surgery_type'      => 'Elect',
            'date_of_surgery'   => now()->subDays(10)->toDateString(),
            'asa_grade'         => 'I',
        ]);

        $response = $this->actingAs($this->doctor)->get(route('doctor.surgical-statistics.index', [
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to'   => now()->endOfMonth()->toDateString(),
        ]));

        $response->assertOk();
        $viewData = $response->original->getData();
        $this->assertFalse($viewData['forecastReady']);
        $this->assertNotEmpty($viewData['forecastMessage']);
    }

    public function test_forecast_computed_when_sufficient_data_exists(): void
    {
        $patient = Patient::create([
            'name' => 'Multi-month Patient',
            'phn'  => 'PHN-0004',
            'age'  => 60,
            'sex'  => 'Male',
        ]);

        // Create records across 4 consecutive historical months
        for ($i = 4; $i >= 1; $i--) {
            Surgery::create([
                'patient_id'        => $patient->patient_id,
                'operation_name'    => 'Procedure ' . $i,
                'surgical_specialty'=> 'General Surgery',
                'surgery_type'      => 'Elect',
                'date_of_surgery'   => now()->subMonths($i)->startOfMonth()->addDays(5)->toDateString(),
                'asa_grade'         => 'II',
            ]);
        }

        $response = $this->actingAs($this->doctor)->get(route('doctor.surgical-statistics.index', [
            'date_from' => now()->subMonths(4)->startOfMonth()->toDateString(),
            'date_to'   => now()->subMonths(1)->endOfMonth()->toDateString(),
        ]));

        $response->assertOk();
        $viewData = $response->original->getData();
        $this->assertTrue($viewData['forecastReady']);
        $this->assertCount(3, $viewData['forecastData']);
        foreach ($viewData['forecastData'] as $estimate) {
            $this->assertGreaterThanOrEqual(0, $estimate);
        }
    }

    public function test_specialty_filter_isolates_records(): void
    {
        $patient = Patient::create([
            'name' => 'Specialty Patient',
            'phn'  => 'PHN-0005',
            'age'  => 45,
            'sex'  => 'Female',
        ]);

        Surgery::create([
            'patient_id'        => $patient->patient_id,
            'operation_name'    => 'Mastectomy',
            'surgical_specialty'=> 'General Surgery',
            'surgery_type'      => 'Elect',
            'date_of_surgery'   => now()->subDays(5)->toDateString(),
            'asa_grade'         => 'I',
        ]);

        Surgery::create([
            'patient_id'        => $patient->patient_id,
            'operation_name'    => 'Total Knee Replacement',
            'surgical_specialty'=> 'Orthopedic Surgery',
            'surgery_type'      => 'Elect',
            'date_of_surgery'   => now()->subDays(5)->toDateString(),
            'asa_grade'         => 'II',
        ]);

        $response = $this->actingAs($this->doctor)->get(route('doctor.surgical-statistics.index', [
            'specialty' => 'Orthopedic Surgery',
        ]));

        $response->assertOk();
        $viewData = $response->original->getData();
        $this->assertEquals(1, $viewData['totalSurgeries']);
        $this->assertArrayHasKey('Orthopedic Surgery', $viewData['specialtyDistribution']);
        $this->assertArrayNotHasKey('General Surgery', $viewData['specialtyDistribution']);
    }

    public function test_comorbidity_profile_joins_database_systems_correctly(): void
    {
        $patient = Patient::create([
            'name' => 'Comorbid Patient',
            'phn'  => 'PHN-0006',
            'age'  => 65,
            'sex'  => 'Male',
        ]);

        $surgery = Surgery::create([
            'patient_id'        => $patient->patient_id,
            'operation_name'    => 'CABG',
            'surgical_specialty'=> 'Cardiothoracic Surgery',
            'surgery_type'      => 'Elect',
            'date_of_surgery'   => now()->subDays(5)->toDateString(),
            'asa_grade'         => 'III',
        ]);

        $sys = MedicalSystem::create([
            'system_id'   => 101,
            'system_code' => 'CVS',
            'system_name' => 'Cardiovascular',
        ]);

        $cond = MedicalCondition::create([
            'condition_id'   => 101,
            'system_id'      => $sys->system_id,
            'condition_name' => 'Hypertension',
            'patient_type'   => 'Adult',
        ]);

        DB::table('patient_conditions')->insert([
            'patient_id'   => $patient->patient_id,
            'condition_id' => $cond->condition_id,
        ]);

        $response = $this->actingAs($this->doctor)->get(route('doctor.surgical-statistics.index'));
        $response->assertOk();
        $viewData = $response->original->getData();
        $this->assertArrayHasKey('Cardiovascular', $viewData['comorbidityData']);
        $this->assertEquals(1, $viewData['comorbidityData']['Cardiovascular']);
    }

    public function test_asa_grade_filter_reflects_in_trend_data(): void
    {
        $patient = Patient::create([
            'name' => 'ASA Test Patient',
            'phn'  => 'PHN-0007',
            'age'  => 35,
            'sex'  => 'Female',
        ]);

        // Create an ASA I record
        Surgery::create([
            'patient_id'        => $patient->patient_id,
            'operation_name'    => 'Minor Excision',
            'surgical_specialty'=> 'General Surgery',
            'surgery_type'      => 'Elect',
            'date_of_surgery'   => now()->subDays(5)->toDateString(),
            'asa_grade'         => 'I',
        ]);

        $response = $this->actingAs($this->doctor)->get(route('doctor.surgical-statistics.index', [
            'asa_grade' => 'I',
        ]));

        $response->assertOk();
        $viewData = $response->original->getData();
        $this->assertEquals(1, $viewData['totalSurgeries']);
        // Verify highRiskTrend contains the count for ASA I (not 0)
        $this->assertGreaterThan(0, array_sum($viewData['highRiskTrend']));
    }

    public function test_forecast_returns_mathematical_metrics_slope_and_r_squared(): void
    {
        $patient = Patient::create([
            'name' => 'Regression Patient',
            'phn'  => 'PHN-0008',
            'age'  => 55,
            'sex'  => 'Male',
        ]);

        // Create 6 consecutive months with an increasing trend (2, 4, 6, 8, 10, 12)
        for ($m = 6; $m >= 1; $m--) {
            $count = (7 - $m) * 2;
            for ($k = 0; $k < $count; $k++) {
                Surgery::create([
                    'patient_id'        => $patient->patient_id,
                    'operation_name'    => 'Op ' . $m . '_' . $k,
                    'surgical_specialty'=> 'General Surgery',
                    'surgery_type'      => 'Elect',
                    'date_of_surgery'   => now()->subMonths($m)->startOfMonth()->addDays(5)->toDateString(),
                    'asa_grade'         => 'II',
                ]);
            }
        }

        $response = $this->actingAs($this->doctor)->get(route('doctor.surgical-statistics.index', [
            'date_from' => now()->subMonths(6)->startOfMonth()->toDateString(),
            'date_to'   => now()->subMonths(1)->endOfMonth()->toDateString(),
        ]));

        $response->assertOk();
        $viewData = $response->original->getData();
        $this->assertTrue($viewData['forecastReady']);
        $this->assertNotNull($viewData['forecastSlope']);
        // Slope should be positive because data is increasing
        $this->assertGreaterThan(0, $viewData['forecastSlope']);
        $this->assertNotNull($viewData['forecastRSquared']);
        // Perfect linear line has R² = 1.0
        $this->assertGreaterThanOrEqual(0.9, $viewData['forecastRSquared']);
        $this->assertNotEmpty($viewData['forecastConfidence']);
    }

    public function test_case_type_filter_handles_emergency_context_accurately(): void
    {
        $patient = Patient::create([
            'name' => 'Emergency Patient',
            'phn'  => 'PHN-0009',
            'age'  => 28,
            'sex'  => 'Female',
        ]);

        Surgery::create([
            'patient_id'        => $patient->patient_id,
            'operation_name'    => 'Emergency Laparotomy',
            'surgical_specialty'=> 'General Surgery',
            'surgery_type'      => 'Em',
            'date_of_surgery'   => now()->subDays(3)->toDateString(),
            'asa_grade'         => 'III',
        ]);

        $response = $this->actingAs($this->doctor)->get(route('doctor.surgical-statistics.index', [
            'case_type' => 'Em',
        ]));

        $response->assertOk();
        $viewData = $response->original->getData();
        $this->assertTrue($viewData['caseTypeFilterActive']);
        $this->assertEquals(1, $viewData['totalSurgeries']);
        $this->assertEquals(1, $viewData['emergencyCount']);
        $this->assertEquals(0, $viewData['electiveCount']);
        $response->assertSee('Emergency-only filter active');
    }
}
