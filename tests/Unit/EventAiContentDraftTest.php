<?php

namespace Tests\Unit;

use App\Models\Event\EventAiContentDraft;
use Tests\TestCase;

class EventAiContentDraftTest extends TestCase
{
    public function test_normalize_audit_status_preserves_ai_status_within_column_limit(): void
    {
        $status = 'listo_con_revision_de_promocion';

        $this->assertSame($status, EventAiContentDraft::normalizeAuditStatus($status));
    }

    public function test_normalize_audit_status_handles_empty_and_null(): void
    {
        $this->assertSame('needs_human_review', EventAiContentDraft::normalizeAuditStatus(null));
        $this->assertSame('needs_human_review', EventAiContentDraft::normalizeAuditStatus(''));
        $this->assertSame('needs_human_review', EventAiContentDraft::normalizeAuditStatus('   '));
    }

    public function test_normalize_audit_status_truncates_oversized_values(): void
    {
        $long = str_repeat('a', 300);

        $normalized = EventAiContentDraft::normalizeAuditStatus($long);

        $this->assertSame(190, mb_strlen($normalized));
    }
}
