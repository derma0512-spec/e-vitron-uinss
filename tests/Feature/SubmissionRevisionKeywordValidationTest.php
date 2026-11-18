<?php

namespace Tests\Feature;

use App\Filament\Resources\SubmissionResource\Pages\ListSubmissions;
use App\Models\Submission;
use App\Models\User;
use App\Services\SubmissionDecisionService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionRevisionKeywordValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_cannot_request_revision_without_surat_or_drive_keyword(): void
    {
        $admin = User::where('role', 'admin')->first();
        $submission = Submission::where('status', 'menunggu')->first();

        Livewire::actingAs($admin)
            ->test(ListSubmissions::class)
            ->call('selectSubmission', $submission->id)
            ->call('confirmRevision', 'Tolong diperbaiki secepatnya ya')
            ->assertSet('revisionError', 'Revisi belum bisa dikirim! Mohon pilih bagian yang wajib diperbaiki (Surat atau Drive).');

        $this->assertEquals('menunggu', $submission->fresh()->status);
    }

    public function test_can_request_revision_with_surat_keyword(): void
    {
        $admin = User::where('role', 'admin')->first();
        $submission = Submission::where('status', 'menunggu')->first();

        Livewire::actingAs($admin)
            ->test(ListSubmissions::class)
            ->call('selectSubmission', $submission->id)
            ->call('confirmRevision', 'Mohon lengkapi tanda tangan pada surat permohonan.')
            ->assertSet('revisionError', null)
            ->assertNotified();

        $fresh = $submission->fresh();
        $this->assertEquals('revisi', $fresh->status);
        $this->assertEquals('surat', $fresh->effective_revision_type);
    }

    public function test_can_request_revision_with_drive_keyword(): void
    {
        $admin = User::where('role', 'admin')->first();
        $submission = Submission::where('status', 'menunggu')->first();

        Livewire::actingAs($admin)
            ->test(ListSubmissions::class)
            ->call('selectSubmission', $submission->id)
            ->call('confirmRevision', 'Akses link Google Drive terkunci, mohon dibuka aksesnya.')
            ->assertSet('revisionError', null)
            ->assertNotified();

        $fresh = $submission->fresh();
        $this->assertEquals('revisi', $fresh->status);
        $this->assertEquals('materi', $fresh->effective_revision_type);
    }

    public function test_can_request_revision_with_both_keywords(): void
    {
        $admin = User::where('role', 'admin')->first();
        $submission = Submission::where('status', 'menunggu')->first();

        Livewire::actingAs($admin)
            ->test(ListSubmissions::class)
            ->call('selectSubmission', $submission->id)
            ->call('confirmRevision', 'Mohon stempel basah pada surat dan ubah tautan drive materi.')
            ->assertSet('revisionError', null)
            ->assertNotified();

        $fresh = $submission->fresh();
        $this->assertEquals('revisi', $fresh->status);
        $this->assertEquals('keduanya', $fresh->effective_revision_type);
    }

    public function test_decision_service_throws_domain_exception_when_no_keywords_present(): void
    {
        $submission = Submission::where('status', 'menunggu')->first();
        $service = app(SubmissionDecisionService::class);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Revisi tidak dapat dikirim: Mohon sertakan kata "surat" atau "drive / video" agar sistem mengetahui form yang harus dibuka untuk pengaju.');

        $service->requestRevision($submission, 'Perbaiki secepatnya');
    }
}
