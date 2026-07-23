<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Event\Booking;
use App\Models\Event\EventContent;
use App\Models\Event\EventDates;
use App\Models\Event;
use App\Models\Language;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EventConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function build(): self
    {
        $language = Language::where('is_default', 1)->first();
        $event = Event::find($this->booking->event_id);
        $eventContent = EventContent::where('event_id', $this->booking->event_id)
            ->where('language_id', $language?->id)
            ->first();

        if (!$eventContent) {
            $eventContent = EventContent::where('event_id', $this->booking->event_id)->first();
        }

        $eventTitle = $eventContent?->title ?? 'Evento';
        $subject = '🎟️ Tus entradas para ' . $eventTitle . ' — TukiPass';

        // Preparar datos de entradas
        $tickets = $this->prepareTicketSummary();
        $addons = $this->booking->addons()->get();
        $paymentAmounts = $this->paymentAmounts();

        // Adjuntar PDF de entradas si existe
        $pdfPath = storage_path('app/invoices/') . $this->booking->invoice;
        $attachments = [];
        if (!empty($this->booking->invoice) && file_exists($pdfPath)) {
            $attachments[] = [
                'path' => $pdfPath,
                'as' => 'Entradas_' . $this->booking->booking_id . '.pdf',
                'mime' => 'application/pdf',
            ];
        } else {
            Log::warning('EventConfirmationMail: PDF de entradas no encontrado', [
                'booking_id' => $this->booking->booking_id,
            ]);
        }

        // Fecha del evento
        $eventDate = null;
        $eventTime = null;
        if ($this->booking->event_date) {
            try {
                $eventDate = \Carbon\Carbon::parse($this->booking->event_date)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
                $eventTime = \Carbon\Carbon::parse($this->booking->event_date)->format('H:i');
            } catch (\Exception $e) {
                $eventDate = $this->booking->event_date;
            }
        }

        // Guest link
        $guestLink = null;
        if ($this->booking->access_token) {
            $guestLink = route('booking.guest_view', [$this->booking->id]) . '?token=' . $this->booking->access_token;
        }

        // Fiscal invoice link
        $invoiceLink = null;
        if ($this->booking->fiscal_invoice_token) {
            $invoiceLink = route('booking.fiscal_invoice.show', [$this->booking->fiscal_invoice_token]);
        }

        // Dirección del evento (del eventContent, no del booking)
        $eventLocation = collect([$eventContent?->city, $eventContent?->state])
            ->filter(fn($v) => !empty($v) && strtoupper($v) !== 'N/A')
            ->implode(', ');
        $eventAddress = collect([$eventContent?->address, $eventContent?->zip_code, $eventContent?->country])
            ->filter(fn($v) => !empty($v) && strtoupper($v) !== 'N/A')
            ->implode(', ');

        $mail = $this->subject($subject)
            ->view('emails.event_confirmation')
            ->with([
                'booking'       => $this->booking,
                'event'         => $event,
                'eventContent'  => $eventContent,
                'eventTitle'    => $eventTitle,
                'eventDate'     => $eventDate,
                'eventTime'     => $eventTime,
                'eventLocation' => $eventLocation,
                'eventAddress'  => $eventAddress,
                'tickets'       => $tickets,
                'addons'        => $addons,
                'serviceFee'    => $paymentAmounts['service_fee'],
                'discountTotal' => $paymentAmounts['discount_total'],
                'amountCharged' => $paymentAmounts['amount_charged'],
                'guestLink'     => $guestLink,
                'invoiceLink'   => $invoiceLink,
            ]);

        foreach ($attachments as $att) {
            $mail->attach($att['path'], ['as' => $att['as'], 'mime' => $att['mime']]);
        }

        return $mail;
    }

    /**
     * Preparar lista de entradas (variations o quantity simple).
     */
    private function prepareTickets(): array
    {
        $tickets = [];

        if ($this->booking->variation != null) {
            $variations = json_decode($this->booking->variation, true);
            if (is_array($variations)) {
                foreach ($variations as $i => $variation) {
                    $tickets[] = [
                        'index'     => $i + 1,
                        'name'      => Booking::displayTicketName($variation['ticket_id'] ?? null, $variation['name'] ?? null),
                        'unique_id' => $variation['unique_id'] ?? ($this->booking->booking_id . '__' . ($i + 1)),
                        'qty'       => $variation['qty'] ?? 1,
                        'price'     => $variation['price'] ?? 0,
                    ];
                }
            }
        } else {
            $quantity = $this->booking->quantity ?? 1;
            for ($i = 1; $i <= $quantity; $i++) {
                $tickets[] = [
                    'index'     => $i,
                    'name'      => Booking::displayTicketName($this->booking->ticket_id ?? null),
                    'unique_id' => $this->booking->booking_id . '__' . $i,
                    'qty'       => 1,
                    'price'     => $quantity > 0 ? ($this->booking->price ?? 0) / $quantity : 0,
                ];
            }
        }

        return $tickets;
    }

    private function prepareTicketSummary(): array
    {
        $grouped = [];

        foreach ($this->prepareTickets() as $ticket) {
            $name = (string) ($ticket['name'] ?? 'Entrada');
            $price = (float) ($ticket['price'] ?? 0);
            $key = $name . '|' . number_format($price, 2, '.', '');

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'index' => count($grouped) + 1,
                    'name' => $name,
                    'qty' => 0,
                    'price' => $price,
                ];
            }

            $grouped[$key]['qty'] += (int) ($ticket['qty'] ?? 1);
        }

        return array_values($grouped);
    }

    private function paymentAmounts(): array
    {
        $subtotal = (float) ($this->booking->price ?? 0);
        $serviceFee = (float) ($this->booking->tax ?? 0);
        $discountTotal = (float) ($this->booking->discount ?? 0)
            + (float) ($this->booking->early_bird_discount ?? 0);

        return [
            'service_fee' => round($serviceFee, 2),
            'discount_total' => round($discountTotal, 2),
            'amount_charged' => max(round($subtotal + $serviceFee - $discountTotal, 2), 0),
        ];
    }

    /**
     * Generar imágenes QR en PNG base64 para embeber inline.
     */
    private function generateQrImages(array $tickets): array
    {
        $qrImages = [];

        foreach ($tickets as $ticket) {
            try {
                // Intentar PNG (requiere GD); si falla, usar SVG (sin dependencias)
                try {
                    $data   = QrCode::format('png')->size(200)->errorCorrection('H')->generate($ticket['unique_id']);
                    $mime   = 'image/png';
                } catch (\Exception) {
                    $data   = QrCode::size(200)->generate($ticket['unique_id']);
                    $mime   = 'image/svg+xml';
                    Log::info('EventConfirmationMail: QR generado con SVG (GD no disponible)', [
                        'unique_id' => $ticket['unique_id'],
                    ]);
                }

                $qrImages[] = [
                    'unique_id' => $ticket['unique_id'],
                    'name'      => $ticket['name'],
                    'index'     => $ticket['index'],
                    'base64'    => base64_encode((string) $data),
                    'mime'      => $mime,
                ];
            } catch (\Exception $e) {
                Log::error('EventConfirmationMail: Error generando QR', [
                    'unique_id' => $ticket['unique_id'],
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        return $qrImages;
    }
}
