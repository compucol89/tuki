<?php

namespace App\Http\Requests\Instructor;

use App\Rules\ImageMimeTypeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;


class StoreRequest extends FormRequest
{
  protected function prepareForValidation(): void
  {
    App::setLocale('admin');
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
   * @return array
   */
  public function rules()
  {
    return [
      'image' => [
        'required',
        new ImageMimeTypeRule()
      ],
      'language_id' => 'required',
      'name' => 'required|max:255',
      'occupation' => 'required|max:255',
      'description' => 'min:30'
    ];
  }

  public function messages()
  {
    return [
      'language_id.required' => __('The language field is required.')
    ];
  }
}
