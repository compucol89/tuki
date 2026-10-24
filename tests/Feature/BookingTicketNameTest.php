<?php

namespace Tests\Feature;

use App\Models\Event\Booking;
use Tests\TestCase;

class BookingTicketNameTest extends TestCase
{
    public function test_display_ticket_name_uses_preloaded_map_without_querying(): void
    {
        $this->assertSame('VIP', Booking::displayTicketName(123, null, null, null, [123 => 'VIP']));
        $this->assertSame('VIP', Booking::displayTicketName('123', null, 'variation', null, ['123' => 'VIP']));
        $this->assertSame('VIP — Otro', Booking::displayTicketName(123, 'Otro', 'variation', null, [123 => 'VIP']));
    }

    public function test_display_ticket_name_keeps_selected_name_when_not_variation(): void
    {
        $this->assertSame('Entrada general', Booking::displayTicketName(123, 'Entrada general', null, null, []));
    }
}
