<?php

namespace App\Services\EventAi;

/**
 * Contrato de hechos canónicos V3.
 *
 * Cada hecho relevante del evento expresa: VALUE + PROVENANCE + TRUST + PUBLICATION SAFETY.
 * La resolución de conflictos entre fuentes es DETERMINISTA (no se delega al modelo).
 */
final class CanonicalEventFacts
{
  public const SOURCE_DATABASE = 'database';
  public const SOURCE_TICKET_DB = 'ticket_database';
  public const SOURCE_FORM = 'confirmed_form_fields';
  public const SOURCE_ORGANIZER_TEXT = 'organizer_free_text';
  public const SOURCE_ORGANIZER_NOTES = 'organizer_notes';
  public const SOURCE_ORGANIZER_REVIEW = 'organizer_review';
  public const SOURCE_FLYER = 'accepted_image_fields';
  public const SOURCE_PLATFORM = 'platform_configuration';
  public const SOURCE_DERIVED = 'derived_deterministic';
  public const SOURCE_AI_INFERENCE = 'ai_inference';

  /**
   * Prioridad editorial por fuente. Menor índice = mayor autoridad.
   */
  public const SOURCE_PRIORITY = [
    self::SOURCE_TICKET_DB => 10,
    self::SOURCE_DATABASE => 20,
    self::SOURCE_FORM => 30,
    self::SOURCE_ORGANIZER_REVIEW => 35,
    self::SOURCE_ORGANIZER_TEXT => 40,
    self::SOURCE_ORGANIZER_NOTES => 45,
    self::SOURCE_FLYER => 50,
    self::SOURCE_PLATFORM => 60,
    self::SOURCE_DERIVED => 70,
    self::SOURCE_AI_INFERENCE => 90,
  ];

  /** Datos sensibles: una afirmación sin evidencia suficiente NO se publica. */
  public const SENSITIVE_KEYS = [
    'title', 'start_date', 'end_date', 'start_time', 'end_time', 'timezone',
    'venue', 'address', 'city', 'state', 'country', 'zip_code',
    'price', 'price_min', 'price_max', 'currency', 'pricing_type',
    'promotion', 'ticket_available', 'capacity', 'sold_out',
    'min_age', 'artists', 'benefits', 'access', 'refund_policy',
    'sponsors', 'meeting_url',
  ];

  /** Datos volátiles: correctos hoy, obsoletos mañana → copy_safe = false. */
  public const VOLATILE_KEYS = [
    'price', 'price_min', 'price_max', 'ticket_available',
    'early_bird_discount', 'early_bird_ends_at', 'sold_out', 'promotion',
  ];

  /** @var array<string, array> */
  private array $facts = [];

  /** @var array<string, array> conflictos detectados [key => ['winner' => ..., 'loser' => ...]] */
  private array $conflicts = [];

  public function __construct(
    private string $locale = 'es-AR',
    private string $timezone = 'America/Argentina/Buenos_Aires',
  ) {
  }

  public function add(string $key, mixed $value, string $source, array $options = []): self
  {
    $key = trim($key);
    if ($key === '' || $value === null || $value === '' || $value === []) {
      return $this;
    }

    $existing = $this->facts[$key] ?? null;

    if ($existing !== null && $existing['value'] !== $value) {
      $existingPriority = self::SOURCE_PRIORITY[$existing['source']] ?? 100;
      $newPriority = self::SOURCE_PRIORITY[$source] ?? 100;

      if ($newPriority > $existingPriority) {
        $this->conflicts[$key] = [
          'winner' => $existing,
          'loser' => $this->fact($value, $source, $options),
        ];

        return $this;
      }

      if ($newPriority < $existingPriority) {
        $this->conflicts[$key] = [
          'winner' => $this->fact($value, $source, $options),
          'loser' => $existing,
        ];
      } else {
        $this->conflicts[$key] = [
          'winner' => $existing,
          'loser' => $this->fact($value, $source, $options),
          'note' => 'same_source_conflict',
        ];

        return $this;
      }
    }

    $this->facts[$key] = $this->fact($value, $source, $options);

    return $this;
  }

  private function fact(mixed $value, string $source, array $options): array
  {
    return [
      'value' => $value,
      'source' => $source,
      'confidence' => (float) ($options['confidence'] ?? (in_array($source, [self::SOURCE_AI_INFERENCE], true) ? 0.5 : 1.0)),
      'verified' => (bool) ($options['verified'] ?? $source !== self::SOURCE_AI_INFERENCE),
      'sensitive' => (bool) ($options['sensitive'] ?? false),
      'copy_safe' => (bool) ($options['copy_safe'] ?? !in_array($source, [self::SOURCE_AI_INFERENCE], true)),
      'updated_at' => (string) ($options['updated_at'] ?? now()->toIso8601String()),
    ];
  }

  public function resolve(string $key): ?array
  {
    return $this->facts[$key] ?? null;
  }

  public function conflicts(): array
  {
    return $this->conflicts;
  }

  public function hasConflict(string $key): bool
  {
    return isset($this->conflicts[$key]);
  }

  public function all(): array
  {
    return $this->facts;
  }

  public function locale(): string
  {
    return $this->locale;
  }

  public function timezone(): string
  {
    return $this->timezone;
  }

  /**
   * Facts aptos para incluir en el contexto del modelo (sin notas internas).
   */
  public function toArray(): array
  {
    $out = [];
    foreach ($this->facts as $key => $fact) {
      $out[$key] = [
        'value' => $fact['value'],
        'source' => $fact['source'],
        'confidence' => $fact['confidence'],
        'verified' => $fact['verified'],
        'sensitive' => $fact['sensitive'],
        'copy_safe' => $fact['copy_safe'],
      ];
    }

    return [
      'locale' => $this->locale,
      'timezone' => $this->timezone,
      'facts' => $out,
      'conflicts' => array_keys($this->conflicts),
    ];
  }
}
