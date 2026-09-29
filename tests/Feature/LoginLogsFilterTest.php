<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\LoginLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class LoginLogsFilterTest extends TestCase
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
            'name' => 'Dr. Gregory House',
            'email' => 'house@hospital.test',
            'role' => 'doctor',
        ]);
    }

    public function test_non_admin_cannot_access_login_logs(): void
    {
        $response = $this->actingAs($this->doctor)->get(route('admin.logs.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_login_logs_with_timeframe_options(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.logs.index'));

        $response->assertStatus(200);
        $response->assertSee('System Login Logs');
        $response->assertSee('Last Hour');
        $response->assertSee('Last 24 Hours');
        $response->assertSee('Last Week');
        $response->assertSee('Last Month');
        $response->assertSee('All Time');
    }

    public function test_timeframe_filter_last_hour(): void
    {
        // Log 1: 15 minutes ago
        LoginLog::create([
            'user_id' => $this->doctor->id,
            'ip_address' => '192.168.1.10',
            'user_agent' => 'Mozilla/5.0 Recent',
            'login_at' => Carbon::now()->subMinutes(15),
        ]);

        // Log 2: 3 hours ago
        LoginLog::create([
            'user_id' => $this->admin->id,
            'ip_address' => '192.168.1.20',
            'user_agent' => 'Mozilla/5.0 Older',
            'login_at' => Carbon::now()->subHours(3),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.logs.index', ['timeframe' => 'last_hour']));

        $response->assertStatus(200);
        $response->assertSee('192.168.1.10');
        $response->assertDontSee('192.168.1.20');
    }

    public function test_timeframe_filter_last_24_hours(): void
    {
        // Log 1: 5 hours ago
        LoginLog::create([
            'user_id' => $this->doctor->id,
            'ip_address' => '10.0.0.1',
            'user_agent' => 'Device Today',
            'login_at' => Carbon::now()->subHours(5),
        ]);

        // Log 2: 2 days ago
        LoginLog::create([
            'user_id' => $this->admin->id,
            'ip_address' => '10.0.0.2',
            'user_agent' => 'Device Old',
            'login_at' => Carbon::now()->subDays(2),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.logs.index', ['timeframe' => 'last_24h']));

        $response->assertStatus(200);
        $response->assertSee('10.0.0.1');
        $response->assertDontSee('10.0.0.2');
    }

    public function test_timeframe_filter_week_and_month(): void
    {
        // Log 1: 3 days ago
        LoginLog::create([
            'user_id' => $this->doctor->id,
            'ip_address' => '172.16.0.1',
            'user_agent' => 'Device This Week',
            'login_at' => Carbon::now()->subDays(3),
        ]);

        // Log 2: 20 days ago
        LoginLog::create([
            'user_id' => $this->admin->id,
            'ip_address' => '172.16.0.2',
            'user_agent' => 'Device This Month',
            'login_at' => Carbon::now()->subDays(20),
        ]);

        // Log 3: 60 days ago
        LoginLog::create([
            'user_id' => $this->admin->id,
            'ip_address' => '172.16.0.3',
            'user_agent' => 'Device Long Ago',
            'login_at' => Carbon::now()->subDays(60),
        ]);

        // Week filter (should have log 1, not log 2 or 3)
        $weekResponse = $this->actingAs($this->admin)->get(route('admin.logs.index', ['timeframe' => 'week']));
        $weekResponse->assertStatus(200);
        $weekResponse->assertSee('172.16.0.1');
        $weekResponse->assertDontSee('172.16.0.2');
        $weekResponse->assertDontSee('172.16.0.3');

        // Month filter (should have log 1 and 2, not log 3)
        $monthResponse = $this->actingAs($this->admin)->get(route('admin.logs.index', ['timeframe' => 'month']));
        $monthResponse->assertStatus(200);
        $monthResponse->assertSee('172.16.0.1');
        $monthResponse->assertSee('172.16.0.2');
        $monthResponse->assertDontSee('172.16.0.3');

        // All filter (should have all 3)
        $allResponse = $this->actingAs($this->admin)->get(route('admin.logs.index', ['timeframe' => 'all']));
        $allResponse->assertStatus(200);
        $allResponse->assertSee('172.16.0.1');
        $allResponse->assertSee('172.16.0.2');
        $allResponse->assertSee('172.16.0.3');
    }

    public function test_search_and_timeframe_combined(): void
    {
        LoginLog::create([
            'user_id' => $this->doctor->id,
            'ip_address' => '192.168.99.1',
            'user_agent' => 'Chrome',
            'login_at' => Carbon::now()->subMinutes(10),
        ]);

        LoginLog::create([
            'user_id' => $this->admin->id,
            'ip_address' => '192.168.99.2',
            'user_agent' => 'Firefox',
            'login_at' => Carbon::now()->subMinutes(10),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.logs.index', [
            'timeframe' => 'last_hour',
            'search' => 'Gregory House',
        ]));

        $response->assertStatus(200);
        $response->assertSee('192.168.99.1');
        $response->assertDontSee('192.168.99.2');
    }
}
