<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['role_name' => 'Operator Provinsi']);
        Role::firstOrCreate(['role_name' => 'Sub Operator']);
        Role::firstOrCreate(['role_name' => 'Pelapor']);
    }

    protected function createUserWithRole(string $roleName)
    {
        return User::create([
            'full_name' => 'Test User',
            'username' => 'testuser_'.uniqid(),
            'phone_number' => '08123456789',
            'email' => 'test_'.uniqid().'@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', $roleName)->first()->id,
            'is_active' => true,
        ]);
    }

    public function test_operator_can_access_operator_route()
    {
        $operator = $this->createUserWithRole('Operator Provinsi');
        $this->actingAs($operator)->getJson('/operator/dashboard')->assertStatus(200);
    }

    public function test_sub_operator_denied_from_operator_route()
    {
        $subOperator = $this->createUserWithRole('Sub Operator');
        $this->actingAs($subOperator)->getJson('/operator/dashboard')->assertStatus(403);
    }

    public function test_pelapor_denied_from_operator_route()
    {
        $pelapor = $this->createUserWithRole('Pelapor');
        $this->actingAs($pelapor)->getJson('/operator/dashboard')->assertStatus(403);
    }

    public function test_sub_operator_can_access_sub_operator_route()
    {
        $subOperator = $this->createUserWithRole('Sub Operator');
        $this->actingAs($subOperator)->getJson('/sub-operator/dashboard')->assertStatus(200);
    }

    public function test_operator_denied_from_sub_operator_route()
    {
        $operator = $this->createUserWithRole('Operator Provinsi');
        $this->actingAs($operator)->getJson('/sub-operator/dashboard')->assertStatus(403);
    }

    public function test_pelapor_denied_from_sub_operator_route()
    {
        $pelapor = $this->createUserWithRole('Pelapor');
        $this->actingAs($pelapor)->getJson('/sub-operator/dashboard')->assertStatus(403);
    }

    public function test_pelapor_can_access_pelapor_route()
    {
        $pelapor = $this->createUserWithRole('Pelapor');
        $this->actingAs($pelapor)->getJson('/pelapor/dashboard')->assertStatus(200);
    }

    public function test_operator_denied_from_pelapor_route()
    {
        $operator = $this->createUserWithRole('Operator Provinsi');
        $this->actingAs($operator)->getJson('/pelapor/dashboard')->assertStatus(403);
    }

    public function test_guest_denied_from_protected_routes()
    {
        $this->get('/operator/dashboard')->assertRedirect('/login');
        $this->get('/sub-operator/dashboard')->assertRedirect('/login');
        $this->get('/pelapor/dashboard')->assertRedirect('/login');
    }

    public function test_unknown_role_denied()
    {
        Role::firstOrCreate(['role_name' => 'Unknown']);
        $unknown = $this->createUserWithRole('Unknown');
        $this->actingAs($unknown)->getJson('/operator/dashboard')->assertStatus(403);
        $this->actingAs($unknown)->getJson('/pelapor/dashboard')->assertStatus(403);
    }
}
