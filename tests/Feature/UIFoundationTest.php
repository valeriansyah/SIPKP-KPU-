<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UIFoundationTest extends TestCase
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
            'full_name' => 'Test '.$role->role_name,
            'username' => 'testuser_'.uniqid(),
            'phone_number' => '08'.rand(1000000000, 9999999999),
            'email' => 'test_'.uniqid().'@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id,
            'district_id' => $district ? $district->id : null,
            'is_active' => true,
        ]);
    }

    public function test_operator_dashboard_renders_with_layout_and_components()
    {
        $operator = $this->createUser($this->operatorRole);

        $response = $this->actingAs($operator)->get('/operator/dashboard');

        $response->assertStatus(200);

        // Assert Layout Shell
        $response->assertSee('<aside id="app-sidebar"', false);
        $response->assertSee('<header class="bg-surface', false);

        // Assert Role Specific
        $response->assertSee('Monitoring Global Provinsi');
        $response->assertSee('Total Laporan');
    }

    public function test_sub_operator_dashboard_renders_with_layout_and_district_scoping()
    {
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $subOperator = $this->createUser($this->subOperatorRole, $district);

        $response = $this->actingAs($subOperator)->get('/sub-operator/dashboard');

        $response->assertStatus(200);

        // Assert Layout Shell
        $response->assertSee('<aside id="app-sidebar"', false);

        // Assert Role Specific
        $response->assertSee('Antrean Verifikasi District');

        // Assert Scope Rendering
        $response->assertSee('Kabupaten/Kota: Palembang');
    }

    public function test_pelapor_dashboard_renders_with_layout()
    {
        $pelapor = $this->createUser($this->pelaporRole);

        $response = $this->actingAs($pelapor)->get('/pelapor/dashboard');

        $response->assertStatus(200);

        // Assert Role Specific
        $response->assertSee('Buat Laporan Baru');
        $response->assertSee('Laporan Terbaru Saya');
    }

    public function test_unauthorized_user_cannot_access_dashboards()
    {
        $response = $this->get('/operator/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/sub-operator/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/pelapor/dashboard');
        $response->assertRedirect('/login');
    }
}
