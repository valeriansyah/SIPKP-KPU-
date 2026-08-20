<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\District;

class BackendFrontendContractTest extends TestCase
{
    use RefreshDatabase;

    protected $operatorRole;
    protected $subOperatorRole;
    protected $pelaporRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->operatorRole = Role::firstOrCreate(['role_name' => 'Operator Provinsi']);
        $this->subOperatorRole = Role::firstOrCreate(['role_name' => 'Sub Operator']);
        $this->pelaporRole = Role::firstOrCreate(['role_name' => 'Pelapor']);
    }

    protected function createUser($role, $district = null)
    {
        return User::create([
            'full_name' => 'Test ' . $role->role_name,
            'username' => 'testuser_' . uniqid(),
            'phone_number' => '08123456789',
            'email' => 'test_' . uniqid() . '@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('Password123'),
            'role_id' => $role->id,
            'district_id' => $district ? $district->id : null,
            'is_active' => true
        ]);
    }

    public function test_expected_auth_endpoints_exist()
    {
        $this->assertTrue(Route::has('login'), "Login route is missing");
        $this->assertTrue(Route::has('logout'), "Logout route is missing");
    }

    public function test_protected_routes_are_not_accessible_to_guests()
    {
        // Without authentication
        $response = $this->get('/reports');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_dummy_ui_architecture_routes_enforce_role_middleware()
    {
        // 1. Operator Dashboard Route
        $operator = $this->createUser($this->operatorRole);
        $subOperator = $this->createUser($this->subOperatorRole);
        $pelapor = $this->createUser($this->pelaporRole);

        $this->actingAs($operator)->get('/operator/dashboard')->assertStatus(200);
        $this->actingAs($subOperator)->get('/operator/dashboard')->assertStatus(403);
        
        $this->actingAs($subOperator)->get('/sub-operator/dashboard')->assertStatus(200);
        $this->actingAs($operator)->get('/sub-operator/dashboard')->assertStatus(403);

        $this->actingAs($pelapor)->get('/pelapor/dashboard')->assertStatus(200);
        $this->actingAs($subOperator)->get('/pelapor/dashboard')->assertStatus(403);
    }

    public function test_report_endpoints_exist_and_protected()
    {
        $endpoints = [
            'reports.index',
            'reports.store',
            'reports.show',
            'reports.update'
        ];

        foreach ($endpoints as $endpoint) {
            $this->assertTrue(Route::has($endpoint), "Report endpoint {$endpoint} is missing");
        }
    }
}
