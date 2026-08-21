<?php

namespace App\Services\OpenAI\EventAI\Quality;

use App\Services\EventAi\CanonicalEventFacts;

/**
 * Gate determinista de CALIDAD ESTRUCTURAL. Reglas binarias que PHP puede probar.
 * El modelo juzga calidad editorial; este gate juzga reglas objetivas.
 */
class EventContentQualityGate
{
  private const UNEXPECTED_URL_PATTERN = '#(?:javascript:|data:text/html|vbscript:)#i';

  public function failures(array $payload, ?CanonicalEventFacts $facts = null): array
  {
    $failures = [];
    $content = $payload['content'] ?? [];
    $seo = $payload['seo'] ?? [];
    $social = $payload['social'] ?? [];
    $faq = $payload['faq'] ?? [];
    $checklist = $payload['review_checklist'] ?? [];

    if (mb_strlen(trim((string) ($content['public_title'] ?? ''))) < 24) {
      $failures[] = 'El título público es demasiado corto o genérico.';
    }
    if (count(array_filter($content['title_options'] ?? [])) < 4) {
      $failures[] = 'Faltan opciones de título.';
    }
    if (mb_strlen(trim(strip_tags((string) ($content['main_description'] ?? '')))) < 450) {
      $failures[] = 'La descripción principal es demasiado breve.';
    }
    if (count(array_filter($content['what_you_will_experience'] ?? [])) < 3) {
      $failures[] = 'Faltan beneficios o experiencia del evento.';
    }
    if (count(array_filter($content['important_information'] ?? [])) < 3) {
      $failures[] = 'Falta información importante para decidir la reserva.';
    }
    if (count(array_filter($seo['tags'] ?? [])) < 8) {
      $failures[] = 'Faltan tags SEO útiles.';
    }
    if (mb_strlen(trim((string) ($seo['google_short_description'] ?? ''))) < 110) {
      $failures[] = 'La descripción corta para Google es insuficiente.';
    }
    if (mb_strlen(trim((string) ($seo['ai_search_summary'] ?? ''))) < 160) {
      $failures[] = 'El resumen para agentes IA es insuficiente.';
    }
    if (mb_strlen(trim((string) ($social['open_graph_description'] ?? ''))) < 80) {
      $failures[] = 'La descripción Open Graph es insuficiente.';
    }
    if (count($faq) > 6) {
      $failures[] = 'Demasiadas FAQ (máximo 6).';
    }
    if (count(array_filter($checklist)) < 6) {
      $failures[] = 'Falta checklist de revisión humana.';
    }

    $titles = array_map('mb_strtolower', array_filter([$content['public_title'] ?? null, $seo['seo_title'] ?? null]));
    if (count($titles) === 2 && $titles[0] === $titles[1]) {
      $failures[] = 'Título público y SEO title duplicados.';
    }

    foreach (array_merge([$content['main_description'] ?? '', $content['short_description'] ?? '', $seo['meta_description'] ?? '', $seo['ai_search_summary'] ?? '']) as $text) {
      if (preg_match(self::UNEXPECTED_URL_PATTERN, (string) $text)) {
        $failures[] = 'URL o protocolo inesperado en texto público.';
        break;
      }
    }

    foreach ($this->publicTexts($payload) as $text) {
      if (preg_match('/\bticket\b/iu', (string) $text)) {
        $failures[] = 'Uso de "ticket": debe decir "entrada".';
        break;
      }
    }

    return $failures;
  }

  public function publicTexts(array $payload): array
  {
    $content = $payload['content'] ?? [];
    $seo = $payload['seo'] ?? [];
    $social = $payload['social'] ?? [];
    $faq = $payload['faq'] ?? [];

    $texts = [
      $content['public_title'] ?? '',
      $content['subtitle'] ?? '',
      $content['short_description'] ?? '',
      $content['main_description'] ?? '',
      $content['cta'] ?? '',
      $seo['seo_title'] ?? '',
      $seo['meta_description'] ?? '',
      $seo['google_short_description'] ?? '',
      $seo['ai_search_summary'] ?? '',
      $social['open_graph_title'] ?? '',
      $social['open_graph_description'] ?? '',
      $social['instagram_caption'] ?? '',
      $social['whatsapp_share_text'] ?? '',
    ];

    foreach (($content['what_you_will_experience'] ?? []) as $item) {
      $texts[] = $item;
    }
    foreach (($content['important_information'] ?? []) as $item) {
      $texts[] = $item;
    }
    foreach ($faq as $item) {
      $texts[] = $item['question'] ?? '';
      $texts[] = $item['answer'] ?? '';
    }

    return array_filter($texts);
  }
}
