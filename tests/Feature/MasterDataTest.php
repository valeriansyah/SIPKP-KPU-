<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\DocumentType;
use App\Models\ReportStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist
        Role::firstOrCreate(['role_name' => 'Operator Provinsi']);
        Role::firstOrCreate(['role_name' => 'Sub Operator']);
        Role::firstOrCreate(['role_name' => 'Pelapor']);
    }

    private function getOperatorUser()
    {
        return User::factory()->create([
            'role_id' => Role::where('role_name', 'Operator Provinsi')->first()->id,
            'is_active' => true,
        ]);
    }

    private function getSubOperatorUser()
    {
        return User::factory()->create([
            'role_id' => Role::where('role_name', 'Sub Operator')->first()->id,
            'is_active' => true,
        ]);
    }

    private function getPelaporUser()
    {
        return User::factory()->create([
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true,
        ]);
    }

    public function test_master_data_access_control()
    {
        // 1. Operator -> 200
        $operator = $this->getOperatorUser();
        $response = $this->actingAs($operator)->get(route('operator.master-data.index'));
        $response->assertStatus(200);

        // 2. Sub Operator -> 403
        $subOperator = $this->getSubOperatorUser();
        $response = $this->actingAs($subOperator)->get(route('operator.master-data.index'));
        $response->assertStatus(403);

        // 3. Pelapor -> 403
        $pelapor = $this->getPelaporUser();
        $response = $this->actingAs($pelapor)->get(route('operator.master-data.index'));
        $response->assertStatus(403);
    }

    public function test_district_crud_and_safe_delete()
    {
        $operator = $this->getOperatorUser();
        
        // 4. Operator can create district
        $this->actingAs($operator)->post(route('operator.master-data.districts.store'), [
            'name' => 'Kabupaten Test',
            'code' => '123'
        ])->assertRedirect();
        
        $this->assertDatabaseHas('districts', ['name' => 'Kabupaten Test']);
        $district = District::where('name', 'Kabupaten Test')->first();
        
        // 5. Operator can update
        $this->actingAs($operator)->put(route('operator.master-data.districts.update', $district->id), [
            'name' => 'Kabupaten Test Updated',
        ])->assertRedirect();
        
        $this->assertDatabaseHas('districts', ['name' => 'Kabupaten Test Updated']);

        // Safe delete test (used district)
        // 7. Cannot remove used district
        User::factory()->create(['district_id' => $district->id]); // Simulate used district
        $this->actingAs($operator)->post(route('operator.master-data.districts.destroy', $district->id))
            ->assertSessionHas('error');
            
        $this->assertDatabaseHas('districts', ['id' => $district->id]); // Should still exist
    }

    public function test_document_type_crud_and_safe_delete()
    {
        $operator = $this->getOperatorUser();
        
        $this->actingAs($operator)->post(route('operator.master-data.document-types.store'), [
            'name' => 'Dokumen Wajib Test',
            'is_required' => '1'
        ])->assertRedirect();
        
        $this->assertDatabaseHas('document_types', ['name' => 'Dokumen Wajib Test', 'is_required' => 1]);
        $docType = DocumentType::where('name', 'Dokumen Wajib Test')->first();
        
        $this->actingAs($operator)->post(route('operator.master-data.document-types.destroy', $docType->id))
            ->assertSessionHas('success');
            
        $this->assertDatabaseMissing('document_types', ['id' => $docType->id]); // Should be deleted if not used
    }

    public function test_report_status_canonical_protection()
    {
        $operator = $this->getOperatorUser();
        $status = ReportStatus::firstOrCreate([
            'status_name' => 'Pending',
        ], [
            'description' => 'Original Description'
        ]);

        $this->actingAs($operator)->put(route('operator.master-data.report-statuses.update', $status->id), [
            'description' => 'New Description'
        ])->assertRedirect();

        $this->assertDatabaseHas('report_statuses', [
            'id' => $status->id,
            'status_name' => 'Pending', // Key uncompromised
            'description' => 'New Description'
        ]);
    }

    public function test_sub_operator_management_and_role_enforcement()
    {
        $operator = $this->getOperatorUser();
        $district = District::create(['name' => 'Kab. Assign', 'code' => '99.99']);
        
        // 16 & 17. Created user automatically assigned Role Sub Operator and selected district
        $this->actingAs($operator)->post(route('operator.master-data.sub-operators.store'), [
            'full_name' => 'Sub Op Test',
            'email' => 'subop@test.com',
            'password' => 'password123',
            'district_id' => $district->id,
            'is_active' => '1'
        ])->assertRedirect();

        $subOp = User::where('email', 'subop@test.com')->first();
        $this->assertNotNull($subOp);
        $this->assertEquals($district->id, $subOp->district_id);
        $this->assertEquals('Sub Operator', $subOp->role->role_name); // Forced by controller
    }
}
