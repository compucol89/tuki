<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;


class EventFeatureRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
  {
    return [
      'language_id' => request()->isMethod('POST') ? 'required' : '',
      'icon' => 'required',
      'title' => 'required',
      'text' => 'required',
      'serial_number' => 'required|numeric'
    ];
  }

  public function messages()
  {
    return [
      'language_id.required' => __('The language field is required.')
    ];
  }
}
