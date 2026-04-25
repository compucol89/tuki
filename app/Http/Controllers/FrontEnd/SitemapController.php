<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Event\EventContent;
use App\Models\Language;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
  public function index()
  {
    $defaultLanguageId = optional(Language::where('is_default', 1)->first())->id;

    $staticPages = collect([
      ['route' => 'index', 'priority' => '1.0', 'changefreq' => 'daily'],
      ['route' => 'events', 'priority' => '0.9', 'changefreq' => 'daily'],
      ['route' => 'about', 'priority' => '0.7', 'changefreq' => 'monthly'],
      ['route' => 'contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
      ['route' => 'faqs', 'priority' => '0.6', 'changefreq' => 'monthly'],
      ['route' => 'blogs', 'priority' => '0.7', 'changefreq' => 'weekly'],
      ['route' => 'frontend.all.organizer', 'priority' => '0.6', 'changefreq' => 'weekly'],
      ['route' => 'shop', 'priority' => '0.6', 'changefreq' => 'daily'],
    ])->filter(function ($page) {
      return Route::has($page['route']);
    })->map(function ($page) {
      return [
        'loc' => route($page['route'], [], true),
        'lastmod' => null,
        'changefreq' => $page['changefreq'],
        'priority' => $page['priority'],
      ];
    });

    $events = EventContent::join('events', 'events.id', '=', 'event_contents.event_id')
      ->where('events.status', 1)
      ->whereDate('events.end_date_time', '>=', $this->now_date_time)
      ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
        return $query->where('event_contents.language_id', $defaultLanguageId);
      })
      ->select('events.id', 'events.updated_at', 'event_contents.slug')
      ->orderBy('events.updated_at', 'desc')
      ->get()
      ->map(function ($event) {
        return [
          'loc' => route('event.details', ['slug' => $event->slug, 'id' => $event->id], true),
          'lastmod' => optional($event->updated_at)->toAtomString(),
          'changefreq' => 'daily',
          'priority' => '0.8',
        ];
      });

    $urls = $staticPages->concat($events);

    return response()
      ->view('frontend.sitemap', compact('urls'))
      ->header('Content-Type', 'application/xml; charset=UTF-8');
  }
}
