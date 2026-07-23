@extends('organizer.layout')

@php
  use Illuminate\Support\Str;

  $organizer = Auth::guard('organizer')->user();
  $organizerInfos = App\Models\OrganizerInfo::where('organizer_id', $organizer->id)
    ->whereIn('language_id', $languages->pluck('id'))
    ->get()
    ->keyBy('language_id');
  $defaultLanguage = $languages->firstWhere('is_default', 1) ?: $languages->first();
  $defaultInfo = $defaultLanguage ? $organizerInfos->get($defaultLanguage->id) : null;
  $profileName = trim((string) ($defaultInfo->name ?? $organizer->username ?? __('Organizador')));
  $profileRole = trim((string) ($defaultInfo->designation ?? __('Productor de eventos')));
  $profileBio = trim(strip_tags((string) ($defaultInfo->details ?? '')));
  $profileBio = $profileBio !== '' ? $profileBio : __('Contá qué tipo de eventos producís, qué experiencia ofrecés y por qué la gente debería seguir tu agenda.');
  $profileBioLength = mb_strlen($profileBio);
  $profileLocation = trim(implode(', ', array_filter([
    $defaultInfo->city ?? null,
    $defaultInfo->country ?? null,
  ])));
  $profileSlug = Str::slug($profileName);
  $profileUrl = route('frontend.organizer.details', [
    $organizer->id,
    $profileSlug !== '' ? $profileSlug : Str::slug($organizer->username ?: 'organizador'),
  ], true);
  $photoUrl = !empty($organizer->photo)
    ? asset('assets/admin/img/organizer-photo/' . $organizer->photo)
    : asset('assets/admin/img/noimage.jpg');
  $coverUrl = !empty($organizer->cover_photo)
    ? asset('assets/admin/img/organizer-cover-photo/' . $organizer->cover_photo)
    : asset('assets/admin/img/noimage.jpg');
  $socialFields = [
    ['name' => 'website', 'label' => __('Sitio web'), 'icon' => 'fas fa-globe', 'placeholder' => 'https://tusitio.com', 'value' => $organizer->website],
    ['name' => 'instagram', 'label' => 'Instagram', 'icon' => 'fab fa-instagram', 'placeholder' => 'https://www.instagram.com/tuusuario', 'value' => $organizer->instagram],
    ['name' => 'tiktok', 'label' => 'TikTok', 'icon' => 'fab fa-tiktok', 'placeholder' => 'https://www.tiktok.com/@tuusuario', 'value' => $organizer->tiktok],
    ['name' => 'facebook', 'label' => 'Facebook', 'icon' => 'fab fa-facebook-f', 'placeholder' => 'https://www.facebook.com/tuorganizacion', 'value' => $organizer->facebook],
    ['name' => 'twitter', 'label' => 'X / Twitter', 'icon' => 'fab fa-twitter', 'placeholder' => 'https://x.com/tuusuario', 'value' => $organizer->twitter],
    ['name' => 'linkedin', 'label' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in', 'placeholder' => 'https://www.linkedin.com/company/tuorganizacion', 'value' => $organizer->linkedin],
  ];
  $socialCount = collect($socialFields)->filter(fn ($field) => trim((string) $field['value']) !== '')->count();
  $metaPixelValid = preg_match('/^\d{6,32}$/', trim((string) $organizer->meta_pixel_id));
  $profileEventStats = $profileEventStats ?? ['total' => 0, 'upcoming' => 0, 'past' => 0];
  $hasVisualIdentity = !empty($organizer->photo) && !empty($organizer->cover_photo);
  $hasCustomName = $profileName !== '' && $profileName !== $organizer->username;
  $hasStrongDescription = $profileBioLength >= 80;
  $hasLocation = $profileLocation !== '';
  $hasOfficialLinks = $socialCount > 0;
  $hasActiveAgenda = ($profileEventStats['upcoming'] ?? 0) > 0;
  $completionChecks = [
    ['label' => __('Foto de perfil'), 'complete' => !empty($organizer->photo), 'hint' => __('Usá una imagen cuadrada y reconocible.'), 'href' => '#opb-media-title'],
    ['label' => __('Portada'), 'complete' => !empty($organizer->cover_photo), 'hint' => __('Mostrá ambiente, público o escenario.'), 'href' => '#opb-media-title'],
    ['label' => __('Nombre público'), 'complete' => $hasCustomName, 'hint' => __('Que coincida con tu marca en redes.'), 'href' => '#opb-content-title'],
    ['label' => __('Descripción clara'), 'complete' => $hasStrongDescription, 'hint' => __('Mínimo 80 caracteres con qué hacés y dónde.'), 'href' => '#opb-content-title'],
    ['label' => __('Ubicación'), 'complete' => $hasLocation, 'hint' => __('Ayuda a búsquedas por ciudad o país.'), 'href' => '#opb-content-title'],
    ['label' => __('Redes o sitio web'), 'complete' => $hasOfficialLinks, 'hint' => __('Refuerzan identidad para Google e IA.'), 'href' => '#opb-social-title'],
    ['label' => __('Meta Pixel válido'), 'complete' => $metaPixelValid, 'hint' => __('Permite medir visitas y contactos.'), 'href' => '#opb-measurement-title'],
  ];
  $completionDone = collect($completionChecks)->where('complete', true)->count();
  $completionPercent = (int) round(($completionDone / max(count($completionChecks), 1)) * 100);
  $nextProfileActions = collect($completionChecks)->filter(fn ($check) => !$check['complete'])->take(3)->values();
  $profileQualityLabel = $completionPercent >= 86
    ? __('Listo para compartir')
    : ($completionPercent >= 58 ? __('Buen avance') : __('Necesita base pública'));
  $profileQualityCopy = $completionPercent >= 86
    ? __('Tu perfil ya tiene las señales principales para verse confiable y medible.')
    : ($completionPercent >= 58 ? __('Con dos o tres ajustes más, tu perfil queda mucho más sólido para difusión.') : __('Completá primero identidad, descripción y enlaces oficiales.'));
  $readinessChecks = [
    [
      'label' => __('Google e IA'),
      'complete' => $hasCustomName && $hasStrongDescription && $hasOfficialLinks,
      'copy' => __('Nombre, descripción y enlaces oficiales para ProfilePage, Organization y sameAs.'),
    ],
    [
      'label' => __('Confianza visual'),
      'complete' => $hasVisualIdentity,
      'copy' => __('Foto y portada hacen que el perfil se vea propio cuando se comparte.'),
    ],
    [
      'label' => __('Agenda pública'),
      'complete' => $hasActiveAgenda,
      'copy' => __('Al menos un evento activo mantiene el perfil vivo y útil para reservar.'),
    ],
    [
      'label' => __('Meta Pixel'),
      'complete' => $metaPixelValid,
      'copy' => __('Mide PageView, ViewContent y Contact sin enviar datos personales.'),
    ],
  ];
@endphp

@section('style')
  <style>
    .org-profile-builder {
      --opb-background: #e8ebed;
      --opb-foreground: #333333;
      --opb-card: #ffffff;
      --opb-primary: #e05d38;
      --opb-primary-strong: #bf4424;
      --opb-secondary: #f3f4f6;
      --opb-muted: #f9fafb;
      --opb-muted-foreground: #6b7280;
      --opb-accent: #d6e4f0;
      --opb-accent-foreground: #1e3a8a;
      --opb-border: #dcdfe2;
      --opb-input: #f4f5f7;
      --opb-ring: rgba(224, 93, 56, .18);
      --opb-radius: 12px;
      --opb-shadow: 0 18px 38px rgba(30, 37, 50, .08);
      color: var(--opb-foreground);
    }

    .org-profile-builder .opb-hero {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 20px;
      align-items: center;
      margin-bottom: 22px;
      padding: 22px;
      border: 1px solid var(--opb-border);
      border-radius: var(--opb-radius);
      background: var(--opb-card);
      box-shadow: var(--opb-shadow);
    }

    .org-profile-builder .opb-kicker {
      margin: 0 0 8px;
      color: var(--opb-primary);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .12em;
      line-height: 1;
      text-transform: uppercase;
    }

    .org-profile-builder .opb-title {
      margin: 0;
      color: #1e2532;
      font-size: clamp(28px, 3vw, 42px);
      font-weight: 800;
      line-height: 1.05;
      letter-spacing: 0;
    }

    .org-profile-builder .opb-copy {
      max-width: 760px;
      margin: 10px 0 0;
      color: var(--opb-muted-foreground);
      font-size: 14px;
      line-height: 1.65;
    }

    .org-profile-builder .opb-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: flex-end;
    }

    .org-profile-builder .opb-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 42px;
      padding: 10px 16px;
      border: 1px solid var(--opb-border);
      border-radius: 10px;
      background: var(--opb-card);
      color: #1e2532;
      font-size: 13px;
      font-weight: 800;
      text-decoration: none;
      transition: transform .16s ease, border-color .16s ease, background .16s ease, color .16s ease, box-shadow .16s ease;
    }

    .org-profile-builder .opb-btn:hover,
    .org-profile-builder .opb-btn:focus {
      border-color: var(--opb-primary);
      color: var(--opb-primary-strong);
      text-decoration: none;
      transform: translateY(-1px);
      box-shadow: 0 0 0 4px var(--opb-ring);
    }

    .org-profile-builder .opb-btn--primary {
      border-color: var(--opb-primary);
      background: var(--opb-primary);
      color: #fff;
    }

    .org-profile-builder .opb-btn--primary:hover,
    .org-profile-builder .opb-btn--primary:focus {
      background: var(--opb-primary-strong);
      color: #fff;
    }

    .org-profile-builder .opb-layout {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(300px, 380px);
      gap: 24px;
      align-items: start;
    }

    .org-profile-builder .opb-panel {
      margin-bottom: 18px;
      border: 1px solid var(--opb-border);
      border-radius: var(--opb-radius);
      background: var(--opb-card);
      box-shadow: 0 12px 26px rgba(30, 37, 50, .05);
      overflow: hidden;
    }

    .org-profile-builder .opb-panel__head {
      display: flex;
      gap: 14px;
      align-items: flex-start;
      padding: 18px 20px;
      border-bottom: 1px solid var(--opb-border);
      background: linear-gradient(180deg, #fff 0%, var(--opb-muted) 100%);
    }

    .org-profile-builder .opb-panel__icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 38px;
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: var(--opb-primary);
      color: #fff;
    }

    .org-profile-builder .opb-panel__head h2 {
      margin: 0;
      color: #1e2532;
      font-size: 18px;
      font-weight: 800;
      line-height: 1.2;
      letter-spacing: 0;
    }

    .org-profile-builder .opb-panel__head p {
      margin: 5px 0 0;
      color: var(--opb-muted-foreground);
      font-size: 13px;
      line-height: 1.5;
    }

    .org-profile-builder .opb-panel__body {
      padding: 20px;
    }

    .org-profile-builder .form-group label {
      color: #1e2532;
      font-size: 12px;
      font-weight: 800;
      letter-spacing: .02em;
    }

    .org-profile-builder .form-control {
      min-height: 46px;
      border-color: var(--opb-border);
      border-radius: 10px;
      background: var(--opb-input);
      color: #1e2532;
      font-size: 14px;
      box-shadow: none;
    }

    .org-profile-builder textarea.form-control {
      min-height: 118px;
      resize: vertical;
    }

    .org-profile-builder .form-control:focus {
      border-color: var(--opb-primary);
      background: #fff;
      box-shadow: 0 0 0 4px var(--opb-ring);
    }

    .org-profile-builder .opb-upload-grid,
    .org-profile-builder .opb-social-grid,
    .org-profile-builder .opb-metrics {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
    }

    .org-profile-builder .opb-upload {
      display: grid;
      gap: 12px;
      align-content: start;
      min-width: 0;
      padding: 14px;
      border: 1px solid var(--opb-border);
      border-radius: var(--opb-radius);
      background: var(--opb-muted);
    }

    .org-profile-builder .opb-upload__preview {
      position: relative;
      overflow: hidden;
      border-radius: 10px;
      background: #fff;
      aspect-ratio: 16 / 7;
    }

    .org-profile-builder .opb-upload__preview--avatar {
      width: 128px;
      max-width: 100%;
      aspect-ratio: 1;
    }

    .org-profile-builder .opb-upload__preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .org-profile-builder .opb-file {
      position: relative;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 40px;
      padding: 9px 12px;
      border: 1px solid var(--opb-border);
      border-radius: 10px;
      background: #fff;
      color: #1e2532;
      font-size: 13px;
      font-weight: 800;
      cursor: pointer;
    }

    .org-profile-builder .opb-file input {
      position: absolute;
      inset: 0;
      opacity: 0;
      cursor: pointer;
    }

    .org-profile-builder .opb-help {
      margin: 0;
      color: var(--opb-muted-foreground);
      font-size: 12px;
      line-height: 1.5;
    }

    .org-profile-builder .opb-input-icon {
      display: flex;
      gap: 10px;
      align-items: center;
      padding: 0 12px;
      border: 1px solid var(--opb-border);
      border-radius: 10px;
      background: var(--opb-input);
    }

    .org-profile-builder .opb-input-icon i {
      flex: 0 0 auto;
      color: var(--opb-primary);
    }

    .org-profile-builder .opb-input-icon .form-control {
      padding-left: 0;
      border: 0;
      background: transparent;
    }

    .org-profile-builder .opb-language-card {
      margin-bottom: 12px;
      border: 1px solid var(--opb-border);
      border-radius: var(--opb-radius);
      background: #fff;
      overflow: hidden;
    }

    .org-profile-builder .opb-language-toggle {
      display: flex;
      width: 100%;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 15px 16px;
      border: 0;
      background: var(--opb-muted);
      color: #1e2532;
      font-size: 14px;
      font-weight: 800;
      text-align: left;
    }

    .org-profile-builder .opb-language-toggle span {
      color: var(--opb-muted-foreground);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .08em;
      text-transform: uppercase;
    }

    .org-profile-builder .opb-language-body {
      padding: 18px;
    }

    .org-profile-builder .opb-checklist {
      position: sticky;
      top: 86px;
      display: grid;
      gap: 16px;
    }

    .org-profile-builder .opb-score,
    .org-profile-builder .opb-preview,
    .org-profile-builder .opb-ai-card {
      border: 1px solid var(--opb-border);
      border-radius: var(--opb-radius);
      background: var(--opb-card);
      box-shadow: var(--opb-shadow);
      overflow: hidden;
    }

    .org-profile-builder .opb-score {
      padding: 18px;
    }

    .org-profile-builder .opb-score__value {
      display: flex;
      align-items: baseline;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 12px;
    }

    .org-profile-builder .opb-score__value strong {
      color: #1e2532;
      font-size: 34px;
      font-weight: 800;
      line-height: 1;
    }

    .org-profile-builder .opb-score__state {
      display: inline-flex;
      align-items: center;
      min-height: 28px;
      margin: 0 0 10px;
      padding: 6px 10px;
      border: 1px solid rgba(224, 93, 56, .18);
      border-radius: 999px;
      background: rgba(224, 93, 56, .09);
      color: var(--opb-primary-strong);
      font-size: 12px;
      font-weight: 800;
      line-height: 1;
    }

    .org-profile-builder .opb-score__copy {
      margin: -2px 0 14px;
      color: var(--opb-muted-foreground);
      font-size: 13px;
      line-height: 1.5;
    }

    .org-profile-builder .opb-progress {
      height: 10px;
      border-radius: 999px;
      background: var(--opb-secondary);
      overflow: hidden;
    }

    .org-profile-builder .opb-progress span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: var(--opb-primary);
    }

    .org-profile-builder .opb-checks {
      display: grid;
      gap: 8px;
      margin: 14px 0 0;
      padding: 0;
      list-style: none;
    }

    .org-profile-builder .opb-checks li {
      display: flex;
      align-items: flex-start;
      gap: 9px;
      color: #1e2532;
      font-size: 13px;
      font-weight: 700;
    }

    .org-profile-builder .opb-checks i {
      margin-top: 2px;
      color: #9ca3af;
    }

    .org-profile-builder .opb-checks li.is-complete i {
      color: #16a34a;
    }

    .org-profile-builder .opb-checks strong,
    .org-profile-builder .opb-checks span {
      display: block;
    }

    .org-profile-builder .opb-checks span {
      margin-top: 2px;
      color: var(--opb-muted-foreground);
      font-size: 11px;
      font-weight: 600;
      line-height: 1.35;
    }

    .org-profile-builder .opb-next-actions {
      margin-top: 16px;
      padding-top: 14px;
      border-top: 1px solid var(--opb-border);
    }

    .org-profile-builder .opb-next-actions > strong {
      display: block;
      margin-bottom: 8px;
      color: #1e2532;
      font-size: 12px;
      font-weight: 800;
      letter-spacing: .06em;
      text-transform: uppercase;
    }

    .org-profile-builder .opb-next-actions a {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding: 9px 0;
      color: #1e2532;
      font-size: 13px;
      font-weight: 800;
      text-decoration: none;
      border-bottom: 1px solid rgba(220, 223, 226, .72);
    }

    .org-profile-builder .opb-next-actions a:last-child {
      border-bottom: 0;
    }

    .org-profile-builder .opb-next-actions a:hover,
    .org-profile-builder .opb-next-actions a:focus {
      color: var(--opb-primary-strong);
      text-decoration: none;
    }

    .org-profile-builder .opb-preview__cover {
      min-height: 118px;
      background-image: linear-gradient(180deg, rgba(30, 37, 50, .08), rgba(30, 37, 50, .42)), var(--opb-cover);
      background-position: center;
      background-size: cover;
    }

    .org-profile-builder .opb-preview__body {
      padding: 0 18px 18px;
    }

    .org-profile-builder .opb-preview__avatar {
      width: 86px;
      height: 86px;
      margin-top: -43px;
      padding: 4px;
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 8px 22px rgba(30, 37, 50, .15);
    }

    .org-profile-builder .opb-preview__avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 9px;
    }

    .org-profile-builder .opb-preview h3 {
      margin: 14px 0 4px;
      color: #1e2532;
      font-size: 21px;
      font-weight: 800;
      line-height: 1.1;
    }

    .org-profile-builder .opb-preview__role,
    .org-profile-builder .opb-preview__bio {
      margin: 0;
      color: var(--opb-muted-foreground);
      font-size: 13px;
      line-height: 1.55;
    }

    .org-profile-builder .opb-preview__role {
      color: #1e2532;
      font-weight: 800;
    }

    .org-profile-builder .opb-metrics {
      grid-template-columns: repeat(3, minmax(0, 1fr));
      margin-top: 14px;
      gap: 1px;
      border: 1px solid var(--opb-border);
      border-radius: 10px;
      background: var(--opb-border);
      overflow: hidden;
    }

    .org-profile-builder .opb-metrics div {
      padding: 10px 8px;
      background: var(--opb-muted);
      text-align: center;
    }

    .org-profile-builder .opb-metrics strong {
      display: block;
      color: var(--opb-primary);
      font-size: 18px;
      font-weight: 800;
      line-height: 1;
    }

    .org-profile-builder .opb-metrics span {
      display: block;
      margin-top: 5px;
      color: var(--opb-muted-foreground);
      font-size: 10px;
      font-weight: 800;
      letter-spacing: .06em;
      text-transform: uppercase;
    }

    .org-profile-builder .opb-ai-card {
      padding: 18px;
      background: var(--opb-accent);
      color: var(--opb-accent-foreground);
    }

    .org-profile-builder .opb-ai-card h3 {
      margin: 0 0 10px;
      color: var(--opb-accent-foreground);
      font-size: 16px;
      font-weight: 800;
    }

    .org-profile-builder .opb-ai-card ul {
      display: grid;
      gap: 8px;
      margin: 0;
      padding-left: 18px;
      font-size: 13px;
      line-height: 1.45;
    }

    .org-profile-builder .opb-readiness-list {
      display: grid;
      gap: 10px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .org-profile-builder .opb-readiness-list li {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr);
      gap: 10px;
      align-items: start;
      padding: 10px;
      border-radius: 10px;
      background: rgba(255, 255, 255, .42);
    }

    .org-profile-builder .opb-readiness-list i {
      margin-top: 2px;
      color: var(--opb-primary);
    }

    .org-profile-builder .opb-readiness-list li.is-complete i {
      color: #16a34a;
    }

    .org-profile-builder .opb-readiness-list strong,
    .org-profile-builder .opb-readiness-list span {
      display: block;
    }

    .org-profile-builder .opb-readiness-list strong {
      color: var(--opb-accent-foreground);
      font-size: 13px;
      font-weight: 800;
      line-height: 1.2;
    }

    .org-profile-builder .opb-readiness-list span {
      margin-top: 3px;
      font-size: 12px;
      line-height: 1.4;
    }

    .org-profile-builder .opb-savebar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      margin-top: 18px;
      padding: 16px 20px;
      border: 1px solid var(--opb-border);
      border-radius: var(--opb-radius);
      background: #fff;
      box-shadow: var(--opb-shadow);
    }

    @media (max-width: 991.98px) {
      .org-profile-builder .opb-hero,
      .org-profile-builder .opb-layout {
        grid-template-columns: 1fr;
      }

      .org-profile-builder .opb-actions {
        justify-content: flex-start;
      }

      .org-profile-builder .opb-checklist {
        position: static;
      }
    }

    @media (max-width: 767.98px) {
      .org-profile-builder .opb-upload-grid,
      .org-profile-builder .opb-social-grid {
        grid-template-columns: 1fr;
      }

      .org-profile-builder .opb-hero,
      .org-profile-builder .opb-panel__body,
      .org-profile-builder .opb-panel__head,
      .org-profile-builder .opb-savebar {
        padding-left: 16px;
        padding-right: 16px;
      }

      .org-profile-builder .opb-savebar {
        align-items: stretch;
        flex-direction: column;
      }
    }
  </style>
@endsection

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Perfil del organizador') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('organizer.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Constructor de perfil') }}</a>
      </li>
    </ul>
  </div>

  <div class="org-profile-builder">
    <section class="opb-hero" aria-labelledby="opb-title">
      <div>
        <p class="opb-kicker">{{ __('Perfil público') }}</p>
        <h1 class="opb-title" id="opb-title">{{ __('Armá un perfil que venda tu agenda') }}</h1>
        <p class="opb-copy">{{ __('Tu perfil es tu página pública dentro de Tukipass: ayuda a que te descubran en Google, en buscadores con IA y en redes, y le da confianza a la gente antes de reservar una entrada.') }}</p>
      </div>
      <div class="opb-actions">
        <a class="opb-btn" href="{{ $profileUrl }}" target="_blank" rel="noopener">
          <i class="fas fa-external-link-alt" aria-hidden="true"></i>
          {{ __('Ver perfil público') }}
        </a>
        <button type="button" class="opb-btn opb-btn--primary js-profile-submit">
          <i class="fas fa-save" aria-hidden="true"></i>
          {{ __('Guardar perfil') }}
        </button>
      </div>
    </section>

    <div class="alert alert-danger pb-1 dis-none" id="eventErrors">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <ul></ul>
    </div>

    <form id="eventForm" action="{{ route('organizer.update_profile') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="opb-layout">
        <div>
          <section class="opb-panel" aria-labelledby="opb-media-title">
            <div class="opb-panel__head">
              <span class="opb-panel__icon"><i class="fas fa-image" aria-hidden="true"></i></span>
              <div>
                <h2 id="opb-media-title">{{ __('Identidad visual') }}</h2>
                <p>{{ __('La foto y la portada son la primera señal de confianza cuando alguien comparte tu perfil o entra desde un evento.') }}</p>
              </div>
            </div>
            <div class="opb-panel__body">
              <div class="opb-upload-grid">
                <div class="opb-upload">
                  <label>{{ __('Foto de perfil') . '*' }}</label>
                  <div class="opb-upload__preview opb-upload__preview--avatar">
                    <img src="{{ $photoUrl }}" alt="{{ __('Vista previa de la foto de perfil') }}" data-profile-preview="photo">
                  </div>
                  <label class="opb-file">
                    <i class="fas fa-upload" aria-hidden="true"></i>
                    {{ __('Elegir foto') }}
                    <input type="file" name="photo" accept="image/png,image/jpeg" data-preview-target="photo">
                  </label>
                  <p class="opb-help">{{ __('JPG o PNG. Tamaño requerido: 300x300 px.') }}</p>
                  @if ($errors->has('photo'))
                    <p class="mt-2 mb-0 text-danger em">{{ $errors->first('photo') }}</p>
                  @endif
                </div>

                <div class="opb-upload">
                  <label>{{ __('Foto de portada') }}</label>
                  <div class="opb-upload__preview">
                    <img src="{{ $coverUrl }}" alt="{{ __('Vista previa de la portada') }}" data-profile-preview="cover">
                  </div>
                  <label class="opb-file">
                    <i class="fas fa-upload" aria-hidden="true"></i>
                    {{ __('Elegir portada') }}
                    <input type="file" name="cover_photo" accept="image/png,image/jpeg,image/webp" data-preview-target="cover">
                  </label>
                  <p class="opb-help">{{ __('Recomendado: 1600x600 px en JPG, PNG o WebP.') }}</p>
                  @if ($errors->has('cover_photo'))
                    <p class="mt-2 mb-0 text-danger em">{{ $errors->first('cover_photo') }}</p>
                  @endif
                </div>
              </div>
            </div>
          </section>

          <section class="opb-panel" aria-labelledby="opb-account-title">
            <div class="opb-panel__head">
              <span class="opb-panel__icon"><i class="fas fa-id-card" aria-hidden="true"></i></span>
              <div>
                <h2 id="opb-account-title">{{ __('Datos de cuenta y contacto') }}</h2>
                <p>{{ __('Estos datos alimentan tu perfil público, formularios de contacto y señales básicas de confianza.') }}</p>
              </div>
            </div>
            <div class="opb-panel__body">
              <div class="row">
                <div class="col-lg-4">
                  <div class="form-group">
                    <label>{{ __('Email') . '*' }}</label>
                    <input type="email" class="form-control" name="email" value="{{ $organizer->email }}">
                    @if ($errors->has('email'))
                      <p class="mt-2 mb-0 text-danger em">{{ $errors->first('email') }}</p>
                    @endif
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="form-group">
                    <label>{{ __('Teléfono') }}</label>
                    <input type="tel" class="form-control" name="phone" value="{{ $organizer->phone }}">
                    @if ($errors->has('phone'))
                      <p class="mt-2 mb-0 text-danger em">{{ $errors->first('phone') }}</p>
                    @endif
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="form-group">
                    <label>{{ __('Usuario') . '*' }}</label>
                    <input type="text" class="form-control" name="username" value="{{ $organizer->username }}">
                    @if ($errors->has('username'))
                      <p class="mt-2 mb-0 text-danger em">{{ $errors->first('username') }}</p>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </section>

          <section class="opb-panel" aria-labelledby="opb-social-title">
            <div class="opb-panel__head">
              <span class="opb-panel__icon"><i class="fas fa-share-alt" aria-hidden="true"></i></span>
              <div>
                <h2 id="opb-social-title">{{ __('Redes, comunidad y sitio oficial') }}</h2>
                <p>{{ __('Sumá sólo enlaces oficiales. También se usan como señales sameAs para buscadores y asistentes de IA.') }}</p>
              </div>
            </div>
            <div class="opb-panel__body">
              <div class="opb-social-grid">
                @foreach($socialFields as $field)
                  <div class="form-group">
                    <label>{{ $field['label'] }}</label>
                    <div class="opb-input-icon">
                      <i class="{{ $field['icon'] }}" aria-hidden="true"></i>
                      <input type="url" class="form-control" name="{{ $field['name'] }}" value="{{ $field['value'] }}" placeholder="{{ $field['placeholder'] }}">
                    </div>
                    @if ($errors->has($field['name']))
                      <p class="mt-2 mb-0 text-danger em">{{ $errors->first($field['name']) }}</p>
                    @endif
                  </div>
                @endforeach
              </div>
            </div>
          </section>

          <section class="opb-panel" aria-labelledby="opb-content-title">
            <div class="opb-panel__head">
              <span class="opb-panel__icon"><i class="fas fa-pen-nib" aria-hidden="true"></i></span>
              <div>
                <h2 id="opb-content-title">{{ __('Contenido del perfil') }}</h2>
                <p>{{ __('Escribí en bloques concretos: qué hacés, qué tipo de eventos producís, dónde operás y qué puede esperar la gente.') }}</p>
              </div>
            </div>
            <div class="opb-panel__body">
              <div id="accordion">
                @foreach ($languages as $language)
                  @php
                    $organizer_info = $organizerInfos->get($language->id);
                    $isDefaultLanguage = $language->is_default == 1;
                  @endphp

                  <div class="opb-language-card version">
                    <div id="heading{{ $language->id }}">
                      <button type="button" class="opb-language-toggle" data-toggle="collapse"
                        data-target="#collapse{{ $language->id }}"
                        aria-expanded="{{ $isDefaultLanguage ? 'true' : 'false' }}"
                        aria-controls="collapse{{ $language->id }}">
                        {{ $language->name }}
                        <span>{{ $isDefaultLanguage ? __('Idioma principal') : __('Idioma adicional') }}</span>
                      </button>
                    </div>

                    <div id="collapse{{ $language->id }}"
                      class="collapse {{ $isDefaultLanguage ? 'show' : '' }}"
                      aria-labelledby="heading{{ $language->id }}" data-parent="#accordion">
                      <div class="opb-language-body version-body">
                        <div class="row">
                          <div class="col-lg-4">
                            <div class="form-group">
                              <label>{{ __('Nombre público') . '*' }}</label>
                              <input type="text" class="form-control" name="{{ $language->code }}_name"
                                value="{{ $organizer_info ? $organizer_info->name : '' }}"
                                placeholder="{{ __('Ej: Rumba Colombiana') }}"
                                {{ $isDefaultLanguage ? 'data-profile-live=name' : '' }}>
                              @if ($errors->has("$language->code" . '_name'))
                                <p class="mt-2 mb-0 text-danger em">{{ $errors->first("$language->code" . '_name') }}</p>
                              @endif
                            </div>
                          </div>
                          <div class="col-lg-4">
                            <div class="form-group">
                              <label>{{ __('Especialidad') }}</label>
                              <input type="text" class="form-control" name="{{ $language->code }}_designation"
                                value="{{ $organizer_info ? $organizer_info->designation : '' }}"
                                placeholder="{{ __('Ej: Fiestas, shows y experiencias culturales') }}"
                                {{ $isDefaultLanguage ? 'data-profile-live=role' : '' }}>
                            </div>
                          </div>
                          <div class="col-lg-4">
                            <div class="form-group">
                              <label>{{ __('País') }}</label>
                              <input type="text" class="form-control" name="{{ $language->code }}_country"
                                value="{{ $organizer_info ? $organizer_info->country : '' }}">
                            </div>
                          </div>
                          <div class="col-lg-4">
                            <div class="form-group">
                              <label>{{ __('Ciudad') }}</label>
                              <input type="text" class="form-control" name="{{ $language->code }}_city"
                                value="{{ $organizer_info ? $organizer_info->city : '' }}">
                            </div>
                          </div>
                          <div class="col-lg-4">
                            <div class="form-group">
                              <label>{{ __('Provincia / Estado') }}</label>
                              <input type="text" class="form-control" name="{{ $language->code }}_state"
                                value="{{ $organizer_info ? $organizer_info->state : '' }}">
                            </div>
                          </div>
                          <div class="col-lg-4">
                            <div class="form-group">
                              <label>{{ __('Código postal') }}</label>
                              <input type="text" class="form-control" name="{{ $language->code }}_zip_code"
                                value="{{ $organizer_info ? $organizer_info->zip_code : '' }}">
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="form-group">
                              <label>{{ __('Dirección o zona de referencia') }}</label>
                              <textarea name="{{ $language->code }}_address" class="form-control" rows="3">{{ $organizer_info ? $organizer_info->address : '' }}</textarea>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="form-group">
                              <label>{{ __('Descripción del organizador') }}</label>
                              <textarea name="{{ $language->code }}_details" rows="7" class="form-control" {{ $isDefaultLanguage ? 'data-profile-live=bio' : '' }}>{{ $organizer_info ? $organizer_info->details : '' }}</textarea>
                              <p class="opb-help">
                                {{ __('Recomendado: 2 a 4 frases concretas. Incluí tipo de eventos, ciudad o público, y una razón para seguirte.') }}
                                @if($isDefaultLanguage)
                                  <span data-profile-bio-count>{{ $profileBioLength }}</span>{{ __(' caracteres') }}
                                @endif
                              </p>
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col">
                            @php $currLang = $language; @endphp

                            @foreach ($languages as $language)
                              @continue($language->id == $currLang->id)

                              <div class="form-check py-0">
                                <label class="form-check-label">
                                  <input class="form-check-input" type="checkbox"
                                    onchange="cloneInput('collapse{{ $currLang->id }}', 'collapse{{ $language->id }}', event)">
                                  <span class="form-check-sign">{{ __('Clonar para') }} <strong class="text-capitalize text-secondary">{{ $language->name }}</strong></span>
                                </label>
                              </div>
                            @endforeach
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </section>

          <section class="opb-panel" aria-labelledby="opb-measurement-title">
            <div class="opb-panel__head">
              <span class="opb-panel__icon"><i class="fas fa-chart-line" aria-hidden="true"></i></span>
              <div>
                <h2 id="opb-measurement-title">{{ __('Meta Pixel y medición') }}</h2>
                <p>{{ __('Tu Pixel se aplica en tu perfil público para medir visitas e interés sin enviar datos personales de usuarios.') }}</p>
              </div>
            </div>
            <div class="opb-panel__body">
              <div class="form-group mb-0">
                <label>{{ __('Meta Pixel ID') }}</label>
                <input type="text" class="form-control" name="meta_pixel_id"
                  value="{{ $organizer->meta_pixel_id }}" placeholder="Ej: 1234567890123456" inputmode="numeric" pattern="[0-9]{6,32}">
                <p class="opb-help mt-2">{{ __('Sólo números, entre 6 y 32 dígitos. Se usa para PageView, ViewContent y Contact en tu perfil público.') }}</p>
                @if ($errors->has('meta_pixel_id'))
                  <p class="mt-2 mb-0 text-danger em">{{ $errors->first('meta_pixel_id') }}</p>
                @endif
              </div>
            </div>
          </section>

          <div class="opb-savebar">
            <div>
              <strong>{{ __('Guardá cada mejora importante') }}</strong>
              <p class="opb-help mb-0">{{ __('Después podés abrir tu perfil público y compartirlo con sponsors, artistas o comunidad.') }}</p>
            </div>
            <button type="submit" id="EventSubmit" class="opb-btn opb-btn--primary">
              <i class="fas fa-save" aria-hidden="true"></i>
              {{ __('Guardar perfil') }}
            </button>
          </div>
        </div>

        <aside class="opb-checklist" aria-label="{{ __('Resumen del perfil') }}">
          <section class="opb-score">
            <p class="opb-kicker">{{ __('Calidad del perfil') }}</p>
            <p class="opb-score__state">{{ $profileQualityLabel }}</p>
            <div class="opb-score__value">
              <strong>{{ $completionPercent }}%</strong>
              <span>{{ $completionDone }}/{{ count($completionChecks) }}</span>
            </div>
            <p class="opb-score__copy">{{ $profileQualityCopy }}</p>
            <div class="opb-progress" aria-hidden="true">
              <span style="width: {{ $completionPercent }}%;"></span>
            </div>
            <ul class="opb-checks">
              @foreach($completionChecks as $check)
                <li class="{{ $check['complete'] ? 'is-complete' : '' }}">
                  <i class="{{ $check['complete'] ? 'fas fa-check-circle' : 'far fa-circle' }}" aria-hidden="true"></i>
                  <span>
                    <strong>{{ $check['label'] }}</strong>
                    <span>{{ $check['hint'] }}</span>
                  </span>
                </li>
              @endforeach
            </ul>

            @if($nextProfileActions->isNotEmpty())
              <div class="opb-next-actions">
                <strong>{{ __('Siguiente mejora') }}</strong>
                @foreach($nextProfileActions as $action)
                  <a href="{{ $action['href'] }}">
                    <span>{{ $action['label'] }}</span>
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                  </a>
                @endforeach
              </div>
            @endif
          </section>

          <section class="opb-preview">
            <div class="opb-preview__cover" style="--opb-cover: url('{{ $coverUrl }}');" data-profile-preview-cover></div>
            <div class="opb-preview__body">
              <div class="opb-preview__avatar">
                <img src="{{ $photoUrl }}" alt="{{ __('Preview del perfil') }}" data-profile-preview="photo-card">
              </div>
              <h3 data-profile-text="name">{{ $profileName }}</h3>
              <p class="opb-preview__role" data-profile-text="role">{{ $profileRole }}</p>
              <p class="opb-preview__bio" data-profile-text="bio">{{ Str::limit($profileBio, 210) }}</p>
              <div class="opb-metrics">
                <div>
                  <strong>{{ $profileEventStats['upcoming'] ?? 0 }}</strong>
                  <span>{{ __('Próximos') }}</span>
                </div>
                <div>
                  <strong>{{ $profileEventStats['past'] ?? 0 }}</strong>
                  <span>{{ __('Realizados') }}</span>
                </div>
                <div>
                  <strong>{{ $socialCount }}</strong>
                  <span>{{ __('Redes') }}</span>
                </div>
              </div>
            </div>
          </section>

          <section class="opb-ai-card">
            <h3>{{ __('Checklist SEO + IA') }}</h3>
            <ul class="opb-readiness-list">
              @foreach($readinessChecks as $item)
                <li class="{{ $item['complete'] ? 'is-complete' : '' }}">
                  <i class="{{ $item['complete'] ? 'fas fa-check-circle' : 'far fa-circle' }}" aria-hidden="true"></i>
                  <span>
                    <strong>{{ $item['label'] }}</strong>
                    <span>{{ $item['copy'] }}</span>
                  </span>
                </li>
              @endforeach
            </ul>
          </section>
        </aside>
      </div>
    </form>
  </div>
@endsection

@section('script')
  <script>
    (function () {
      'use strict';

      document.querySelectorAll('.js-profile-submit').forEach(function (button) {
        button.addEventListener('click', function () {
          var submit = document.getElementById('EventSubmit');
          if (submit) submit.click();
        });
      });

      document.querySelectorAll('[data-preview-target]').forEach(function (input) {
        input.addEventListener('change', function (event) {
          var file = event.target.files && event.target.files[0];
          if (!file) return;

          var reader = new FileReader();
          reader.onload = function (readerEvent) {
            var dataUrl = readerEvent.target.result;
            var target = input.getAttribute('data-preview-target');
            document.querySelectorAll('[data-profile-preview="' + target + '"], [data-profile-preview="' + target + '-card"]').forEach(function (image) {
              image.setAttribute('src', dataUrl);
            });

            if (target === 'cover') {
              var cover = document.querySelector('[data-profile-preview-cover]');
              if (cover) cover.style.setProperty('--opb-cover', 'url("' + dataUrl + '")');
            }
          };
          reader.readAsDataURL(file);
        });
      });

      document.querySelectorAll('[data-profile-live]').forEach(function (input) {
        input.addEventListener('input', function () {
          var key = input.getAttribute('data-profile-live');
          var target = document.querySelector('[data-profile-text="' + key + '"]');

          var fallback = {
            name: @json($profileName),
            role: @json($profileRole),
            bio: @json(Str::limit($profileBio, 210))
          };
          var value = input.value.trim();
          if (target) {
            target.textContent = value !== '' ? value : fallback[key];
          }

          if (key === 'bio') {
            document.querySelectorAll('[data-profile-bio-count]').forEach(function (counter) {
              counter.textContent = value.length;
            });
          }
        });
      });
    })();
  </script>
@endsection
