<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\District;
use App\Models\Deceased;
use App\Models\DocumentType;
use App\Models\Document;
use App\Models\ReportStatus;
use App\Models\Report;
use App\Models\ReportVerification;
use App\Models\OtpCode;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_user_relationship()
    {
        $role = new Role();
        $user = new User();

        $this->assertInstanceOf(HasMany::class, $role->users());
        $this->assertInstanceOf(User::class, $role->users()->getRelated());

        $this->assertInstanceOf(BelongsTo::class, $user->role());
        $this->assertInstanceOf(Role::class, $user->role()->getRelated());
        $this->assertEquals('role_id', $user->role()->getForeignKeyName());
    }

    public function test_district_user_relationship()
    {
        $district = new District();
        $user = new User();

        $this->assertInstanceOf(HasMany::class, $district->users());
        $this->assertInstanceOf(User::class, $district->users()->getRelated());

        $this->assertInstanceOf(BelongsTo::class, $user->district());
        $this->assertInstanceOf(District::class, $user->district()->getRelated());
        $this->assertEquals('district_id', $user->district()->getForeignKeyName());
    }

    public function test_district_deceased_relationship()
    {
        $district = new District();
        $deceased = new Deceased();

        $this->assertInstanceOf(HasMany::class, $district->deceased());
        $this->assertInstanceOf(Deceased::class, $district->deceased()->getRelated());

        $this->assertInstanceOf(BelongsTo::class, $deceased->district());
        $this->assertInstanceOf(District::class, $deceased->district()->getRelated());
        $this->assertEquals('district_id', $deceased->district()->getForeignKeyName());
    }

    public function test_document_type_document_relationship()
    {
        $documentType = new DocumentType();
        $document = new Document();

        $this->assertInstanceOf(HasMany::class, $documentType->documents());
        $this->assertInstanceOf(Document::class, $documentType->documents()->getRelated());

        $this->assertInstanceOf(BelongsTo::class, $document->documentType());
        $this->assertInstanceOf(DocumentType::class, $document->documentType()->getRelated());
        $this->assertEquals('document_type_id', $document->documentType()->getForeignKeyName());
    }

    public function test_report_status_report_relationship()
    {
        $reportStatus = new ReportStatus();
        $report = new Report();

        $this->assertInstanceOf(HasMany::class, $reportStatus->reports());
        $this->assertInstanceOf(Report::class, $reportStatus->reports()->getRelated());
        $this->assertEquals('report_status_id', $reportStatus->reports()->getForeignKeyName());

        $this->assertInstanceOf(BelongsTo::class, $report->reportStatus());
        $this->assertInstanceOf(ReportStatus::class, $report->reportStatus()->getRelated());
        $this->assertEquals('report_status_id', $report->reportStatus()->getForeignKeyName());
    }

    public function test_report_status_report_verification_relationship()
    {
        $reportStatus = new ReportStatus();
        $reportVerification = new ReportVerification();

        $this->assertInstanceOf(HasMany::class, $reportStatus->reportVerifications());
        $this->assertInstanceOf(ReportVerification::class, $reportStatus->reportVerifications()->getRelated());
        $this->assertEquals('report_status_id', $reportStatus->reportVerifications()->getForeignKeyName());

        $this->assertInstanceOf(BelongsTo::class, $reportVerification->reportStatus());
        $this->assertInstanceOf(ReportStatus::class, $reportVerification->reportStatus()->getRelated());
        $this->assertEquals('report_status_id', $reportVerification->reportStatus()->getForeignKeyName());
    }

    public function test_user_otp_code_relationship()
    {
        $user = new User();
        $otpCode = new OtpCode();

        $this->assertInstanceOf(HasMany::class, $user->otpCodes());
        $this->assertInstanceOf(OtpCode::class, $user->otpCodes()->getRelated());

        $this->assertInstanceOf(BelongsTo::class, $otpCode->user());
        $this->assertInstanceOf(User::class, $otpCode->user()->getRelated());
        $this->assertEquals('user_id', $otpCode->user()->getForeignKeyName());
    }

    public function test_user_audit_log_relationship()
    {
        $user = new User();
        $auditLog = new AuditLog();

        $this->assertInstanceOf(HasMany::class, $user->auditLogs());
        $this->assertInstanceOf(AuditLog::class, $user->auditLogs()->getRelated());

        $this->assertInstanceOf(BelongsTo::class, $auditLog->user());
        $this->assertInstanceOf(User::class, $auditLog->user()->getRelated());
        $this->assertEquals('user_id', $auditLog->user()->getForeignKeyName());
    }

    public function test_user_report_relationship()
    {
        $user = new User();
        $report = new Report();

        $this->assertInstanceOf(HasMany::class, $user->reports());
        $this->assertInstanceOf(Report::class, $user->reports()->getRelated());

        $this->assertInstanceOf(BelongsTo::class, $report->user());
        $this->assertInstanceOf(User::class, $report->user()->getRelated());
        $this->assertEquals('user_id', $report->user()->getForeignKeyName());
    }

    public function test_user_report_verification_relationship()
    {
        $user = new User();
        $reportVerification = new ReportVerification();

        $this->assertInstanceOf(HasMany::class, $user->reportVerifications());
        $this->assertInstanceOf(ReportVerification::class, $user->reportVerifications()->getRelated());

        $this->assertInstanceOf(BelongsTo::class, $reportVerification->user());
        $this->assertInstanceOf(User::class, $reportVerification->user()->getRelated());
        $this->assertEquals('user_id', $reportVerification->user()->getForeignKeyName());
    }

    public function test_report_deceased_relationship()
    {
        $report = new Report();
        $deceased = new Deceased();

        $this->assertInstanceOf(HasOne::class, $report->deceased());
        $this->assertInstanceOf(Deceased::class, $report->deceased()->getRelated());

        $this->assertInstanceOf(BelongsTo::class, $deceased->report());
        $this->assertInstanceOf(Report::class, $deceased->report()->getRelated());
        $this->assertEquals('report_id', $deceased->report()->getForeignKeyName());
    }

    public function test_report_document_relationship()
    {
        $report = new Report();
        $document = new Document();

        $this->assertInstanceOf(HasMany::class, $report->documents());
        $this->assertInstanceOf(Document::class, $report->documents()->getRelated());

        $this->assertInstanceOf(BelongsTo::class, $document->report());
        $this->assertInstanceOf(Report::class, $document->report()->getRelated());
        $this->assertEquals('report_id', $document->report()->getForeignKeyName());
    }

    public function test_report_report_verification_relationship()
    {
        $report = new Report();
        $reportVerification = new ReportVerification();

        $this->assertInstanceOf(HasMany::class, $report->reportVerifications());
        $this->assertInstanceOf(ReportVerification::class, $report->reportVerifications()->getRelated());

        $this->assertInstanceOf(BelongsTo::class, $reportVerification->report());
        $this->assertInstanceOf(Report::class, $reportVerification->report()->getRelated());
        $this->assertEquals('report_id', $reportVerification->report()->getForeignKeyName());
    }

    public function test_deceased_report_id_unique()
    {
        $indexes = Schema::getIndexes('deceased');
        $isUnique = false;

        foreach ($indexes as $index) {
            if ($index['unique'] && in_array('report_id', $index['columns'])) {
                // Should not contain other columns to be purely report_id unique
                if (count($index['columns']) === 1) {
                    $isUnique = true;
                    break;
                }
            }
        }

        $this->assertTrue($isUnique, "Deceased table does not have a UNIQUE index on report_id");
    }

    public function test_user_soft_deletes()
    {
        $this->assertContains(
            SoftDeletes::class,
            class_uses_recursive(User::class),
            "User model should use SoftDeletes trait."
        );
    }

    public function test_report_verification_timestamps()
    {
        $model = new ReportVerification();
        
        $this->assertTrue($model->usesTimestamps(), "ReportVerification should use timestamps for created_at");
        $this->assertNull($model::UPDATED_AT, "ReportVerification UPDATED_AT should be null to be append-only");
    }
}
