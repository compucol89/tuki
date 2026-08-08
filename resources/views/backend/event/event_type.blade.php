@extends('backend.layout')

@section('content')
  <div class="admin-event-type">
    <div class="page-header">
      <h4 class="page-title">{{ __('Choose Event Type') }}</h4>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('admin.dashboard') }}">
            <i class="flaticon-home"></i>
          </a>
        </li>
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a href="#">{{ __('Events Management') }}</a>
        </li>
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a href="#">{{ __('Choose Event Type') }}</a>
        </li>
      </ul>
    </div>

    <div class="admin-event-type__grid">
      <a href="{{ route('add.event.event', ['type' => 'online']) }}" class="admin-event-type__card">
        <span class="admin-event-type__icon admin-event-type__icon--online" aria-hidden="true">
          <i class="fas fa-cloud-upload-alt"></i>
        </span>
        <span class="admin-event-type__label">{{ __('Online Event') }}</span>
        <span class="admin-event-type__hint">{{ __('Evento virtual con acceso online') }}</span>
      </a>

      <a href="{{ route('add.event.event', ['type' => 'venue']) }}" class="admin-event-type__card">
        <span class="admin-event-type__icon admin-event-type__icon--venue" aria-hidden="true">
          <i class="fas fa-map-marker-alt"></i>
        </span>
        <span class="admin-event-type__label">{{ __('Venue Event') }}</span>
        <span class="admin-event-type__hint">{{ __('Evento presencial en un lugar físico') }}</span>
      </a>
    </div>
  </div>
@endsection

@section('style')
  <style>
    .admin-event-type {
      --et-ink: #1e2532;
      --et-ink-strong: #111827;
      --et-muted: #667085;
      --et-border: #e4e7ec;
      --et-card: #ffffff;
      --et-soft: #f8fafc;
      --et-online: #0f766e;
      --et-online-soft: #ecfdf5;
      --et-venue: #c2410c;
      --et-venue-soft: #fff7ed;
      --et-radius: 12px;
      color: var(--et-ink);
      max-width: 880px;
    }

    .admin-event-type .page-title {
      color: var(--et-ink-strong) !important;
      font-size: 24px !important;
      font-weight: 750 !important;
    }

    .admin-event-type .breadcrumbs,
    .admin-event-type .breadcrumbs a {
      color: var(--et-muted) !important;
    }

    .admin-event-type__grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
    }

    .admin-event-type__card {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 10px;
      min-height: 220px;
      padding: 28px 24px;
      border: 1px solid var(--et-border);
      border-radius: var(--et-radius);
      background: var(--et-card);
      box-shadow: var(--adm-shadow-sm, 0 1px 2px rgba(16, 24, 40, .06));
      text-align: center;
      text-decoration: none !important;
      transition: border-color .16s ease, box-shadow .16s ease, transform .16s ease;
    }

    .admin-event-type__card:hover,
    .admin-event-type__card:focus {
      border-color: var(--adm-primary, #f97316);
      box-shadow: var(--adm-shadow, 0 18px 42px rgba(30, 37, 50, .10));
      transform: translateY(-2px);
      outline: 0;
    }

    .admin-event-type__card:focus-visible {
      box-shadow: 0 0 0 3px var(--adm-ring, rgba(249, 115, 22, .28));
    }

    .admin-event-type__icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 64px;
      height: 64px;
      border-radius: 16px;
      font-size: 26px;
    }

    .admin-event-type__icon--online {
      background: var(--et-online-soft);
      color: var(--et-online);
    }

    .admin-event-type__icon--venue {
      background: var(--et-venue-soft);
      color: var(--et-venue);
    }

    .admin-event-type__label {
      color: var(--et-ink-strong);
      font-size: 18px;
      font-weight: 750;
      letter-spacing: .02em;
      text-transform: uppercase;
    }

    .admin-event-type__hint {
      color: var(--et-muted);
      font-size: 13px;
      line-height: 1.45;
      max-width: 260px;
    }

    @media (max-width: 767px) {
      .admin-event-type__grid {
        grid-template-columns: 1fr;
      }

      .admin-event-type__card {
        min-height: 180px;
      }
    }

    html[data-theme="dark"] .admin-event-type {
      --et-ink: #e5e5e5;
      --et-ink-strong: #ffffff;
      --et-muted: #a3a3a3;
      --et-border: #3d4354;
      --et-card: #2a3040;
      --et-soft: #1f2838;
      --et-online: #4ade80;
      --et-online-soft: #14352a;
      --et-venue: #fdba74;
      --et-venue-soft: #3a2c26;
    }
  </style>
@endsection
