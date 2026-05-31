<?php

namespace App\Http\Requests\ShopManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class CategoryRequest extends FormRequest
{
  protected function prepareForValidation(): void
  {
  }

  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, mixed>
   */
  public function rules()
  {
    return [
      'language_id' => 'required',
      'name' => 'required',
      'status' => 'required',
    ];
  }

  public function messages()
  {
    return [
      'language_id.required' => __('The language field is required.'),
      'name.required' => __('The name field is required.'),
      'status.required' => __('The status field is required.'),
    ];
  }
}
