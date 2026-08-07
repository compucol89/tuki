<?php

namespace App\Services;

use App\Models\Organizer;
use Illuminate\Support\Facades\DB;

class OrganizerBalanceService
{
  public function credit(array $data): void
  {
    // Transacción + lock: evita pérdida de actualizaciones con créditos concurrentes
    DB::transaction(function () use ($data) {
      $organizer = Organizer::query()->lockForUpdate()->find($data['organizer_id'] ?? null);

      if (!$organizer) {
        return;
      }

      $organizer->amount = $organizer->amount + ($data['price'] - $data['commission']);
      $organizer->save();
    });
  }
}
