@extends('organizer.layout')

@section('style')
  <style>
    .organizer-booking-admin {
      --ob-gap-tight: 8px;
      --ob-gap: 12px;
      --ob-gap-loose: 18px;
      --ob-label-size: 12px;
      --ob-meta-size: 12px;
      --ob-title-size: 17px;
      --ob-value-size: 24px;
      --ob-card-bg: var(--surface-card);
      --ob-card-alt-bg: #fffdfb;
      --ob-card-alt-border: rgba(194, 65, 12, .22);
      --ob-card-focus-border: var(--adm-primary-dark);
      --ob-text-primary: #1e2532;
      --ob-text-secondary: #5f6b7d;
      --ob-text-muted: #5f6b7d;
      --ob-event-muted: #6b7686;
      --ob-chip-bg: #f8fafc;
      --ob-chip-strong-bg: #fff7ed;
      --ob-chip-strong-fg: #7C2D12;
      --ob-kpi-bg: #ffffff;
      --ob-kpi-money-bg: #fff7ed;
      --ob-kpi-money-fg: var(--ob-text-primary);
      --ob-control-hover-bg: rgba(249, 115, 22, .08);
      --ob-button-primary-bg: var(--adm-primary-dark);
      --ob-button-primary-hover-bg: var(--adm-primary-strong);
      max-width: 100%;
      overflow-x: clip;
      overflow-y: visible;
      color: var(--ob-text-primary);
    }

    html[data-theme="dark"] .organizer-booking-admin {
      --ob-card-bg: var(--surface-card);
      --ob-card-alt-bg: #283242;
      --ob-card-alt-border: rgba(253, 186, 116, .38);
      --ob-card-focus-border: var(--adm-primary);
      --ob-text-primary: #f8fafc;
      --ob-text-secondary: #cbd5e1;
      --ob-text-muted: #cbd5e1;
      --ob-event-muted: #cbd5e1;
      --ob-chip-bg: #202a37;
      --ob-chip-strong-bg: rgba(124, 45, 18, .40);
      --ob-chip-strong-fg: #ffedd5;
      --ob-kpi-bg: rgba(15, 23, 42, .22);
      --ob-kpi-money-bg: rgba(124, 45, 18, .32);
      --ob-kpi-money-fg: #ffedd5;
      --ob-control-hover-bg: rgba(253, 186, 116, .10);
      --ob-button-primary-bg: #9A3412;
      --ob-button-primary-hover-bg: #7C2D12;
    }

    .organizer-booking-admin .page-title {
      color: var(--ob-text-primary);
      font-size: 24px;
      font-weight: 760;
      line-height: 1.14;
      letter-spacing: 0;
    }

    .organizer-booking-admin .breadcrumbs a,
    .organizer-booking-admin .breadcrumbs i {
      color: var(--ob-text-secondary);
    }

    .ob-summary {
      display: grid;
      grid-template-columns: repeat(6, minmax(0, 1fr));
      gap: var(--ob-gap);
      margin-bottom: var(--ob-gap-loose);
    }

    .ob-metric {
      display: flex;
      min-height: 78px;
      flex-direction: column;
      justify-content: center;
      gap: 5px;
      padding: 14px 15px;
      border: 1px solid var(--border-default);
      border-radius: 8px;
      background: var(--surface-card);
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .ob-metric__label {
      margin-bottom: 0;
      color: var(--ob-event-muted);
      font-size: 11px;
      font-weight: 700;
      line-height: 1.25;
      letter-spacing: .04em;
      text-transform: uppercase;
    }

    .ob-metric__value {
      color: var(--ob-text-primary);
      font-family: var(--tuki-font-data, 'IBM Plex Mono', ui-monospace, 'SFMono-Regular', Consolas, 'Liberation Mono', monospace);
      font-size: var(--ob-value-size);
      font-weight: 720;
      line-height: 1.05;
      letter-spacing: 0;
      font-variant-numeric: tabular-nums lining-nums;
    }

    .ob-metric__hint {
      margin-top: 0;
      color: var(--ob-text-muted);
      font-size: var(--ob-meta-size);
      font-weight: 500;
      line-height: 1.4;
    }

    .ob-metric--primary {
      border-color: rgba(30, 37, 50, .18);
      background: linear-gradient(180deg, var(--surface-card) 0%, var(--surface-card-soft) 100%);
    }

    .ob-metric--money {
      border-color: rgba(154, 52, 18, .24);
      background: linear-gradient(180deg, var(--surface-card) 0%, #fff7ed 100%);
    }

    .ob-metric--primary .ob-metric__value,
    .ob-metric--money .ob-metric__value {
      font-size: 25px;
      font-weight: 760;
    }

    html[data-theme="dark"] .organizer-booking-admin .ob-metric--primary,
    html[data-theme="dark"] .organizer-booking-admin .ob-metric--money {
      background: linear-gradient(180deg, var(--surface-card) 0%, rgba(253, 186, 116, .08) 100%);
    }

    .ob-panel {
      overflow: visible;
      margin-bottom: 18px;
      border: 1px solid var(--border-default);
      border-radius: var(--adm-radius-2xl);
      background: var(--surface-card);
      box-shadow: 0 12px 30px rgba(30, 37, 50, .07);
    }

    .ob-panel__header {
      display: flex;
      justify-content: space-between;
      gap: 14px;
      padding: 16px 18px;
      border-bottom: 1px solid var(--border-subtle);
    }

    .ob-panel__title {
      margin: 0;
      color: var(--ob-text-primary);
      font-size: 17px;
      font-weight: 700;
      line-height: 1.2;
    }

    .ob-panel__body {
      padding: 0;
    }

    .ob-panel__footer {
      display: flex;
      justify-content: center;
      padding: 14px 18px;
      border-top: 1px solid var(--border-subtle);
      background: var(--surface-card-soft);
    }

    .ob-panel--flat {
      display: grid;
      gap: var(--ob-gap);
      margin-bottom: 18px;
      border: 0;
      border-radius: 0;
      background: transparent;
      box-shadow: none;
    }

    .ob-panel--flat > .ob-panel__header {
      align-items: flex-end;
      padding: 0 2px 2px;
      border-bottom: 0;
      background: transparent;
    }

    .ob-panel--flat > .ob-panel__body {
      padding: 0;
    }

    .ob-panel--flat > .ob-panel__footer {
      padding: 4px 0 0;
      border-top: 0;
      background: transparent;
    }

    .ob-panel--flat .ob-toolbar {
      border: 1px solid var(--border-default);
      border-radius: var(--adm-radius-2xl);
      background: var(--surface-card);
      box-shadow: 0 12px 30px rgba(30, 37, 50, .07);
    }

    .ob-panel--flat .ob-mobile-list {
      padding: 0;
    }

    .ob-toolbar {
      display: grid;
      grid-template-columns: minmax(150px, .75fr) minmax(150px, .75fr) minmax(150px, .6fr) auto;
      align-items: flex-end;
      gap: var(--ob-gap);
      padding: 16px 18px;
      border-bottom: 1px solid var(--border-subtle);
      background: var(--surface-card);
    }

    .ob-toolbar .form-group {
      min-width: 0;
      margin-bottom: 0;
    }

    .ob-toolbar__actions {
      display: flex;
      flex-wrap: wrap;
      gap: var(--ob-gap-tight);
      margin-left: auto;
    }

    .ob-toolbar__actions .btn {
      min-height: 40px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border-radius: 9px;
      font-weight: 600;
    }

    .ob-context-note {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 8px 10px;
      padding: 14px 16px;
      border-color: var(--border-default) !important;
      border-radius: var(--adm-radius-lg) !important;
      background: linear-gradient(180deg, var(--surface-card) 0%, var(--surface-card-soft) 100%) !important;
      color: var(--ob-text-secondary) !important;
      font-size: 14px;
      font-weight: 500;
      line-height: 1.55;
      box-shadow: 0 8px 18px rgba(30, 37, 50, .04);
    }

    .ob-type-summary {
      max-width: 100%;
      overflow: visible;
      margin-bottom: 18px;
      border: 1px solid var(--border-default);
      border-radius: 8px;
      background: var(--surface-card);
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .ob-type-summary__head {
      display: flex;
      justify-content: space-between;
      gap: 14px;
      padding: 16px 18px;
      border-bottom: 1px solid var(--border-default);
    }

    .ob-type-summary__title {
      margin: 0;
      color: var(--ob-text-primary);
      font-size: 17px;
      font-weight: 700;
      line-height: 1.2;
      letter-spacing: 0;
    }

    .ob-type-summary__formula {
      align-self: flex-start;
      padding: 6px 9px;
      border: 1px solid var(--border-subtle);
      border-radius: 999px;
      background: var(--ob-chip-bg);
      color: var(--ob-text-secondary);
      font-size: 12px;
      font-weight: 650;
      line-height: 1.2;
      white-space: nowrap;
    }

    .ob-event-list {
      display: grid;
      gap: 12px;
      padding: 16px;
    }

    .ob-event-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      align-items: stretch;
      gap: 14px 16px;
      min-height: 72px;
      padding: 14px;
      border: 1px solid var(--border-default);
      border-radius: 16px;
      background: var(--ob-card-bg);
      color: inherit;
      text-decoration: none;
      scroll-margin-top: 80px;
      scroll-margin-bottom: 72px;
      box-shadow: 0 12px 30px rgba(30, 37, 50, .07);
      transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
    }

    .ob-event-row:nth-child(even) {
      border-color: var(--ob-card-alt-border);
      background: var(--ob-card-alt-bg);
    }

    .ob-event-row:hover {
      box-shadow: 0 12px 30px rgba(30, 37, 50, .07);
      color: inherit;
      text-decoration: none;
    }

    .ob-event-row:focus {
      color: inherit;
      outline: none;
      text-decoration: none;
    }

    .ob-event-row:focus-visible {
      border-color: var(--ob-card-focus-border);
      box-shadow: 0 0 0 3px var(--focus-ring), 0 12px 30px rgba(30, 37, 50, .07);
    }

    .ob-event-row__main {
      min-width: 0;
      display: grid;
      gap: 11px;
    }

    .ob-event-row__head {
      display: grid;
      grid-template-columns: 54px minmax(0, 1fr) auto;
      gap: 10px;
      align-items: start;
      min-width: 0;
    }

    .ob-event-row__thumb {
      width: 54px;
      height: 54px;
      flex: 0 0 54px;
      overflow: hidden;
      border-radius: var(--adm-radius-lg);
      background: var(--surface-hover);
    }

    .ob-event-row__thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .ob-event-row__text {
      display: grid;
      min-width: 0;
      gap: 3px;
    }

    .ob-event-row__title {
      display: -webkit-box;
      margin: 0;
      overflow: hidden;
      color: var(--ob-text-primary);
      font-size: 14px;
      font-weight: 700;
      line-height: 1.25;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
      overflow-wrap: anywhere;
      text-decoration: none;
    }

    .ob-event-row__date {
      display: block;
      color: var(--ob-event-muted);
      font-size: 12px;
      font-weight: 400;
      line-height: 1.35;
    }

    .ob-event-row__date-label {
      font-weight: 400;
    }

    .ob-event-row__category {
      display: block;
      color: var(--ob-event-muted);
      font-size: 12px;
      font-weight: 400;
      line-height: 1.35;
    }

    .ob-event-row__badges {
      display: grid;
      gap: 6px;
      justify-items: end;
      align-self: start;
      min-width: 0;
    }

    .ob-event-row__badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
      min-height: 24px;
      max-width: 104px;
      padding: 3px 8px;
      border: 1px solid var(--border-subtle);
      border-radius: 999px;
      background: var(--ob-chip-bg);
      color: var(--ob-text-secondary);
      font-size: 12px;
      font-weight: 700;
      line-height: 18px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .ob-event-row__badge--status {
      border-color: rgba(154, 52, 18, .22);
      background: var(--ob-chip-strong-bg);
      color: var(--ob-chip-strong-fg);
      font-weight: 700;
    }

    .ob-event-row__badge--type {
      border-color: rgba(154, 52, 18, .18);
      background: var(--ob-chip-strong-bg);
      color: var(--ob-chip-strong-fg);
      font-size: 12px;
    }

    .ob-event-row__grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      column-gap: 16px;
      row-gap: 10px;
      padding-top: 12px;
      border-top: 1px solid var(--border-subtle);
    }

    .ob-event-row__stat {
      display: grid;
      min-width: 0;
      gap: 3px;
      color: var(--ob-event-muted);
      line-height: 1.25;
    }

    .ob-event-row__label {
      display: block;
      color: var(--ob-event-muted);
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0;
      text-transform: none;
    }

    .ob-event-row__value {
      display: block;
      margin-top: 3px;
      color: var(--ob-text-primary);
      font-family: var(--tuki-font-data, 'IBM Plex Mono', ui-monospace, 'SFMono-Regular', Consolas, 'Liberation Mono', monospace);
      font-size: 15px;
      font-weight: 700;
      line-height: 1.25;
      letter-spacing: 0;
      text-transform: none;
      font-variant-numeric: tabular-nums lining-nums;
      overflow-wrap: anywhere;
    }

    .ob-event-row__muted {
      display: block;
      color: var(--ob-event-muted);
      font-size: 12px;
      font-weight: 400;
      line-height: 1.35;
    }

    .ob-event-row__muted .tuki-data {
      font-family: var(--tuki-font-data, 'IBM Plex Mono', ui-monospace, 'SFMono-Regular', Consolas, 'Liberation Mono', monospace);
      font-weight: 600;
      font-variant-numeric: tabular-nums lining-nums;
    }

    .ob-event-row__progress {
      height: 5px;
      max-width: 144px;
      overflow: hidden;
      margin-top: 4px;
      border-radius: 999px;
      background: var(--border-default);
    }

    .ob-event-row__progress span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: var(--adm-primary);
    }

    .ob-event-row__settlement {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding-top: 10px;
      border-top: 1px solid var(--border-subtle);
    }

    .ob-event-row__settlement-copy {
      display: grid;
      min-width: 0;
      gap: 3px;
    }

    .ob-event-row__money {
      color: var(--ob-kpi-money-fg);
    }

    .ob-event-row__cta {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      min-height: 40px;
      padding: 0 14px;
      border: 1px solid var(--ob-button-primary-bg);
      border-radius: 9px;
      background-color: var(--ob-button-primary-bg);
      color: var(--text-on-accent) !important;
      font-size: 12px;
      font-weight: 600;
      line-height: 1.2;
      white-space: nowrap;
      pointer-events: none;
      transition: color .18s ease, background-color .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .ob-event-row__cta i {
      font-size: 12px;
      line-height: 1;
    }

    @media (max-width: 767px) {
      .ob-event-row {
        grid-template-columns: 1fr;
        gap: 12px;
        padding: 13px 14px 14px;
      }

      .ob-event-row__main {
        gap: 10px;
      }

      .ob-event-row__head {
        grid-template-columns: 54px minmax(0, 1fr) auto;
      }

      .ob-event-row__badges {
        max-width: 112px;
      }

      .ob-event-row__cta {
        width: 100%;
        min-height: 40px;
      }
    }

    .ob-focused-meta {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 6px;
      padding: 14px 16px 2px;
    }

    .ob-chip {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 4px 8px;
      border: 1px solid var(--border-subtle);
      border-radius: 999px;
      background: var(--surface-card);
      color: var(--ob-text-secondary);
      font-size: 12px;
      font-weight: 600;
      line-height: 1.2;
      white-space: nowrap;
    }

    .ob-event-summary-card__status {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 4px 9px;
      border: 1px solid color-mix(in srgb, var(--adm-primary) 22%, transparent);
      border-radius: 999px;
      background: var(--ob-chip-strong-bg);
      color: var(--ob-chip-strong-fg);
      font-size: 12px;
      font-weight: 700;
      text-transform: none;
    }

    .ob-type-table {
      width: 100%;
      table-layout: fixed;
      margin-bottom: 0;
      font-size: 11px;
    }

    .ob-focused-meta + .ob-type-table {
      margin-bottom: 16px;
    }

    .ob-type-table th {
      border-top: 0;
      color: var(--ob-text-secondary);
      font-size: 11px;
      line-height: 1.25;
      padding: 8px 6px;
      font-weight: 700;
      letter-spacing: .04em;
      text-transform: uppercase;
      white-space: normal;
    }

    .ob-type-table td {
      padding: 9px 6px;
      vertical-align: middle;
      line-height: 1.35;
      overflow-wrap: anywhere;
    }

    .ob-type-table__ticket {
      width: 43%;
    }

    .ob-type-table__counts {
      width: 10%;
    }

    .ob-type-table__scan {
      width: 15%;
    }

    .ob-type-table__money {
      width: 12%;
    }

    .ob-type-name {
      display: block;
      color: var(--ob-text-primary);
      font-weight: 760;
      overflow-wrap: anywhere;
    }

    .ob-type-event {
      display: block;
      max-width: 100%;
      overflow: hidden;
      color: var(--ob-text-secondary);
      font-size: 12px;
      font-weight: 500;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .ob-toolbar .form-group {
      margin-bottom: 0;
    }

    .ob-table {
      margin-bottom: 0;
    }

    .ob-table th {
      border-top: 0;
      color: var(--ob-text-secondary);
      font-size: 11px;
      font-weight: 700;
      line-height: 1.25;
      letter-spacing: .045em;
      padding: 13px 14px;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .ob-table td {
      vertical-align: middle;
      padding: 13px 14px;
      font-size: 13px;
      line-height: 1.45;
    }

    .ob-title {
      display: block;
      max-width: 280px;
      overflow: hidden;
      color: var(--ob-text-primary);
      font-weight: 760;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .ob-muted {
      display: block;
      color: var(--ob-text-secondary);
      font-size: 12px;
      font-weight: 500;
      line-height: 1.45;
    }

    .ob-data-value {
      color: var(--ob-text-secondary);
      font-family: var(--tuki-font-data, 'IBM Plex Mono', ui-monospace, 'SFMono-Regular', Consolas, 'Liberation Mono', monospace);
      font-variant-numeric: tabular-nums lining-nums;
    }

    .ob-money {
      color: var(--ob-text-primary);
      font-family: var(--tuki-font-data, 'IBM Plex Mono', ui-monospace, 'SFMono-Regular', Consolas, 'Liberation Mono', monospace);
      font-weight: 760;
      letter-spacing: 0;
      font-variant-numeric: tabular-nums lining-nums;
      white-space: nowrap;
    }

    .ob-status {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 3px 8px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
      white-space: nowrap;
    }

    .ob-status i {
      margin-right: 5px;
      font-size: 11px;
    }

    .ob-expand-btn,
    .ob-action-btn {
      width: 40px;
      height: 40px;
      min-width: 40px;
      min-height: 40px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      border-radius: var(--adm-radius);
    }

    .ob-actions {
      display: flex;
      flex-wrap: nowrap;
      gap: 6px;
    }

    .ob-detail-row td {
      background: var(--surface-card);
      border-top: 0;
    }

    .ob-detail-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 12px;
      padding: 14px 16px;
      border: 1px solid var(--border-subtle);
      border-radius: 8px;
      background: var(--surface-card);
    }

    .ob-detail-section {
      grid-column: 1 / -1;
      padding-top: 12px;
      border-top: 1px solid var(--border-default);
    }

    .ob-detail-label {
      display: block;
      color: var(--ob-text-secondary);
      font-size: 11px;
      font-weight: 700;
      line-height: 1.25;
      letter-spacing: .04em;
      text-transform: uppercase;
    }

    .ob-detail-value {
      display: block;
      margin-top: 4px;
      color: var(--ob-text-primary);
      font-weight: 760;
    }

    .ob-mini-list {
      display: grid;
      gap: 8px;
      margin-top: 8px;
    }

    .ob-mini-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto auto;
      gap: 10px;
      align-items: center;
      padding: 8px 10px;
      border: 1px solid var(--border-subtle);
      border-radius: var(--adm-radius);
      background: var(--surface-card);
    }

    .ob-mini-title {
      display: block;
      overflow-wrap: anywhere;
      color: var(--ob-text-primary);
      font-weight: 760;
    }

    .ob-pill {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 3px 8px;
      border-radius: 999px;
      background: var(--status-warning-bg);
      color: var(--status-warning-fg);
      font-size: 12px;
      font-weight: 650;
      white-space: nowrap;
    }

    .ob-progress {
      width: 100%;
      height: 6px;
      overflow: hidden;
      margin-top: 5px;
      border-radius: 999px;
      background: var(--surface-hover);
    }

    .ob-progress span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: var(--sidebar-accent);
    }

    .ob-mobile-list {
      display: grid;
      gap: 12px;
      padding: 16px;
    }

    .ob-buyer__name {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 6px;
      min-width: 0;
    }

    .ob-buyer__name strong {
      color: var(--ob-text-primary);
      font-weight: 700;
      overflow-wrap: anywhere;
    }

    .ob-buyer__badge {
      font-size: 10px;
      font-weight: 700;
      padding: 2px 7px;
    }

    .ob-buyer__meta {
      margin-top: 2px;
      color: var(--ob-text-secondary);
      font-size: 12px;
      line-height: 1.4;
      overflow-wrap: anywhere;
    }

    .ob-buyer__meta a {
      color: inherit;
    }

    .ob-buyer__meta a:hover {
      color: var(--adm-primary-dark);
    }

    .ob-mobile-booking {
      display: grid;
      gap: 0;
      padding: 12px 13px;
      border: 1px solid var(--border-default);
      border-radius: var(--adm-radius-2xl);
      background: var(--surface-card);
      scroll-margin-top: 80px;
      scroll-margin-bottom: 72px;
      box-shadow: 0 12px 30px rgba(30, 37, 50, .07);
    }

    .ob-mobile-booking:nth-child(even) {
      border-color: var(--ob-card-alt-border);
      background: var(--ob-card-alt-bg);
    }

    .ob-mobile-booking:focus-within {
      border-color: var(--ob-card-focus-border);
      box-shadow: 0 0 0 3px var(--focus-ring), 0 10px 24px rgba(30, 37, 50, .08);
    }

    .ob-mobile-booking__head {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      align-items: start;
      gap: 8px;
    }

    .ob-mobile-booking__main {
      display: grid;
      gap: 3px;
      min-width: 0;
    }

    .ob-mobile-booking__heading {
      margin: 0;
      color: var(--ob-text-primary);
      font-size: 14px;
      font-weight: 700;
      line-height: 1.22;
      overflow-wrap: anywhere;
    }

    .ob-mobile-booking__title {
      color: inherit;
      text-decoration: none;
    }

    .ob-mobile-booking__title:hover,
    .ob-mobile-booking__title:focus {
      color: var(--adm-primary-dark);
      text-decoration: none;
    }

    .ob-mobile-booking__title:focus-visible {
      outline: 2px solid var(--adm-primary);
      outline-offset: 3px;
      border-radius: 6px;
    }

    .ob-mobile-booking__ref {
      margin-left: 6px;
      color: var(--ob-text-secondary);
      font-size: 11px;
      font-weight: 500;
      vertical-align: baseline;
      white-space: nowrap;
    }

    .ob-mobile-booking__meta {
      display: flex;
      flex-wrap: wrap;
      gap: 4px 8px;
      color: var(--ob-text-secondary);
      font-size: 12px;
      font-weight: 400;
      line-height: 1.35;
    }

    .ob-mobile-booking__meta .tuki-data {
      font-weight: 500;
    }

    .ob-mobile-booking__badges {
      display: grid;
      gap: 6px;
      justify-items: end;
      min-width: 0;
    }

    .ob-mobile-booking__badges .ob-status {
      max-width: 112px;
      justify-content: center;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .ob-mobile-booking__grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      column-gap: 12px;
      row-gap: 8px;
      padding-top: 9px;
      margin-top: 9px;
      border-top: 1px solid var(--border-subtle);
    }

    .ob-mobile-buyerline {
      display: flex;
      flex-wrap: wrap;
      align-items: baseline;
      min-width: 0;
      gap: 3px 7px;
      padding-top: 9px;
      margin-top: 9px;
      border-top: 1px solid var(--border-subtle);
      color: var(--ob-text-secondary);
      font-size: 12px;
      font-weight: 500;
      line-height: 1.35;
    }

    .ob-mobile-buyerline__label {
      color: var(--ob-text-secondary);
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: .04em;
      text-transform: uppercase;
    }

    .ob-mobile-buyerline__name {
      color: var(--ob-text-primary);
      font-size: 13px;
      font-weight: 700;
    }

    .ob-mobile-buyerline__contact {
      min-width: 0;
      overflow-wrap: anywhere;
    }

    .ob-mobile-buyerline a {
      color: inherit;
      text-decoration: none;
    }

    .ob-mobile-buyerline a:hover,
    .ob-mobile-buyerline a:focus {
      color: var(--adm-primary-dark);
      text-decoration: none;
    }

    .ob-mobile-stat {
      display: grid;
      gap: 2px;
      min-width: 0;
    }

    .ob-mobile-stat__line {
      display: flex;
      align-items: baseline;
      justify-content: space-between;
      gap: 8px;
      min-width: 0;
    }

    .ob-mobile-stat__line .ob-detail-value,
    .ob-mobile-stat__line .ob-mobile-money {
      margin-top: 0;
    }

    .ob-mobile-money {
      display: block;
      margin-top: 0;
      font-size: 15px;
      line-height: 1.25;
    }

    .ob-mobile-controls {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 8px;
      margin-top: 10px;
      padding-top: 10px;
      border-top: 1px solid var(--border-subtle);
    }

    .ob-mobile-controls .deleteForm {
      display: grid;
      margin: 0;
    }

    .ob-mobile-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 40px;
      gap: 7px;
      border-radius: var(--adm-radius);
      font-size: 12px;
      font-weight: 700;
      line-height: 1.2;
      transition: color .18s ease, background-color .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .ob-mobile-btn--secondary {
      border-color: var(--ob-control-hover-bg);
      background-color: transparent;
      color: var(--ob-text-primary);
    }

    .ob-mobile-btn--secondary:hover,
    .ob-mobile-btn--secondary:focus {
      border-color: var(--ob-card-focus-border);
      background-color: var(--ob-control-hover-bg);
      color: var(--ob-button-primary-hover-bg);
    }

    .ob-mobile-btn:focus {
      box-shadow: 0 0 0 3px var(--focus-ring);
    }

    .ob-mobile-extra {
      padding-top: 9px;
      margin-top: 9px;
      border-top: 1px solid var(--border-subtle);
    }

    .ob-mobile-extra--tickets {
      display: grid;
      gap: 7px;
    }

    .ob-mobile-extra__head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      min-width: 0;
    }

    .ob-mobile-payment {
      display: inline-flex;
      align-items: center;
      max-width: 56%;
      min-height: 24px;
      padding: 3px 8px;
      overflow: hidden;
      border-radius: 999px;
      background: var(--ob-chip-bg);
      color: var(--ob-text-secondary);
      font-size: 11px;
      font-weight: 700;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .ob-mobile-booking .ob-mini-list {
      gap: 0;
      margin-top: 0;
    }

    .ob-mobile-booking .ob-mini-row {
      grid-template-columns: minmax(0, 1fr) auto;
      align-items: center;
      gap: 8px;
      padding: 6px 0;
      border: 0;
      border-top: 1px solid var(--border-subtle);
      border-radius: 0;
      background: transparent;
    }

    .ob-mobile-booking .ob-mini-row:first-child {
      border-top: 0;
    }

    .ob-mini-row__main {
      display: flex;
      flex-wrap: wrap;
      align-items: baseline;
      min-width: 0;
      gap: 2px 8px;
    }

    .ob-mini-row__amount {
      align-self: center;
      margin-top: 0;
      font-size: 13px;
    }

    .ob-empty {
      padding: 32px 16px;
      text-align: center;
    }

    .ob-empty i {
      color: var(--ob-text-muted);
      font-size: 34px;
    }

    .ob-empty h3 {
      margin-top: 14px;
      color: var(--ob-text-primary);
      font-size: 18px;
      font-weight: 760;
    }

    @media (max-width: 1199px) {
      .ob-detail-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .ob-summary {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }

      .ob-toolbar {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .ob-toolbar__actions {
        width: 100%;
        margin-left: 0;
      }
    }

    @media (max-width: 767px) {
      .ob-detail-grid,
      .ob-mini-row {
        grid-template-columns: 1fr;
      }

      .ob-mobile-booking .ob-mini-row {
        grid-template-columns: minmax(0, 1fr) auto;
      }

      .organizer-booking-admin {
        --ob-gap-loose: 16px;
        --ob-title-size: 16px;
        --ob-value-size: 23px;
      }

      .ob-summary {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .ob-panel__header {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
      }

      .ob-toolbar {
        grid-template-columns: 1fr;
        padding: 14px 16px;
      }

      .ob-panel--flat > .ob-panel__header {
        padding-right: 0;
        padding-left: 0;
      }

      .ob-toolbar__actions {
        width: 100%;
        margin-left: 0;
      }

      .ob-event-list {
        padding: 12px;
      }

      .ob-focused-meta {
        padding: 12px 12px 2px;
      }

      .ob-metric {
        min-height: 72px;
        padding: 12px;
      }

      .ob-metric__value {
        font-size: var(--ob-value-size);
      }

      .ob-type-summary__head {
        flex-direction: column;
      }

      .ob-type-table {
        padding: 10px;
        border-top: 1px solid var(--border-default);
        background: var(--surface-card);
        font-size: 12px;
      }

      .ob-type-table,
      .ob-type-table thead,
      .ob-type-table tbody,
      .ob-type-table tr,
      .ob-type-table th,
      .ob-type-table td {
        display: block;
        width: 100%;
      }

      .ob-type-table thead {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
      }

      .ob-type-table tbody {
        display: grid;
        gap: 10px;
      }

      .ob-type-table tr {
        padding: 10px 12px;
        border: 1px solid var(--border-subtle);
        border-radius: 10px;
        background: var(--surface-card);
        box-shadow: 0 8px 18px rgba(30, 37, 50, .04);
      }

      .ob-type-table td {
        display: grid;
        grid-template-columns: minmax(92px, 38%) minmax(0, 1fr);
        gap: 10px;
        align-items: center;
        min-height: 28px;
        padding: 6px 0;
        border-top: 0;
      }

      .ob-type-table td:first-child {
        display: block;
        min-height: 0;
        margin-bottom: 4px;
        padding: 0 0 9px;
        border-bottom: 1px solid var(--border-subtle);
      }

      .ob-type-table td:first-child::before {
        display: none;
      }

      .ob-type-table td::before {
        content: attr(data-label);
        color: var(--ob-text-secondary);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
      }

      .ob-type-table td:not(:first-child) {
        color: var(--ob-text-primary);
        font-weight: 760;
      }

      .ob-type-name {
        font-size: 13px;
        line-height: 1.3;
      }

      .ob-progress {
        max-width: 180px;
      }
    }

    @media (max-width: 360px) {
      .ob-summary,
      .ob-mobile-booking__grid,
      .ob-mobile-controls {
        grid-template-columns: 1fr;
      }

      .ob-mobile-booking__head {
        grid-template-columns: 1fr;
      }

      .ob-mobile-booking__badges {
        justify-items: start;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $formatBaseMoney = function ($amount) use ($currencySettings) {
        $symbol = optional($currencySettings)->base_currency_symbol;
        $position = optional($currencySettings)->base_currency_symbol_position;
        $amount = number_format((float) $amount, 0, ',', '.');
        return ($position == 'left' ? $symbol : '') . $amount . ($position == 'right' ? $symbol : '');
    };
    $focusedEventId = $focusedEventId ?? null;
    $focusedEvent = $focusedEvent ?? null;
    $focusedEventTitle = $focusedEventId
      ? (optional($eventInfos[$focusedEventId] ?? null)->title ?: __('Evento') . ' #' . $focusedEventId)
      : null;
    $bookingFiltersAction = $focusedEventId
      ? route('organizer.event_booking.by_event', $focusedEventId)
      : route('organizer.event.booking');
    $statusOptions = [
        'completed' => ['label' => __('Completado'), 'class' => 'success', 'icon' => 'fa-check-circle'],
        'pending' => ['label' => __('Pendiente'), 'class' => 'warning text-dark', 'icon' => 'fa-clock'],
        'rejected' => ['label' => __('Rechazado'), 'class' => 'danger', 'icon' => 'fa-times-circle'],
        'free' => ['label' => __('Gratis'), 'class' => 'primary', 'icon' => 'fa-gift'],
    ];
    $ticketSummaryByEventId = collect($ticketSalesByEvent ?? [])->keyBy('event_id');
  @endphp

  <div class="organizer-booking-admin">
    <div class="page-header">
      <h1 class="page-title">
        @if ($focusedEventId)
          {{ __('Compradores') }}: {{ $focusedEventTitle }}
        @else
          {{ __('Reservas de eventos') }}
        @endif
      </h1>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('organizer.dashboard') }}" aria-label="{{ __('Ir al panel') }}">
            <i class="flaticon-home"></i>
          </a>
        </li>
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a href="{{ route('organizer.event.booking') }}">{{ __('Reservas') }}</a>
        </li>
        @if ($focusedEventId)
          <li class="separator">
            <i class="flaticon-right-arrow"></i>
          </li>
          <li class="nav-item">
            <a href="#">{{ Str::limit($focusedEventTitle, 40) }}</a>
          </li>
        @endif
      </ul>
    </div>

    @if (!$focusedEventId)
      <p class="alert alert-light border mb-3 ob-context-note">
        {{ __('Lista de eventos con reservas. Entrá a un evento para ver tipos de entrada, compradores y acciones.') }}
      </p>
    @endif

    <div class="ob-summary" role="group" aria-label="{{ __('Resumen de reservas') }}">
      <div class="ob-metric ob-metric--primary">
        <div class="ob-metric__label">{{ __('Reservas') }}</div>
        <div class="ob-metric__value tuki-data tuki-data-count">{{ number_format($kpis['total'] ?? 0, 0, ',', '.') }}</div>
      </div>
      <div class="ob-metric ob-metric--money">
        <div class="ob-metric__label">{{ __('Total cobrado') }}</div>
        <div class="ob-metric__value tuki-data tuki-data-money">{{ $formatBaseMoney($kpis['charged'] ?? 0) }}</div>
        <div class="ob-metric__hint">{{ __('Lo que pagaron los clientes') }}</div>
      </div>
      <div class="ob-metric ob-metric--money">
        <div class="ob-metric__label">{{ __('Recibís') }}</div>
        <div class="ob-metric__value tuki-data tuki-data-money">{{ $formatBaseMoney($kpis['organizer_net'] ?? 0) }}</div>
        <div class="ob-metric__hint">{{ __('Entradas menos comisión') }}</div>
      </div>
      <div class="ob-metric">
        <div class="ob-metric__label">{{ __('Completadas') }}</div>
        <div class="ob-metric__value tuki-data tuki-data-count">{{ number_format($kpis['completed'] ?? 0, 0, ',', '.') }}</div>
      </div>
      <div class="ob-metric">
        <div class="ob-metric__label">{{ __('Pendientes') }}</div>
        <div class="ob-metric__value tuki-data tuki-data-count">{{ number_format($kpis['pending'] ?? 0, 0, ',', '.') }}</div>
      </div>
      <div class="ob-metric">
        <div class="ob-metric__label">{{ __('Gratis') }}</div>
        <div class="ob-metric__value tuki-data tuki-data-count">{{ number_format($kpis['free'] ?? 0, 0, ',', '.') }}</div>
      </div>
    </div>

    <section class="ob-type-summary" aria-labelledby="organizerTicketTypeSummaryTitle">
      <div class="ob-type-summary__head">
        <div>
          <h2 id="organizerTicketTypeSummaryTitle" class="ob-type-summary__title">
            @if ($focusedEventId)
              {{ __('Resumen del evento') }}
            @else
              {{ __('Eventos') }}
            @endif
          </h2>
          <div class="ob-muted">
            @if ($focusedEventId)
              {{ __('Tipos de entrada de este evento.') }}
            @else
              {{ __('Ordenado por fecha. Tocá un evento para ver todo.') }}
            @endif
          </div>
        </div>
        <div class="ob-type-summary__formula">{{ __('Vendido') }} = {{ __('completado') }} + {{ __('gratis') }}</div>
      </div>
      @if (empty($ticketSalesByEvent ?? []))
          <div class="ob-empty py-3">
            <p class="text-muted mb-0">{{ __('No hay entradas para resumir con estos filtros.') }}</p>
          </div>
        @else
          @unless ($focusedEventId)
            <div class="ob-event-list" role="list">
              @foreach ($ticketSalesByEvent as $eventSummary)
                @php
                  $thumbName = trim((string) ($eventSummary['thumbnail'] ?? ''));
                  $thumbPath = $thumbName !== '' ? public_path('assets/admin/img/event/thumbnail/' . $thumbName) : '';
                  $thumb = ($thumbPath !== '' && is_file($thumbPath))
                    ? asset('assets/admin/img/event/thumbnail/' . $thumbName)
                    : asset('assets/admin/img/noimage.jpg');
                  $fallbackThumb = asset('assets/admin/img/noimage.jpg');
                  $categoryLabel = $eventSummary['category_label'] ?? '-';
                  $eventTypeLabel = $eventSummary['event_type_label'] ?? '-';
                @endphp
                <a role="listitem" class="ob-event-row"
                  href="{{ route('organizer.event_booking.by_event', $eventSummary['event_id']) }}">
                  <div class="ob-event-row__main">
                    <div class="ob-event-row__head">
                      <div class="ob-event-row__thumb">
                        <img src="{{ $thumb }}" alt="" loading="lazy" onerror="this.onerror=null;this.src='{{ $fallbackThumb }}';">
                      </div>
                      <div class="ob-event-row__text">
                        <h3 class="ob-event-row__title">{{ $eventSummary['event_title'] }}</h3>
                        <span class="ob-event-row__date">
                          <span class="ob-event-row__date-label">{{ __('Función') }}:</span>
                          <span class="ob-event-row__date-value">{{ $eventSummary['date_label'] }}</span>
                        </span>
                        <span class="ob-event-row__category">{{ __('Categoría') }}: {{ $categoryLabel ?: '-' }}</span>
                      </div>
                      <div class="ob-event-row__badges">
                        <span class="ob-event-row__badge ob-event-row__badge--status">{{ $eventSummary['date_status'] }}</span>
                        <span class="ob-event-row__badge ob-event-row__badge--type">{{ __($eventTypeLabel) }}</span>
                      </div>
                    </div>
                    <div class="ob-event-row__grid" role="group" aria-label="{{ __('Totales del evento') }}">
                      <div class="ob-event-row__stat">
                        <span class="ob-event-row__label">{{ __('Reservas') }}</span>
                        <strong class="ob-event-row__value tuki-data tuki-data-count">{{ number_format($eventSummary['bookings_count'], 0, ',', '.') }}</strong>
                        <span class="ob-event-row__muted">{{ __('Vendidas') }}: <span class="tuki-data tuki-data-count">{{ number_format($eventSummary['sold'], 0, ',', '.') }}</span></span>
                        <span class="ob-event-row__muted">{{ __('Pendientes') }}: <span class="tuki-data tuki-data-count">{{ number_format($eventSummary['pending'], 0, ',', '.') }}</span></span>
                      </div>
                      <div class="ob-event-row__stat">
                        <span class="ob-event-row__label">{{ __('Escaneo') }}</span>
                        <strong class="ob-event-row__value tuki-data tuki-data-count">{{ number_format($eventSummary['scanned'], 0, ',', '.') }}/{{ number_format($eventSummary['total'], 0, ',', '.') }}</strong>
                        <div class="ob-event-row__progress" aria-hidden="true"><span style="width: {{ $eventSummary['scan_percent'] ?? 0 }}%"></span></div>
                      </div>
                    </div>
                    <div class="ob-event-row__settlement">
                      <span class="ob-event-row__settlement-copy">
                        <span class="ob-event-row__label">{{ __('Ingreso neto') }}</span>
                        <span class="ob-event-row__muted">{{ __('Neto') }}: <span class="ob-event-row__money tuki-data tuki-data-money">{{ $formatBaseMoney($eventSummary['organizer_amount']) }}</span></span>
                      </span>
                    </div>
                  </div>
                  <span class="ob-event-row__cta">
                    {{ __('Abrir evento') }}
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                  </span>
                </a>
              @endforeach
            </div>
          @else
            @foreach ($ticketSalesByEvent as $eventSummary)
              <div class="ob-focused-meta">
                <span class="ob-chip">{{ $eventSummary['date_label'] }}</span>
                <span class="ob-chip">{{ number_format($eventSummary['bookings_count'], 0, ',', '.') }} {{ __('reservas') }}</span>
                <span class="ob-chip">{{ count($eventSummary['tickets']) }} {{ __('tipos de entrada') }}</span>
                <span class="ob-event-summary-card__status">{{ $eventSummary['date_status'] }}</span>
              </div>

              <table class="table ob-type-table">
                <colgroup>
                  <col class="ob-type-table__ticket">
                  <col class="ob-type-table__counts">
                  <col class="ob-type-table__counts">
                  <col class="ob-type-table__counts">
                  <col class="ob-type-table__scan">
                  <col class="ob-type-table__money">
                </colgroup>
                <thead>
                  <tr>
                    <th scope="col">{{ __('Entrada') }}</th>
                    <th scope="col" class="tuki-data">{{ __('Vendidas') }}</th>
                    <th scope="col" class="tuki-data">{{ __('Pendientes') }}</th>
                    <th scope="col" class="tuki-data">{{ __('Rechazadas') }}</th>
                    <th scope="col" class="tuki-data">{{ __('Escaneo') }}</th>
                    <th scope="col" class="tuki-data">{{ __('Ingresos') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($eventSummary['tickets'] as $summaryRow)
                    <tr>
                      <td data-label="{{ __('Entrada') }}">
                        <span class="ob-type-name">{{ $summaryRow['ticket_name'] }}</span>
                      </td>
                      <td data-label="{{ __('Vendidas') }}"><span class="ob-pill tuki-data tuki-data-count">{{ number_format($summaryRow['sold'], 0, ',', '.') }}</span></td>
                      <td data-label="{{ __('Pendientes') }}" class="tuki-data tuki-data-count">{{ number_format($summaryRow['pending'], 0, ',', '.') }}</td>
                      <td data-label="{{ __('Rechazadas') }}" class="tuki-data tuki-data-count">{{ number_format($summaryRow['rejected'], 0, ',', '.') }}</td>
                      <td data-label="{{ __('Escaneo') }}">
                        <strong>{{ number_format($summaryRow['scanned'], 0, ',', '.') }}/{{ number_format($summaryRow['total'], 0, ',', '.') }}</strong>
                        <div class="ob-progress" aria-hidden="true"><span style="width: {{ $summaryRow['scan_percent'] }}%"></span></div>
                      </td>
                      <td data-label="{{ __('Ingresos') }}"><span class="ob-money tuki-data tuki-data-money">{{ $formatBaseMoney($summaryRow['organizer_amount']) }}</span></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @endforeach
          @endunless
        @endif
    </section>

    @if ($focusedEventId)
    <section class="ob-panel ob-panel--flat" aria-labelledby="organizerBuyersTitle">
      <div class="ob-panel__header">
        <div>
          <h2 id="organizerBuyersTitle" class="ob-panel__title">{{ __('Compradores') }}</h2>
          <span class="ob-muted">{{ __('Personas que reservaron este evento') }} · <span class="ob-data-value">{{ number_format($bookings->total(), 0, ',', '.') }}</span> {{ __('resultados') }}</span>
        </div>
        <div>
          <button class="btn btn-danger d-none bulk-delete"
            data-href="{{ route('organizer.event_booking.bulk_delete') }}" type="button">
            <i class="flaticon-interface-5" aria-hidden="true"></i> {{ __('Eliminar') }}
          </button>
        </div>
      </div>

      <form id="organizerBookingFiltersForm" action="{{ $bookingFiltersAction }}" method="GET" class="ob-toolbar">
        <div class="form-group">
          <label for="organizerBookingId">{{ __('Reserva') }}</label>
          <input id="organizerBookingId" name="booking_id" type="text" class="form-control"
            placeholder="{{ __('Buscar por reserva') }}" value="{{ request()->input('booking_id') }}">
        </div>
        <div class="form-group">
          <label for="organizerEventTitle">{{ __('Evento') }}</label>
          <input id="organizerEventTitle" name="event_title" type="text" class="form-control"
            placeholder="{{ __('Buscar por evento') }}" value="{{ request()->input('event_title') }}">
        </div>
        <div class="form-group">
          <label for="organizerPaymentStatus">{{ __('Pago') }}</label>
          <select id="organizerPaymentStatus" class="form-control" name="status"
            onchange="document.getElementById('organizerBookingFiltersForm').submit()">
            <option value="" {{ empty(request()->input('status')) ? 'selected' : '' }}>{{ __('Todos') }}</option>
            <option value="completed" {{ request()->input('status') == 'completed' ? 'selected' : '' }}>{{ __('Completado') }}</option>
            <option value="pending" {{ request()->input('status') == 'pending' ? 'selected' : '' }}>{{ __('Pendiente') }}</option>
            <option value="free" {{ request()->input('status') == 'free' ? 'selected' : '' }}>{{ __('Gratis') }}</option>
            <option value="rejected" {{ request()->input('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rechazado') }}</option>
          </select>
        </div>
        <div class="ob-toolbar__actions">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-search" aria-hidden="true"></i>{{ __('Buscar') }}
          </button>
          <a href="{{ $bookingFiltersAction }}" class="btn btn-light">{{ __('Limpiar') }}</a>
        </div>
      </form>

      <div class="ob-panel__body">
            @if (count($bookings) == 0)
              <div class="ob-empty">
                <i class="fas fa-ticket-alt" aria-hidden="true"></i>
                <h3>{{ __('No se encontraron reservas') }}</h3>
                <p class="text-muted mb-0">{{ __('Probá ajustar los filtros de búsqueda.') }}</p>
              </div>
            @else
              <div class="table-responsive d-none d-lg-block">
                <table class="table ob-table">
                  <thead>
                    <tr>
                      <th scope="col">
                        <input type="checkbox" class="bulk-check" data-val="all" aria-label="{{ __('Seleccionar todas las reservas') }}">
                      </th>
                      <th scope="col">{{ __('Reserva') }}</th>
                      <th scope="col">{{ __('Evento') }}</th>
                      <th scope="col">{{ __('Comprador') }}</th>
                      <th scope="col">{{ __('Importe') }}</th>
                      <th scope="col">{{ __('Pago') }}</th>
                      <th scope="col">{{ __('Escaneo') }}</th>
                      <th scope="col">{{ __('Acciones') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($bookings as $booking)
                      @php
                        $eventInfo = $eventInfos[$booking->event_id] ?? null;
                        $title = $eventInfo ? $eventInfo->title : '-';
                        $slug = $eventInfo ? $eventInfo->slug : '';
                        $customer = $booking->customerInfo;
                        $position = $booking->currencyTextPosition;
                        $symbol = $booking->currencySymbol;
                        $formatMoney = function ($amount) use ($position, $symbol) {
                            $amount = number_format((float) $amount, 0, ',', '.');
                            return ($position == 'left' ? $symbol : '') . $amount . ($position == 'right' ? $symbol : '');
                        };
                        $paidTotal = ($booking->price ?? 0) + ($booking->tax ?? 0);
                        $organizerTotal = ($booking->price ?? 0) - ($booking->commission ?? 0);
                        $ticketBreakdown = $booking->ticketBreakdown();
                        $addonBreakdown = $booking->addonBreakdown();
                        $addonsCount = collect($addonBreakdown)->sum('quantity');
                        $addonsTotal = collect($addonBreakdown)->sum('subtotal');
                        $scannedCount = $booking->scannedTicketsCount();
                        $pendingScanCount = $booking->pendingTicketsCount();
                        $scanPercent = $booking->scanPercent();
                        $eventSummaryInfo = $ticketSummaryByEventId->get($booking->event_id);
                        $eventDateLabel = !empty($booking->event_date) ? \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y H:i') : ($eventSummaryInfo['date_label'] ?? '-');
                        $status = $statusOptions[$booking->paymentStatus] ?? ['label' => ucfirst((string) $booking->paymentStatus), 'class' => 'secondary', 'icon' => 'fa-circle'];
                        $invoiceExtension = pathinfo((string) $booking->invoice, PATHINFO_EXTENSION);
                        $hasInvoiceFile = $invoiceExtension == 'pdf' && $booking->hasInvoiceFile();
                      @endphp
                      <tr>
                        <td>
                          <input type="checkbox" class="bulk-check" data-val="{{ $booking->id }}"
                            aria-label="{{ __('Seleccionar reserva') }} #{{ $booking->booking_id }}">
                        </td>
                        <td>
                          <button class="btn btn-light ob-expand-btn mr-1" type="button"
                            data-target="#organizerBookingDetail{{ $booking->id }}" aria-expanded="false"
                            aria-controls="organizerBookingDetail{{ $booking->id }}"
                            aria-label="{{ __('Ver datos adicionales de la reserva') }} #{{ $booking->booking_id }}">
                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
                          </button>
                          <strong class="tuki-data tuki-data-id">#{{ Str::limit($booking->booking_id, 12, '') }}</strong>
                          <span class="ob-muted">{{ optional($booking->created_at)->format('d/m/Y H:i') }}</span>
                        </td>
                        <td>
                          @if ($eventInfo)
                            <a class="ob-title" href="{{ route('event.details', ['slug' => $slug, 'id' => $eventInfo->event_id]) }}"
                              target="_blank" rel="noopener" title="{{ $title }}">{{ $title }}</a>
                          @else
                            <span class="ob-title">-</span>
                          @endif
                          <span class="ob-muted">{{ __('Función') }}: {{ $eventDateLabel }}</span>
                        </td>
                        <td>
                          @include('organizer.event.booking.partials.buyer-cell', ['booking' => $booking])
                        </td>
                        <td>
                          <div class="ob-money tuki-data tuki-data-money">{{ $formatMoney($paidTotal) }}</div>
                          <span class="ob-muted">{{ __('Recibís') }}: {{ $formatMoney($organizerTotal) }}</span>
                        </td>
                        <td>
                          <span class="badge badge-{{ $status['class'] }} ob-status">
                            <i class="fas {{ $status['icon'] }}" aria-hidden="true"></i>{{ $status['label'] }}
                          </span>
                          <span class="ob-muted">{{ $booking->paymentMethod ?: '-' }}</span>
                        </td>
                        <td>
                          @if ((int) $booking->quantity <= 0)
                            <strong>{{ __('Datos incompletos') }}</strong>
                            <span class="ob-muted">{{ __('Sin entradas registradas') }}</span>
                          @else
                            <strong>{{ $scannedCount }}/{{ $booking->quantity }}</strong>
                            <span class="ob-muted">{{ __('Faltan') }}: {{ $pendingScanCount }}</span>
                          @endif
                          <div class="ob-progress" aria-hidden="true"><span style="width: {{ $scanPercent }}%"></span></div>
                        </td>
                        <td>
                          <div class="ob-actions">
                            <a href="{{ route('organizer.event_booking.details', ['id' => $booking->id]) }}"
                              class="btn btn-outline-primary ob-action-btn" title="{{ __('Ver detalles') }}"
                              aria-label="{{ __('Ver detalles de la reserva') }} #{{ $booking->booking_id }}">
                              <i class="fas fa-eye" aria-hidden="true"></i>
                            </a>
                            @if ($hasInvoiceFile)
                              <a href="{{ route('booking.ticket.download', $booking->id) }}"
                                class="btn btn-outline-secondary ob-action-btn" target="_blank" rel="noopener" title="{{ __('Descargar entrada') }}"
                                aria-label="{{ __('Descargar entrada de la reserva') }} #{{ $booking->booking_id }}">
                                <i class="fas fa-file-pdf" aria-hidden="true"></i>
                              </a>
                            @endif
                            @if (!is_null($booking->attachmentFile))
                              <a href="#" data-toggle="modal" data-target="#attachmentModal-{{ $booking->id }}"
                                class="btn btn-outline-info ob-action-btn" title="{{ __('Ver comprobante') }}"
                                aria-label="{{ __('Ver comprobante de la reserva') }} #{{ $booking->booking_id }}">
                                <i class="fas fa-paperclip" aria-hidden="true"></i>
                              </a>
                            @endif
                            <form class="deleteForm d-inline-block" action="{{ route('organizer.event_booking.delete', ['id' => $booking->id]) }}" method="post">
                              @csrf
                              <button type="submit" class="btn btn-outline-danger ob-action-btn deleteBtn"
                                title="{{ __('Eliminar') }}" aria-label="{{ __('Eliminar reserva') }} #{{ $booking->booking_id }}">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                              </button>
                            </form>
                          </div>
                        </td>
                      </tr>
                      <tr id="organizerBookingDetail{{ $booking->id }}" class="ob-detail-row d-none">
                        <td colspan="8">
                          <div class="ob-detail-grid">
                            <div>
                              <span class="ob-detail-label">{{ __('Método de pago') }}</span>
                              <span class="ob-detail-value">{{ $booking->paymentMethod ?: '-' }}</span>
                            </div>
                            <div>
                              <span class="ob-detail-label">{{ __('Fecha / función') }}</span>
                              <span class="ob-detail-value">{{ $eventDateLabel }}</span>
                            </div>
                            <div>
                              <span class="ob-detail-label">{{ __('Comisión') }}</span>
                              <span class="ob-detail-value">{{ $formatMoney($booking->commission ?? 0) }}</span>
                            </div>
                            <div>
                              <span class="ob-detail-label">{{ __('Add-ons') }}</span>
                              <span class="ob-detail-value">{{ $addonsCount > 0 ? $addonsCount . ' - ' . $formatMoney($addonsTotal) : '-' }}</span>
                            </div>
                            <div class="ob-detail-section">
                              <span class="ob-detail-label">{{ __('Tipos de entrada') }}</span>
                              <div class="ob-mini-list">
                                @foreach ($ticketBreakdown as $ticketItem)
                                  <div class="ob-mini-row">
                                    <div>
                                      <span class="ob-mini-title">{{ $ticketItem['name'] }}</span>
                                      <span class="ob-muted">{{ __('Escaneo') }}: {{ $ticketItem['scanned'] }}/{{ $ticketItem['quantity'] }} · {{ __('Faltan') }} {{ $ticketItem['pending'] }}</span>
                                      <div class="ob-progress" aria-hidden="true"><span style="width: {{ $ticketItem['scan_percent'] }}%"></span></div>
                                    </div>
                                    <span class="ob-pill">{{ $ticketItem['quantity'] }} {{ $ticketItem['quantity'] == 1 ? __('entrada') : __('entradas') }}</span>
                                    <span class="ob-detail-value">{{ $formatMoney($ticketItem['subtotal']) }}</span>
                                  </div>
                                @endforeach
                              </div>
                            </div>
                            @if (count($addonBreakdown) > 0)
                              <div class="ob-detail-section">
                                <span class="ob-detail-label">{{ __('Detalle de add-ons') }}</span>
                                <div class="ob-mini-list">
                                  @foreach ($addonBreakdown as $addonItem)
                                    <div class="ob-mini-row">
                                      <div>
                                        <span class="ob-mini-title">{{ $addonItem['title'] }}</span>
                                        <span class="ob-muted">{{ $addonItem['redeemed'] ? __('Canjeado') : __('Pendiente de canje') }}</span>
                                      </div>
                                      <span class="ob-pill">{{ $addonItem['quantity'] }} x {{ $formatMoney($addonItem['unit_price']) }}</span>
                                      <span class="ob-detail-value">{{ $formatMoney($addonItem['subtotal']) }}</span>
                                    </div>
                                  @endforeach
                                </div>
                              </div>
                            @endif
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <div class="ob-mobile-list d-lg-none">
                @foreach ($bookings as $booking)
                  @php
                    $eventInfo = $eventInfos[$booking->event_id] ?? null;
                    $title = $eventInfo ? $eventInfo->title : '-';
                    $slug = $eventInfo ? $eventInfo->slug : '';
                    $customer = $booking->customerInfo;
                    $position = $booking->currencyTextPosition;
                    $symbol = $booking->currencySymbol;
                    $formatMoney = function ($amount) use ($position, $symbol) {
                        $amount = number_format((float) $amount, 0, ',', '.');
                        return ($position == 'left' ? $symbol : '') . $amount . ($position == 'right' ? $symbol : '');
                    };
                    $paidTotal = ($booking->price ?? 0) + ($booking->tax ?? 0);
                    $organizerTotal = ($booking->price ?? 0) - ($booking->commission ?? 0);
                    $ticketBreakdown = $booking->ticketBreakdown();
                    $addonBreakdown = $booking->addonBreakdown();
                    $addonsCount = collect($addonBreakdown)->sum('quantity');
                    $addonsTotal = collect($addonBreakdown)->sum('subtotal');
                    $scannedCount = $booking->scannedTicketsCount();
                    $pendingScanCount = $booking->pendingTicketsCount();
                    $scanPercent = $booking->scanPercent();
                    $eventSummaryInfo = $ticketSummaryByEventId->get($booking->event_id);
                    $eventDateLabel = !empty($booking->event_date) ? \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y H:i') : ($eventSummaryInfo['date_label'] ?? '-');
                    $buyerName = $booking->buyerName();
                    $buyerEmail = $booking->buyerEmail();
                    $buyerPhone = $booking->buyerPhone();
                    $buyerDisplayName = $buyerName !== '' ? $buyerName : __('Invitado');
                    $paymentMethodLabel = $booking->paymentMethod ?: '-';
                    $status = $statusOptions[$booking->paymentStatus] ?? ['label' => ucfirst((string) $booking->paymentStatus), 'class' => 'secondary', 'icon' => 'fa-circle'];
                    $invoiceExtension = pathinfo((string) $booking->invoice, PATHINFO_EXTENSION);
                    $hasInvoiceFile = $invoiceExtension == 'pdf' && $booking->hasInvoiceFile();
                  @endphp
                  <article class="ob-mobile-booking" aria-labelledby="organizerMobileBookingTitle{{ $booking->id }}">
                    <div class="ob-mobile-booking__head">
                      <div class="ob-mobile-booking__main">
                        <h3 class="ob-mobile-booking__heading">@if ($eventInfo)<a id="organizerMobileBookingTitle{{ $booking->id }}" class="ob-mobile-booking__title"
                              href="{{ route('event.details', ['slug' => $slug, 'id' => $eventInfo->event_id]) }}"
                              target="_blank" rel="noopener">{{ $title }}</a>@else<span id="organizerMobileBookingTitle{{ $booking->id }}" class="ob-mobile-booking__title">-</span>@endif
                          <span class="ob-mobile-booking__ref tuki-data tuki-data-id">#{{ Str::limit($booking->booking_id, 16, '') }}</span>
                        </h3>
                        <div id="organizerMobileBookingMeta{{ $booking->id }}" class="ob-mobile-booking__meta">
                          <span>{{ $eventDateLabel }}</span>
                        </div>
                      </div>
                      <div class="ob-mobile-booking__badges">
                        <span class="badge badge-{{ $status['class'] }} ob-status"
                          aria-label="{{ __('Estado de pago') }}: {{ $status['label'] }}">{{ $status['label'] }}</span>
                      </div>
                    </div>

                    <section class="ob-mobile-buyerline" aria-label="{{ __('Comprador') }}">
                      <span class="ob-mobile-buyerline__label">{{ __('Comprador') }}</span>
                      <strong class="ob-mobile-buyerline__name">{{ $buyerDisplayName }}</strong>
                      @if ($booking->isGuestBuyer())
                        <span class="badge badge-secondary ob-buyer__badge">{{ __('Invitado') }}</span>
                      @endif
                      @if ($buyerEmail !== '')
                        <a class="ob-mobile-buyerline__contact" href="mailto:{{ $buyerEmail }}">{{ $buyerEmail }}</a>
                      @endif
                      @if ($buyerPhone !== '')
                        <a class="ob-mobile-buyerline__contact tuki-data tuki-data-count" href="tel:{{ preg_replace('/\s+/', '', $buyerPhone) }}">{{ $buyerPhone }}</a>
                      @endif
                    </section>

                    <div class="ob-mobile-booking__grid" role="group" aria-label="{{ __('Resumen de la reserva') }}">
                      <div class="ob-mobile-stat">
                        <div class="ob-mobile-stat__line">
                          <span class="ob-detail-label">{{ __('Importe') }}</span>
                          <span class="ob-money ob-mobile-money tuki-data tuki-data-money">{{ $formatMoney($paidTotal) }}</span>
                        </div>
                        <span class="ob-muted">{{ __('Recibís') }}: <span class="tuki-data tuki-data-money">{{ $formatMoney($organizerTotal) }}</span></span>
                      </div>
                      <div class="ob-mobile-stat">
                        <div class="ob-mobile-stat__line">
                          <span class="ob-detail-label">{{ __('Escaneo') }}</span>
                        @if ((int) $booking->quantity <= 0)
                          <span class="ob-detail-value">{{ __('Datos incompletos') }}</span>
                        @else
                          <span class="ob-detail-value tuki-data tuki-data-count">{{ $scannedCount }}/{{ $booking->quantity }}</span>
                        @endif
                        </div>
                        @if ((int) $booking->quantity > 0)
                          <span class="ob-muted">{{ __('Faltan') }}: <span class="tuki-data tuki-data-count">{{ $pendingScanCount }}</span></span>
                        @endif
                        <div class="ob-progress" aria-hidden="true"><span style="width: {{ $scanPercent }}%"></span></div>
                      </div>
                    </div>

                    <section class="ob-mobile-extra ob-mobile-extra--tickets" aria-labelledby="organizerMobileTicketsTitle{{ $booking->id }}">
                      <div class="ob-mobile-extra__head">
                        <span id="organizerMobileTicketsTitle{{ $booking->id }}" class="ob-detail-label">{{ __('Entradas') }}</span>
                        <span class="ob-mobile-payment"><span class="sr-only">{{ __('Pago') }}: </span>{{ $paymentMethodLabel }}</span>
                      </div>
                      <div class="ob-mini-list">
                        @foreach ($ticketBreakdown as $ticketItem)
                          @php
                            $ticketName = trim((string) $ticketItem['name']);
                            $ticketDisplayName = trim((string) preg_replace('/^\s*Entrada\s+/iu', '', $ticketName));
                            $ticketDisplayName = $ticketDisplayName !== '' ? Str::ucfirst($ticketDisplayName) : $ticketName;
                          @endphp
                          <div class="ob-mini-row">
                            <div class="ob-mini-row__main">
                              <span class="ob-mini-title">{{ $ticketDisplayName }}</span>
                              <span class="ob-muted">
                                {{ __('Escaneo') }}:
                                <span class="tuki-data tuki-data-count">{{ $ticketItem['scanned'] }}/{{ $ticketItem['quantity'] }}</span>
                                · {{ __('Cantidad') }}:
                                <span class="tuki-data tuki-data-count">{{ $ticketItem['quantity'] }}</span>
                              </span>
                            </div>
                            <span class="ob-detail-value ob-mini-row__amount ob-money tuki-data tuki-data-money">{{ $formatMoney($ticketItem['subtotal']) }}</span>
                          </div>
                        @endforeach
                      </div>
                    </section>

                    @if (count($addonBreakdown) > 0)
                      <section class="ob-mobile-extra" aria-label="{{ __('Add-ons') }}">
                        <span class="ob-detail-label">
                          {{ __('Add-ons') }}:
                          <span class="tuki-data tuki-data-count">{{ $addonsCount }}</span>
                          · <span class="tuki-data tuki-data-money">{{ $formatMoney($addonsTotal) }}</span>
                        </span>
                        <div class="ob-mini-list">
                          @foreach ($addonBreakdown as $addonItem)
                            <div class="ob-mini-row">
                              <div class="ob-mini-row__main">
                                <span class="ob-mini-title">{{ $addonItem['title'] }}</span>
                                <span class="ob-muted">{{ $addonItem['redeemed'] ? __('Canjeado') : __('Pendiente de canje') }}</span>
                              </div>
                              <span class="ob-detail-value ob-mini-row__amount ob-money tuki-data tuki-data-money">{{ $formatMoney($addonItem['subtotal']) }}</span>
                            </div>
                          @endforeach
                        </div>
                      </section>
                    @endif

                    <div class="ob-mobile-controls">
                      <a href="{{ route('organizer.event_booking.details', ['id' => $booking->id]) }}"
                        class="btn btn-sm ob-mobile-btn ob-mobile-btn--secondary"
                        aria-label="{{ __('Ver detalles de la reserva') }} #{{ $booking->booking_id }}">
                        <i class="fas fa-eye" aria-hidden="true"></i>{{ __('Ver') }}
                      </a>
                      @if ($hasInvoiceFile)
                        <a href="{{ route('booking.ticket.download', $booking->id) }}" class="btn btn-sm ob-mobile-btn ob-mobile-btn--secondary" target="_blank" rel="noopener"
                          aria-label="{{ __('Descargar entrada de la reserva') }} #{{ $booking->booking_id }}">
                          <i class="fas fa-file-pdf" aria-hidden="true"></i>{{ __('Entrada') }}
                        </a>
                      @endif
                      @if (!is_null($booking->attachmentFile))
                        <a href="#" data-toggle="modal" data-target="#attachmentModal-{{ $booking->id }}" class="btn btn-sm ob-mobile-btn ob-mobile-btn--secondary"
                          aria-label="{{ __('Ver comprobante de la reserva') }} #{{ $booking->booking_id }}">
                          <i class="fas fa-paperclip" aria-hidden="true"></i>{{ __('Comprobante') }}
                        </a>
                      @endif
                      <form class="deleteForm d-inline-block m-0" action="{{ route('organizer.event_booking.delete', ['id' => $booking->id]) }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-sm ob-mobile-btn ob-mobile-btn--secondary deleteBtn"
                          aria-label="{{ __('Eliminar reserva') }} #{{ $booking->booking_id }}">
                          <i class="fas fa-trash" aria-hidden="true"></i>{{ __('Eliminar') }}
                        </button>
                      </form>
                    </div>
                  </article>
                @endforeach
              </div>

              @foreach ($bookings as $booking)
                @includeIf('organizer.event.booking.show-attachment')
              @endforeach
            @endif
      </div>

      @if (count($bookings) > 0)
        <div class="ob-panel__footer">
          <div class="d-inline-block">
            {{ $bookings->links() }}
          </div>
        </div>
      @endif
    </section>
    @endif
  </div>
@endsection

@section('script')
  <script>
    'use strict';

    (function($) {
      $('.ob-expand-btn').on('click', function() {
        var target = $($(this).data('target'));
        var expanded = $(this).attr('aria-expanded') === 'true';

        target.toggleClass('d-none', expanded);
        $(this).attr('aria-expanded', expanded ? 'false' : 'true');
        $(this).find('i').toggleClass('fa-chevron-down', expanded).toggleClass('fa-chevron-up', !expanded);
      });
    })(jQuery);
  </script>
@endsection
