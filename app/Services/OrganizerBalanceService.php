<?php

namespace App\Services;

use App\Models\Organizer;

class OrganizerBalanceService
{
  public function credit(array $data): void
  {
    $organizer = Organizer::query()->find($data['organizer_id'] ?? null);

    if (!$organizer) {
      return;
    }

    $organizer->amount = $organizer->amount + ($data['price'] - $data['commission']);
    $organizer->save();
  }
}
