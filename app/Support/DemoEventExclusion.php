<?php

namespace App\Support;

final class DemoEventExclusion
{
  public const EVENT_IDS = [104, 103, 102, 101, 93, 92, 91];

  public const EVENT_SLUGS = [
    'the-conference-planners',
    'design-research-by-australia',
    'decoration-of-the-marriage',
    'motivation-for-online-business',
    'small-business-ideas',
    'grand-night-party',
    'sports-grand-opening',
  ];

  public static function eventIdColumn(): string
  {
    return 'events.id';
  }

  public static function eventSlugColumn(): string
  {
    return 'event_contents.slug';
  }
}
