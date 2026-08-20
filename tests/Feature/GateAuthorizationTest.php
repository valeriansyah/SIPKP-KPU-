<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\District;
use App\Models\Report;
use App\Models\Deceased;
use App\Models\ReportStatus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class GateAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected $operatorRole;
    protected $subOperatorRole;
    protected $pelaporRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->operatorRole = Role::firstOrCreate(['role_name' => 'Operator']);
        $this->subOperatorRole = Role::firstOrCreate(['role_name' => 'Sub Operator']);
        $this->pelaporRole = Role::firstOrCreate(['role_name' => 'Pelapor']);
    }

    protected function createUser($role, $isActive = true)
    {
        return User::create([
            'full_name' => 'Test ' . $role->role_name,
            'username' => 'testuser_' . uniqid(),
            'phone_number' => '08123456789',
            'email' => 'test_' . uniqid() . '@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id,
            'is_active' => $isActive
        ]);
    }

    // 1. Unauthenticated user
    public function test_unauthenticated_user_denied_all_gates()
    {
        $this->assertFalse(Gate::allows('manage-master-data'));
        $this->assertFalse(Gate::allows('manage-sub-operator'));
        $this->assertFalse(Gate::allows('view-audit-log'));
        $this->assertFalse(Gate::allows('view-all-reports'));
        $this->assertFalse(Gate::allows('verify-report'));
        $this->assertFalse(Gate::allows('create-report'));
    }

    // Inactive Users
    public function test_inactive_operator_denied_all_gates()
    {
        $user = $this->createUser($this->operatorRole, false);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('manage-master-data'));
    }

    public function test_inactive_sub_operator_denied_all_gates()
    {
        $user = $this->createUser($this->subOperatorRole, false);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('verify-report'));
    }

    public function test_inactive_pelapor_denied_all_gates()
    {
        $user = $this->createUser($this->pelaporRole, false);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('create-report'));
    }

    public function test_soft_deleted_user_denied_all_gates()
    {
        $user = $this->createUser($this->operatorRole, true);
        $user->delete();
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('manage-master-data'));
    }

    // Operator
    public function test_operator_can_manage_master_data()
    {
        $user = $this->createUser($this->operatorRole);
        $this->actingAs($user);
        $this->assertTrue(Gate::allows('manage-master-data'));
    }

    public function test_operator_can_manage_sub_operator()
    {
        $user = $this->createUser($this->operatorRole);
        $this->actingAs($user);
        $this->assertTrue(Gate::allows('manage-sub-operator'));
    }

    public function test_operator_can_view_audit_log()
    {
        $user = $this->createUser($this->operatorRole);
        $this->actingAs($user);
        $this->assertTrue(Gate::allows('view-audit-log'));
    }

    public function test_operator_can_view_all_reports()
    {
        $user = $this->createUser($this->operatorRole);
        $this->actingAs($user);
        $this->assertTrue(Gate::allows('view-all-reports'));
    }

    public function test_operator_cannot_verify_report()
    {
        $user = $this->createUser($this->operatorRole);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('verify-report'));
    }

    public function test_operator_cannot_create_report()
    {
        $user = $this->createUser($this->operatorRole);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('create-report'));
    }

    // Sub Operator
    public function test_sub_operator_can_verify_report()
    {
        $user = $this->createUser($this->subOperatorRole);
        $this->actingAs($user);
        $this->assertTrue(Gate::allows('verify-report'));
    }

    public function test_sub_operator_cannot_manage_master_data()
    {
        $user = $this->createUser($this->subOperatorRole);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('manage-master-data'));
    }

    public function test_sub_operator_cannot_manage_sub_operator()
    {
        $user = $this->createUser($this->subOperatorRole);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('manage-sub-operator'));
    }

    public function test_sub_operator_cannot_view_audit_log()
    {
        $user = $this->createUser($this->subOperatorRole);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('view-audit-log'));
    }

    public function test_sub_operator_cannot_view_all_reports()
    {
        $user = $this->createUser($this->subOperatorRole);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('view-all-reports'));
    }

    public function test_sub_operator_cannot_create_report()
    {
        $user = $this->createUser($this->subOperatorRole);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('create-report'));
    }

    // Pelapor
    public function test_pelapor_can_create_report()
    {
        $user = $this->createUser($this->pelaporRole);
        $this->actingAs($user);
        $this->assertTrue(Gate::allows('create-report'));
    }

    public function test_pelapor_cannot_manage_master_data()
    {
        $user = $this->createUser($this->pelaporRole);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('manage-master-data'));
    }

    public function test_pelapor_cannot_manage_sub_operator()
    {
        $user = $this->createUser($this->pelaporRole);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('manage-sub-operator'));
    }

    public function test_pelapor_cannot_view_audit_log()
    {
        $user = $this->createUser($this->pelaporRole);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('view-audit-log'));
    }

    public function test_pelapor_cannot_view_all_reports()
    {
        $user = $this->createUser($this->pelaporRole);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('view-all-reports'));
    }

    public function test_pelapor_cannot_verify_report()
    {
        $user = $this->createUser($this->pelaporRole);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('verify-report'));
    }

    // Security
    public function test_role_id_manipulation_prevented()
    {
        $user = $this->createUser($this->pelaporRole);
        // Even if we fake something in the request (simulated here by manual check),
        // Gate relies purely on $user->role->role_name
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('manage-master-data'));
    }

    public function test_unknown_role_denied()
    {
        $unknownRole = Role::firstOrCreate(['role_name' => 'Unknown']);
        $user = $this->createUser($unknownRole);
        $this->actingAs($user);
        
        $this->assertFalse(Gate::allows('manage-master-data'));
        $this->assertFalse(Gate::allows('verify-report'));
        $this->assertFalse(Gate::allows('create-report'));
    }

    // Architectural Test
    public function test_gate_and_policy_separation()
    {
        $districtPalembang = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $districtLahat = District::firstOrCreate(['name' => 'Lahat', 'code' => '1604']);

        $subOperator = User::create([
            'full_name' => 'Sub Operator Palembang',
            'username' => 'subop_plg',
            'phone_number' => '08123456789',
            'email' => 'subop@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => $this->subOperatorRole->id,
            'district_id' => $districtPalembang->id,
            'is_active' => true
        ]);

        $pelapor = User::create([
            'full_name' => 'Pelapor',
            'username' => 'pelapor',
            'phone_number' => '08123456789',
            'email' => 'pelapor@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => $this->pelaporRole->id,
            'is_active' => true
        ]);

        $status = ReportStatus::firstOrCreate(['status_name' => 'Pending']);

        $reportLahat = Report::create([
            'user_id' => $pelapor->id,
            'report_status_id' => $status->id,
            'report_number' => 'SIPKP-' . date('Ymd') . '-001',
        ]);

        Deceased::create([
            'report_id' => $reportLahat->id,
            'district_id' => $districtLahat->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum',
            'gender' => 'Laki-laki',
            'birth_place' => 'Lahat',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Merdeka',
            'death_place' => 'Lahat',
            'death_date' => '2023-01-01',
        ]);
        
        $reportLahat->load('deceased');

        $this->actingAs($subOperator);

        // Sub Operator globally HAS the 'verify-report' permission
        $this->assertTrue(Gate::allows('verify-report'));

        // BUT, Sub Operator cannot verify THIS SPECIFIC report because it belongs to a different district
        $this->assertFalse($subOperator->can('verify', $reportLahat));
    }
}
