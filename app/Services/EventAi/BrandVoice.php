<?php

namespace App\Services\EventAi;

/**
 * Voz canónica de TukiPass para copy es-AR.
 * Contrato único: las reglas de voz NO se dispersan en los prompts.
 */
final class BrandVoice
{
  public const LOCALE = 'es-AR';

  public const VOICE = [
    'directa',
    'humana',
    'clara',
    'enérgica',
    'comercial sin exageración',
  ];

  public const GRAMMAR = [
    'preferred' => 'voseo rioplatense',
    'notes' => 'Español argentino natural, sin caricaturizar.',
  ];

  public const PREFERRED_TERMS = [
    'ticket' => 'entrada',
    'buy' => 'reservar',
  ];

  public const BANNED_CLICHES = [
    'experiencia inolvidable',
    'evento imperdible',
    'prepárate para',
    'preparate para',
    'sumérgete en',
    'sumergete en',
    'una noche única',
    'una noche unica',
    'no te lo puedes perder',
    'no te lo podés perder',
    'música, energía y diversión',
    'musica, energia y diversion',
    'viví una experiencia',
    'vivi una experiencia',
    'últimas entradas',
    'se está agotando',
    'se esta agotando',
    'entradas volando',
    'quedan pocos lugares',
    'evento más esperado',
    'evento mas esperado',
  ];

  public function toArray(): array
  {
    return [
      'locale' => self::LOCALE,
      'voice' => self::VOICE,
      'grammar' => self::GRAMMAR,
      'preferred_terms' => self::PREFERRED_TERMS,
      'banned_cliches' => self::BANNED_CLICHES,
    ];
  }

  public function bannedCliches(): array
  {
    return self::BANNED_CLICHES;
  }
}
