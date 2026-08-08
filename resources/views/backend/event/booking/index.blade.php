@extends('backend.layout')

@section('style')
  <style>
    .event-booking-admin {
      color: var(--adm-ink);
    }

    .eb-buyer__name {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 6px;
      font-weight: 700;
      color: var(--adm-ink, #1e2532);
    }

    .eb-buyer__guest {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.02em;
      text-transform: uppercase;
    }

    .eb-buyer__label {
      font-weight: 600;
    }

    /* Hub: lista limpia de eventos (entrar al evento para ver todo) */
    .eb-event-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .eb-event-row {
      --eb-row-accent: #2563eb;
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      align-items: center;
      gap: 16px 20px;
      min-height: 72px;
      padding: 16px 18px 16px 20px;
      border: 1px solid var(--adm-border);
      border-left: 4px solid var(--eb-row-accent);
      border-radius: 10px;
      background: var(--adm-card);
      color: inherit;
      text-decoration: none;
      box-shadow: 0 4px 14px rgba(30, 37, 50, .04);
      transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .eb-event-row:hover,
    .eb-event-row:focus {
      border-color: color-mix(in srgb, var(--adm-primary) 35%, var(--adm-border));
      box-shadow: 0 8px 20px rgba(30, 37, 50, .08);
      color: inherit;
      text-decoration: none;
      transform: translateY(-1px);
    }

    .eb-event-row:focus-visible {
      outline: 2px solid var(--adm-primary);
      outline-offset: 2px;
    }

    .eb-event-row--paid {
      --eb-row-accent: #16a34a;
    }

    .eb-event-row--free {
      --eb-row-accent: #2563eb;
    }

    .eb-event-row__main {
      min-width: 0;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .eb-event-row__title {
      margin: 0;
      color: var(--adm-ink);
      font-size: 15px;
      font-weight: 800;
      line-height: 1.3;
    }

    .eb-event-row__meta {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 8px;
    }

    .eb-event-row__chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      min-height: 28px;
      padding: 4px 10px;
      border: 1px solid var(--adm-border);
      border-radius: 999px;
      background: var(--adm-bg-soft, #f8fafc);
      color: var(--adm-muted);
      font-size: 12px;
      font-weight: 600;
      line-height: 1.2;
      white-space: nowrap;
    }

    .eb-event-row__chip--date {
      border-color: color-mix(in srgb, var(--adm-primary) 22%, transparent);
      background: var(--adm-primary-soft);
      color: var(--adm-primary-strong);
    }

    .eb-event-row__chip--status {
      border-color: rgba(240, 90, 40, .22);
      background: var(--adm-primary-soft);
      color: var(--adm-primary-strong);
    }

    .eb-event-row__kpis {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 8px 16px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .eb-event-row__kpi {
      display: inline-flex;
      align-items: baseline;
      gap: 6px;
      color: var(--adm-muted);
      font-size: 12px;
      font-weight: 600;
      white-space: nowrap;
    }

    .eb-event-row__kpi strong {
      color: var(--adm-ink);
      font-size: 14px;
      font-weight: 800;
    }

    .eb-event-row__cta {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 40px;
      padding: 0 16px;
      border-radius: 8px;
      background: linear-gradient(135deg, #c2410c 0%, #9a3412 100%);
      color: #ffffff;
      font-size: 13px;
      font-weight: 700;
      line-height: 1;
      white-space: nowrap;
      pointer-events: none;
    }

    .eb-event-row__cta i {
      font-size: 12px;
    }

    .eb-hub-hint {
      margin: 0 0 16px;
      padding: 14px 16px;
      border: 1px solid var(--adm-border);
      border-radius: 8px;
      background: var(--adm-bg-soft);
      color: var(--adm-muted);
      font-size: 14px;
      line-height: 1.45;
    }

    @media (max-width: 767px) {
      .eb-event-row {
        grid-template-columns: 1fr;
        gap: 14px;
        padding: 14px 14px 14px 16px;
      }

      .eb-event-row__cta {
        width: 100%;
      }
    }

    .eb-summary {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 18px;
      margin-bottom: 22px;
    }

    .eb-metric {
      --eb-accent: #94a3b8;
      --eb-soft: color-mix(in srgb, var(--eb-accent) 10%, var(--adm-card));
      --eb-ink: var(--adm-ink);
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      min-height: 128px;
      padding: 22px 22px;
      border: 1px solid var(--adm-border);
      border-radius: 8px;
      background: var(--adm-card);
      box-shadow: 0 8px 18px rgba(30, 37, 50, .06);
    }

    .eb-metric::before {
      position: absolute;
      inset: 0 0 auto;
      height: 4px;
      background: var(--eb-accent);
      content: "";
    }

    .eb-metric__body {
      flex: 1 1 auto;
      min-width: 0;
    }

    .eb-metric__icon {
      order: -1;
      flex: 0 0 64px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 64px;
      height: 64px;
      margin-right: 24px;
      border: 0;
      border-radius: 14px;
      background: var(--eb-soft);
      color: var(--eb-accent);
      font-size: 24px;
    }

    .eb-metric--primary::before {
      background: #f97316;
    }

    .eb-metric--primary {
      --eb-accent: #f97316;
    }

    .eb-metric--money::before {
      background: #0f766e;
    }

    .eb-metric--money {
      --eb-accent: #0f766e;
    }

    .eb-metric--paid::before {
      background: #16a34a;
    }

    .eb-metric--paid {
      --eb-accent: #16a34a;
    }

    .eb-metric--free::before {
      background: #2563eb;
    }

    .eb-metric--free {
      --eb-accent: #2563eb;
    }

    .eb-metric--pending::before {
      background: #f59e0b;
    }

    .eb-metric--pending {
      --eb-accent: #f59e0b;
    }

    .eb-metric--platform::before {
      background: #7c3aed;
    }

    .eb-metric--platform {
      --eb-accent: #7c3aed;
    }

    .eb-metric__label {
      margin-bottom: 7px;
      color: var(--adm-muted);
      font-size: 12px;
      font-weight: 500;
      letter-spacing: .04em;
      text-transform: none;
    }

    .eb-metric__value {
      color: var(--eb-ink);
      font-size: 24px;
      font-weight: 700;
      letter-spacing: 0;
      line-height: 1.15;
    }

    .eb-metric__hint {
      margin-top: 6px;
      color: var(--adm-muted);
      font-size: 11px;
      font-weight: 400;
      line-height: 1.35;
    }

    .eb-toolbar {
      border-bottom: 1px solid var(--adm-border);
      background: var(--adm-card);
    }

    .eb-type-summary {
      max-width: 100%;
      overflow: hidden;
      margin-bottom: 18px;
      border: 1px solid var(--adm-border);
      border-radius: 8px;
      background: var(--adm-card);
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .eb-type-summary__head {
      display: flex;
      justify-content: space-between;
      gap: 14px;
      padding: 16px 18px;
      border-bottom: 1px solid var(--adm-border);
    }

    .eb-type-summary__title {
      margin: 0;
      color: var(--adm-ink);
      font-size: 16px;
      font-weight: 800;
    }

    .eb-type-summary__body {
      padding: 18px;
    }

    .eb-event-summary-list {
      display: grid;
      gap: 16px;
    }

    .eb-event-summary-card {
      --eb-event-accent: #16a34a;
      --eb-event-soft: color-mix(in srgb, var(--eb-event-accent) 12%, var(--adm-card));
      position: relative;
      overflow: hidden;
      border: 1px solid var(--adm-border);
      border-radius: 10px;
      background: var(--adm-card);
      box-shadow: 0 8px 22px rgba(30, 37, 50, .04);
    }

    .eb-event-summary-card::before {
      position: absolute;
      inset: 0 auto 0 0;
      width: 4px;
      background: var(--eb-event-accent);
      content: "";
    }

    .eb-event-summary-card--free {
      --eb-event-accent: #2563eb;
    }

    .eb-event-summary-card--paid {
      --eb-event-accent: #16a34a;
    }

    .eb-event-summary-card__head {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 16px;
      align-items: start;
      padding: 16px 18px;
      border-bottom: 1px solid var(--adm-border);
      background: linear-gradient(90deg, var(--eb-event-soft) 0%, var(--adm-card) 64%);
      list-style: none;
      cursor: pointer;
    }

    .eb-event-summary-card__head::-webkit-details-marker {
      display: none;
    }

    .eb-event-summary-card__head:focus-visible {
      outline: 3px solid color-mix(in srgb, var(--adm-primary) 45%, transparent);
      outline-offset: -3px;
      border-radius: 10px;
    }

    .eb-event-summary-card__aside {
      position: relative;
      padding-right: 22px;
    }

    .eb-event-summary-card__aside::after {
      content: "";
      position: absolute;
      right: 2px;
      top: 50%;
      width: 8px;
      height: 8px;
      margin-top: -5px;
      border-right: 2px solid var(--adm-muted);
      border-bottom: 2px solid var(--adm-muted);
      transform: rotate(45deg);
      transition: transform 0.18s ease;
    }

    .eb-event-summary-card[open] .eb-event-summary-card__aside::after {
      transform: rotate(225deg);
      margin-top: -3px;
    }

    .eb-event-summary-card .eb-event-summary-stats,
    .eb-event-summary-card .eb-type-table {
      display: none;
    }

    .eb-event-summary-card[open] .eb-event-summary-stats,
    .eb-event-summary-card[open] .eb-type-table {
      display: revert;
    }

    .eb-event-summary-card__head > div {
      min-width: 0;
    }

    .eb-event-summary-card__title {
      margin: 0 0 7px;
      color: var(--adm-ink);
      font-size: 16px;
      font-weight: 800;
      line-height: 1.25;
    }

    .eb-event-summary-card__meta {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 7px;
      color: var(--adm-muted);
      font-size: 12px;
      font-weight: 500;
    }

    .eb-event-summary-card__meta > span {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      min-height: 24px;
      padding: 4px 9px;
      border: 1px solid var(--adm-border);
      border-radius: 999px;
      background: var(--adm-bg-soft);
      line-height: 1.2;
      white-space: nowrap;
    }

    .eb-event-summary-card__date {
      border-color: color-mix(in srgb, var(--adm-primary) 22%, transparent) !important;
      background: var(--adm-primary-soft) !important;
      color: var(--adm-primary-strong);
      font-weight: 650;
    }

    .eb-event-summary-card__date i {
      color: var(--adm-primary);
      font-size: 11px;
    }

    .eb-event-summary-card__meta-chip {
      color: var(--adm-muted);
      font-weight: 500;
    }

    .eb-event-summary-card__status {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 5px 9px;
      border: 1px solid rgba(240, 90, 40, .22);
      border-radius: 999px;
      background: var(--adm-primary-soft);
      color: var(--adm-primary-strong);
      font-size: 11.5px;
      font-weight: 650;
      letter-spacing: 0;
      text-transform: none;
    }

    .eb-event-summary-card__aside {
      display: grid;
      justify-items: end;
      gap: 8px;
    }

    .eb-event-summary-card__amount {
      min-width: 128px;
      padding: 8px 10px;
      border: 1px solid var(--adm-border);
      border: 1px solid color-mix(in srgb, var(--eb-event-accent) 18%, #e7eaf0);
      border-radius: 9px;
      background: var(--adm-card);
      text-align: right;
    }

    .eb-event-summary-card__amount span {
      display: block;
      color: var(--adm-muted);
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .eb-event-summary-card__amount strong {
      display: block;
      margin-top: 2px;
      color: var(--adm-ink);
      font-size: 16px;
      font-weight: 900;
      line-height: 1.15;
    }

    .eb-event-summary-stats {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 10px;
      padding: 14px;
      border-bottom: 1px solid var(--adm-border);
      background: var(--adm-card);
    }

    .eb-event-summary-stat {
      min-width: 0;
      padding: 12px 14px;
      border: 1px solid var(--adm-border);
      border-radius: 9px;
      background: var(--adm-card);
    }

    .eb-event-summary-stat span {
      display: block;
      color: var(--adm-muted);
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .eb-event-summary-stat strong {
      display: block;
      margin-top: 3px;
      color: var(--adm-ink);
      font-size: 18px;
      font-weight: 800;
      line-height: 1.15;
      overflow-wrap: anywhere;
    }

    .eb-event-summary-stat--sold {
      border-color: rgba(22, 163, 74, .20);
      background: color-mix(in srgb, var(--adm-success) 16%, var(--adm-card));
    }

    .eb-event-summary-stat--scan {
      border-color: rgba(37, 99, 235, .18);
      background: color-mix(in srgb, var(--adm-info) 16%, var(--adm-card));
    }

    .eb-event-summary-stat--pending {
      border-color: rgba(245, 158, 11, .18);
      background: color-mix(in srgb, var(--adm-warning) 14%, var(--adm-card));
    }

    .eb-event-summary-stat--rejected {
      border-color: rgba(220, 38, 38, .14);
      background: color-mix(in srgb, var(--adm-danger) 12%, var(--adm-card));
    }

    .eb-type-summary__formula {
      padding: 10px 18px;
      border-bottom: 1px solid var(--adm-border);
      background: var(--adm-card);
      color: var(--adm-muted);
      font-size: 12px;
    }

    .eb-type-table {
      width: 100%;
      table-layout: fixed;
      margin-bottom: 0;
      font-size: 11px;
    }

    .eb-type-table th {
      border-top: 0;
      color: var(--adm-muted);
      font-size: 10px;
      line-height: 1.25;
      padding: 8px 6px;
      text-transform: uppercase;
      white-space: normal;
    }

    .eb-type-table td {
      padding: 9px 6px;
      vertical-align: middle;
      line-height: 1.35;
      overflow-wrap: anywhere;
    }

    .eb-type-row--paid td:first-child {
      border-left: 3px solid #16a34a;
      padding-left: 10px;
    }

    .eb-type-row--free td:first-child {
      border-left: 3px solid #2563eb;
      padding-left: 10px;
    }

    .eb-type-table__ticket {
      width: 43%;
    }

    .eb-type-table__counts {
      width: 10%;
    }

    .eb-type-table__scan {
      width: 15%;
    }

    .eb-type-table__money {
      width: 12%;
    }

    .eb-type-lines {
      display: grid;
      gap: 2px;
    }

    .eb-type-line {
      display: flex;
      justify-content: space-between;
      gap: 6px;
      min-width: 0;
    }

    .eb-type-line span:first-child {
      color: var(--adm-muted);
    }

    .eb-type-line strong,
    .eb-type-line span:last-child {
      color: var(--adm-ink);
      font-weight: 800;
    }

    .eb-type-name {
      display: block;
      color: var(--adm-ink);
      font-weight: 800;
      overflow-wrap: anywhere;
    }

    .eb-type-event {
      display: block;
      max-width: 100%;
      overflow: hidden;
      color: var(--adm-muted);
      font-size: 12px;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .eb-toolbar .form-group {
      margin-bottom: 12px;
    }

    .eb-table {
      margin-bottom: 0;
    }

    .eb-table th {
      border-top: 0;
      color: var(--adm-muted);
      font-size: 12px;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .eb-table td {
      vertical-align: middle;
    }

    .eb-title {
      display: block;
      max-width: 280px;
      overflow: hidden;
      color: var(--adm-ink);
      font-weight: 700;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .eb-muted {
      color: var(--adm-muted);
      font-size: 12px;
    }

    .eb-money {
      color: var(--adm-ink);
      font-weight: 800;
      overflow-wrap: anywhere;
    }

    .eb-status {
      display: inline-flex;
      align-items: center;
      min-height: 26px;
      padding: 6px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
    }

    .eb-status i {
      margin-right: 5px;
    }

    .eb-status--paid,
    .badge-success.eb-status {
      background: color-mix(in srgb, var(--adm-success) 16%, var(--adm-card)) !important;
      color: var(--adm-success) !important;
    }

    .eb-status--free,
    .badge-info.eb-status {
      background: color-mix(in srgb, var(--adm-info) 16%, var(--adm-card)) !important;
      color: var(--adm-info) !important;
    }

    .eb-status--pending,
    .badge-warning.eb-status {
      background: color-mix(in srgb, var(--adm-warning) 16%, var(--adm-card)) !important;
      color: var(--adm-warning) !important;
    }

    .eb-status--rejected,
    .badge-danger.eb-status {
      background: color-mix(in srgb, var(--adm-danger) 14%, var(--adm-card)) !important;
      color: var(--adm-danger) !important;
    }

    .eb-expand-btn,
    .eb-action-btn {
      width: 34px;
      height: 34px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      border-radius: 6px;
    }

    .eb-actions {
      display: flex;
      flex-wrap: nowrap;
      gap: 6px;
    }

    .eb-detail-row td {
      background: var(--adm-card);
      border-top: 0;
    }

    .eb-detail-grid {
      display: grid;
      grid-template-columns: repeat(5, minmax(0, 1fr));
      gap: 12px;
      padding: 14px 16px;
      border: 1px solid var(--adm-border);
      border-radius: 8px;
      background: var(--adm-card);
    }

    .eb-detail-section {
      grid-column: 1 / -1;
      padding-top: 12px;
      border-top: 1px solid var(--adm-border);
    }

    .eb-mini-list {
      display: grid;
      gap: 8px;
      margin-top: 8px;
    }

    .eb-mini-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto auto;
      gap: 10px;
      align-items: center;
      padding: 8px 10px;
      border: 1px solid var(--adm-border);
      border-radius: 7px;
      background: var(--adm-card);
    }

    .eb-mini-title {
      display: block;
      overflow-wrap: anywhere;
      color: var(--adm-ink);
      font-weight: 800;
    }

    .eb-pill {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 3px 8px;
      border-radius: 999px;
      background: var(--adm-primary-soft);
      color: var(--adm-primary-strong);
      font-size: 12px;
      font-weight: 800;
      white-space: nowrap;
    }

    .eb-pill--paid {
      background: color-mix(in srgb, var(--adm-success) 16%, var(--adm-card));
      color: var(--adm-success);
    }

    .eb-pill--free {
      background: color-mix(in srgb, var(--adm-info) 16%, var(--adm-card));
      color: var(--adm-info);
    }

    .eb-type-badge {
      display: inline-flex;
      align-items: center;
      min-height: 22px;
      margin-top: 6px;
      padding: 3px 8px;
      border-radius: 999px;
      font-size: 10px;
      font-weight: 800;
      letter-spacing: .04em;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .eb-type-badge--paid {
      background: color-mix(in srgb, var(--adm-success) 16%, var(--adm-card));
      color: var(--adm-success);
    }

    .eb-type-badge--free {
      background: color-mix(in srgb, var(--adm-info) 16%, var(--adm-card));
      color: var(--adm-info);
    }

    .eb-scan-cell {
      min-width: 120px;
    }

    .eb-progress {
      width: 100%;
      height: 6px;
      overflow: hidden;
      margin-top: 5px;
      border-radius: 999px;
      background: var(--adm-border);
    }

    .eb-progress span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: #F97316;
    }

    .eb-mobile-extra {
      padding-top: 10px;
      margin-top: 10px;
      border-top: 1px solid var(--adm-border);
    }

    .eb-detail-label {
      display: block;
      color: var(--adm-muted);
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .eb-detail-value {
      display: block;
      margin-top: 4px;
      color: var(--adm-ink);
      font-weight: 700;
    }

    .eb-mobile-list {
      display: grid;
      gap: 12px;
    }

    .eb-mobile-booking {
      padding: 14px;
      border: 1px solid var(--adm-border);
      border-radius: 8px;
      background: var(--adm-card);
    }

    .eb-mobile-booking__head {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 10px;
    }

    .eb-mobile-booking__title {
      margin-bottom: 2px;
      color: var(--adm-ink);
      font-weight: 800;
    }

    .eb-mobile-booking__grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
      margin: 12px 0;
    }

    .eb-empty {
      padding: 42px 16px;
      text-align: center;
    }

    .eb-empty i {
      color: var(--adm-muted);
      font-size: 34px;
    }

    .eb-empty h3 {
      margin-top: 14px;
      color: var(--adm-ink);
      font-size: 18px;
      font-weight: 800;
    }

    @media (max-width: 1199px) {
      .eb-summary,
      .eb-detail-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }
    }

    @media (max-width: 767px) {
      .eb-summary,
      .eb-detail-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .eb-type-summary__body {
        padding: 12px;
      }

      .eb-event-summary-list {
        gap: 12px;
      }

      .eb-event-summary-card__head {
        grid-template-columns: 1fr;
        gap: 10px;
        padding: 14px;
      }

      .eb-event-summary-card__aside {
        justify-items: start;
      }

      .eb-event-summary-card__amount {
        min-width: 0;
        text-align: left;
      }

      .eb-event-summary-card__title {
        margin-bottom: 10px;
        font-size: 15px;
      }

      .eb-event-summary-card__status {
        justify-self: start;
      }

      .eb-event-summary-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        padding: 12px;
        background: var(--adm-card);
      }

      .eb-event-summary-stat {
        padding: 11px 12px;
        border: 1px solid var(--adm-border);
        border-radius: 9px;
        background: var(--adm-card);
      }

      .eb-event-summary-stat strong {
        font-size: 17px;
      }

      .eb-mini-row {
        grid-template-columns: 1fr;
      }

      .eb-metric {
        min-height: 122px;
        padding: 20px 22px;
      }

      .eb-metric__icon {
        flex-basis: 56px;
        width: 56px;
        height: 56px;
        margin-right: 18px;
        font-size: 21px;
      }

      .eb-metric__value {
        font-size: 22px;
      }

      .eb-type-summary__head {
        flex-direction: column;
      }
    }

    @media (max-width: 480px) {
      .eb-summary,
      .eb-detail-grid,
      .eb-mobile-booking__grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 360px) {
      .eb-event-summary-stats {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 767px) {
      .eb-type-table,
      .eb-type-table thead,
      .eb-type-table tbody,
      .eb-type-table tr,
      .eb-type-table th,
      .eb-type-table td {
        display: block;
        width: 100%;
      }

      .eb-type-table {
        padding: 10px;
        border-top: 1px solid var(--adm-border);
        background: var(--adm-card);
        font-size: 12px;
      }

      .eb-type-table thead {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
      }

      .eb-type-table tbody {
        display: grid;
        gap: 10px;
      }

      .eb-type-table tr {
        padding: 10px 12px;
        border: 1px solid var(--adm-border);
        border-radius: 10px;
        background: var(--adm-card);
        box-shadow: 0 8px 18px rgba(30, 37, 50, .04);
      }

      .eb-type-table td {
        display: grid;
        grid-template-columns: minmax(92px, 38%) minmax(0, 1fr);
        gap: 10px;
        align-items: center;
        min-height: 28px;
        padding: 6px 0;
        border-top: 0;
      }

      .eb-type-table td:first-child {
        display: block;
        min-height: 0;
        margin-bottom: 4px;
        padding: 0 0 9px;
        border-bottom: 1px solid var(--adm-border);
      }

      .eb-type-table td:first-child::before {
        display: none;
      }

      .eb-type-table td::before {
        content: attr(data-label);
        color: var(--adm-muted);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
      }

      .eb-type-table td:not(:first-child) {
        color: var(--adm-ink);
        font-weight: 700;
      }

      .eb-type-name {
        font-size: 13px;
        line-height: 1.3;
      }

      .eb-progress {
        max-width: 180px;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $hasAdvancedFilters = request()->filled('status') || request()->filled('from_date') || request()->filled('to_date') || request()->filled('document_number');
    $formatBaseMoney = function ($amount) use ($currencySettings) {
        $symbol = optional($currencySettings)->base_currency_symbol;
        $position = optional($currencySettings)->base_currency_symbol_position;
        $amount = number_format((float) $amount, 0, ',', '.');
        return ($position == 'left' ? $symbol : '') . $amount . ($position == 'right' ? $symbol : '');
    };
    $defaultLanguageCode = optional($defaultLanguage)->code ?: 'es';
    $focusedEventId = $focusedEventId ?? null;
    $focusedEvent = $focusedEvent ?? null;
    $focusedEventTitle = $focusedEventId
      ? (optional($eventInfos[$focusedEventId] ?? null)->title ?: __('Evento') . ' #' . $focusedEventId)
      : null;
    $bookingFiltersAction = $focusedEventId
      ? route('admin.event_booking.by_event', $focusedEventId)
      : route('admin.event.booking');
    $statusOptions = [
        'completed' => ['label' => __('Pago'), 'class' => 'success eb-status--paid', 'icon' => 'fa-check-circle'],
        'pending' => ['label' => __('Pendiente'), 'class' => 'warning eb-status--pending', 'icon' => 'fa-clock'],
        'rejected' => ['label' => __('Rechazado'), 'class' => 'danger eb-status--rejected', 'icon' => 'fa-times-circle'],
        'free' => ['label' => __('Gratis'), 'class' => 'info eb-status--free', 'icon' => 'fa-gift'],
    ];
  @endphp

  <div class="event-booking-admin">
    <div class="page-header">
      <h4 class="page-title">
        @if ($focusedEventId)
          {{ __('Compradores') }}: {{ $focusedEventTitle }}
        @else
          {{ __('Reservas de eventos') }}
        @endif
      </h4>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('admin.dashboard') }}" aria-label="{{ __('Ir al panel') }}">
            <i class="flaticon-home"></i>
          </a>
        </li>
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.event.booking') }}">{{ __('Reservas') }}</a>
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

    @if ($focusedEventId)
      <p class="eb-hub-hint">
        <a href="{{ route('admin.event.booking') }}" class="btn btn-sm btn-outline-secondary mr-2">
          <i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>{{ __('Volver a eventos') }}
        </a>
        {{ __('Lista de personas que reservaron este evento (incluye invitados: nombre, email y teléfono de la reserva).') }}
      </p>
    @else
      <p class="eb-hub-hint">
        {{ __('Lista de eventos con reservas. Entrá a un evento para ver tipos de entrada, compradores (nombre, email y teléfono) y acciones.') }}
      </p>
    @endif

    <div class="eb-summary" aria-label="{{ __('Resumen de reservas') }}">
      <div class="eb-metric eb-metric--primary">
        <div class="eb-metric__body">
          <div class="eb-metric__label">{{ __('Reservas') }}</div>
          <div class="eb-metric__value">{{ number_format($kpis['total'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <span class="eb-metric__icon"><i class="fas fa-ticket-alt" aria-hidden="true"></i></span>
      </div>
      <div class="eb-metric eb-metric--money">
        <div class="eb-metric__body">
          <div class="eb-metric__label">{{ __('Total cobrado') }}</div>
          <div class="eb-metric__value">{{ $formatBaseMoney($kpis['charged'] ?? 0) }}</div>
          <div class="eb-metric__hint">{{ __('Lo que pagó el cliente') }}</div>
        </div>
        <span class="eb-metric__icon"><i class="fas fa-coins" aria-hidden="true"></i></span>
      </div>
      <div class="eb-metric eb-metric--paid">
        <div class="eb-metric__body">
          <div class="eb-metric__label">{{ __('Base entradas') }}</div>
          <div class="eb-metric__value">{{ $formatBaseMoney($kpis['ticket_revenue'] ?? 0) }}</div>
          <div class="eb-metric__hint">{{ __('Valor de entradas sin cargo de servicio') }}</div>
        </div>
        <span class="eb-metric__icon"><i class="fas fa-tags" aria-hidden="true"></i></span>
      </div>
      <div class="eb-metric eb-metric--paid">
        <div class="eb-metric__body">
          <div class="eb-metric__label">{{ __('Neto organizadores') }}</div>
          <div class="eb-metric__value">{{ $formatBaseMoney($kpis['organizer_net'] ?? 0) }}</div>
          <div class="eb-metric__hint">{{ __('Base menos comisión descontada') }}</div>
        </div>
        <span class="eb-metric__icon"><i class="fas fa-wallet" aria-hidden="true"></i></span>
      </div>
      <div class="eb-metric eb-metric--platform">
        <div class="eb-metric__body">
          <div class="eb-metric__label">{{ __('Plataforma') }}</div>
          <div class="eb-metric__value">{{ $formatBaseMoney($kpis['platform_earning'] ?? 0) }}</div>
          <div class="eb-metric__hint">{{ __('Cargo de servicio más comisión') }}</div>
        </div>
        <span class="eb-metric__icon"><i class="fas fa-percentage" aria-hidden="true"></i></span>
      </div>
      <div class="eb-metric eb-metric--paid">
        <div class="eb-metric__body">
          <div class="eb-metric__label">{{ __('Completadas') }}</div>
          <div class="eb-metric__value">{{ number_format($kpis['completed'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <span class="eb-metric__icon"><i class="fas fa-check-circle" aria-hidden="true"></i></span>
      </div>
      <div class="eb-metric eb-metric--pending">
        <div class="eb-metric__body">
          <div class="eb-metric__label">{{ __('Pendientes') }}</div>
          <div class="eb-metric__value">{{ number_format($kpis['pending'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <span class="eb-metric__icon"><i class="fas fa-clock" aria-hidden="true"></i></span>
      </div>
      <div class="eb-metric eb-metric--free">
        <div class="eb-metric__body">
          <div class="eb-metric__label">{{ __('Gratis') }}</div>
          <div class="eb-metric__value">{{ number_format($kpis['free'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <span class="eb-metric__icon"><i class="fas fa-gift" aria-hidden="true"></i></span>
      </div>
    </div>

    @unless ($focusedEventId)
      <div class="card mb-3">
        <div class="card-body py-3">
          <form action="{{ route('admin.event.booking') }}" method="GET" class="row align-items-end">
            <div class="col-md-6 col-lg-5">
              <div class="form-group mb-md-0">
                <label for="hubEventSearch">{{ __('Buscar evento o comprador') }}</label>
                <input id="hubEventSearch" name="search" type="search" class="form-control"
                  placeholder="{{ __('Título del evento, nombre, email o teléfono') }}"
                  value="{{ request('search') }}" autocomplete="off">
              </div>
            </div>
            <div class="col-md-3 col-lg-2">
              <div class="form-group mb-md-0">
                <label for="hubStatus">{{ __('Estado') }}</label>
                <select id="hubStatus" name="status" class="form-control">
                  <option value="">{{ __('Todos') }}</option>
                  <option value="completed" @selected(request('status') === 'completed')>{{ __('Completado') }}</option>
                  <option value="pending" @selected(request('status') === 'pending')>{{ __('Pendiente') }}</option>
                  <option value="rejected" @selected(request('status') === 'rejected')>{{ __('Rechazado') }}</option>
                  <option value="free" @selected(request('status') === 'free')>{{ __('Gratis') }}</option>
                </select>
              </div>
            </div>
            <div class="col-md-3 col-lg-3">
              <div class="form-group mb-0">
                <button class="btn btn-primary" type="submit">{{ __('Filtrar') }}</button>
                <a href="{{ route('admin.event.booking') }}" class="btn btn-light">{{ __('Limpiar') }}</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    @endunless

    <section class="eb-type-summary" aria-labelledby="ticketTypeSummaryTitle">
      <div class="eb-type-summary__head">
        <div>
          <h2 id="ticketTypeSummaryTitle" class="eb-type-summary__title">
            @if ($focusedEventId)
              {{ __('Resumen del evento') }}
            @else
              {{ __('Eventos') }}
            @endif
          </h2>
          <div class="eb-muted">
            @if ($focusedEventId)
              {{ __('Tipos de entrada de este evento.') }}
            @else
              {{ __('Ordenado por fecha. Tocá un evento para ver todo.') }}
            @endif
          </div>
        </div>
        <div class="eb-muted">{{ __('Vendido') }} = {{ __('completado') }} + {{ __('gratis') }}</div>
      </div>
      <div class="eb-type-summary__body">
        @if (empty($ticketSalesByEvent ?? []))
          <div class="eb-empty py-3">
            <p class="text-muted mb-0">{{ __('No hay entradas para resumir con estos filtros.') }}</p>
          </div>
        @else
          @unless ($focusedEventId)
            <div class="eb-event-list" role="list">
              @foreach ($ticketSalesByEvent as $eventSummary)
                @php
                  $isPaidEventSummary = (float) ($eventSummary['revenue'] ?? 0) > 0;
                @endphp
                <a role="listitem"
                  class="eb-event-row {{ $isPaidEventSummary ? 'eb-event-row--paid' : 'eb-event-row--free' }}"
                  href="{{ route('admin.event_booking.by_event', $eventSummary['event_id']) }}">
                  <div class="eb-event-row__main">
                    <h3 class="eb-event-row__title">{{ $eventSummary['event_title'] }}</h3>
                    <div class="eb-event-row__meta">
                      <span class="eb-event-row__chip eb-event-row__chip--date">
                        <i class="far fa-calendar-alt" aria-hidden="true"></i>
                        {{ $eventSummary['date_label'] }}
                      </span>
                      <span class="eb-event-row__chip eb-event-row__chip--status">{{ $eventSummary['date_status'] }}</span>
                      <span class="eb-event-row__chip">{{ number_format($eventSummary['bookings_count'], 0, ',', '.') }} {{ __('reservas') }}</span>
                    </div>
                    <ul class="eb-event-row__kpis" aria-label="{{ __('Totales del evento') }}">
                      <li class="eb-event-row__kpi"><span>{{ __('Vendidas') }}</span> <strong>{{ number_format($eventSummary['sold'], 0, ',', '.') }}</strong></li>
                      <li class="eb-event-row__kpi"><span>{{ __('Pendientes') }}</span> <strong>{{ number_format($eventSummary['pending'], 0, ',', '.') }}</strong></li>
                      <li class="eb-event-row__kpi"><span>{{ __('Escaneadas') }}</span> <strong>{{ number_format($eventSummary['scanned'], 0, ',', '.') }}/{{ number_format($eventSummary['total'], 0, ',', '.') }}</strong></li>
                      <li class="eb-event-row__kpi"><span>{{ __('Base') }}</span> <strong>{{ $formatBaseMoney($eventSummary['revenue']) }}</strong></li>
                    </ul>
                  </div>
                  <span class="eb-event-row__cta">
                    {{ __('Abrir evento') }}
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                  </span>
                </a>
              @endforeach
            </div>
          @else
            <div class="eb-event-summary-list">
              @foreach ($ticketSalesByEvent as $eventSummary)
                @php
                  $isPaidEventSummary = (float) ($eventSummary['revenue'] ?? 0) > 0;
                @endphp
                <details class="eb-event-summary-card {{ $isPaidEventSummary ? 'eb-event-summary-card--paid' : 'eb-event-summary-card--free' }}">
                  <summary class="eb-event-summary-card__head">
                    <div>
                      <h3 class="eb-event-summary-card__title">{{ $eventSummary['event_title'] }}</h3>
                      <div class="eb-event-summary-card__meta">
                        <span class="eb-event-summary-card__date">
                          <i class="far fa-calendar-alt" aria-hidden="true"></i>
                          {{ $eventSummary['date_label'] }}
                        </span>
                        <span class="eb-event-summary-card__meta-chip">{{ number_format($eventSummary['bookings_count'], 0, ',', '.') }} {{ __('reservas') }}</span>
                        <span class="eb-event-summary-card__meta-chip">{{ count($eventSummary['tickets']) }} {{ __('tipos de entrada') }}</span>
                      </div>
                    </div>
                    <div class="eb-event-summary-card__aside" aria-hidden="true">
                      <span class="eb-event-summary-card__status">{{ $eventSummary['date_status'] }}</span>
                      <span class="eb-event-summary-card__amount">
                        <span>{{ __('Base entradas') }}</span>
                        <strong>{{ $formatBaseMoney($eventSummary['revenue']) }}</strong>
                      </span>
                    </div>
                  </div>
                  </summary>

                  <div class="eb-event-summary-stats" aria-label="{{ __('Totales del evento') }}">
                    <div class="eb-event-summary-stat eb-event-summary-stat--sold">
                      <span>{{ __('Entradas vendidas') }}</span>
                      <strong>{{ number_format($eventSummary['sold'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="eb-event-summary-stat eb-event-summary-stat--pending">
                      <span>{{ __('Pendientes') }}</span>
                      <strong>{{ number_format($eventSummary['pending'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="eb-event-summary-stat eb-event-summary-stat--rejected">
                      <span>{{ __('Rechazadas') }}</span>
                      <strong>{{ number_format($eventSummary['rejected'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="eb-event-summary-stat eb-event-summary-stat--scan">
                      <span>{{ __('Escaneadas') }}</span>
                      <strong>{{ number_format($eventSummary['scanned'], 0, ',', '.') }}/{{ number_format($eventSummary['total'], 0, ',', '.') }}</strong>
                    </div>
                  </div>

                  <table class="table eb-type-table">
                    <colgroup>
                      <col class="eb-type-table__ticket">
                      <col class="eb-type-table__counts">
                      <col class="eb-type-table__counts">
                      <col class="eb-type-table__counts">
                      <col class="eb-type-table__scan">
                      <col class="eb-type-table__money">
                    </colgroup>
                    <thead>
                      <tr>
                        <th scope="col">{{ __('Entrada') }}</th>
                        <th scope="col">{{ __('Vendidas') }}</th>
                        <th scope="col">{{ __('Pendientes') }}</th>
                        <th scope="col">{{ __('Rechazadas') }}</th>
                        <th scope="col">{{ __('Escaneo') }}</th>
                        <th scope="col">{{ __('Ingresos') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($eventSummary['tickets'] as $summaryRow)
                        @php
                          $isPaidTicketSummary = (float) ($summaryRow['revenue'] ?? 0) > 0;
                        @endphp
                        <tr class="{{ $isPaidTicketSummary ? 'eb-type-row--paid' : 'eb-type-row--free' }}">
                          <td data-label="{{ __('Entrada') }}">
                            <span class="eb-type-name">{{ $summaryRow['ticket_name'] }}</span>
                            <span class="eb-type-badge {{ $isPaidTicketSummary ? 'eb-type-badge--paid' : 'eb-type-badge--free' }}">
                              {{ $isPaidTicketSummary ? __('Pago') : __('Gratis') }}
                            </span>
                          </td>
                          <td data-label="{{ __('Vendidas') }}"><span class="eb-pill {{ $isPaidTicketSummary ? 'eb-pill--paid' : 'eb-pill--free' }}">{{ number_format($summaryRow['sold'], 0, ',', '.') }}</span></td>
                          <td data-label="{{ __('Pendientes') }}">{{ number_format($summaryRow['pending'], 0, ',', '.') }}</td>
                          <td data-label="{{ __('Rechazadas') }}">{{ number_format($summaryRow['rejected'], 0, ',', '.') }}</td>
                          <td data-label="{{ __('Escaneo') }}">
                            <strong>{{ number_format($summaryRow['scanned'], 0, ',', '.') }}/{{ number_format($summaryRow['total'], 0, ',', '.') }}</strong>
                            <div class="eb-progress" aria-hidden="true"><span style="width: {{ $summaryRow['scan_percent'] }}%"></span></div>
                          </td>
                          <td data-label="{{ __('Ingresos') }}"><span class="eb-money">{{ $formatBaseMoney($summaryRow['revenue']) }}</span></td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </details>
              @endforeach
            </div>
          @endunless
        @endif
      </div>
    </section>

    @if ($focusedEventId)
    <div class="card">
      <div class="card-header eb-toolbar">
        <form id="bookingFiltersForm" action="{{ $bookingFiltersAction }}" method="GET">
          <div class="row align-items-end">
            <div class="col-xl-5 col-lg-6">
              <div class="form-group">
                <label for="bookingSearch">{{ __('Buscar comprador') }}</label>
                <input id="bookingSearch" name="search" type="search" class="form-control"
                  placeholder="{{ __('Nombre, apellido, email, teléfono o ID de reserva') }}"
                  value="{{ request()->input('search', request()->input('booking_id', request()->input('event_title'))) }}"
                  autocomplete="off">
              </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-6">
              <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit">
                  <i class="fas fa-search mr-1" aria-hidden="true"></i>{{ __('Filtrar') }}
                </button>
              </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-6">
              <div class="form-group">
                <button class="btn btn-outline-secondary btn-block" type="button" data-toggle="collapse"
                  data-target="#advancedBookingFilters" aria-expanded="{{ $hasAdvancedFilters ? 'true' : 'false' }}"
                  aria-controls="advancedBookingFilters">
                  <i class="fas fa-sliders-h mr-1" aria-hidden="true"></i>{{ __('Más filtros') }}
                </button>
              </div>
            </div>
            <div class="col-xl-3 text-xl-right">
              <div class="form-group">
                <a href="{{ $bookingFiltersAction }}" class="btn btn-light">
                  {{ __('Limpiar') }}
                </a>
                <button class="btn btn-danger d-none bulk-delete ml-2" type="button"
                  data-href="{{ route('admin.event_booking.bulk_delete') }}">
                  <i class="flaticon-interface-5 mr-1" aria-hidden="true"></i>{{ __('Eliminar') }}
                </button>
              </div>
            </div>
          </div>

          <div id="advancedBookingFilters" class="collapse {{ $hasAdvancedFilters ? 'show' : '' }}">
            <div class="row">
              <div class="col-xl-3 col-md-6">
                <div class="form-group">
                  <label for="bookingStatus">{{ __('Estado del pago') }}</label>
                  <select id="bookingStatus" class="form-control" name="status">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="completed" {{ request()->input('status') == 'completed' ? 'selected' : '' }}>{{ __('Completado') }}</option>
                    <option value="pending" {{ request()->input('status') == 'pending' ? 'selected' : '' }}>{{ __('Pendiente') }}</option>
                    <option value="free" {{ request()->input('status') == 'free' ? 'selected' : '' }}>{{ __('Gratis') }}</option>
                    <option value="rejected" {{ request()->input('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rechazado') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-xl-3 col-md-6">
                <div class="form-group">
                  <label for="bookingFromDate">{{ __('Desde') }}</label>
                  <input id="bookingFromDate" class="form-control" type="date" name="from_date"
                    value="{{ request()->input('from_date') }}">
                </div>
              </div>
              <div class="col-xl-3 col-md-6">
                <div class="form-group">
                  <label for="bookingToDate">{{ __('Hasta') }}</label>
                  <input id="bookingToDate" class="form-control" type="date" name="to_date"
                    value="{{ request()->input('to_date') }}">
                </div>
              </div>
              <div class="col-xl-3 col-md-6">
                <div class="form-group">
                  <label for="bookingDocument">{{ __('Documento') }}</label>
                  <input id="bookingDocument" class="form-control" type="text" name="document_number"
                    value="{{ request()->input('document_number') }}" placeholder="{{ __('DNI, CUIT o CUIL') }}">
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="card-body">
        @if (count($bookings) == 0)
          <div class="eb-empty">
            <i class="fas fa-search" aria-hidden="true"></i>
            <h3>{{ __('No se encontraron reservas') }}</h3>
            <p class="text-muted mb-0">{{ __('Probá limpiar filtros o buscar con otro dato.') }}</p>
          </div>
        @else
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-muted small">
              {{ __('Resultados') }}: {{ $bookings->total() }}
            </div>
          </div>

          <div class="table-responsive d-none d-xl-block">
            <table class="table eb-table">
              <thead>
                <tr>
                  <th scope="col">
                    <input type="checkbox" class="bulk-check" data-val="all" aria-label="{{ __('Seleccionar todas las reservas') }}">
                  </th>
                  <th scope="col">{{ __('Reserva') }}</th>
                  <th scope="col">{{ __('Evento') }}</th>
                  <th scope="col">{{ __('Comprador') }}</th>
                  <th scope="col">{{ __('Dinero') }}</th>
                  <th scope="col">{{ __('Estado') }}</th>
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
                    $organizerTotal = $booking->organizer ? (($booking->price ?? 0) - ($booking->commission ?? 0)) : null;
                    $platformTotal = $booking->organizer ? (($booking->tax ?? 0) + ($booking->commission ?? 0)) : $paidTotal;
                    $ticketBreakdown = $booking->ticketBreakdown();
                    $addonBreakdown = $booking->addonBreakdown();
                    $addonsCount = collect($addonBreakdown)->sum('quantity');
                    $addonsTotal = collect($addonBreakdown)->sum('subtotal');
                    $scannedCount = $booking->scannedTicketsCount();
                    $pendingScanCount = $booking->pendingTicketsCount();
                    $scanPercent = $booking->scanPercent();
                    $eventDateLabel = !empty($booking->event_date) ? \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y H:i') : '-';
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
                      <button class="btn btn-light eb-expand-btn mr-1" type="button"
                        data-target="#bookingDetail{{ $booking->id }}"
                        aria-expanded="false" aria-controls="bookingDetail{{ $booking->id }}"
                        aria-label="{{ __('Ver datos adicionales de la reserva') }} #{{ $booking->booking_id }}">
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                      </button>
                      <strong>#{{ Str::limit($booking->booking_id, 12, '') }}</strong>
                      <div class="eb-muted">{{ optional($booking->created_at)->format('d/m/Y H:i') }}</div>
                    </td>
                    <td>
                      @if ($eventInfo)
                        <a class="eb-title" href="{{ route('event.details', ['slug' => $slug, 'id' => $eventInfo->event_id]) }}"
                          target="_blank" rel="noopener" title="{{ $title }}">{{ $title }}</a>
                      @else
                        <span class="eb-title">-</span>
                      @endif
                      <span class="eb-muted">{{ __('Organizador') }}:
                        @if ($booking->organizer)
                          <a href="{{ route('admin.organizer_management.organizer_details', ['id' => $booking->organizer_id, 'language' => $defaultLanguageCode]) }}"
                            target="_blank">{{ Str::limit($booking->organizer->username, 22) }}</a>
                        @else
                          {{ __('Admin') }}
                        @endif
                      </span>
                      <span class="eb-muted">{{ __('Función') }}: {{ $eventDateLabel }}</span>
                    </td>
                    <td>
                      @include('backend.event.booking.partials.buyer-cell', ['booking' => $booking, 'defaultLanguageCode' => $defaultLanguageCode])
                    </td>
                    <td>
                      <div class="eb-money">{{ __('Cobrado') }}: {{ $formatMoney($paidTotal) }}</div>
                      <div class="eb-muted">{{ __('Neto org.') }}: {{ $organizerTotal !== null ? $formatMoney($organizerTotal) : '-' }}</div>
                      <div class="eb-muted">{{ __('Plataforma') }}: {{ $formatMoney($platformTotal) }}</div>
                    </td>
                    <td>
                      @if ($booking->gatewayType == 'offline' && $booking->paymentStatus == 'pending')
                        <form class="paymentStatusForm" action="{{ route('admin.event_booking.update_payment_status', $booking->id) }}" method="post">
                          @csrf
                          <select class="form-control paymentStatusBtn form-control-sm bg-warning text-dark" name="payment_status"
                            aria-label="{{ __('Cambiar estado de pago') }}">
                            <option value="completed">{{ __('Completado') }}</option>
                            <option value="pending" selected>{{ __('Pendiente') }}</option>
                            <option value="rejected">{{ __('Rechazado') }}</option>
                          </select>
                        </form>
                      @else
                        <span class="badge badge-{{ $status['class'] }} eb-status">
                          <i class="fas {{ $status['icon'] }}" aria-hidden="true"></i>{{ $status['label'] }}
                        </span>
                      @endif
                      <div class="eb-muted">{{ $booking->paymentMethod ?: '-' }}</div>
                    </td>
                    <td>
                      <div class="eb-scan-cell">
                        @if ($booking->isFullyScanned())
                          <span class="badge badge-success">{{ __('Completo') }}</span>
                        @elseif ((int) $booking->quantity <= 0)
                          <span class="badge badge-warning text-dark">{{ __('Datos incompletos') }}</span>
                          <div class="eb-muted">{{ __('Sin entradas registradas') }}</div>
                        @else
                          <strong>{{ $scannedCount }}/{{ $booking->quantity }}</strong>
                          <div class="eb-muted">{{ __('Faltan') }}: {{ $pendingScanCount }}</div>
                        @endif
                        <div class="eb-progress" aria-hidden="true"><span style="width: {{ $scanPercent }}%"></span></div>
                      </div>
                    </td>
                    <td>
                      <div class="eb-actions">
                        <a href="{{ route('admin.event_booking.details', ['id' => $booking->id]) }}"
                          class="btn btn-outline-primary eb-action-btn" title="{{ __('Ver detalles') }}"
                          aria-label="{{ __('Ver detalles de la reserva') }} #{{ $booking->booking_id }}">
                          <i class="fas fa-eye" aria-hidden="true"></i>
                        </a>
                        @if ($hasInvoiceFile)
                          <a href="{{ route('booking.ticket.download', $booking->id) }}"
                            class="btn btn-outline-secondary eb-action-btn" target="_blank" rel="noopener" title="{{ __('Descargar entrada') }}"
                            aria-label="{{ __('Descargar entrada de la reserva') }} #{{ $booking->booking_id }}">
                            <i class="fas fa-file-pdf" aria-hidden="true"></i>
                          </a>
                        @endif
                        @if (!is_null($booking->attachmentFile))
                          <a href="#" data-toggle="modal" data-target="#attachmentModal-{{ $booking->id }}"
                            class="btn btn-outline-info eb-action-btn" title="{{ __('Ver comprobante') }}"
                            aria-label="{{ __('Ver comprobante de la reserva') }} #{{ $booking->booking_id }}">
                            <i class="fas fa-paperclip" aria-hidden="true"></i>
                          </a>
                        @endif
                        <form class="deleteForm d-inline-block" action="{{ route('admin.event_booking.delete', ['id' => $booking->id]) }}" method="post">
                          @csrf
                          <button type="submit" class="btn btn-outline-danger eb-action-btn deleteBtn"
                            title="{{ __('Eliminar') }}" aria-label="{{ __('Eliminar reserva') }} #{{ $booking->booking_id }}">
                            <i class="fas fa-trash" aria-hidden="true"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                  <tr id="bookingDetail{{ $booking->id }}" class="eb-detail-row d-none">
                    <td colspan="8">
                      <div class="eb-detail-grid">
                        <div>
                          <span class="eb-detail-label">{{ __('Método de pago') }}</span>
                          <span class="eb-detail-value">{{ $booking->paymentMethod ?: '-' }}</span>
                        </div>
                        <div>
                          <span class="eb-detail-label">{{ __('Fecha / función') }}</span>
                          <span class="eb-detail-value">{{ $eventDateLabel }}</span>
                        </div>
                        <div>
                          <span class="eb-detail-label">{{ __('Cargo de servicio') }}</span>
                          <span class="eb-detail-value">{{ $formatMoney($booking->tax ?? 0) }} <span class="eb-muted">{{ $booking->tax_percentage ? '(' . $booking->tax_percentage . '%)' : '' }}</span></span>
                        </div>
                        <div>
                          <span class="eb-detail-label">{{ __('Comisión descontada') }}</span>
                          <span class="eb-detail-value">{{ $formatMoney($booking->commission ?? 0) }} <span class="eb-muted">{{ $booking->commission_percentage ? '(' . $booking->commission_percentage . '%)' : '' }}</span></span>
                        </div>
                        <div>
                          <span class="eb-detail-label">{{ __('Add-ons') }}</span>
                          <span class="eb-detail-value">{{ $addonsCount > 0 ? $addonsCount . ' - ' . $formatMoney($addonsTotal) : '-' }}</span>
                        </div>
                        <div>
                          <span class="eb-detail-label">{{ __('Documento') }}</span>
                          <span class="eb-detail-value">{{ $booking->fiscalProfile ? $booking->fiscalProfile->document_type . ' ' . $booking->fiscalProfile->document_number : '-' }}</span>
                        </div>
                        <div>
                          <span class="eb-detail-label">{{ __('Factura ARCA') }}</span>
                          <span class="eb-detail-value">
                            @if ($booking->arcaInvoice)
                              <a href="{{ route('admin.arca_invoices.show', $booking->arcaInvoice->id) }}">{{ strtoupper($booking->arcaInvoice->status) }}</a>
                            @else
                              -
                            @endif
                          </span>
                        </div>
                        <div class="eb-detail-section">
                          <span class="eb-detail-label">{{ __('Tipos de entrada') }}</span>
                          <div class="eb-mini-list">
                            @foreach ($ticketBreakdown as $ticketItem)
                              <div class="eb-mini-row">
                                <div>
                                  <span class="eb-mini-title">{{ $ticketItem['name'] }}</span>
                                  <span class="eb-muted">{{ __('Escaneo') }}: {{ $ticketItem['scanned'] }}/{{ $ticketItem['quantity'] }} · {{ __('Faltan') }} {{ $ticketItem['pending'] }}</span>
                                  <div class="eb-progress" aria-hidden="true"><span style="width: {{ $ticketItem['scan_percent'] }}%"></span></div>
                                </div>
                                <span class="eb-pill">{{ $ticketItem['quantity'] }} {{ $ticketItem['quantity'] == 1 ? __('entrada') : __('entradas') }}</span>
                                <span class="eb-detail-value">{{ $formatMoney($ticketItem['subtotal']) }}</span>
                              </div>
                            @endforeach
                          </div>
                        </div>
                        @if (count($addonBreakdown) > 0)
                          <div class="eb-detail-section">
                            <span class="eb-detail-label">{{ __('Detalle de add-ons') }}</span>
                            <div class="eb-mini-list">
                              @foreach ($addonBreakdown as $addonItem)
                                <div class="eb-mini-row">
                                  <div>
                                    <span class="eb-mini-title">{{ $addonItem['title'] }}</span>
                                    <span class="eb-muted">{{ $addonItem['redeemed'] ? __('Canjeado') : __('Pendiente de canje') }}</span>
                                  </div>
                                  <span class="eb-pill">{{ $addonItem['quantity'] }} x {{ $formatMoney($addonItem['unit_price']) }}</span>
                                  <span class="eb-detail-value">{{ $formatMoney($addonItem['subtotal']) }}</span>
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

          <div class="eb-mobile-list d-xl-none">
            @foreach ($bookings as $booking)
              @php
                $eventInfo = $eventInfos[$booking->event_id] ?? null;
                $title = $eventInfo ? $eventInfo->title : '-';
                $customer = $booking->customerInfo;
                $position = $booking->currencyTextPosition;
                $symbol = $booking->currencySymbol;
                $formatMoney = function ($amount) use ($position, $symbol) {
                    $amount = number_format((float) $amount, 0, ',', '.');
                    return ($position == 'left' ? $symbol : '') . $amount . ($position == 'right' ? $symbol : '');
                };
                $paidTotal = ($booking->price ?? 0) + ($booking->tax ?? 0);
                $organizerTotal = $booking->organizer ? (($booking->price ?? 0) - ($booking->commission ?? 0)) : null;
                $platformTotal = $booking->organizer ? (($booking->tax ?? 0) + ($booking->commission ?? 0)) : $paidTotal;
                $ticketBreakdown = $booking->ticketBreakdown();
                $addonBreakdown = $booking->addonBreakdown();
                $addonsCount = collect($addonBreakdown)->sum('quantity');
                $addonsTotal = collect($addonBreakdown)->sum('subtotal');
                $scannedCount = $booking->scannedTicketsCount();
                $pendingScanCount = $booking->pendingTicketsCount();
                $scanPercent = $booking->scanPercent();
                $eventDateLabel = !empty($booking->event_date) ? \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y H:i') : '-';
                $status = $statusOptions[$booking->paymentStatus] ?? ['label' => ucfirst((string) $booking->paymentStatus), 'class' => 'secondary', 'icon' => 'fa-circle'];
                $invoiceExtension = pathinfo((string) $booking->invoice, PATHINFO_EXTENSION);
                $hasInvoiceFile = $invoiceExtension == 'pdf' && $booking->hasInvoiceFile();
              @endphp
              <div class="eb-mobile-booking">
                <div class="eb-mobile-booking__head">
                  <div>
                    <div class="eb-mobile-booking__title">{{ Str::limit($title, 44) }}</div>
                    <div class="eb-muted">#{{ $booking->booking_id }}</div>
                    <div class="eb-muted">{{ __('Función') }}: {{ $eventDateLabel }}</div>
                  </div>
                  @if ($booking->gatewayType == 'offline' && $booking->paymentStatus == 'pending')
                    <form class="paymentStatusForm" action="{{ route('admin.event_booking.update_payment_status', $booking->id) }}" method="post">
                      @csrf
                      <select class="form-control paymentStatusBtn form-control-sm bg-warning text-dark" name="payment_status"
                        aria-label="{{ __('Cambiar estado de pago') }}">
                        <option value="completed">{{ __('Completado') }}</option>
                        <option value="pending" selected>{{ __('Pendiente') }}</option>
                        <option value="rejected">{{ __('Rechazado') }}</option>
                      </select>
                    </form>
                  @else
                    <span class="badge badge-{{ $status['class'] }} eb-status">
                      <i class="fas {{ $status['icon'] }}" aria-hidden="true"></i>{{ $status['label'] }}
                    </span>
                  @endif
                </div>

                <div class="eb-mobile-booking__grid">
                  <div>
                    <span class="eb-detail-label">{{ __('Comprador') }}</span>
                    <span class="eb-detail-value">
                      @include('backend.event.booking.partials.buyer-cell', ['booking' => $booking, 'defaultLanguageCode' => $defaultLanguageCode])
                    </span>
                  </div>
                  <div>
                    <span class="eb-detail-label">{{ __('Total cobrado') }}</span>
                    <span class="eb-detail-value">{{ $formatMoney($paidTotal) }}</span>
                    <span class="eb-muted">{{ __('Neto org.') }}: {{ $organizerTotal !== null ? $formatMoney($organizerTotal) : '-' }}</span>
                    <span class="eb-muted">{{ __('Plataforma') }}: {{ $formatMoney($platformTotal) }}</span>
                  </div>
                  <div>
                    <span class="eb-detail-label">{{ __('Escaneo') }}</span>
                    @if ((int) $booking->quantity <= 0)
                      <span class="eb-detail-value">{{ __('Datos incompletos') }}</span>
                      <span class="eb-muted">{{ __('Sin entradas registradas') }}</span>
                    @else
                      <span class="eb-detail-value">{{ $scannedCount }}/{{ $booking->quantity }}</span>
                      <span class="eb-muted">{{ __('Faltan') }}: {{ $pendingScanCount }}</span>
                    @endif
                    <div class="eb-progress" aria-hidden="true"><span style="width: {{ $scanPercent }}%"></span></div>
                  </div>
                  <div>
                    <span class="eb-detail-label">{{ __('Pago') }}</span>
                    <span class="eb-detail-value">{{ $booking->paymentMethod ?: '-' }}</span>
                  </div>
                </div>

                <div class="eb-mobile-extra">
                  <span class="eb-detail-label">{{ __('Entradas') }}</span>
                  <div class="eb-mini-list">
                    @foreach ($ticketBreakdown as $ticketItem)
                      <div class="eb-mini-row">
                        <div>
                          <span class="eb-mini-title">{{ $ticketItem['name'] }}</span>
                          <span class="eb-muted">{{ __('Escaneo') }}: {{ $ticketItem['scanned'] }}/{{ $ticketItem['quantity'] }}</span>
                        </div>
                        <span class="eb-pill">{{ $ticketItem['quantity'] }}</span>
                        <span class="eb-detail-value">{{ $formatMoney($ticketItem['subtotal']) }}</span>
                      </div>
                    @endforeach
                  </div>
                </div>

                @if (count($addonBreakdown) > 0)
                  <div class="eb-mobile-extra">
                    <span class="eb-detail-label">{{ __('Add-ons') }}: {{ $addonsCount }} · {{ $formatMoney($addonsTotal) }}</span>
                    <div class="eb-mini-list">
                      @foreach ($addonBreakdown as $addonItem)
                        <div class="eb-mini-row">
                          <div>
                            <span class="eb-mini-title">{{ $addonItem['title'] }}</span>
                            <span class="eb-muted">{{ $addonItem['redeemed'] ? __('Canjeado') : __('Pendiente de canje') }}</span>
                          </div>
                          <span class="eb-pill">{{ $addonItem['quantity'] }} x {{ $formatMoney($addonItem['unit_price']) }}</span>
                          <span class="eb-detail-value">{{ $formatMoney($addonItem['subtotal']) }}</span>
                        </div>
                      @endforeach
                    </div>
                  </div>
                @endif

                <div class="eb-actions">
                  <a href="{{ route('admin.event_booking.details', ['id' => $booking->id]) }}"
                    class="btn btn-outline-primary btn-sm" aria-label="{{ __('Ver detalles de la reserva') }} #{{ $booking->booking_id }}">
                    <i class="fas fa-eye mr-1" aria-hidden="true"></i>{{ __('Ver') }}
                  </a>
                  @if ($hasInvoiceFile)
                    <a href="{{ route('booking.ticket.download', $booking->id) }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
                      <i class="fas fa-file-pdf mr-1" aria-hidden="true"></i>{{ __('Entrada') }}
                    </a>
                  @endif
                  @if (!is_null($booking->attachmentFile))
                    <a href="#" data-toggle="modal" data-target="#attachmentModal-{{ $booking->id }}" class="btn btn-outline-info btn-sm">
                      <i class="fas fa-paperclip mr-1" aria-hidden="true"></i>{{ __('Comprobante') }}
                    </a>
                  @endif
                  <form class="deleteForm d-inline-block" action="{{ route('admin.event_booking.delete', ['id' => $booking->id]) }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm deleteBtn">
                      <i class="fas fa-trash mr-1" aria-hidden="true"></i>{{ __('Eliminar') }}
                    </button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>

          @foreach ($bookings as $booking)
            @includeIf('backend.event.booking.show-attachment')
          @endforeach
        @endif
      </div>

      @if (count($bookings) > 0)
        <div class="card-footer text-center">
          <div class="d-inline-block mt-3">
            {{ $bookings->links() }}
          </div>
        </div>
      @endif
    </div>
    @endif
  </div>
@endsection

@section('script')
  <script>
    'use strict';

    (function($) {
      var searchTimer = null;
      var initialSearch = $('#bookingSearch').val();

      $('#bookingSearch').on('input', function() {
        var value = $(this).val();

        clearTimeout(searchTimer);

        if (value.length > 0 && value.length < 3) {
          return;
        }

        searchTimer = setTimeout(function() {
          if (value !== initialSearch) {
            $('#bookingFiltersForm').trigger('submit');
          }
        }, 700);
      });

      $('.eb-expand-btn').on('click', function() {
        var target = $($(this).data('target'));
        var expanded = $(this).attr('aria-expanded') === 'true';

        target.toggleClass('d-none', expanded);
        $(this).attr('aria-expanded', expanded ? 'false' : 'true');
        $(this).find('i').toggleClass('fa-chevron-down', expanded).toggleClass('fa-chevron-up', !expanded);
      });
    })(jQuery);
  </script>
@endsection
