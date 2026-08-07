<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ImageMimeTypeRule implements Rule
{
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $value)
  {
    // Validar el MIME REAL del contenido (finfo), no la extensión declarada por el cliente
    if (!is_object($value) || !method_exists($value, 'getMimeType')) {
      return false;
    }

    $allowedMimes = ['image/jpeg', 'image/png', 'image/svg+xml', 'image/gif', 'image/webp'];

    return in_array(strtolower((string) $value->getMimeType()), $allowedMimes, true);
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return 'Solo se permiten archivos JPG, JPEG, PNG, SVG, GIF o WebP.';
  }
}
