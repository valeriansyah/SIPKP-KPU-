<?php

namespace Tests\Feature;

use App\Models\Deceased;
use App\Models\District;
use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\Role;
use App\Models\User;
use App\Policies\ReportPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected $policy;

    protected $operatorRole;

    protected $subOperatorRole;

    protected $pelaporRole;

    protected $perluPerbaikanStatus;

    protected $pendingStatus;
    protected $diprosesStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new ReportPolicy;

        $this->operatorRole = Role::firstOrCreate(['role_name' => 'Operator Provinsi']);
        $this->subOperatorRole = Role::firstOrCreate(['role_name' => 'Sub Operator']);
        $this->pelaporRole = Role::firstOrCreate(['role_name' => 'Pelapor']);

        $this->perluPerbaikanStatus = ReportStatus::firstOrCreate(['status_name' => 'Perlu Perbaikan']);
        $this->pendingStatus = ReportStatus::firstOrCreate(['status_name' => 'Pending']);
        $this->diprosesStatus = ReportStatus::firstOrCreate(['status_name' => 'Diproses']);
    }

    protected function createUser($role, $districtId = null)
    {
        return User::create([
            'full_name' => 'Test '.$role->role_name,
            'username' => 'testuser_'.uniqid(),
            'phone_number' => '08123456789',
            'email' => 'test_'.uniqid().'@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id,
            'district_id' => $districtId,
            'is_active' => true,
        ]);
    }

    protected function createReport($user, $district, $status)
    {
        $report = Report::create([
            'user_id' => $user->id,
            'report_status_id' => $status->id,
            'report_number' => 'SIPKP-'.date('Ymd').'-'.uniqid(),
        ]);

        Deceased::create([
            'report_id' => $report->id,
            'district_id' => $district->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum '.uniqid(),
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Merdeka',
            'death_place' => 'Palembang',
            'death_date' => '2023-01-01',
        ]);

        return $report->load('deceased', 'reportStatus');
    }

    // VIEW TESTS
    public function test_operator_can_view_all_reports()
    {
        $operator = $this->createUser($this->operatorRole);
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $district, $this->pendingStatus);

        $this->assertTrue($this->policy->view($operator, $report));
    }

    public function test_sub_operator_can_view_report_in_own_district()
    {
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $subOperator = $this->createUser($this->subOperatorRole, $district->id);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $district, $this->pendingStatus);

        $this->assertTrue($this->policy->view($subOperator, $report));
    }

    public function test_sub_operator_cannot_view_report_in_other_district()
    {
        $districtPalembang = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $districtLahat = District::firstOrCreate(['name' => 'Lahat', 'code' => '1604']);

        $subOperator = $this->createUser($this->subOperatorRole, $districtPalembang->id);
        $pelapor = $this->createUser($this->pelaporRole);

        $reportLahat = $this->createReport($pelapor, $districtLahat, $this->pendingStatus);

        $this->assertFalse($this->policy->view($subOperator, $reportLahat));
    }

    public function test_pelapor_can_view_own_report()
    {
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $district, $this->pendingStatus);

        $this->assertTrue($this->policy->view($pelapor, $report));
    }

    public function test_pelapor_cannot_view_other_users_report()
    {
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $pelapor1 = $this->createUser($this->pelaporRole);
        $pelapor2 = $this->createUser($this->pelaporRole);

        $reportPelapor2 = $this->createReport($pelapor2, $district, $this->pendingStatus);

        // IDOR Prevention check
        $this->assertFalse($this->policy->view($pelapor1, $reportPelapor2));
    }

    // UPDATE TESTS
    public function test_pelapor_can_update_own_report_if_status_perlu_perbaikan()
    {
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $district, $this->perluPerbaikanStatus);

        $this->assertTrue($this->policy->update($pelapor, $report));
    }

    public function test_pelapor_cannot_update_own_report_if_status_not_pending_or_perlu_perbaikan()
    {
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $district, $this->diprosesStatus);

        $this->assertFalse($this->policy->update($pelapor, $report));
    }

    public function test_pelapor_cannot_update_other_users_report()
    {
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $pelapor1 = $this->createUser($this->pelaporRole);
        $pelapor2 = $this->createUser($this->pelaporRole);
        $reportPelapor2 = $this->createReport($pelapor2, $district, $this->perluPerbaikanStatus);

        $this->assertFalse($this->policy->update($pelapor1, $reportPelapor2));
    }

    public function test_sub_operator_cannot_update_report()
    {
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $subOperator = $this->createUser($this->subOperatorRole, $district->id);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $district, $this->perluPerbaikanStatus);

        $this->assertFalse($this->policy->update($subOperator, $report));
    }

    public function test_operator_cannot_update_report()
    {
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $operator = $this->createUser($this->operatorRole);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $district, $this->perluPerbaikanStatus);

        $this->assertFalse($this->policy->update($operator, $report));
    }

    // VERIFY TESTS
    public function test_sub_operator_can_verify_report_in_own_district()
    {
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $subOperator = $this->createUser($this->subOperatorRole, $district->id);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $district, $this->pendingStatus);

        $this->assertTrue($this->policy->verify($subOperator, $report));
    }

    public function test_sub_operator_cannot_verify_report_in_other_district()
    {
        $districtPalembang = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $districtLahat = District::firstOrCreate(['name' => 'Lahat', 'code' => '1604']);

        $subOperator = $this->createUser($this->subOperatorRole, $districtPalembang->id); // Sub operator from palembang
        $pelapor = $this->createUser($this->pelaporRole);
        $reportLahat = $this->createReport($pelapor, $districtLahat, $this->pendingStatus); // Report domisili almarhum lahat

        // District Bypass prevention check
        $this->assertFalse($this->policy->verify($subOperator, $reportLahat));
    }

    public function test_operator_cannot_verify_report()
    {
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $operator = $this->createUser($this->operatorRole);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $district, $this->pendingStatus);

        // Operator Privilege Escalation prevention check
        $this->assertFalse($this->policy->verify($operator, $report));
    }

    public function test_pelapor_cannot_verify_report()
    {
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $district, $this->pendingStatus);

        $this->assertFalse($this->policy->verify($pelapor, $report));
    }

    // ADDITIONAL SECURITY TESTS
    public function test_role_privilege_escalation_is_prevented_by_database_state()
    {
        $district = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $pelapor = $this->createUser($this->pelaporRole);

        // Simulasikan request memiliki role_id manipulator di session atau form
        // Policy HANYA mengambil dari database $user->role->role_name.
        // Jika Pelapor mencoba verify, akan selalu false walau request bilang dia operator.
        // Kita hanya ngetest behavior backend yang aman dengan policy ini.
        $report = $this->createReport($pelapor, $district, $this->pendingStatus);

        $this->assertFalse($this->policy->verify($pelapor, $report));
    }
}
