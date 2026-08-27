<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\ReportStatusSeeder::class);
        
        $pelaporRole = \App\Models\Role::firstOrCreate(['role_name' => 'Pelapor']);
        $subOpRole = \App\Models\Role::firstOrCreate(['role_name' => 'Sub Operator']);
        
        $this->pelapor = \App\Models\User::factory()->create([
            'role_id' => $pelaporRole->id,
            'email' => 'pelapor@example.com',
        ]);
        
        $this->subOp = \App\Models\User::factory()->create([
            'role_id' => $subOpRole->id,
        ]);
        
        $docType = \App\Models\DocumentType::firstOrCreate(['name' => 'KTP', 'is_required' => true]);
        
        $this->report = \App\Models\Report::create([
            'user_id' => $this->pelapor->id,
            'report_number' => 'REP-' . time(),
            'voter_name' => 'John Doe',
            'voter_nik' => '1234567890123456',
            'report_status_id' => \App\Models\ReportStatus::where('status_name', 'Pending')->first()->id,
        ]);
        
        $this->verificationService = new \App\Services\VerificationService();
    }

    public function test_perlu_perbaikan_sends_notification()
    {
        \Illuminate\Support\Facades\Notification::fake();

        $this->verificationService->verifyReport($this->subOp, $this->report, 'Perlu Perbaikan', 'KTP kurang jelas');

        \Illuminate\Support\Facades\Notification::assertSentTo(
            $this->pelapor,
            \App\Notifications\ReportNeedsRevisionNotification::class,
            function ($notification, $channels) {
                // TEST 2: Catatan perbaikan diteruskan
                $mailData = $notification->toMail($this->pelapor);
                $this->assertStringContainsString('KTP kurang jelas', collect($mailData->introLines)->join(' '));
                return true;
            }
        );
    }

    public function test_selesai_sends_notification()
    {
        \Illuminate\Support\Facades\Notification::fake();
        
        // Setup report to 'Diproses' first to simulate valid transition to 'Disetujui'
        $this->report->update(['report_status_id' => \App\Models\ReportStatus::where('status_name', 'Diproses')->first()->id]);

        $this->verificationService->verifyReport($this->subOp, $this->report, 'Disetujui', 'Laporan valid');

        \Illuminate\Support\Facades\Notification::assertSentTo(
            $this->pelapor,
            \App\Notifications\ReportCompletedNotification::class
        );
    }

    public function test_no_duplicate_notification_on_same_status()
    {
        \Illuminate\Support\Facades\Notification::fake();

        $this->verificationService->verifyReport($this->subOp, $this->report, 'Perlu Perbaikan', 'KTP kurang jelas');
        
        \Illuminate\Support\Facades\Notification::assertSentToTimes(
            $this->pelapor,
            \App\Notifications\ReportNeedsRevisionNotification::class,
            1
        );
        
        // Attempt same status update manually to bypass the service exception (if it has one for same status, wait, VerificationService doesn't block same status update except 'Disetujui' and 'Ditolak')
        $this->verificationService->verifyReport($this->subOp, $this->report, 'Perlu Perbaikan', 'Masih belum diperbaiki');

        \Illuminate\Support\Facades\Notification::assertSentToTimes(
            $this->pelapor,
            \App\Notifications\ReportNeedsRevisionNotification::class,
            1
        );
    }

    public function test_pelapor_b_does_not_receive_notification()
    {
        \Illuminate\Support\Facades\Notification::fake();
        
        $pelaporB = \App\Models\User::factory()->create([
            'role_id' => \App\Models\Role::where('role_name', 'Pelapor')->first()->id,
        ]);

        $this->verificationService->verifyReport($this->subOp, $this->report, 'Perlu Perbaikan', 'KTP kurang jelas');

        \Illuminate\Support\Facades\Notification::assertNotSentTo(
            $pelaporB,
            \App\Notifications\ReportNeedsRevisionNotification::class
        );
    }

    public function test_null_email_does_not_crash_and_changes_status()
    {
        \Illuminate\Support\Facades\Notification::fake();
        
        $this->pelapor->update(['email' => '']);
        
        $this->verificationService->verifyReport($this->subOp, $this->report, 'Perlu Perbaikan', 'KTP kurang jelas');
        
        $this->assertEquals('perlu_perbaikan', \Illuminate\Support\Str::slug($this->report->fresh()->reportStatus->status_name, '_'));
        
        \Illuminate\Support\Facades\Notification::assertNothingSent();
    }

    public function test_notification_failure_does_not_rollback_status()
    {
        // Mock notification to throw exception
        \Illuminate\Support\Facades\Notification::fake();
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Notifications\Events\NotificationSending::class, function () {
            throw new \Exception('Simulated SMTP failure');
        });

        try {
            $this->verificationService->verifyReport($this->subOp, $this->report, 'Perlu Perbaikan', 'KTP kurang jelas');
        } catch (\Exception $e) {
            // Should not reach here if handled internally
        }
        
        // Status should still be updated
        $this->assertEquals('perlu_perbaikan', \Illuminate\Support\Str::slug($this->report->fresh()->reportStatus->status_name, '_'));
    }
}
