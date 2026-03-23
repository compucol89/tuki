<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FlexibleImageDimensionRule implements Rule
{
  private int $longestSideMin;
  private int $shortestSideMin;
  private string $message;

  public function __construct(int $longestSideMin, int $shortestSideMin, ?string $message = null)
  {
    $this->longestSideMin = $longestSideMin;
    $this->shortestSideMin = $shortestSideMin;
    $this->message = $message ?? "La imagen debe tener al menos {$longestSideMin}px en su lado mas largo y {$shortestSideMin}px en su lado mas corto.";
  }

  public function passes($attribute, $value)
  {
    if (!$value || !method_exists($value, 'getRealPath')) {
      return false;
    }

    $imageSize = @getimagesize($value->getRealPath());

    if ($imageSize === false) {
      return false;
    }

    [$width, $height] = $imageSize;

    $longestSide = max($width, $height);
    $shortestSide = min($width, $height);

    return $longestSide >= $this->longestSideMin && $shortestSide >= $this->shortestSideMin;
  }

  public function message()
  {
    return $this->message;
  }
}
