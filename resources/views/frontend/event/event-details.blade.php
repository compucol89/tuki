@extends('frontend.layout')

@section('body-class', 'page-event-detail')

@php
  $cleanSeoText = function ($value) {
    return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
  };

  $eventName = $cleanSeoText($content->title);
  $eventUrl = $canonical ?? route('event.details', ['slug' => $content->eventSlug ?? $content->slug, 'id' => $content->id], true);
  $eventMode = $content->event_type == 'online' ? 'online' : 'presencial';
  $eventDateLabel = !empty($startDateTime)
    ? \Carbon\Carbon::parse($startDateTime, $websiteTimezone ?? $websiteInfo->timezone)->locale('es')->translatedFormat('j \d\e F \d\e Y')
    : 'próximamente';

  $metaDescriptionSource = $cleanSeoText($content->meta_description ?? '');
  $descriptionSource = $cleanSeoText($content->description ?? '');
  $placeholderPatterns = ['lorem ipsum', 'pseudo-latin text', 'placeholder text'];

  if ($metaDescriptionSource !== '' && !\Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($metaDescriptionSource), $placeholderPatterns)) {
    $seoDescription = $metaDescriptionSource;
  } elseif ($descriptionSource !== '' && !\Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($descriptionSource), $placeholderPatterns)) {
    $seoDescription = $descriptionSource;
  } else {
    $seoDescription = "{$eventName} es un evento {$eventMode} en TukiPass. Reservá tu lugar para el {$eventDateLabel} y accedé a toda la información.";
  }

  $seoDescription = \Illuminate\Support\Str::limit($cleanSeoText($seoDescription), 158, '');
@endphp

@section('pageHeading', $eventName)
@section('meta-keywords', $content->meta_keywords ?? '')
@section('meta-description', $seoDescription)
@section('og-title', $eventName . ' | ' . $websiteInfo->website_title)
@section('og-description', $seoDescription)
@section('og-image', $og_image ?? asset('assets/admin/img/event/thumbnail/' . $content->thumbnail))
@section('og-image-alt', $og_image_alt ?? $eventName)
@section('og-image-width', '1200')
@section('og-image-height', '630')
@section('og-url', $eventUrl)
@section('og-type', 'event')
@section('canonical', $eventUrl)

@section('custom-style')
  <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-content.css') }}">
  <style>
    .page-event-detail .ed-body {
      padding-top: var(--tuki-space-8);
      background: #f8fafc;
    }

    .page-event-detail .ed-card {
      margin-bottom: var(--tuki-space-10);
      border: 1px solid rgba(var(--tuki-dark-rgb), 0.08);
      border-radius: var(--tuki-radius-lg);
      background: #ffffff;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
      overflow: hidden;
    }

    .page-event-detail .ed-card__head {
      padding: var(--tuki-space-5) var(--tuki-space-6) var(--tuki-space-3);
    }

    .page-event-detail .ed-card__body {
      padding: 0 var(--tuki-space-6) var(--tuki-space-6);
    }

    .page-event-detail .ed-card__eyebrow {
      display: block;
      margin-bottom: var(--tuki-space-1);
      font-size: var(--tuki-text-xs);
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--tuki-muted);
    }

    .page-event-detail .ed-card__title {
      margin: 0;
      color: var(--tuki-dark);
    }

    .page-event-detail .ed-section {
      margin-bottom: var(--tuki-space-10);
      border: 0;
      border-top: 1px solid rgba(var(--tuki-dark-rgb), 0.08);
      border-radius: 0;
      background: transparent;
      box-shadow: none;
      overflow: visible;
      padding-top: 42px;
    }

    .page-event-detail .ed-section .ed-card__head {
      padding: 0 0 var(--tuki-space-4);
    }

    .page-event-detail .ed-section .ed-card__body {
      padding: 0;
    }

    .page-event-detail .ed-section__head {
      margin-bottom: var(--tuki-space-5);
    }

    .page-event-detail .ed-section__title {
      margin: 0;
      color: var(--tuki-dark);
      font-size: clamp(22px, 2vw, 28px);
      line-height: 1.25;
      font-weight: 750;
      letter-spacing: -0.02em;
    }

    .page-event-detail .ed-section__content {
      color: #4b5563;
      font-size: var(--tuki-text-base);
      line-height: 1.75;
    }

    .page-event-detail .ed-section--summary {
      padding: 24px 0 18px;
      border-top: 1px solid rgba(var(--tuki-dark-rgb), 0.08);
      border-bottom: 1px solid rgba(var(--tuki-dark-rgb), 0.06);
      border-left: 0;
      border-right: 0;
      border-radius: 0;
      background: transparent;
      box-shadow: none;
    }

    .page-event-detail .ed-section--summary .ed-card__head {
      padding: 0 0 var(--tuki-space-3);
    }

    .page-event-detail .ed-section--summary .ed-card__body {
      padding: 0;
    }

    .page-event-detail .ed-section--summary .ed-summary-grid {
      gap: 14px 20px;
    }

    .page-event-detail .ed-section--contextual {
      margin-top: calc(-1 * var(--tuki-space-6));
      padding-top: 0;
      border-top: 0;
      margin-bottom: var(--tuki-space-5);
    }

    .page-event-detail .ed-context-band {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      padding: 2px 0 0;
      margin-top: 0;
    }

    .page-event-detail .ed-body-chip {
      display: inline-flex;
      align-items: center;
      gap: var(--tuki-space-1);
      padding: var(--tuki-space-2) var(--tuki-space-3);
      border: 1px solid rgba(var(--tuki-dark-rgb), 0.08);
      border-radius: var(--tuki-radius-full);
      background: var(--tuki-surface-alt);
      color: var(--tuki-dark);
      font-size: var(--tuki-text-xs);
      font-weight: 700;
      text-decoration: none;
    }

    .page-event-detail .ed-section--description .summernote-content {
      max-width: 72ch;
      color: var(--tuki-dark);
    }

    .page-event-detail .ed-section--media {
      padding: 0;
      border: 0;
      border-radius: 0;
      background: transparent;
      box-shadow: none;
    }

    .page-event-detail .ed-section--media .ed-card__head {
      padding: 0 0 var(--tuki-space-4);
    }

    .page-event-detail .ed-section--media .ed-card__body {
      padding: 0;
    }

    .page-event-detail .ed-gallery-wrap,
    .page-event-detail .ed-spotify-embed,
    .page-event-detail .ed-card__body--embed,
    .page-event-detail .ed-card__video-wrap {
      border-radius: var(--tuki-radius-lg);
      overflow: hidden;
    }

    .page-event-detail .ed-refund-band {
      display: flex;
      align-items: flex-start;
      gap: var(--tuki-space-3);
      padding: var(--tuki-space-4);
      margin-top: var(--tuki-space-10);
      border: 1px solid #f1e7dc;
      border-radius: var(--tuki-radius-md);
      background: linear-gradient(180deg, rgba(249, 115, 22, 0.06) 0%, #fffaf3 100%);
      color: #4b5563;
    }

    .page-event-detail .ed-refund-band__icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 36px;
      width: 36px;
      height: 36px;
      border-radius: var(--tuki-radius-full);
      background: rgba(249, 115, 22, 0.12);
      color: var(--tuki-primary-accessible);
    }

    .page-event-detail .ed-refund-band__content {
      min-width: 0;
      max-width: 72ch;
    }

    .page-event-detail .ed-refund-band__content strong {
      display: block;
      margin-bottom: var(--tuki-space-1);
      color: var(--tuki-dark);
      font-weight: 700;
    }

    .page-event-detail .ed-refund-band__content p {
      margin: 0;
      font-size: var(--tuki-text-sm);
      line-height: 1.6;
    }

    .page-event-detail .ed-ticket-card,
    .page-event-detail .ei-card {
      border: 1px solid rgba(var(--tuki-dark-rgb), 0.10);
      border-radius: var(--tuki-radius-lg);
      background: rgba(255, 255, 255, 0.96);
      box-shadow: 0 18px 40px rgba(var(--tuki-dark-rgb), 0.10);
      overflow: hidden;
    }

    .page-event-detail .ed-related > .container > .ed-card {
      box-shadow: none;
      border-color: transparent;
      background: transparent;
    }

    .ed-breadcrumbs {
      padding: var(--tuki-radius-lg) 0 0;
    }

    .ed-breadcrumbs__list {
      display: flex;
      flex-wrap: wrap;
      gap: var(--tuki-space-2);
      margin: 0;
      padding: 0;
      list-style: none;
      font-size: var(--tuki-text-sm);
      color: var(--tuki-muted);
    }

    .ed-breadcrumbs__list a {
      color: var(--tuki-dark);
      text-decoration: none;
    }

    .ed-breadcrumbs__current {
      color: var(--tuki-primary);
      font-weight: 700;
    }

    .ed-breadcrumbs__sep {
      opacity: 0.5;
    }

    .ed-hero__commerce {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 14px var(--tuki-radius-lg);
      margin-top: 22px;
    }

    .ed-hero__pricebox {
      display: inline-flex;
      flex-direction: column;
      gap: 2px;
      padding: 14px 18px;
      border-radius: var(--tuki-radius-lg);
      background: rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(10px);
      color: var(--tuki-surface);
      min-width: 180px;
    }

    .ed-hero__pricebox-label {
      font-size: var(--tuki-text-xs);
      text-transform: uppercase;
      letter-spacing: 0.08em;
      opacity: 0.78;
    }

    .ed-hero__pricebox-value {
      font-size: var(--tuki-text-3xl);
      line-height: 1.1;
      font-weight: 800;
    }

    .ed-hero__cta-group {
      display: flex;
      flex-wrap: wrap;
      gap: var(--tuki-space-3);
    }

    .ed-hero__cta {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 50px;
      padding: 0 22px;
      border-radius: var(--tuki-radius-full);
      font-weight: 700;
      text-decoration: none !important;
      transition: all var(--tuki-transition-base);
    }

    .ed-hero__cta--primary {
      background: var(--tuki-primary-accessible);
      color: var(--tuki-surface);
      box-shadow: 0 16px 30px rgba(194, 65, 12, 0.24);
    }

    .ed-hero__cta--primary:hover {
      color: var(--tuki-surface);
      background: var(--tuki-primary-hover);
      transform: translateY(-1px);
    }

    .ed-hero__cta--secondary {
      border: 1px solid rgba(255, 255, 255, 0.38);
      color: var(--tuki-surface);
      background: rgba(255, 255, 255, 0.08);
    }

    .ed-hero__cta--secondary:hover {
      color: var(--tuki-surface);
      background: rgba(255, 255, 255, 0.14);
    }

    .ed-mobile-bar {
      display: none;
    }

    .ed-related {
      padding: 20px 0 28px;
      background: transparent;
      border-top: 0;
    }

    .ed-related__grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 360px));
      justify-content: center;
      gap: 20px;
    }

    .ed-related--count-1 .ed-related__grid {
      grid-template-columns: minmax(280px, 360px);
    }

    .ed-related--count-1 .ed-card__body {
      padding-bottom: 0;
    }

    .ed-related__card {
      display: flex;
      flex-direction: column;
      height: 100%;
      border-radius: var(--tuki-radius-xl);
      overflow: hidden;
      background: rgba(255, 255, 255, 0.88);
      border: 1px solid rgba(var(--tuki-dark-rgb), 0.08);
      box-shadow: 0 10px 24px rgba(var(--tuki-dark-rgb), 0.06);
      text-decoration: none !important;
      color: inherit;
      transition: transform var(--tuki-transition-base), box-shadow var(--tuki-transition-base), border-color var(--tuki-transition-base);
    }

    .ed-related__card:hover {
      transform: translateY(-2px);
      border-color: rgba(249, 115, 22, 0.18);
      box-shadow: 0 16px 30px rgba(var(--tuki-dark-rgb), 0.08);
    }

    .ed-related__thumb {
      width: 100%;
      aspect-ratio: 16 / 10;
      object-fit: cover;
      background: var(--tuki-border-light);
    }

    .ed-related__body {
      display: flex;
      flex-direction: column;
      gap: 10px;
      padding: var(--tuki-radius-lg);
    }

    .ed-related__meta {
      display: flex;
      flex-wrap: wrap;
      gap: 8px 12px;
      font-size: 13px;
      color: var(--tuki-muted);
    }

    .ed-related__title {
      margin: 0;
      font-size: var(--tuki-text-xl);
      line-height: 1.3;
      color: var(--tuki-dark);
    }

    .ed-related__desc {
      margin: 0;
      color: #4b5563;
    }

    .ed-related__footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-top: auto;
      font-weight: 700;
      color: var(--tuki-dark);
    }

    .ed-related__price {
      color: var(--tuki-primary-accessible);
    }

    .ed-hero__btn,
    .quantity-input button {
      min-width: 44px;
      min-height: 44px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .quantity {
      min-height: 44px;
    }

    .ed-hero__btn:focus-visible,
    .ed-hero__cta:focus-visible,
    .ed-buy-btn:focus-visible,
    .ed-mobile-bar__cta:focus-visible,
    .ei-org__link:focus-visible,
    .ei-cal__btn:focus-visible,
    .quantity-input button:focus-visible,
    .quantity:focus-visible,
    .ed-breadcrumbs__list a:focus-visible,
    .read-more-btn:focus-visible {
      outline: 3px solid var(--tuki-dark);
      outline-offset: 3px;
      box-shadow: var(--tuki-shadow-focus);
    }

    html {
      scroll-behavior: smooth;
    }

    @media (max-width: 991.98px) {
      .ed-hero__commerce {
        align-items: stretch;
      }

      .ed-hero__pricebox,
      .ed-hero__cta-group {
        width: 100%;
      }

      .ed-hero__cta-group {
        flex-direction: column;
      }

      .ed-hero__cta {
        width: 100%;
      }

      .page-event-detail .ed-body {
        padding-bottom: 108px;
      }

      .ed-mobile-bar {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: var(--tuki-z-modal);
        display: block;
        padding: 10px 14px calc(10px + env(safe-area-inset-bottom));
        background: rgba(255, 255, 255, 0.97);
        border-top: 1px solid rgba(var(--tuki-dark-rgb), 0.08);
        box-shadow: 0 -12px 28px rgba(var(--tuki-dark-rgb), 0.14);
        backdrop-filter: blur(12px);
      }

      .ed-mobile-bar__inner {
        display: flex;
        align-items: center;
        gap: 12px;
      }

      .ed-mobile-bar__price {
        min-width: 0;
        display: flex;
        flex-direction: column;
      }

      .ed-mobile-bar__label {
        font-size: 11px;
        line-height: 1.2;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--tuki-muted);
      }

      .ed-mobile-bar__value {
        font-size: var(--tuki-text-xl);
        line-height: 1.1;
        font-weight: 800;
        color: var(--tuki-dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .ed-mobile-bar__cta {
        flex: 1 1 auto;
        min-height: 48px;
        border-radius: var(--tuki-radius-full);
        background: var(--tuki-primary-accessible);
        color: var(--tuki-surface) !important;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
      }

      .ed-mobile-bar__cta--disabled {
        background: var(--tuki-muted-light);
        pointer-events: none;
      }

      .ed-related__grid {
        grid-template-columns: 1fr;
      }

      .ed-related {
        padding: 16px 0 24px;
      }

      .page-event-detail .ed-section,
      .page-event-detail .ed-card,
      .page-event-detail .ed-ticket-card,
      .page-event-detail .ei-card {
        margin-bottom: var(--tuki-space-6);
      }

      .page-event-detail .ed-section--summary,
      .page-event-detail .ed-section--media {
        padding: 0;
      }

      .page-event-detail .ed-section {
        margin-bottom: var(--tuki-space-6);
        padding-top: 24px;
      }

      .page-event-detail .ed-section__title {
        font-size: 22px;
      }

      .page-event-detail .ed-refund-band {
        padding: 14px;
        margin-top: var(--tuki-space-6);
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .ed-hero__cta,
      .ed-gallery-main__img,
      .skip-link {
        transition: none !important;
      }

      html:focus-within {
        scroll-behavior: auto;
      }
    }

    /* ================================================================
       HERO EVENTO — scope: .ed-hero-event / .ed-ev-*
       ================================================================ */

    .ed-hero-event .hero-content-wrapper {
      padding-top: 60px;
      padding-bottom: 60px;
    }

    /* Hero: anular centrado global de .hero-content */
    .ed-hero-event .hero-content--premium {
      max-width: 960px;
      margin-left: 0;
      margin-right: auto;
      text-align: left;
    }
    .ed-hero-event .hero-content--premium h1 {
      margin-left: 0;
      margin-right: 0;
      text-align: left;
      white-space: normal;
    }
    /* Kicker: neutralizar herencia de .ed-hero__status-pill en chips */
    .ed-ev-kicker .ed-hero__status-pill {
      margin-bottom: 0;
      margin-top: 0;
    }
    .ed-ev-kicker .ed-hero__status-pill::before {
      display: none;
    }
    .ed-ev-kicker .ed-hero__status-pill[class*="--"] {
      box-shadow: none;
      padding: 4px 12px;
    }

    .ed-ev-badge-row {
      margin-bottom: 14px;
    }

    .ed-ev-category-badge {
      display: inline-flex;
      align-items: center;
      padding: 5px 14px;
      border-radius: 999px;
      background: rgba(249, 115, 22, 0.18);
      border: 1px solid rgba(249, 115, 22, 0.42);
      color: #fdba74;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      text-decoration: none !important;
      transition: background 0.2s;
    }

    .ed-ev-category-badge:hover {
      background: rgba(249, 115, 22, 0.30);
      color: #fed7aa;
    }

    .ed-ev-kicker {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 8px;
      margin-bottom: 14px;
    }
    .ed-ev-kicker__chip {
      display: inline-flex;
      align-items: center;
      padding: 4px 12px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      text-decoration: none;
      transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }
    .ed-ev-kicker__chip--category {
      background: rgba(249, 115, 22, 0.18);
      border: 1px solid rgba(249, 115, 22, 0.42);
      color: #fdba74;
    }
    .ed-ev-kicker__chip--category:hover,
    .ed-ev-kicker__chip--category:focus-visible {
      background: rgba(249, 115, 22, 0.30);
      color: #fed7aa;
      text-decoration: none;
    }
    .ed-ev-kicker__chip--category:focus-visible {
      outline: 2px solid rgba(249, 115, 22, 0.55);
      outline-offset: 3px;
    }
    .ed-ev-kicker__chip--status {
      background: rgba(255, 255, 255, 0.10);
      border: 1px solid rgba(255, 255, 255, 0.18);
      color: rgba(255, 255, 255, 0.88);
    }

    .ed-ev-title {
      font-size: clamp(24px, 3.5vw, 50px);
      font-weight: 800;
      line-height: 1.1;
      letter-spacing: -0.03em;
      color: #ffffff;
      margin: 0 0 20px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .ed-ev-meta {
      display: flex;
      flex-direction: column;
      gap: 11px;
      margin: 0 0 22px;
    }

    .ed-ev-meta__item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      color: rgba(255, 255, 255, 0.80);
      font-size: 15px;
      line-height: 1.45;
    }

    .ed-ev-meta__item svg {
      flex-shrink: 0;
      margin-top: 2px;
      stroke: #f97316;
    }

    .ed-ev-price {
      display: inline-flex;
      flex-direction: column;
      gap: 2px;
      padding: 12px 18px;
      margin-bottom: 22px;
      border-radius: 14px;
      background: rgba(255, 255, 255, 0.10);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .ed-ev-price__label {
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.07em;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.60);
    }

    .ed-ev-price__value {
      font-size: clamp(22px, 2.2vw, 30px);
      font-weight: 800;
      line-height: 1.1;
      color: #ffffff;
    }

    .ed-hero-event .hero-actions {
      justify-content: flex-start;
      margin-bottom: 18px;
    }

    .ed-ev-actions {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .ed-breadcrumbs--below-hero {
      padding: 14px 0 4px;
      border-bottom: 1px solid rgba(var(--tuki-dark-rgb), 0.07);
      margin-bottom: var(--tuki-space-6);
    }

    .ed-body-breadcrumbs {
      padding: 8px 0 16px;
    }
    .ed-body-breadcrumbs .ed-breadcrumbs__list {
      font-size: var(--tuki-text-xs);
    }

    .ed-event-quickfacts {
      padding: 0;
      border: 0;
      border-top: 1px solid rgba(var(--tuki-dark-rgb), 0.08);
      margin-bottom: var(--tuki-space-6);
    }
    .ed-event-quickfacts .ed-summary-item {
      background: #ffffff;
      border: 1px solid rgba(var(--tuki-dark-rgb), 0.06);
      box-shadow: none;
      border-radius: var(--tuki-radius-md);
    }

    @media (max-width: 991.98px) {
      .ed-ev-title {
        font-size: clamp(20px, 6.5vw, 32px);
        margin-bottom: 14px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
      }

      .ed-ev-meta {
        gap: 9px;
        margin-bottom: 18px;
      }

      .ed-ev-price {
        margin-bottom: 16px;
      }

      .ed-hero-event .hero-actions {
        flex-direction: column;
        align-items: stretch;
      }

      .ed-hero-event .hero-actions .hero-btn {
        text-align: center;
        justify-content: center;
      }

      .ed-hero-event .hero-content--premium {
        max-width: 100%;
      }
    }

    @media (max-width: 767px) {
      .ed-hero-event .hero-content--premium {
        max-width: 100%;
      }
      .ed-ev-title {
        font-size: clamp(18px, 5.5vw, 26px);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
      }
    }
  </style>
@endsection

@push('scripts')
@php
  $schemaStart = !empty($startDateTime)
    ? \Carbon\Carbon::parse($startDateTime, $websiteTimezone ?? $websiteInfo->timezone)
    : null;
  $schemaEnd = !empty($endDateTime)
    ? \Carbon\Carbon::parse($endDateTime, $websiteTimezone ?? $websiteInfo->timezone)
    : null;

  if (!empty($schemaStart) && !empty($schemaEnd)) {
    if ($schemaEnd->lessThanOrEqualTo($schemaStart) || $schemaStart->diffInDays($schemaEnd) > 31) {
      $schemaEnd = null;
    }
  }

  $schemaStartDate = !empty($schemaStart) ? $schemaStart->toIso8601String() : null;
  $schemaEndDate = !empty($schemaEnd) ? $schemaEnd->toIso8601String() : null;
  $schemaDescription = $seoDescription;
  $schemaLocationName = collect([$content->address, $content->city, $content->state, $content->country])->filter()->implode(', ');
  $schemaLocation = null;

  if ($content->event_type == 'online') {
    $schemaLocation = [
      '@type' => 'VirtualLocation',
      'url' => $eventUrl,
      'name' => __('Evento online'),
    ];
  } elseif ($schemaLocationName !== '') {
    $schemaLocation = [
      '@type' => 'Place',
      'name' => $schemaLocationName,
      'address' => array_filter([
        '@type' => 'PostalAddress',
        'streetAddress' => $content->address ?? '',
        'addressLocality' => $content->city ?? '',
        'addressRegion' => $content->state ?? '',
        'postalCode' => $content->zip_code ?? '',
        'addressCountry' => $content->country ?? '',
      ]),
    ];
  }

  $jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'Event',
    'name' => $eventName,
    'description' => \Illuminate\Support\Str::limit($schemaDescription, 300, ''),
    'startDate' => $schemaStartDate,
    'endDate' => $schemaEndDate,
    'eventStatus' => 'https://schema.org/EventScheduled',
    'eventAttendanceMode' => 'https://schema.org/' . ($content->event_type == 'online' ? 'OnlineEventAttendanceMode' : 'OfflineEventAttendanceMode'),
    'location' => $schemaLocation,
    'image' => !empty($og_image) ? [$og_image] : null,
    'url' => $eventUrl,
    'organizer' => [
      '@type' => 'Organization',
      'name' => !empty($organizer) ? $organizer->username : $websiteInfo->website_title,
    ],
  ];
  if (
    !$over &&
    (
      (is_numeric($ticketSummary['min_ticket_price'] ?? null) && (float) $ticketSummary['min_ticket_price'] >= 0)
      || (($content->pricing_type ?? null) === 'free')
    )
  ) {
    $jsonLd['offers'] = [
      '@type' => 'Offer',
      'price' => is_numeric($ticketSummary['min_ticket_price'] ?? null) ? $ticketSummary['min_ticket_price'] : 0,
      'priceCurrency' => $event_currency ?? 'ARS',
      'availability' => 'https://schema.org/InStock',
      'url' => $eventUrl,
    ];
  }
  $jsonLd = array_filter($jsonLd, function ($value) {
    return !is_null($value) && $value !== '';
  });

  $breadcrumbJsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      [
        '@type' => 'ListItem',
        'position' => 1,
        'name' => __('Inicio'),
        'item' => url('/'),
      ],
      [
        '@type' => 'ListItem',
        'position' => 2,
        'name' => __('Eventos'),
        'item' => route('events', [], true),
      ],
    ],
  ];

  if (!empty($content->name)) {
    $breadcrumbJsonLd['itemListElement'][] = [
      '@type' => 'ListItem',
      'position' => 3,
      'name' => $content->name,
      'item' => route('events', ['category' => $content->slug], true),
    ];
  }

  $breadcrumbJsonLd['itemListElement'][] = [
    '@type' => 'ListItem',
    'position' => !empty($content->name) ? 4 : 3,
    'name' => $eventName,
    'item' => $eventUrl,
  ];
@endphp
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

@if(!empty($content->meta_pixel_id))
<!-- Meta Pixel -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{ $content->meta_pixel_id }}');
fbq('track', 'ViewContent', {content_name: {!! json_encode($content->title, JSON_UNESCAPED_UNICODE | JSON_HEX_AMP) !!}, content_type: 'event'});
</script>
@endif

@if(!empty($content->google_analytics_id))
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $content->google_analytics_id }}"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '{{ $content->google_analytics_id }}');
</script>
@endif

@if(!empty($content->tiktok_pixel_id))
<!-- TikTok Pixel -->
<script>
!function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{};ttq._i[e]=[];ttq._i[e]._u=i;ttq._t=ttq._t||{};ttq._t[e]=+new Date;ttq._n=ttq._n||{};ttq._n[e]=n||{};var o=document.createElement("script");o.type="text/javascript";o.async=!0;o.src=i+"?sdkid="+e+"\x26lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
ttq.load('{{ $content->tiktok_pixel_id }}');
ttq.page();
}(window, document, 'ttq');
</script>
@endif
@endpush

@push('scripts')
<script>
(function() {
  var form = document.querySelector('.sidebar-sticky form');
  if (!form) return;
  form.addEventListener('submit', function() {
    var btn = form.querySelector('button[type="submit"]');
    if (btn && !btn.classList.contains('btn-loading')) {
      btn.classList.add('btn-loading');
      btn.setAttribute('aria-busy', 'true');
      btn.setAttribute('disabled', 'disabled');
      btn.textContent = '{{ __("Procesando...") }}';
    }
  });
})();
</script>
@endpush

@section('content')
  <!-- Event Details V2 -->
  @php
    $map_address = preg_replace('/\s+/u', ' ', trim($content->address));
    $map_address = str_replace('/', ' ', $map_address);
    $map_address = str_replace('?', ' ', $map_address);
    $map_address = str_replace(',', ' ', $map_address);
    $eventDescriptionHtml = clean($content->description);
    $eventDescriptionHtml = preg_replace('/<\s*h1\b/i', '<h2', $eventDescriptionHtml);
    $eventDescriptionHtml = preg_replace('/<\s*\/\s*h1\s*>/i', '</h2>', $eventDescriptionHtml);
  @endphp
  @php
    if ($content->pricing_type == 'free' || !is_numeric($ticketSummary['min_ticket_price'])) {
      $heroPriceLabel = __('Gratis');
    } elseif($ticketSummary['min_ticket_price'] == 0 && $ticketSummary['max_ticket_price'] > 0) {
      $heroPriceLabel = __('Gratis') . ' — ' . symbolPrice($ticketSummary['max_ticket_price']);
    } elseif($ticketSummary['min_ticket_price'] == 0) {
      $heroPriceLabel = __('Gratis');
    } elseif($ticketSummary['has_price_range']) {
      $heroPriceLabel = symbolPrice($ticketSummary['min_ticket_price']) . ' — ' . symbolPrice($ticketSummary['max_ticket_price']);
    } else {
      $heroPriceLabel = symbolPrice($ticketSummary['min_ticket_price']);
    }
  @endphp

  {{-- Hero Evento: Collage Slideshow --}}
  @php
    $heroSlides = [];
    if ($images->count() > 0) {
      foreach ($images as $img) {
        $heroSlides[] = asset('assets/admin/img/event-gallery/' . $img->image);
      }
    }
    if (empty($heroSlides)) {
      $heroSlides[] = asset('assets/admin/img/event/thumbnail/' . $content->thumbnail);
    }
  @endphp

  <section class="hero-section hero-collage-section hero-collage-section--premium ed-hero-event" id="heroSection" aria-labelledby="heroHeadingEvent">

    <div class="hero-slideshow" id="heroCollageBg">
      @foreach($heroSlides as $slideUrl)
        <div class="hero-slide" style="background-image: url('{{ $slideUrl }}');"></div>
      @endforeach
    </div>

    <div class="hero-overlay hero-overlay--premium" aria-hidden="true"></div>
    <div class="hero-vignette" aria-hidden="true"></div>
    <div class="hero-ambient" aria-hidden="true">
      <span class="hero-ambient__orb hero-ambient__orb--a"></span>
      <span class="hero-ambient__orb hero-ambient__orb--b"></span>
      <span class="hero-ambient__orb hero-ambient__orb--c"></span>
    </div>
    <div class="hero-noise" aria-hidden="true"></div>

    <div class="container hero-content-wrapper">
      <div class="hero-content hero-content--premium">

        {{-- Kicker: categoría + estado --}}
        <div class="ed-ev-kicker" aria-label="{{ __('Información rápida del evento') }}">
          @if (!empty($content->name))
            <a href="{{ route('events', ['category' => $content->slug]) }}"
               class="ed-ev-kicker__chip ed-ev-kicker__chip--category"
               aria-label="{{ __('Ver categoría') }}: {{ $content->name }}">
              {{ $content->name }}
            </a>
          @endif
          @if (!empty($heroStatusLabel))
            <span class="ed-ev-kicker__chip ed-ev-kicker__chip--status ed-hero__status-pill {{ $heroStatusClass ?? '' }}" role="status">
              {{ $heroStatusLabel }}
            </span>
          @endif
        </div>

        {{-- Título H1 --}}
        <h1 id="heroHeadingEvent" class="ed-ev-title">{{ $content->title }}</h1>

        {{-- Meta: fecha, ubicación, organizador --}}
        @php $heroDate = \Carbon\Carbon::parse($heroDateTimestamp)->timezone($websiteInfo->timezone); @endphp
        <div class="ed-ev-meta">

          <div class="ed-ev-meta__item">
            <svg width="15" height="15" stroke-width="2" aria-hidden="true"><use href="#icon-calendar"/></svg>
            <span>
              {{ ucfirst($heroDate->translatedFormat('l')) }},
              {{ $heroDate->format('j') }} de {{ ucfirst($heroDate->translatedFormat('F Y')) }}
              &middot; {{ $heroDate->format('H:i') }}
            </span>
          </div>

          <div class="ed-ev-meta__item">
            <svg width="15" height="15" stroke-width="2" aria-hidden="true"><use href="#icon-map-pin"/></svg>
            @if ($content->event_type == 'venue')
              <span>
                @if ($content->city != null){{ $content->city }}@endif
                @if ($content->state), {{ $content->state }}@endif
                @if ($content->country), {{ $content->country }}@endif
              </span>
            @else
              <span>{{ __('Online') }}</span>
            @endif
          </div>

          @if (!empty($summaryOrganizer))
            <div class="ed-ev-meta__item">
              <svg width="15" height="15" stroke-width="2" aria-hidden="true"><use href="#icon-user"/></svg>
              <span>{{ $summaryOrganizer }}</span>
            </div>
          @endif

        </div>
        {{-- /Meta --}}

        {{-- Acciones: favoritos, share, mapa --}}
        <div class="ed-ev-actions">
          @if (Auth::guard('customer')->check())
            @php
              $customer_id = Auth::guard('customer')->user()->id;
              $event_id = $content->id;
              $checkWishList = checkWishList($event_id, $customer_id);
            @endphp
          @else
            @php $checkWishList = false; @endphp
          @endif
          <a href="{{ $checkWishList == false ? route('addto.wishlist', $content->id) : route('remove.wishlist', $content->id) }}"
            class="ed-hero__btn {{ $checkWishList == true ? 'text-success' : '' }}"
            aria-label="{{ $checkWishList ? __('Quitar de favoritos') : __('Guardar en favoritos') }}"
            title="{{ $checkWishList ? __('Quitar de favoritos') : __('Guardar en favoritos') }}">
            <i class="fas fa-bookmark"></i>
            <span class="sr-only">{{ $checkWishList ? __('Quitar de favoritos') : __('Guardar en favoritos') }}</span>
          </a>
          <button type="button" class="ed-hero__btn" data-toggle="modal" data-target=".share-event" aria-label="{{ __('Compartir evento') }}">
            <i class="fas fa-share-alt"></i>
          </button>
          @if ($content->event_type != 'online' && !empty($map_address))
            <button type="button" class="ed-hero__btn" data-toggle="modal" data-target=".bd-example-modal-lg" aria-label="{{ __('Ver mapa') }}">
              <i class="fas fa-map-marker-alt"></i>
            </button>
          @endif
        </div>

      </div>
    </div>

  </section>
  {{-- /Hero Evento --}}

  <section class="ed-body" id="main-content" tabindex="-1">
    <div class="container">
      <div class="row">

        {{-- Left column --}}
        <div class="col-lg-8">

          {{-- Breadcrumb --}}
          <nav class="ed-body-breadcrumbs" aria-label="{{ __('Breadcrumb') }}">
            <ol class="ed-breadcrumbs__list">
              <li><a href="{{ url('/') }}">{{ __('Inicio') }}</a></li>
              <li class="ed-breadcrumbs__sep" aria-hidden="true">/</li>
              <li><a href="{{ route('events') }}">{{ __('Eventos') }}</a></li>
              @if (!empty($content->name))
                <li class="ed-breadcrumbs__sep" aria-hidden="true">/</li>
                <li><a href="{{ route('events', ['category' => $content->slug]) }}">{{ $content->name }}</a></li>
              @endif
              <li class="ed-breadcrumbs__sep" aria-hidden="true">/</li>
              <li class="ed-breadcrumbs__current" aria-current="page">{{ $content->title }}</li>
            </ol>
          </nav>

          {{-- Quick facts: datos clave del evento --}}
          @php $quickDate = \Carbon\Carbon::parse($startDateTime)->timezone($websiteInfo->timezone); @endphp
          <div class="ed-event-quickfacts ed-section" aria-label="{{ __('Información clave del evento') }}">
            <div class="ed-summary-grid">
              <div class="ed-summary-item">
                <span class="ed-summary-item__label">{{ __('Horario') }}</span>
                <strong class="ed-summary-item__value">{{ $quickDate->format('H:i') }}</strong>
                <span class="ed-summary-item__meta">{{ $quickDate->format('H:i') }} · {{ timeZoneOffset($websiteInfo->timezone) }} {{ __('GMT') }}</span>
              </div>
              <div class="ed-summary-item">
                <span class="ed-summary-item__label">{{ __('Precio') }}</span>
                <strong class="ed-summary-item__value" dir="ltr">{{ $heroPriceLabel }}</strong>
                <span class="ed-summary-item__meta">{{ $over ? __('Venta cerrada') : __('Venta abierta') }}</span>
              </div>
              <div class="ed-summary-item">
                <span class="ed-summary-item__label">{{ __('Acceso') }}</span>
                <strong class="ed-summary-item__value">{{ $content->event_type == 'online' ? __('Online') : __('Presencial') }}</strong>
                <span class="ed-summary-item__meta">{{ $content->event_type == 'online' ? __('Participá desde cualquier lugar') : __('Revisá el mapa y dirección') }}</span>
              </div>
              <div class="ed-summary-item">
                <span class="ed-summary-item__label">{{ __('Organiza') }}</span>
                <strong class="ed-summary-item__value">{{ $summaryOrganizer }}</strong>
                <span class="ed-summary-item__meta">{{ $content->date_type == 'multiple' ? __('Elegí la fecha que mejor te quede') : __('Reservá online en pocos pasos') }}</span>
              </div>
            </div>
          </div>

	          {{-- Session errors --}}
	          @if (Session::has('paypal_error'))
            <div class="alert alert-danger">{{ Session::get('paypal_error') }}</div>
          @endif
          @php Session::forget('paypal_error'); @endphp

          {{-- Description card --}}
	          <section class="ed-section ed-section--description" aria-labelledby="event-description-title">
	            <div class="ed-section__head">
	                <h2 class="ed-section__title" id="event-description-title">{{ __('Descripción') }}</h2>
	            </div>
	              <div class="ed-section__content summernote-content">
                {!! $eventDescriptionHtml !!}
              </div>
          </section>

          @if($images->count() > 0)
          <div class="ed-card ed-card--gallery ed-section ed-section--media">
            <div class="ed-card__head">
              <div>
                <span class="ed-card__eyebrow">{{ __('Contenido visual') }}</span>
                <h2 class="ed-card__title">{{ __('Galería') }}</h2>
              </div>
            </div>
            <div class="ed-gallery-wrap">
              <div class="ed-gallery-main">
                <button type="button" class="ed-gallery-main__link" id="edMainLink" aria-label="{{ __('Abrir galería del evento') }}">
                  <img id="edMainImg"
                       src="{{ asset('assets/admin/img/event-gallery/' . $images->first()->image) }}"
                       alt="{{ $content->title }}"
                       class="ed-gallery-main__img">
                  <span class="ed-gallery-main__overlay" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                  </span>
                  @if($images->count() > 1)
                  <div class="ed-gallery-count">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    {{ $images->count() }} {{ __('fotos') }}
                  </div>
                  @endif
                </button>
              </div>
              @if($images->count() > 1)
              <div class="ed-gallery-thumbs" id="edGalleryThumbs">
                @foreach($images as $i => $item)
                <button type="button"
                        class="ed-gallery-thumb {{ $i === 0 ? 'ed-gallery-thumb--active' : '' }}"
                        data-src="{{ asset('assets/admin/img/event-gallery/' . $item->image) }}"
                        data-action="thumb-switch">
                  <img src="{{ asset('assets/admin/img/event-gallery/' . $item->image) }}"
                       alt="{{ $content->title }} — foto {{ $i + 1 }}">
                </button>
                @endforeach
              </div>
              @endif
              <div id="edGalleryLinks" style="display:none">
                @foreach($images as $item)
                <a href="{{ asset('assets/admin/img/event-gallery/' . $item->image) }}"
                   class="ed-gallery-popup-link"
                   aria-label="{{ $content->title }} — abrir imagen de galería">
                  <span class="sr-only">{{ $content->title }} — abrir imagen de galería</span>
                </a>
                @endforeach
              </div>
            </div>
          </div>
          @endif

          @if ($spotifyEmbedUrl)
            <div class="ed-card ed-card--context ed-section ed-section--media">
              <div class="ed-card__head">
                <div>
                  <span class="ed-card__eyebrow">{{ __('Ambiente del evento') }}</span>
                  <h2 class="ed-card__title">{{ __('Escuchá el playlist') }}</h2>
                </div>
              </div>
              <div class="ed-card__body">
                <div class="ed-spotify-embed">
                  <iframe src="{{ $spotifyEmbedUrl }}"
                    frameborder="0"
                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    loading="lazy"
                    title="{{ __('Spotify del evento') }}: {{ $content->title }}"></iframe>
                </div>
              </div>
            </div>
          @endif

          {{-- Map card --}}
          @if ($content->event_type != 'online' && !empty($map_address))
	            <div class="ed-card ed-section ed-section--media">
	              <div class="ed-card__head">
	                <div>
	                  <span class="ed-card__eyebrow">{{ __('Ubicación') }}</span>
	                  <h2 class="ed-card__title">{{ __('Mapa') }}</h2>
	                </div>
	              </div>
	              <div class="ed-card__body ed-card__body--embed">
                <iframe
                  src="https://maps.google.com/maps?width=100%25&amp;height=385&amp;hl=es&amp;q={{ urlencode($map_address) }}&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"
                  height="385" class="ed-card__iframe" allow="fullscreen" loading="lazy"
                  title="{{ $content->title }} — {{ __('Mapa') }}"></iframe>
              </div>
            </div>
          @endif

          {{-- YouTube card --}}
          @php
            $youtubeEmbedUrl = null;
            if (!empty($content->youtube_url)) {
              preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $content->youtube_url, $ym);
              if (!empty($ym[1])) $youtubeEmbedUrl = 'https://www.youtube.com/embed/' . $ym[1];
            }
          @endphp
          @if($youtubeEmbedUrl)
	            <div class="ed-card ed-section ed-section--media">
	              <div class="ed-card__head">
	                <div>
	                  <span class="ed-card__eyebrow">{{ __('Contenido') }}</span>
	                  <h2 class="ed-card__title ed-card__title--with-icon">
                    <span class="ed-card__title-icon" aria-hidden="true"><i class="fab fa-youtube"></i></span>{{ __('Video') }}
                  </h2>
	                </div>
	              </div>
              <div class="ed-card__body ed-card__body--embed">
                <div class="ed-card__video-wrap">
                  <iframe src="{{ $youtubeEmbedUrl }}"
                    class="ed-card__video-iframe"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                    loading="lazy" title="{{ $content->title }}"></iframe>
                </div>
              </div>
            </div>
          @endif

          {{-- Refund policy card --}}
          @if (!empty($content->refund_policy))
	            <div class="ed-refund-band" role="note">
                <span class="ed-refund-band__icon" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3l7 4v5c0 5-3.5 8.5-7 9-3.5-.5-7-4-7-9V7l7-4z"/>
                    <path d="M9.5 12.5l1.7 1.7 3.3-3.7"/>
                  </svg>
                </span>
                <div class="ed-refund-band__content">
                  <strong>{{ __('Política de reembolso') }}</strong>
                  <p>{{ $content->refund_policy }}</p>
                </div>
            </div>
          @endif

        </div>
        {{-- /Left column --}}

        {{-- Right column (sticky sidebar) --}}
        <div class="col-lg-4">
          <div class="sidebar-sticky">

            {{-- CARD 1: Ticket form --}}
            <div class="ed-ticket-card" id="event-booking-card">
              <div class="ed-ticket-card__head">
                {{-- Status pill --}}
                <div class="ed-head-top">
                  <span class="ed-head-pill {{ $over ? 'ed-head-pill--over' : 'ed-head-pill--open' }}">
                    <span class="ed-head-pill__dot"></span>
                    {{ $over ? __('Venta cerrada') : __('Venta abierta') }}
                  </span>
                  <span class="ed-ticket-card__head-title">{{ __('Entradas') }}</span>
                </div>
                {{-- Price --}}
                <p class="ed-ticket-card__head-price">
                  @if ($content->pricing_type == 'free' || !is_numeric($ticketSummary['min_ticket_price']))
                    {{ __('Gratis') }}
                  @elseif($ticketSummary['min_ticket_price'] == 0 && $ticketSummary['max_ticket_price'] > 0)
                    {{ __('Gratis') }}<span class="ed-ticket-card__head-sep">—</span>{{ symbolPrice($ticketSummary['max_ticket_price']) }}
                  @elseif($ticketSummary['min_ticket_price'] == 0)
                    {{ __('Gratis') }}
                  @elseif($ticketSummary['has_price_range'])
                    {{ symbolPrice($ticketSummary['min_ticket_price']) }}<span class="ed-ticket-card__head-sep">—</span>{{ symbolPrice($ticketSummary['max_ticket_price']) }}
                  @else
                    {{ symbolPrice($ticketSummary['min_ticket_price']) }}
                  @endif
                </p>
                {{-- Stock indicator --}}
                @if (!$over)
                  <p class="ed-head-stock">
                    @if ($ticketSummary['has_unlimited_stock'])
                      <span class="ed-head-stock__dot"></span>{{ __('Disponible') }}
                    @elseif($ticketSummary['total_stock'] !== null && $ticketSummary['total_stock'] <= 10)
                      <span class="ed-head-stock__dot ed-head-stock__dot--low"></span>{{ __('¡Últimas') }} {{ $ticketSummary['total_stock'] }} {{ $ticketSummary['total_stock'] == 1 ? __('entrada') : __('entradas') }}!
                    @elseif($ticketSummary['total_stock'] !== null)
                      <span class="ed-head-stock__dot"></span>{{ $ticketSummary['total_stock'] }} {{ __('entradas disponibles') }}
                    @endif
                  </p>
                @endif
              </div>
              <div class="ed-ticket-card__body">
                <form action="{{ route('check-out2') }}" method="post">
                  @csrf
                  <input type="hidden" name="event_id" value="{{ $content->id }}">
                  <input type="hidden" name="pricing_type" value="{{ $content->pricing_type }}">
                  <div class="event-details-information">
                    <input type="hidden" name="date_type" value="{{ $content->date_type }}">
                    @if ($content->date_type == 'multiple')
                      @php
                        $dates = eventDates($content->id);
                        $exp_dates = eventExpDates($content->id);
                      @endphp
                      <div class="form-group mb-3">
                        <label class="ed-field-label">{{ __('Seleccioná fecha') }}</label>
                        <select name="event_date" class="form-control">
                          @if (count($dates) > 0)
                            @foreach ($dates as $date)
                              <option value="{{ FullDateTime($date->start_date_time) }}">
                                {{ FullDateTime($date->start_date_time) }}
                                ({{ timeZoneOffset($websiteInfo->timezone) }} {{ __('GMT') }})
                              </option>
                            @endforeach
                          @endif
                          @if (count($exp_dates) > 0)
                            @foreach ($exp_dates as $exp_date)
                              <option disabled value="">
                                {{ FullDateTime($exp_date->start_date_time) }}
                                ({{ timeZoneOffset($websiteInfo->timezone) }} {{ __('GMT') }})
                              </option>
                            @endforeach
                          @endif
                        </select>
                        @error('event_date')
                          <p class="text-danger">{{ $message }}</p>
                        @enderror
                      </div>
                    @else
                      <input type="hidden" name="event_date"
                        value="{{ FullDateTime($content->start_date . $content->start_time) }}">
                    @endif

                    <p class="ed-section-label">{{ __('Seleccioná tus entradas') }}</p>

                    @if ($content->event_type == 'online' && $content->pricing_type == 'normal')

                      @php
                        $ticket = App\Models\Event\Ticket::where('event_id', $content->id)->first();
                        $event_count = App\Models\Event\Ticket::where('event_id', $content->id)
                            ->get()
                            ->count();
                        if ($ticket->ticket_available_type == 'limited') {
                            $stock = $ticket->ticket_available;
                        } else {
                            $stock = 'unlimited';
                        }
                        //ticket purchase or not check
                        if (Auth::guard('customer')->user() && $ticket->max_ticket_buy_type == 'limited') {
                            $purchase = isTicketPurchaseOnline($ticket->event_id, $ticket->max_buy_ticket);
                        } else {
                            $purchase = ['status' => 'false', 'p_qty' => 0];
                        }
                      @endphp
                      @if ($ticket)

                        <div class="price-count">
                          <h6 dir="ltr">

                            @if ($ticket->early_bird_discount == 'enable')
                              @php
                                $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                              @endphp

                              @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                @php
                                  $calculate_price = $ticket->price - $ticket->early_bird_discount_amount;
                                @endphp
                                {{ symbolPrice($calculate_price) }}
                                <del>
                                  {{ symbolPrice($ticket->price) }}
                                </del>
                              @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                @php
                                  $c_price = ($ticket->price * $ticket->early_bird_discount_amount) / 100;
                                  $calculate_price = $ticket->price - $c_price;
                                @endphp
                                {{ symbolPrice($calculate_price) }}
                                <del>
                                  {{ symbolPrice($ticket->price) }}
                                </del>
                              @else
                                @php
                                  $calculate_price = $ticket->price;
                                @endphp
                                {{ symbolPrice($calculate_price) }}
                              @endif
                            @else
                              @php
                                $calculate_price = $ticket->price;
                              @endphp
                              {{ symbolPrice($calculate_price) }}
                            @endif


                          </h6>
                          <div class="quantity-input">
                            <button class="quantity-down" type="button" aria-label="{{ __('Disminuir cantidad') }}">
                              -
                            </button>
                            <input class="quantity" type="number" readonly value="1"
                              aria-label="{{ __('Cantidad de entradas para') }} {{ __('entrada') }}"
                              data-price="{{ $calculate_price }}" data-max_buy_ticket="{{ $ticket->max_buy_ticket }}"
                              name="quantity" data-ticket_id="{{ $ticket->id }}" data-stock="{{ $stock }}"
                              data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                            <button class="quantity-up" type="button" aria-label="{{ __('Aumentar cantidad') }}">
                              +
                            </button>
                          </div>



                          @if ($ticket->early_bird_discount == 'enable')
                            @php
                              $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                            @endphp
                            @if (!$discount_date->isPast())
                              <p>{{ __('Descuento disponible') . ' ' }} :
                                ({{ __('hasta') . ' ' }} :
                                <span
                                  dir="ltr">{{ \Carbon\Carbon::parse($discount_date)->timezone($websiteInfo->timezone)->translatedFormat('d/m/Y H:i') }}</span>)
                              </p>
                            @endif
                          @endif


                        </div>
                        <p
                          class="text-warning max_error_{{ $ticket->id }}{{ $ticket->max_ticket_buy_type == 'limited' ? $ticket->max_buy_ticket : '' }} ">
                        </p>

                      @endif
                    @elseif($content->event_type == 'online' && $content->pricing_type == 'free')
                      @php
                        $ticket = App\Models\Event\Ticket::where('event_id', $content->id)->first();
                        $event_count = App\Models\Event\Ticket::where('event_id', $content->id)
                            ->get()
                            ->count();

                        if ($ticket->ticket_available_type == 'limited') {
                            $stock = $ticket->ticket_available;
                        } else {
                            $stock = 'unlimited';
                        }

                        //ticket purchase or not check
                        if (Auth::guard('customer')->user() && $ticket->max_ticket_buy_type == 'limited') {
                            $purchase = isTicketPurchaseOnline($ticket->event_id, $ticket->max_buy_ticket);
                            $max_buy_ticket = $ticket->max_buy_ticket;
                        } else {
                            $purchase = ['status' => 'false', 'p_qty' => 0];
                            $max_buy_ticket = 999999;
                        }
                      @endphp
                      <div class="price-count">
                        <h6>
                          {{ __('Gratis') }}
                        </h6>
                        <div class="quantity-input">
                          <button class="quantity-down" type="button" aria-label="{{ __('Disminuir cantidad') }}">
                            -
                          </button>
                          <input class="quantity" readonly type="number" value="1"
                            aria-label="{{ __('Cantidad de entradas para') }} {{ __('entrada online') }}"
                            data-price="{{ $content->price }}" data-max_buy_ticket="{{ $max_buy_ticket }}"
                            name="quantity" data-ticket_id="{{ $ticket->id }}" data-stock="{{ $stock }}"
                            data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                          <button class="quantity-up" type="button" aria-label="{{ __('Aumentar cantidad') }}">
                            +
                          </button>
                        </div>

                      </div>
                      <p
                        class="text-warning max_error_{{ $ticket->id }}{{ $ticket->max_ticket_buy_type == 'limited' ? $ticket->max_buy_ticket : '' }} ">
                      </p>
                    @elseif($content->event_type == 'venue')
                      @php
                        $tickets = DB::table('tickets')
                            ->where('event_id', $content->id)
                            ->get();
                      @endphp
                      @if (count($tickets) > 0)
                        @foreach ($tickets as $ticket)
                          @if ($ticket->pricing_type == 'normal')
                            @php
                              if ($ticket->ticket_available_type == 'limited') {
                                  $stock = $ticket->ticket_available;
                              } else {
                                  $stock = 'unlimited';
                              }

                              //ticket purchase or not check
                              $ticket_content = App\Models\Event\TicketContent::where([['language_id', $currentLanguageInfo->id], ['ticket_id', $ticket->id]])->first();

                              if (Auth::guard('customer')->user() && $ticket->max_ticket_buy_type == 'limited') {
                                  $purchase = isTicketPurchaseVenue($ticket->event_id, $ticket->max_buy_ticket, $ticket->id, @$ticket_content->title);
                              } else {
                                  $purchase = ['status' => 'false', 'p_qty' => 0];
                              }

                            @endphp
                            <p class="mb-0"><strong>{{ __(@$ticket_content->title ?: '') }}</strong></p>
                            <div class="click-show">
                              <div class="show-content">
                                {!! clean(@$ticket_content->description ?? '') !!}
                              </div>
                              @if (strlen(@$ticket_content->description) > 50)
                                <div class="read-more-btn">
                                  <span>{{ __('Ver más') }}</span>
                                  <span>{{ __('Ver menos') }}</span>
                                </div>
                              @endif
                            </div>
                            <div class="price-count">
                              <h6 dir="ltr">
                                @if ($ticket->early_bird_discount == 'enable')
                                  @php
                                    $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                  @endphp

                                  @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                    @php $calculate_price = $ticket->price - $ticket->early_bird_discount_amount; @endphp
                                    {{ symbolPrice($calculate_price) }}
                                    <del>{{ symbolPrice($ticket->price) }}</del>
                                  @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                    @php
                                      $c_price = ($ticket->price * $ticket->early_bird_discount_amount) / 100;
                                      $calculate_price = $ticket->price - $c_price;
                                    @endphp
                                    {{ symbolPrice($calculate_price) }}
                                    <del>{{ symbolPrice($ticket->price) }}</del>
                                  @else
                                    @php $calculate_price = $ticket->price; @endphp
                                    {{ symbolPrice($calculate_price) }}
                                  @endif
                                @else
                                  @php $calculate_price = $ticket->price; @endphp
                                  {{ symbolPrice($calculate_price) }}
                                @endif


                              </h6>
                              <div class="quantity-input">
                                <button class="quantity-down" type="button" aria-label="{{ __('Disminuir cantidad') }}">
                                  -
                                </button>
                                <input class="quantity" readonly type="number" value="0"
                                  aria-label="{{ __('Cantidad de entradas para') }} {{ __('entrada') }}"
                                  data-price="{{ $calculate_price }}"
                                  data-max_buy_ticket="{{ $ticket->max_buy_ticket }}" name="quantity[]"
                                  data-ticket_id="{{ $ticket->id }}" data-stock="{{ $stock }}"
                                  data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                                <button class="quantity-up" type="button" aria-label="{{ __('Aumentar cantidad') }}">
                                  +
                                </button>
                              </div>


                              @if ($ticket->early_bird_discount == 'enable')
                                @php
                                  $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                @endphp
                                @if (!$discount_date->isPast())
                                  <p>{{ __('Descuento disponible') . ' ' }} :
                                    ({{ __('hasta') . ' ' }} :
                                    <span
                                      dir="ltr">{{ \Carbon\Carbon::parse($discount_date)->timezone($websiteInfo->timezone)->translatedFormat('d/m/Y H:i') }}</span>)
                                  </p>
                                @endif
                              @endif

                            </div>
                            <p
                              class="text-warning max_error_{{ $ticket->id }}{{ $ticket->max_ticket_buy_type == 'limited' ? $ticket->max_buy_ticket : '' }} ">
                            </p>
                          @elseif($ticket->pricing_type == 'variation')
                            @php
                              $variations = json_decode($ticket->variations);

                              $varition_names = App\Models\Event\VariationContent::where([['ticket_id', $ticket->id], ['language_id', $currentLanguageInfo->id]])->get();
                              if (empty($varition_names)) {
                                  $varition_names = App\Models\Event\VariationContent::where('ticket_id', $ticket->id)->get();
                              }

                              $de_lang = App\Models\Language::where('is_default', 1)->first();
                              $de_varition_names = App\Models\Event\VariationContent::where([['ticket_id', $ticket->id], ['language_id', $de_lang->id]])->get();
                              if (empty($de_varition_names)) {
                                  $de_varition_names = App\Models\Event\VariationContent::where([['ticket_id', $ticket->id]])->get();
                              }
                            @endphp
                            @foreach ($variations as $key => $item)
                              @php
                                //ticket purchase or not check
                                if (Auth::guard('customer')->user()) {
                                    if (count($de_varition_names) > 0) {
                                        $purchase = isTicketPurchaseVenue($ticket->event_id, $item->v_max_ticket_buy, $ticket->id, $de_varition_names[$key]['name']);
                                    }
                                } else {
                                    $purchase = ['status' => 'false', 'p_qty' => 0];
                                }
                                $ticket_content = App\Models\Event\TicketContent::where([['language_id', $currentLanguageInfo->id], ['ticket_id', $ticket->id]])->first();
                                if (empty($ticket_content)) {
                                    $ticket_content = App\Models\Event\TicketContent::where([['ticket_id', $ticket->id]])->first();
                                }
                              @endphp
                              <p class="mb-0"><strong>{{ __(@$ticket_content->title ?: '') }} -
                                  {{ __(@$varition_names[$key]['name'] ?: '') }}</strong>
                              </p>
                              <div class="click-show">
                                <div class="show-content">
                                  {!! clean(@$ticket_content->description ?? '') !!}
                                </div>
                                @if (strlen(@$ticket_content->description) > 50)
                                  <div class="read-more-btn">
                                    <span>{{ __('Ver más') }}</span>
                                    <span>{{ __('Ver menos') }}</span>
                                  </div>
                                @endif
                              </div>
                              <div class="price-count">
                                <h6 dir="ltr">
                                  @if ($ticket->early_bird_discount == 'enable')
                                    @php
                                      $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                    @endphp
                                    @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                      @php
                                        $calculate_price = $item->price - $ticket->early_bird_discount_amount;
                                      @endphp
                                      {{ symbolPrice($calculate_price) }}

                                      <del>
                                        {{ symbolPrice($item->price) }}
                                      </del>
                                    @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                      @php
                                        $c_price = ($item->price * $ticket->early_bird_discount_amount) / 100;
                                        $calculate_price = $item->price - $c_price;
                                      @endphp
                                      {{ symbolPrice($calculate_price) }}

                                      <del>
                                        {{ symbolPrice($item->price) }}
                                      </del>
                                    @else
                                      @php
                                        $calculate_price = $item->price;
                                      @endphp
                                      {{ symbolPrice($calculate_price) }}
                                    @endif
                                  @else
                                    @php
                                      $calculate_price = $item->price;
                                    @endphp
                                    {{ symbolPrice($calculate_price) }}
                                  @endif

                                </h6>

                                <div class="quantity-input">
                                  <button class="quantity-down_variation" type="button" aria-label="{{ __('Disminuir cantidad') }}">
                                    -
                                  </button>
                                  <input type="hidden" name="v_name[]" value="{{ $item->name }}">
                                  @php
                                    if ($item->ticket_available_type == 'limited') {
                                        $stock = $item->ticket_available;
                                    } else {
                                        $stock = 'unlimited';
                                    }
                                    if ($item->max_ticket_buy_type == 'limited') {
                                        $max_buy = $item->v_max_ticket_buy;
                                    } else {
                                        $max_buy = 'unlimited';
                                    }
                                  @endphp
                                  <input type="number" value="0" class="quantity"
                                    aria-label="{{ __('Cantidad para') }} {{ __('entrada') }} {{ __(@$varition_names[$key]['name'] ?: '') }}"
                                    data-price="{{ $calculate_price }}" data-max_buy_ticket="{{ $max_buy }}"
                                    data-name="{{ $item->name }}" name="quantity[]"
                                    data-ticket_id="{{ $ticket->id }}" readonly data-stock="{{ $stock }}"
                                    data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                                  <button class="quantity-up" type="button" aria-label="{{ __('Aumentar cantidad') }}">
                                    +
                                  </button>
                                </div>
                                @if ($ticket->early_bird_discount == 'enable')
                                  @php
                                    $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                  @endphp
                                  @if (!$discount_date->isPast())
                                    <p>{{ __('Descuento disponible') . ' ' }} :
                                      ({{ __('hasta') . ' ' }} :
                                      <span
                                        dir="ltr">{{ \Carbon\Carbon::parse($discount_date)->timezone($websiteInfo->timezone)->translatedFormat('d/m/Y H:i') }}</span>)
                                    </p>
                                  @endif
                                @endif
                              </div>
                              <p class="text-warning max_error_{{ $ticket->id }}{{ $item->v_max_ticket_buy }} ">
                              </p>
                            @endforeach
                          @elseif($ticket->pricing_type == 'free')
                            @php
                              if ($ticket->ticket_available_type == 'limited') {
                                  $stock = $ticket->ticket_available;
                              } else {
                                  $stock = 'unlimited';
                              }

                              //ticket purchase or not check
                              $de_lang = App\Models\Language::where('is_default', 1)->first();
                              $ticket_content_default = App\Models\Event\TicketContent::where([['language_id', $de_lang->id], ['ticket_id', $ticket->id]])->first();
                              if (Auth::guard('customer')->user() && $ticket->max_ticket_buy_type == 'limited') {
                                  $purchase = isTicketPurchaseVenue($ticket->event_id, $ticket->max_buy_ticket, $ticket->id, @$ticket_content_default->title);
                              } else {
                                  $purchase = ['status' => 'false', 'p_qty' => 1];
                              }
                              $ticket_content = App\Models\Event\TicketContent::where([['language_id', $currentLanguageInfo->id], ['ticket_id', $ticket->id]])->first();
                            @endphp
                            <p class="mb-0"><strong>{{ __(@$ticket_content->title ?: '') }}</strong></p>
                            <div class="click-show">
                              <div class="show-content">
                                {!! clean(@$ticket_content->description ?? '') !!}
                              </div>
                              @if (strlen(@$ticket_content->description) > 50)
                                <div class="read-more-btn">
                                  <span>{{ __('Ver más') }}</span>
                                  <span>{{ __('Ver menos') }}</span>
                                </div>
                              @endif
                            </div>
                            <div class="price-count">
                              <h6>
                                <span class="">{{ __('Gratis') }}</span>
                              </h6>
                              <div class="quantity-input">
                                <button class="quantity-down" type="button" aria-label="{{ __('Disminuir cantidad') }}">
                                  -
                                </button>
                                <input class="quantity" data-max_buy_ticket="{{ $ticket->max_buy_ticket }}"
                                  aria-label="{{ __('Cantidad de entradas para') }} {{ __('entrada') }}"
                                  type="number" value="0" data-price="{{ $ticket->price }}" name="quantity[]"
                                  data-ticket_id="{{ $ticket->id }}" readonly data-stock="{{ $stock }}"
                                  data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                                <button class="quantity-up" type="button" aria-label="{{ __('Aumentar cantidad') }}">
                                  +
                                </button>
                              </div>
                            </div>
                            <p
                              class="text-warning max_error_{{ $ticket->id }}{{ $ticket->max_ticket_buy_type == 'limited' ? $ticket->max_buy_ticket : '' }} ">
                            </p>
                          @endif
                        @endforeach
                      @endif
                    @endif

                    @if ($tickets_count > 0)
                      <div class="ed-total-row">
                        <span class="ed-total-label">{{ __('Total a pagar') }}</span>
                        <span class="ed-total-value" dir="ltr">
                          <span>{{ $basicInfo->base_currency_symbol_position == 'left' ? $basicInfo->base_currency_symbol : '' }}</span>
                          <span id="total_price">0</span>
                          <span>{{ $basicInfo->base_currency_symbol_position == 'right' ? $basicInfo->base_currency_symbol : '' }}</span>
                        </span>
                        <input type="hidden" name="total" id="total">
                      </div>
                      {{-- ed-order-recap removed: total already shown above --}}
                      <div class="ed-cta-zone">
                        <button class="ed-buy-btn" type="submit" {{ $over ? 'disabled' : '' }}>
                          {{ $over ? __('Evento finalizado') : __('Reservar mi lugar') }}
                          @if (!$over)
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                          @endif
                        </button>
                        @if (!$over)
                          <div class="ed-trust-row">
                            <span class="ed-trust-item">
                              <svg class="ed-trust-item__icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                              {{ __('Pago seguro') }}
                            </span>
                            <span class="ed-trust-item">
                              <svg class="ed-trust-item__icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
                              {{ __('Política de reembolso') }}
                            </span>
                            <span class="ed-trust-item">
                              <svg class="ed-trust-item__icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/></svg>
                              {{ __('Entradas oficiales') }}
                            </span>
                          </div>
                        @endif
                      </div>
                    @endif
                  </div>

                </form>
              </div>
            </div>
            {{-- /Ticket form card --}}

            {{-- CARD 2: Event info --}}
            <div class="ei-card">

              {{-- Organizer --}}
              @if ($organizer == '')
                @php $admin = App\Models\Admin::first(); @endphp
                <div class="ei-org">
                  <img class="ei-org__avatar lazy"
                    src="{{ asset('assets/front/images/user.png') }}"
                    data-src="{{ asset('assets/admin/img/admins/' . $admin->image) }}"
                    alt="{{ $admin->username }}">
                  <div class="ei-org__info">
                    <span class="ei-label">{{ __('Organizado por') }}</span>
                    <p class="ei-org__name">{{ $admin->username }}</p>
                    <a class="ei-org__link" href="{{ route('frontend.organizer.details', [$admin->id, str_replace(' ', '-', $admin->username), 'admin' => 'true']) }}">{{ __('Ver perfil del organizador') }} <i class="fas fa-arrow-right ei-org__arrow" aria-hidden="true"></i></a>
                  </div>
                </div>
              @else
                <div class="ei-org">
                  <img class="ei-org__avatar lazy"
                    src="{{ asset('assets/front/images/user.png') }}"
                    @if ($organizer->photo != null)
                      data-src="{{ asset('assets/admin/img/organizer-photo/' . $organizer->photo) }}"
                    @endif
                    alt="{{ $organizer->username }}">
                  <div class="ei-org__info">
                    <span class="ei-label">{{ __('Organizado por') }}</span>
                    <p class="ei-org__name">{{ $organizer->username }}</p>
                    <a class="ei-org__link" href="{{ route('frontend.organizer.details', [$organizer->id, str_replace(' ', '-', $organizer->username)]) }}">{{ __('Ver perfil del organizador') }} <i class="fas fa-arrow-right ei-org__arrow" aria-hidden="true"></i></a>
                  </div>
                </div>
              @endif

              {{-- Address --}}
              @if ($content->address != null)
                <div class="ei-meta">
                  <i class="fas fa-map-marker-alt ei-meta__icon"></i>
                  <div>
                    <span class="ei-label">{{ __('Ubicación') }}</span>
                    <p class="ei-meta__text">{{ $content->address }}</p>
                  </div>
                </div>
              @endif

              {{-- Add to Calendar --}}
              @php
                $start_date    = str_replace('-', '', $content->start_date);
                $start_time_cal = str_replace(':', '', $content->start_time);
                $end_date      = str_replace('-', '', $content->end_date);
                $end_time_cal  = str_replace(':', '', $content->end_time);
              @endphp
              <div class="ei-cal">
                <span class="ei-label"><i class="fas fa-calendar-plus ei-cal__icon" aria-hidden="true"></i>{{ __('Añadir al calendario') }}</span>
                <div class="ei-cal__btns">
                  <a target="_blank" rel="noopener noreferrer" class="ei-cal__btn ei-cal__btn--google"
                    href="//calendar.google.com/calendar/u/0/r/eventedit?text={{ urlencode($content->title) }}&dates={{ $start_date }}T{{ $start_time_cal }}/{{ $end_date }}T{{ $end_time_cal }}&ctz={{ $websiteInfo->timezone }}&details={{ urlencode('Más información: ' . route('event.details', [$content->eventSlug, $content->id])) }}&location={{ urlencode($content->event_type == 'online' ? 'En línea' : $content->address) }}&sf=true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Google
                  </a>
                  <a target="_blank" rel="noopener noreferrer" class="ei-cal__btn"
                    href="//calendar.yahoo.com/?v=60&view=d&type=20&TITLE={{ urlencode($content->title) }}&ST={{ $start_date }}T{{ $start_time_cal }}&ET={{ $end_date }}T{{ $end_time_cal }}&DUR=9959&DESC={{ urlencode('Más información: ' . route('event.details', [$content->eventSlug, $content->id])) }}&in_loc={{ urlencode($content->event_type == 'online' ? 'En línea' : $content->address) }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                    Yahoo
                  </a>
                </div>
              </div>

            </div>
            {{-- /Event info card --}}

          </div>
        </div>
        {{-- /Right column --}}

      </div>

      @if (!empty(showAd(3)))
        <div class="text-center mt-4">
          {!! showAd(3) !!}
        </div>
      @endif

    </div>
  </section>
  <!-- Event Details V2 End -->

  <div class="ed-mobile-bar d-lg-none" aria-label="{{ __('Acceso rápido a la compra') }}">
    <div class="container">
      <div class="ed-mobile-bar__inner">
        <div class="ed-mobile-bar__price">
          <span class="ed-mobile-bar__label">{{ __('Entradas desde') }}</span>
          <strong class="ed-mobile-bar__value" id="mobileStickyPrice">{{ $heroPriceLabel }}</strong>
        </div>
        <a href="#event-booking-card"
          class="ed-mobile-bar__cta {{ $over ? 'ed-mobile-bar__cta--disabled' : '' }}"
          data-scroll-target="#event-booking-card">
          {{ $over ? __('Evento finalizado') : __('Reservar mi lugar') }}
        </a>
      </div>
    </div>
  </div>

  @if (!empty($related_events) && $related_events->count() > 0)
    <section class="ed-related ed-related--count-{{ min($related_events->count(), 3) }}">
      <div class="container">
        <div class="ed-card">
          <div class="ed-card__head">
            <div>
              @if (($relatedEventsMode ?? null) === 'upcoming')
                <span class="ed-card__eyebrow">{{ __('Próximos eventos') }}</span>
                <h2 class="ed-card__title">{{ __('De este organizador') }}</h2>
              @elseif (($relatedEventsMode ?? null) === 'past')
                <span class="ed-card__eyebrow">{{ __('Eventos anteriores') }}</span>
                <h2 class="ed-card__title">{{ __('De este organizador') }}</h2>
              @else
                <span class="ed-card__eyebrow">{{ __('Más del organizador') }}</span>
                <h2 class="ed-card__title">{{ __('Eventos de este organizador') }}</h2>
              @endif
            </div>
          </div>
          <div class="ed-card__body">
            <div class="ed-related__grid">
              @foreach ($related_events->take(3) as $item)
                @php
                  $relatedTicket = $relatedTickets[$item->id] ?? null;
                  $relatedOrganizer = !empty($item->organizer_id) ? ($relatedOrganizers[$item->organizer_id] ?? null) : null;
                  $relatedPrice = __('Próximamente');

                  if (!empty($relatedTicket)) {
                    if ($relatedTicket->pricing_type == 'free') {
                      $relatedPrice = __('Gratis');
                    } elseif (is_numeric($relatedTicket->price ?? null)) {
                      $relatedPrice = symbolPrice($relatedTicket->price);
                    }
                  }
                @endphp
                <a href="{{ route('event.details', ['slug' => $item->slug, 'id' => $item->id]) }}" class="ed-related__card">
                  <img
                    src="{{ asset('assets/admin/img/event/thumbnail/' . $item->thumbnail) }}"
                    alt="{{ $item->title }}"
                    class="ed-related__thumb">
                  <div class="ed-related__body">
                    <div class="ed-related__meta">
                      @if (!empty($item->city) || !empty($item->country))
                        <span>{{ collect([$item->city, $item->country])->filter()->implode(', ') }}</span>
                      @endif
                      @if (!empty($relatedOrganizer))
                        <span>{{ __('Por') }} {{ $relatedOrganizer->username }}</span>
                      @endif
                    </div>
                    <h3 class="ed-related__title">{{ $item->title }}</h3>
                    <p class="ed-related__desc">{{ \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags($item->description ?? ''))), 110, '...') }}</p>
                    <div class="ed-related__footer">
                      <span class="ed-related__price" dir="ltr">{{ $relatedPrice }}</span>
                      <span>{{ __('Ver evento') }}</span>
                    </div>
                  </div>
                </a>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </section>
  @endif
@endsection
@section('modals')
  @includeIf('frontend.partials.modals')
@endsection

@push('scripts')
<script>
/* ── Gallery thumbnail switch (delegated) ── */
document.addEventListener('click', function(e) {
  var btn = e.target.closest('[data-action="thumb-switch"]');
  if (!btn) return;
  document.querySelectorAll('.ed-gallery-thumb').forEach(function(t) {
    t.classList.remove('ed-gallery-thumb--active');
  });
  btn.classList.add('ed-gallery-thumb--active');
  var img = document.getElementById('edMainImg');
  if (img) { img.style.opacity = '0'; setTimeout(function(){ img.src = btn.dataset.src; img.style.opacity = '1'; }, 120); }
});
/* ── Total price: vanilla JS (independiente de jQuery/defer) ── */
document.addEventListener('DOMContentLoaded', function() {
  var symL = '{{ $basicInfo->base_currency_symbol_position == "left"  ? addslashes($basicInfo->base_currency_symbol) : "" }}';
  var symR = '{{ $basicInfo->base_currency_symbol_position == "right" ? addslashes($basicInfo->base_currency_symbol) : "" }}';
  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.querySelectorAll('[data-scroll-target]').forEach(function(link) {
    link.addEventListener('click', function(e) {
      var target = document.querySelector(link.getAttribute('data-scroll-target'));
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
    });
  });

  function recalcTotal() {
    var total = 0;
    document.querySelectorAll('.quantity[data-price]').forEach(function(inp) {
      var qty   = parseInt(inp.value,  10) || 0;
      var price = parseFloat(inp.dataset.price) || 0;
      total += qty * price;
    });
    var elTotal   = document.getElementById('total_price');
    var elHidden  = document.getElementById('total');
    var elRecap   = document.getElementById('edRecapPrice');
    var elMobile  = document.getElementById('mobileStickyPrice');
    var formatted = total > 0 ? total.toFixed(2) : '0';
    if (elTotal)  elTotal.textContent = formatted;
    if (elHidden) elHidden.value      = formatted;
    if (elRecap)  elRecap.textContent = total > 0 ? ' · ' + symL + formatted + symR : '';
    if (elMobile) elMobile.textContent = total > 0 ? (symL + formatted + symR) : {!! json_encode($heroPriceLabel, JSON_UNESCAPED_UNICODE | JSON_HEX_AMP) !!};
  }

  /* Ejecutar al cargar */
  recalcTotal();

  /* Ejecutar después de cada click en botones +/- (jQuery los modifica con .val()) */
  document.addEventListener('click', function(e) {
    if (e.target.closest('.quantity-up, .quantity-down, .quantity-down_variation')) {
      setTimeout(recalcTotal, 0);
    }
  });
});

</script>
<script>
(function () {
  var shareBtn = document.querySelector('[data-target=".share-event"]');
  if (!shareBtn || !navigator.share) return;
  shareBtn.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    navigator.share({
      title: {{ json_encode($content->title, JSON_UNESCAPED_UNICODE | JSON_HEX_AMP) }},
      text: {{ json_encode(\Illuminate\Support\Str::limit(strip_tags($content->description ?? ''), 120), JSON_UNESCAPED_UNICODE | JSON_HEX_AMP) }},
      url: window.location.href
    }).catch(function () {});
  });
})();
</script>
<script>
(function() {
  // --- Crossfade slideshow hero de evento ---
  var slides = Array.from(document.querySelectorAll('#heroCollageBg .hero-slide'));
  var n = slides.length;
  if (n === 0) return;

  slides[0].style.opacity = '1';
  slides[0].style.zIndex  = '0';

  if (n === 1) return;

  var cur = 0;

  function nextSlide() {
    var nxt  = (cur + 1) % n;
    var prev = cur;
    slides[prev].style.zIndex = '0';
    slides[nxt].style.zIndex  = '1';
    slides[nxt].style.transition = 'opacity 1.2s ease-in-out';
    slides[nxt].style.opacity    = '1';
    setTimeout(function() {
      slides[prev].style.transition = 'none';
      slides[prev].style.opacity    = '0';
    }, 1200);
    cur = nxt;
  }

  setInterval(nextSlide, 5000);

  // --- Parallax — solo corre cuando el hero es visible ---
  var hero = document.getElementById('heroSection');
  var bg   = document.getElementById('heroCollageBg');
  if (!hero || !bg) return;

  var tx = 0, ty = 0, cx = 0, cy = 0;
  var rafId = null;
  var heroRect = hero.getBoundingClientRect();

  window.addEventListener('resize', function() { heroRect = hero.getBoundingClientRect(); }, { passive: true });

  hero.addEventListener('mousemove', function(e) {
    tx = -(e.clientX - heroRect.left) / heroRect.width  * 14 + 7;
    ty = -(e.clientY - heroRect.top)  / heroRect.height * 14 + 7;
  }, { passive: true });

  hero.addEventListener('mouseleave', function() { tx = 0; ty = 0; }, { passive: true });

  function parallaxLoop() {
    cx += (tx - cx) * 0.04;
    cy += (ty - cy) * 0.04;
    bg.style.transform = 'translate(' + cx.toFixed(2) + 'px,' + cy.toFixed(2) + 'px)';
    rafId = requestAnimationFrame(parallaxLoop);
  }

  var observer = new IntersectionObserver(function(entries) {
    if (entries[0].isIntersecting) {
      if (!rafId) rafId = requestAnimationFrame(parallaxLoop);
    } else {
      if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
      cx = 0; cy = 0; tx = 0; ty = 0;
      bg.style.transform = 'translate(0px,0px)';
    }
  }, { threshold: 0 });

  observer.observe(hero);
})();
</script>
@endpush
