<?php

namespace App\Services\EventAi;

class EventAiDraftPostProcessor
{
  /**
   * Frases de datos ausentes / notas internas que jamás deben ser públicas.
   * Cubre variantes sin verbo ("no informado"), con verbo ("no fue informado"),
   * disponibilidad ("no está disponible"), desconocimiento ("se desconoce") y
   * condicionales editoriales ("sujeto a confirmación", "antes de publicar").
   */
  public const BANNED_PATTERN = '/(?:debe confirmarse|consultar con el organizador|pendiente de confirmación|antes de publicar|antes de reservar|sujeto a confirmación|no contamos con información|no (?:fue|está|estan|están|se|ha|hay) (?:informad|especificad|publicad|disponibl|indicad)|no (?:fue|está|estan|están) disponible|no disponible|no informad|no especificad|no se informó|no se sabe|no se indica|se desconoce|sin información|edad mínima no informad)/iu';

  public function sanitize(array $generated, array $canonicalFacts): array
  {
    $content = $generated['content'] ?? [];
    $seo = $generated['seo'] ?? [];
    $social = $generated['social'] ?? [];

    $title = $content['public_title']
      ?? $this->canonicalFactValue($canonicalFacts, ['titulo', 'título', 'nombre del evento'])
      ?? 'Evento en Tukipass';
    $short = trim((string) ($content['short_description'] ?? ''));
    $main = trim((string) ($content['main_description'] ?? ''));
    $address = $this->canonicalFactValue($canonicalFacts, ['direccion', 'dirección', 'ubicacion', 'ubicación']);
    $promo = $this->canonicalFactValue($canonicalFacts, ['promocion', 'promoción', 'acceso']);

    if (empty($seo['ai_search_summary'])) {
      $generated['seo']['ai_search_summary'] = trim($title . ' es una publicación de evento en Tukipass. ' . ($short ?: strip_tags($main)) . ($address ? ' Dirección: ' . $address . '.' : '') . ($promo ? ' Dato destacado: ' . $promo . '.' : ''));
    }

    if (empty($social['open_graph_title'])) {
      $generated['social']['open_graph_title'] = mb_substr($title, 0, 70);
    }

    if (empty($social['open_graph_description'])) {
      $generated['social']['open_graph_description'] = mb_substr($short ?: strip_tags($main), 0, 220);
    }

    if (!is_array($generated['faq'] ?? null)) {
      $generated['faq'] = [];
    }

    $generated['faq'] = $this->filterFaq($generated['faq']);

    if (empty($generated['review_checklist']) || !is_array($generated['review_checklist'])) {
      $generated['review_checklist'] = $this->fallbackReviewChecklist();
    }

    if (count($generated['review_checklist']) < 6) {
      $generated['review_checklist'] = array_slice(array_merge($generated['review_checklist'], $this->fallbackReviewChecklist()), 0, 8);
    }

    return $generated;
  }

  /**
   * Elimina FAQs vacías o que expongan datos ausentes/notas internas
   * (en la pregunta o en la respuesta).
   */
  public function filterFaq(array $faq): array
  {
    return array_values(array_filter($faq, function ($item) {
      $question = trim((string) ($item['question'] ?? ''));
      $answer = trim((string) ($item['answer'] ?? ''));

      if ($question === '' || $answer === '') {
        return false;
      }

      return !preg_match(self::BANNED_PATTERN, $question) && !preg_match(self::BANNED_PATTERN, $answer);
    }));
  }

  public function containsInternalNote(array $generated): bool
  {
    foreach (($generated['faq'] ?? []) as $item) {
      $answer = trim((string) ($item['answer'] ?? ''));

      if ($answer !== '' && preg_match(self::BANNED_PATTERN, $answer)) {
        return true;
      }
    }

    return false;
  }

  private function canonicalFactValue(array $canonicalFacts, array $needles): ?string
  {
    $fields = array_merge(
      data_get($canonicalFacts, 'image_analysis.extracted_fields', []),
      data_get($canonicalFacts, 'image_analysis.sponsors', [])
    );

    foreach ($fields as $field) {
      $label = mb_strtolower((string) (($field['label'] ?? '') . ' ' . ($field['key'] ?? '')));

      foreach ($needles as $needle) {
        if (str_contains($label, mb_strtolower($needle))) {
          $value = trim((string) ($field['value'] ?? $field['raw_text'] ?? ''));

          return $value !== '' && $value !== '-' ? $value : null;
        }
      }
    }

    return null;
  }

  private function fallbackReviewChecklist(): array
  {
    return [
      ['label' => 'Título', 'status' => 'revisar', 'note' => 'Confirmá que el título sea claro, vendible y coherente con el flyer.'],
      ['label' => 'Fecha y horario', 'status' => 'revisar', 'note' => 'Validá día, mes, año, hora de inicio y hora de cierre antes de publicar.'],
      ['label' => 'Dirección', 'status' => 'revisar', 'note' => 'Revisá dirección, ciudad, provincia, piso o referencias de ingreso.'],
      ['label' => 'Acceso o precio', 'status' => 'revisar', 'note' => 'Confirmá precios, gratuidades, cupos o condiciones especiales.'],
      ['label' => 'Descripción', 'status' => 'revisar', 'note' => 'Leé la descripción completa y ajustá cualquier dato propio del organizador.'],
      ['label' => 'SEO y Google', 'status' => 'revisar', 'note' => 'Validá palabras clave y descripción corta para Google.'],
      ['label' => 'Imagen', 'status' => 'revisar', 'note' => 'Confirmá que la portada se vea clara y represente correctamente el evento.'],
    ];
  }
}
