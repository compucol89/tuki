<?php

namespace App\Http\Requests\Event;

use App\Models\Event\EventContent;
use App\Models\Language;
use App\Rules\ImageMimeTypeRule;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
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
    $request = $this->request->all();
    $ruleArray = [
      'slider_images' => 'required',
      'thumbnail' => [
        'required',
        new ImageMimeTypeRule()
      ],
      'status' => 'required',
      'is_featured' => 'required'
    ];

    if ($this->date_type == 'single') {
      $ruleArray['start_date'] = 'required';
      $ruleArray['start_time'] = 'required';
      $ruleArray['end_date'] = 'required';
      $ruleArray['end_time'] = 'required';
    }

    if ($this->date_type == 'multiple') {
      $ruleArray['m_start_date.**'] = 'required';
      $ruleArray['m_start_time.**'] = 'required';
      $ruleArray['m_end_date.**'] = 'required';
      $ruleArray['m_end_time.**'] = 'required';
    }


    if ($this->event_type == 'online') {
      $ruleArray['early_bird_discount_type'] = 'required';
      $ruleArray['meeting_url'] = 'required';
      $ruleArray['discount_type'] = 'required_if:early_bird_discount_type,enable';
      $ruleArray['early_bird_discount_amount'] = 'required_if:early_bird_discount_type,enable';
      $ruleArray['early_bird_discount_date'] = 'required_if:early_bird_discount_type,enable';
      $ruleArray['early_bird_discount_time'] = 'required_if:early_bird_discount_type,enable';
      $ruleArray['ticket_available_type'] = 'required';
      if ($this->filled('ticket_available_type') && $this->ticket_available_type == 'limited') {
        $ruleArray['ticket_available'] = 'required';
      }
      $ruleArray['max_ticket_buy_type'] = 'required';
      if ($this->filled('max_ticket_buy_type') && $this->max_ticket_buy_type == 'limited') {
        $ruleArray['max_buy_ticket'] = 'required';
      }

      if (!$this->filled('pricing_type')) {
        $ruleArray['price'] = 'required';
      }

      if ($request['early_bird_discount_type'] == 'enable' && $request['discount_type'] == 'percentage') {
        $ruleArray['early_bird_discount_amount'] = 'numeric|between:1,99';
      } elseif ($request['early_bird_discount_type'] == 'enable' && $request['discount_type'] == 'fixed') {
        $price = $request['price'] - 1;
        $ruleArray['early_bird_discount_amount'] = "numeric|between:1, $price";
      }
    }






    if ($this->event_type == 'venue') {
      $ruleArray['latitude'] = 'required_if:event_type,venue';
      $ruleArray['longitude'] = 'required_if:event_type,venue';
    }

    $languages = Language::all();


    foreach ($languages as $language) {
      $slug = createSlug($this[$language->code . '_title']);
      $ruleArray[$language->code . '_title'] = [
        'required',
        'max:255',
        function ($attribute, $value, $fail) use ($slug, $language) {
          $cis = EventContent::where('language_id', $language->id)->get();
          foreach ($cis as $key => $ci) {
            if (strtolower($slug) == strtolower($ci->slug)) {
              $fail('The title field must be unique for ' . $language->name . ' language.');
            }
          }
        }
      ];
      $ruleArray[$language->code . '_title'] = 'required';
      $ruleArray[$language->code . '_category_id'] = 'required';
      $ruleArray[$language->code . '_description'] = 'min:30';
      $ruleArray[$language->code . '_address'] = 'required_if:event_type,venue';
      $ruleArray[$language->code . '_country'] = 'required_if:event_type,venue';
      $ruleArray[$language->code . '_city'] = 'required_if:event_type,venue';
    }
    return $ruleArray;
  }

  public function messages()
  {
    $messageArray = [];

    $languages = Language::all();

    foreach ($languages as $language) {
      $messageArray[$language->code . '_title.required'] = 'El titulo es obligatorio para el idioma ' . $language->name . '.';

      $messageArray[$language->code . '_address.required'] = 'La direccion es obligatoria para el idioma ' . $language->name . '.';
      $messageArray[$language->code . '_country.required'] = 'El pais es obligatorio para el idioma ' . $language->name . '.';
      $messageArray[$language->code . '_city.required'] = 'La ciudad es obligatoria para el idioma ' . $language->name . '.';

      $messageArray[$language->code . '_address.required_if'] = 'La direccion es obligatoria para el idioma ' . $language->name . '.';
      $messageArray[$language->code . '_country.required_if'] = 'El pais es obligatorio para el idioma ' . $language->name . '.';
      $messageArray[$language->code . '_city.required_if'] = 'La ciudad es obligatoria para el idioma ' . $language->name . '.';

      $messageArray[$language->code . '_category_id.required'] = 'La categoria es obligatoria para el idioma ' . $language->name . '.';

      $messageArray[$language->code . '_description.min'] = 'La descripcion debe tener al menos 30 caracteres para el idioma ' . $language->name . '.';
    }


    $messageArray['m_start_date.required'] = 'La fecha de inicio es obligatoria.';
    $messageArray['m_start_time.required'] = 'La hora de inicio es obligatoria.';
    $messageArray['m_end_date.required'] = 'La fecha de finalizacion es obligatoria.';
    $messageArray['m_end_time.required'] = 'La hora de finalizacion es obligatoria.';
    $messageArray['m_start_date.*.required'] = 'La fecha de inicio es obligatoria.';
    $messageArray['m_start_time.*.required'] = 'La hora de inicio es obligatoria.';
    $messageArray['m_end_date.*.required'] = 'La fecha de finalizacion es obligatoria.';
    $messageArray['m_end_time.*.required'] = 'La hora de finalizacion es obligatoria.';

    $messageArray['start_date.required'] = 'La fecha de inicio es obligatoria.';
    $messageArray['start_time.required'] = 'La hora de inicio es obligatoria.';
    $messageArray['end_date.required'] = 'La fecha de finalizacion es obligatoria.';
    $messageArray['end_time.required'] = 'La hora de finalizacion es obligatoria.';

    return $messageArray;
  }

  public function withValidator(Validator $validator)
  {
    $validator->after(function (Validator $validator) {
      $this->validateChronologicalOrder($validator);
    });
  }

  private function validateChronologicalOrder(Validator $validator)
  {
    if ($this->date_type === 'single') {
      if ($this->filled('start_date') && $this->filled('start_time') && $this->filled('end_date') && $this->filled('end_time')) {
        $start = Carbon::parse($this->start_date . ' ' . $this->start_time);
        $end = Carbon::parse($this->end_date . ' ' . $this->end_time);

        if ($end->lessThan($start)) {
          $validator->errors()->add('end_date', 'La fecha y hora de finalizacion deben ser posteriores o iguales a la fecha y hora de inicio.');
        }
      }

      return;
    }

    if ($this->date_type !== 'multiple') {
      return;
    }

    $startDates = $this->input('m_start_date', []);
    $startTimes = $this->input('m_start_time', []);
    $endDates = $this->input('m_end_date', []);
    $endTimes = $this->input('m_end_time', []);

    foreach ($startDates as $index => $startDate) {
      $startTime = $startTimes[$index] ?? null;
      $endDate = $endDates[$index] ?? null;
      $endTime = $endTimes[$index] ?? null;

      if (empty($startDate) || empty($startTime) || empty($endDate) || empty($endTime)) {
        continue;
      }

      $start = Carbon::parse($startDate . ' ' . $startTime);
      $end = Carbon::parse($endDate . ' ' . $endTime);

      if ($end->lessThan($start)) {
        $validator->errors()->add('m_end_date.' . $index, 'La fecha y hora de finalizacion deben ser posteriores o iguales a la fecha y hora de inicio.');
      }
    }
  }
}
