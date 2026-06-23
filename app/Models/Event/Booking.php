<?php

namespace App\Models\Event;

use App\Models\Arca\ArcaInvoice;
use App\Models\Customer;
use App\Models\CustomerFiscalProfile;
use App\Models\Event;
use App\Models\Organizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
  use HasFactory;
  protected $fillable = [
    'customer_id',
    'booking_id',
    'event_id',
    'organizer_id',
    'ticket_id',
    'fname',
    'lname',
    'email',
    'phone',
    'country',
    'state',
    'city',
    'zip_code',
    'address',
    'variation',
    'price',
    'tax_percentage',
    'commission_percentage',
    'tax',
    'commission',
    'quantity',
    'discount',
    'early_bird_discount',
    'currencyText',
    'currencyTextPosition',
    'currencySymbol',
    'currencySymbolPosition',
    'paymentMethod',
    'gatewayType',
    'paymentStatus',
    'invoice',
    'attachmentFile',
    'event_date',
    'scan_status',
    'scanned_tickets',
    'conversation_id',
    'access_token',
    'token_legacy_expires_at',
    'fiscal_invoice_token',
  ];

  public function event()
  {
    return $this->hasOne(EventContent::class, 'event_id', 'event_id');
  }
  public function evnt()
  {
    return $this->belongsTo(Event::class, 'event_id', 'id');
  }
  //userInfo
  public function customerInfo()
  {
    return $this->hasOne(Customer::class, 'id', 'customer_id');
  }
  public function fiscalProfile()
  {
    return $this->hasOne(CustomerFiscalProfile::class, 'booking_id', 'id');
  }
  public function addons()
  {
    return $this->hasMany(BookingAddon::class);
  }
  public function arcaInvoice()
  {
    return $this->hasOne(ArcaInvoice::class);
  }
  public function hasInvoiceFile()
  {
    if (empty($this->invoice)) {
      return false;
    }

    return file_exists(storage_path('app/invoices/' . $this->invoice))
      || file_exists(public_path('assets/admin/file/invoices/' . $this->invoice));
  }
  public function scannedTicketIds()
  {
    $tickets = $this->decodeJsonArray($this->scanned_tickets);

    return array_values(array_filter(array_map('strval', $tickets), function ($ticket) {
      return $ticket !== '';
    }));
  }
  public function scannedTicketsCount()
  {
    return count($this->scannedTicketIds());
  }
  public function issuedTicketUniqueIds()
  {
    $variations = $this->decodeJsonArray($this->variation);
    $ids = [];

    foreach ($variations as $variation) {
      if (!is_array($variation) || empty($variation['unique_id'])) {
        continue;
      }

      $ids[] = (string) $variation['unique_id'];
    }

    if (empty($ids)) {
      $quantity = (int) $this->quantity;

      for ($i = 1; $i <= $quantity; $i++) {
        $ids[] = (string) $i;
      }
    }

    return array_values(array_unique(array_filter($ids, function ($id) {
      return $id !== '';
    })));
  }
  public function hasIssuedTicketUniqueId($uniqueId)
  {
    return in_array((string) $uniqueId, $this->issuedTicketUniqueIds(), true);
  }
  public function pendingTicketsCount()
  {
    return max(((int) $this->quantity) - $this->scannedTicketsCount(), 0);
  }
  public function scanPercent()
  {
    $quantity = (int) $this->quantity;

    if ($quantity <= 0) {
      return 0;
    }

    return min(100, (int) round(($this->scannedTicketsCount() * 100) / $quantity));
  }
  public function isFullyScanned()
  {
    $quantity = (int) $this->quantity;

    return $quantity > 0 && $this->scannedTicketsCount() >= $quantity;
  }
  public function ticketBreakdown()
  {
    $variations = $this->decodeJsonArray($this->variation);

    if (empty($variations)) {
      return [[
        'ticket_id' => $this->ticket_id,
        'name' => 'Entrada general',
        'quantity' => (int) $this->quantity,
        'price' => (float) ($this->price ?? 0),
        'discount' => (float) ($this->early_bird_discount ?? 0),
        'unit_price' => (int) $this->quantity > 0 ? (float) ($this->price ?? 0) / (int) $this->quantity : (float) ($this->price ?? 0),
        'unit_discount' => (int) $this->quantity > 0 ? (float) ($this->early_bird_discount ?? 0) / (int) $this->quantity : 0.0,
        'unit_final' => (int) $this->quantity > 0 ? max(((float) ($this->price ?? 0) - (float) ($this->early_bird_discount ?? 0)) / (int) $this->quantity, 0) : (float) ($this->price ?? 0),
        'subtotal' => max((float) ($this->price ?? 0) - (float) ($this->early_bird_discount ?? 0), 0),
        'unique_ids' => [],
        'scanned' => $this->scannedTicketsCount(),
        'pending' => $this->pendingTicketsCount(),
        'scan_percent' => $this->scanPercent(),
      ]];
    }

    $scannedTickets = $this->scannedTicketIds();
    $grouped = [];

    foreach ($variations as $variation) {
      if (!is_array($variation)) {
        continue;
      }

      $quantity = max((int) ($variation['qty'] ?? 1), 1);
      $price = (float) ($variation['price'] ?? 0);
      $discount = (float) ($variation['early_bird_dicount'] ?? ($variation['early_bird_discount'] ?? 0));
      $unitPrice = $price / $quantity;
      $unitDiscount = $discount / $quantity;
      $name = static::displayTicketName($variation['ticket_id'] ?? null, $variation['name'] ?? null);
      $key = implode('|', [
        $variation['ticket_id'] ?? '',
        $name,
        number_format($unitPrice, 2, '.', ''),
        number_format($unitDiscount, 2, '.', ''),
      ]);

      if (!isset($grouped[$key])) {
        $grouped[$key] = [
          'ticket_id' => $variation['ticket_id'] ?? null,
          'name' => $name,
          'quantity' => 0,
          'price' => 0.0,
          'discount' => 0.0,
          'unit_price' => $unitPrice,
          'unit_discount' => $unitDiscount,
          'unit_final' => max($unitPrice - $unitDiscount, 0),
          'subtotal' => 0.0,
          'unique_ids' => [],
          'scanned' => 0,
          'pending' => 0,
          'scan_percent' => 0,
        ];
      }

      $grouped[$key]['quantity'] += $quantity;
      $grouped[$key]['price'] += $price;
      $grouped[$key]['discount'] += $discount;
      $grouped[$key]['subtotal'] += max($price - $discount, 0);

      if (!empty($variation['unique_id'])) {
        $grouped[$key]['unique_ids'][] = (string) $variation['unique_id'];
      }
    }

    foreach ($grouped as $key => $item) {
      $groupScanned = count(array_intersect($item['unique_ids'], $scannedTickets));

      if (empty($item['unique_ids']) && count($grouped) === 1) {
        $groupScanned = $this->scannedTicketsCount();
      }

      $grouped[$key]['scanned'] = min($groupScanned, $item['quantity']);
      $grouped[$key]['pending'] = max($item['quantity'] - $grouped[$key]['scanned'], 0);
      $grouped[$key]['scan_percent'] = $item['quantity'] > 0 ? min(100, (int) round(($grouped[$key]['scanned'] * 100) / $item['quantity'])) : 0;
    }

    return array_values($grouped);
  }
  public function addonBreakdown()
  {
    $addons = $this->relationLoaded('addons') ? $this->addons : $this->addons()->get();

    return $addons->map(function ($addon) {
      return [
        'title' => $addon->title,
        'unit_price' => (float) $addon->unit_price,
        'quantity' => (int) $addon->quantity,
        'subtotal' => (float) $addon->subtotal,
        'redeemed' => (bool) $addon->redeemed,
      ];
    })->values()->all();
  }

  public static function displayTicketName($ticketId, $selectedName = null, $pricingType = null, $languageId = null)
  {
    $selectedName = trim((string) ($selectedName ?? ''));

    if ($selectedName !== '' && $pricingType !== 'variation') {
      return $selectedName;
    }

    $ticketTitle = static::ticketContentTitle($ticketId, $languageId);

    if ($pricingType === 'variation' && $ticketTitle && $selectedName && strcasecmp($ticketTitle, $selectedName) !== 0) {
      return $ticketTitle . ' — ' . $selectedName;
    }

    if ($selectedName !== '') {
      return $selectedName;
    }

    return $ticketTitle ?: 'Entrada';
  }

  public static function ticketNameMatches($ticketId, $sourceName = null, $targetName = null)
  {
    $sourceName = trim((string) ($sourceName ?? ''));
    $targetName = trim((string) ($targetName ?? ''));

    if ($sourceName === $targetName) {
      return true;
    }

    return static::displayTicketName($ticketId, $sourceName) === $targetName;
  }

  private static function ticketContentTitle($ticketId, $languageId = null)
  {
    if (empty($ticketId)) {
      return null;
    }

    $query = TicketContent::where('ticket_id', $ticketId);

    if (!empty($languageId)) {
      $title = (clone $query)->where('language_id', $languageId)->value('title');

      if (!empty($title)) {
        return $title;
      }
    }

    return $query->whereNotNull('title')->value('title');
  }

  protected function decodeJsonArray($value)
  {
    if (empty($value)) {
      return [];
    }

    $decoded = json_decode($value, true);

    return is_array($decoded) ? $decoded : [];
  }
  public function organizer()
  {
    return $this->belongsTo(Organizer::class);
  }
}
