<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientLookupSearchTest extends TestCase
{
    use RefreshDatabase;

    protected User $doctor;
    protected User $dataEntry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = User::factory()->create(['role' => 'doctor']);
        $this->dataEntry = User::factory()->create(['role' => 'data_entry']);

        // Seed test patients
        Patient::create(['name' => 'Arun Kumar', 'phn' => 'PHN_009', 'age' => 35, 'sex' => 'M']);
        Patient::create(['name' => 'Siva Raj', 'phn' => 'PHN_019', 'age' => 42, 'sex' => 'M']);
        Patient::create(['name' => 'Kumar S', 'phn' => 'PHN_129', 'age' => 50, 'sex' => 'M']);
        Patient::create(['name' => 'Rajan', 'phn' => 'PHN_045', 'age' => 28, 'sex' => 'M']);
    }

    // ── Test 1: Search using a single digit ──────────────────────────────────

    public function test_search_by_single_digit(): void
    {
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => '9']));

        $response->assertOk();

        $phns = collect($response->json())->pluck('phn')->all();

        // PHN_009, PHN_019, PHN_129 all contain '9'
        $this->assertContains('PHN_009', $phns);
        $this->assertContains('PHN_019', $phns);
        $this->assertContains('PHN_129', $phns);

        // PHN_045 does not contain '9'
        $this->assertNotContains('PHN_045', $phns);
    }

    // ── Test 2: Search using a partial PHN ───────────────────────────────────

    public function test_search_by_partial_phn(): void
    {
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => '009']));

        $response->assertOk();

        $phns = collect($response->json())->pluck('phn')->all();

        $this->assertContains('PHN_009', $phns);
        $this->assertNotContains('PHN_019', $phns);
        $this->assertNotContains('PHN_129', $phns);
        $this->assertNotContains('PHN_045', $phns);
    }

    // ── Test 3: Search using the complete PHN ────────────────────────────────

    public function test_search_by_complete_phn(): void
    {
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => 'PHN_009']));

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['phn' => 'PHN_009', 'name' => 'Arun Kumar']);
    }

    // ── Test 4: Search using the PHN prefix ──────────────────────────────────

    public function test_search_by_phn_prefix(): void
    {
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => 'PHN_']));

        $response->assertOk();

        $phns = collect($response->json())->pluck('phn')->all();

        // All four patients have PHN starting with PHN_
        $this->assertContains('PHN_009', $phns);
        $this->assertContains('PHN_019', $phns);
        $this->assertContains('PHN_129', $phns);
        $this->assertContains('PHN_045', $phns);
    }

    // ── Test 5: Search using a complete patient name ─────────────────────────

    public function test_search_by_complete_patient_name(): void
    {
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => 'Arun Kumar']));

        $response->assertOk();
        $response->assertJsonFragment(['phn' => 'PHN_009', 'name' => 'Arun Kumar']);
    }

    // ── Test 6: Search using a partial patient name ──────────────────────────

    public function test_search_by_partial_patient_name(): void
    {
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => 'Arun']));

        $response->assertOk();
        $response->assertJsonFragment(['phn' => 'PHN_009', 'name' => 'Arun Kumar']);
    }

    // ── Test 7: Search using a middle portion of a patient name ──────────────

    public function test_search_by_middle_portion_of_name(): void
    {
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => 'Kumar']));

        $response->assertOk();

        $names = collect($response->json())->pluck('name')->all();

        // Both "Arun Kumar" and "Kumar S" contain "Kumar"
        $this->assertContains('Arun Kumar', $names);
        $this->assertContains('Kumar S', $names);
    }

    // ── Test 8: Search with no matching result ───────────────────────────────

    public function test_search_with_no_matching_result(): void
    {
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => 'ZZZZZ']));

        $response->assertOk();
        $response->assertJsonCount(0);
    }

    // ── Test 9: Select a suggested patient and proceed to Audit Form ─────────

    public function test_patient_lookup_page_loads_and_form_url_is_present(): void
    {
        $response = $this->actingAs($this->doctor)
            ->get(route('doctor.audit-form.start'));

        $response->assertOk();
        $response->assertSee('Patient Lookup');
        // The page should contain the audit form URL for navigation
        $response->assertSee(route('doctor.audit-form.create'));
    }

    public function test_exact_lookup_returns_patient_data(): void
    {
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.patient-lookup', ['phn' => 'PHN_009']));

        $response->assertOk();
        $response->assertJsonFragment(['name' => 'Arun Kumar']);
    }

    public function test_audit_form_prefills_with_selected_patient(): void
    {
        $response = $this->actingAs($this->doctor)
            ->get(route('doctor.audit-form.create', ['phn' => 'PHN_009']));

        $response->assertOk();
        // The form page should load successfully with the PHN parameter
        $response->assertStatus(200);
    }

    // ── Test 10: Existing patient records and other features remain unaffected ─

    public function test_existing_patient_records_remain_intact(): void
    {
        // Verify all 4 patients exist in database
        $this->assertDatabaseHas('patients', ['phn' => 'PHN_009']);
        $this->assertDatabaseHas('patients', ['phn' => 'PHN_019']);
        $this->assertDatabaseHas('patients', ['phn' => 'PHN_129']);
        $this->assertDatabaseHas('patients', ['phn' => 'PHN_045']);

        // Perform searches — this must NOT modify any records
        $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => '9']));

        $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => 'Kumar']));

        // Verify records are still intact
        $this->assertEquals(4, Patient::count());
        $this->assertEquals('Arun Kumar', Patient::where('phn', 'PHN_009')->first()->name);
        $this->assertEquals('Siva Raj', Patient::where('phn', 'PHN_019')->first()->name);
    }

    // ── Additional: Case-insensitive name search ─────────────────────────────

    public function test_name_search_is_case_insensitive(): void
    {
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => 'arun']));

        $response->assertOk();
        $response->assertJsonFragment(['phn' => 'PHN_009', 'name' => 'Arun Kumar']);
    }

    public function test_name_search_uppercase(): void
    {
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => 'KUMAR']));

        $response->assertOk();

        $names = collect($response->json())->pluck('name')->all();

        $this->assertContains('Arun Kumar', $names);
        $this->assertContains('Kumar S', $names);
    }

    // ── Data entry user can also use the same search ─────────────────────────

    public function test_data_entry_user_can_search(): void
    {
        $response = $this->actingAs($this->dataEntry)
            ->getJson(route('data_entry.audit-form.phn-suggestions', ['query' => 'Rajan']));

        $response->assertOk();
        $response->assertJsonFragment(['phn' => 'PHN_045', 'name' => 'Rajan']);
    }

    // ── Empty query returns empty array ──────────────────────────────────────

    public function test_empty_query_returns_empty(): void
    {
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => '']));

        $response->assertOk();
        $response->assertJsonCount(0);
    }

    // ── PHN with underscores and leading zeros are preserved ─────────────────

    public function test_phn_underscores_and_format_preserved(): void
    {
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.audit-form.phn-suggestions', ['query' => 'PHN_0']));

        $response->assertOk();

        $phns = collect($response->json())->pluck('phn')->all();

        // PHN_009, PHN_019, PHN_045 all match PHN_0*
        $this->assertContains('PHN_009', $phns);
        $this->assertContains('PHN_019', $phns);
        $this->assertContains('PHN_045', $phns);

        // Verify the exact format is preserved (not trimmed or modified)
        foreach ($response->json() as $patient) {
            $this->assertStringStartsWith('PHN_', $patient['phn']);
        }
    }
}
