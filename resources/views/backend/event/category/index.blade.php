@extends('backend.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@section('content')
  <div class="admin-category-index">
  <div class="page-header">
    <h4 class="page-title">{{ __('Categories') }}</h4>
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
        <a href="#">{{ __('Categories') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="category-index-header">
            <div class="category-index-header__intro">
              <span class="category-index-header__eyebrow">{{ __('Gestion') }}</span>
              <h3 class="category-index-header__title">{{ __('Categorias de eventos') }}</h3>
              <p class="category-index-header__text">{{ __('Organiza mejor tu catalogo, destaca las categorias clave y manten el listado claro para cargar eventos mas rapido.') }}</p>
            </div>

            <div class="category-index-toolbar">
              <div class="category-index-toolbar__group">
                @if (!empty($langs) && count($langs) > 1)
                  @includeIf('backend.partials.languages')
                @endif

                <a href="#" data-toggle="modal" data-target="#createModal"
                  class="btn btn-primary category-index-add-btn"><i class="fas fa-plus"></i>
                  {{ __('Nueva categoria') }}</a>

                <button class="btn btn-danger d-none bulk-delete category-index-bulk-delete"
                  data-href="{{ route('admin.event_management.bulk_delete_category') }}">
                  <i class="flaticon-interface-5"></i> {{ __('Eliminar seleccionadas') }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              <div class="category-index-filters">
                <div class="category-index-stats">
                  <div class="category-index-stat">
                    <span class="category-index-stat__label">{{ __('Idioma activo') }}</span>
                    <strong class="category-index-stat__value">{{ $language->name }}</strong>
                  </div>
                  <div class="category-index-stat">
                    <span class="category-index-stat__label">{{ __('Categorias') }}</span>
                    <strong class="category-index-stat__value">{{ $categories->count() }}</strong>
                  </div>
                  <div class="category-index-stat">
                    <span class="category-index-stat__label">{{ __('Destacadas') }}</span>
                    <strong class="category-index-stat__value">{{ $categories->where('is_featured', 'yes')->count() }}</strong>
                  </div>
                </div>

                <p class="category-index-filters__hint mb-0">{{ __('Consejo: usa pocas categorias bien definidas y nombres faciles de entender para quien carga eventos.') }}</p>
              </div>

              @if (count($categories) == 0)
                <div class="category-index-empty text-center">
                  <h3>{{ __('Todavia no hay categorias cargadas') }}</h3>
                  <p class="mb-0">{{ __('Crea la primera categoria para empezar a ordenar mejor tus eventos.') }}</p>
                </div>
              @else
                <div class="category-index-table-wrap">
                  <table class="table category-index-table mt-3" id="basic-datatables">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col">{{ __('Image') }}</th>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                        <th scope="col">{{ __('Serial Number') }}</th>

                        <th scope="col">{{ __('Featured') }}</th>

                        <th scope="col">{{ __('Actions') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($categories as $category)
                        <tr>
                          <td>
                            <input type="checkbox" class="bulk-check" data-val="{{ $category->id }}">
                          </td>
                          <td>
                            <img src="{{ asset('assets/admin/img/event-category/' . $category->image) }}"
                              class="img-fluid mh60 category-index-thumb" alt="{{ $category->name }}">
                          </td>
                          <td>
                            <span class="category-index-name">{{ strlen($category->name) > 50 ? mb_substr($category->name, 0, 50, 'UTF-8') . '...' : $category->name }}</span>
                          </td>
                          <td>
                            @if ($category->status == 1)
                              <h2 class="d-inline-block"><span class="badge badge-success category-index-badge">{{ __('Activa') }}</span>
                              </h2>
                            @else
                              <h2 class="d-inline-block"><span class="badge badge-danger category-index-badge">{{ __('Inactiva') }}</span>
                              </h2>
                            @endif
                          </td>
                          <td>{{ $category->serial_number }}</td>

                          <td>
                            @if ($category->is_featured == 'yes')
                              <h2 class="d-inline-block"><span class="badge badge-success category-index-badge">{{ __('Si') }}</span>
                              </h2>
                            @else
                              <h2 class="d-inline-block"><span class="badge badge-danger category-index-badge">{{ __('No') }}</span></h2>
                            @endif
                          </td>

                          <td>
                            <a class="btn btn-secondary btn-xs mr-1 mt-1 category-index-action-btn editBtn" href="#"
                              data-toggle="modal" data-target="#editEventCategoryModal" data-id="{{ $category->id }}"
                              data-icon="{{ $category->icon }}" data-color="{{ $category->color }}"
                              data-name="{{ $category->name }}" data-status="{{ $category->status }}"
                              data-serial_number="{{ $category->serial_number }}"
                              data-is_featured="{{ $category->is_featured }}"
                              data-image="{{ asset('assets/admin/img/event-category/' . $category->image) }}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                            </a>

                            <form class="deleteForm d-inline-block"
                              action="{{ route('admin.event_management.delete_category', ['id' => $category->id]) }}"
                              method="post">

                              @csrf
                              <button type="submit" class="btn btn-danger mt-1 btn-xs deleteBtn category-index-action-btn">
                                <span class="btn-label">
                                  <i class="fas fa-trash"></i>
                                </span>
                              </button>
                            </form>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
          </div>
        </div>

        <div class="card-footer"></div>
      </div>
    </div>
  </div>
  </div>

  {{-- create modal --}}
  @include('backend.event.category.create')

  {{-- edit modal --}}
  @include('backend.event.category.edit')
@endsection

@section('script')
  <script>
    $(document).ready(function() {
      $('#basic-datatables').DataTable({
        destroy: true,
        ordering: false,
        responsive: true,
        language: {
          "decimal": "",
          "emptyTable": "No hay información",
          "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
          "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
          "infoFiltered": "(Filtrado de _MAX_ entradas totales)",
          "infoPostFix": "",
          "thousands": ",",
          "lengthMenu": "Mostrar _MENU_ entradas",
          "loadingRecords": "Cargando...",
          "processing": "Procesando...",
          "search": "Buscar:",
          "zeroRecords": "Sin resultados encontrados",
          "paginate": {
            "first": "Primero",
            "last": "Último",
            "next": "Siguiente",
            "previous": "Anterior"
          }
        }
      });
    });
  </script>
@endsection

@section('style')
  <style>
    .admin-category-index {
      --cat-ink: #1e2532;
      --cat-ink-strong: #111827;
      --cat-muted: #667085;
      --cat-border: #e4e7ec;
      --cat-soft: #f8fafc;
      --cat-card: #ffffff;
      --cat-filters-bg: linear-gradient(180deg, #fcfdff 0%, #f8fbff 100%);
      --cat-stat-border: #dbe5f3;
      --cat-accent-soft: #e8f1ff;
      --cat-accent: #1d4ed8;
      --cat-radius: 10px;
      color: var(--cat-ink);
    }

    .admin-category-index .card {
      background: var(--cat-card) !important;
      border-color: var(--cat-border) !important;
    }

    .admin-category-index .card-header,
    .admin-category-index .card-footer {
      background: var(--cat-soft) !important;
      border-color: var(--cat-border) !important;
    }

    .category-index-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 16px;
      flex-wrap: wrap;
    }

    .category-index-header__eyebrow {
      display: inline-flex;
      align-items: center;
      padding: 5px 10px;
      border-radius: 999px;
      background: var(--cat-accent-soft);
      color: var(--cat-accent);
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .category-index-header__title {
      margin-bottom: 6px;
      color: var(--cat-ink-strong);
      font-size: 24px;
      font-weight: 700;
    }

    .category-index-header__text {
      margin-bottom: 0;
      max-width: 620px;
      color: var(--cat-muted);
      line-height: 1.55;
      font-size: 13.5px;
    }

    .category-index-toolbar__group {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: flex-end;
    }

    .category-index-toolbar__group .form-control {
      min-width: 210px;
      border-radius: var(--cat-radius);
    }

    .category-index-add-btn,
    .category-index-bulk-delete {
      border-radius: var(--cat-radius);
      min-height: 40px;
      padding-inline: 16px;
    }

    .category-index-filters {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
      margin-bottom: 16px;
      padding: 14px 16px;
      border: 1px solid var(--cat-border);
      border-radius: var(--cat-radius);
      background: var(--cat-filters-bg);
    }

    .category-index-stats {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .category-index-stat {
      min-width: 128px;
      padding: 10px 12px;
      border: 1px solid var(--cat-stat-border);
      border-radius: 8px;
      background: var(--cat-card);
    }

    .category-index-stat__label {
      display: block;
      margin-bottom: 4px;
      color: var(--cat-muted);
      font-size: 11.5px;
    }

    .category-index-stat__value {
      color: var(--cat-ink-strong);
      font-size: 15px;
      font-weight: 700;
    }

    .category-index-filters__hint {
      max-width: 420px;
      color: var(--cat-muted);
      line-height: 1.55;
      font-size: 13px;
    }

    .category-index-empty {
      padding: 32px 20px;
      border: 1px dashed var(--cat-border);
      border-radius: var(--cat-radius);
      background: var(--cat-soft);
      color: var(--cat-muted);
    }

    .category-index-empty h3 {
      margin-bottom: 8px;
      color: var(--cat-ink-strong);
      font-size: 20px;
      font-weight: 700;
    }

    .category-index-table-wrap {
      border: 1px solid var(--cat-border);
      border-radius: var(--cat-radius);
      background: var(--cat-card);
      padding: 14px 14px 8px;
      box-shadow: none;
    }

    .category-index-table-wrap .dataTables_wrapper {
      padding: 0;
    }

    .category-index-table-wrap .row:first-child,
    .category-index-table-wrap .row:last-child {
      margin-left: 0;
      margin-right: 0;
      align-items: center;
      padding-inline: 0;
    }

    .category-index-table-wrap .dataTables_length,
    .category-index-table-wrap .dataTables_filter {
      margin-bottom: 12px;
      color: var(--cat-muted);
    }

    .category-index-table-wrap .dataTables_filter {
      text-align: right;
    }

    .category-index-table-wrap .dataTables_filter input,
    .category-index-table-wrap .dataTables_length select {
      border-radius: 8px;
      border: 1px solid var(--cat-border) !important;
      background: var(--cat-soft) !important;
      color: var(--cat-ink) !important;
      min-height: 38px;
      padding: 6px 10px;
    }

    .category-index-table-wrap .dataTables_filter input:focus,
    .category-index-table-wrap .dataTables_length select:focus {
      border-color: var(--adm-primary, #f97316) !important;
      outline: 0;
      box-shadow: 0 0 0 3px var(--adm-ring, rgba(249, 115, 22, .25));
    }

    .category-index-table-wrap .dataTables_info,
    .category-index-table-wrap .dataTables_paginate {
      margin-top: 12px;
      padding-inline: 0;
      color: var(--cat-muted);
    }

    .category-index-table-wrap .table-responsive {
      margin: 0;
      padding: 0;
    }

    .category-index-table {
      margin-top: 0 !important;
      margin-bottom: 0;
    }

    .category-index-table thead th {
      border-top: 0;
      border-bottom: 1px solid var(--cat-border) !important;
      background: var(--cat-soft) !important;
      color: var(--cat-muted) !important;
      font-size: 11.5px;
      font-weight: 700;
      letter-spacing: .04em;
      text-transform: uppercase;
      vertical-align: middle;
      padding: 12px 10px;
    }

    .category-index-table tbody td {
      vertical-align: middle;
      border-top: 1px solid var(--cat-border) !important;
      color: var(--cat-ink) !important;
      padding: 10px;
    }

    .category-index-thumb {
      width: 52px;
      height: 52px;
      object-fit: cover;
      border-radius: 8px;
      background: var(--cat-soft);
      border: 1px solid var(--cat-border);
      padding: 2px;
    }

    .category-index-name {
      color: var(--cat-ink-strong) !important;
      font-weight: 650;
      font-size: 14px;
    }

    .category-index-badge {
      font-size: 12px;
      font-weight: 700;
      padding: 6px 10px;
      border-radius: 999px;
    }

    .category-index-action-btn {
      border-radius: 8px;
      min-width: 34px;
      min-height: 34px;
    }

    #createModal .modal-content,
    #editEventCategoryModal .modal-content {
      border: 0;
      border-radius: 16px;
      overflow: hidden;
      background: var(--adm-card, #fff);
      box-shadow: 0 30px 60px rgba(15, 23, 42, .18);
    }

    #createModal .modal-header,
    #editEventCategoryModal .modal-header {
      padding: 18px 20px 14px;
      border-bottom: 1px solid var(--adm-border, #eef2f7);
      background: var(--adm-bg-soft, #f8fafc);
    }

    #createModal .modal-title,
    #editEventCategoryModal .modal-title {
      color: var(--adm-ink-strong, #0f172a);
      font-size: 20px;
      font-weight: 700;
    }

    #createModal .modal-body,
    #editEventCategoryModal .modal-body {
      padding: 20px;
      background: var(--adm-card, #fff);
      color: var(--adm-ink, #1e2532);
    }

    #createModal .modal-footer,
    #editEventCategoryModal .modal-footer {
      padding: 14px 20px 20px;
      border-top: 1px solid var(--adm-border, #eef2f7);
      background: var(--adm-bg-soft, #f8fafc);
    }

    .category-modal-intro {
      margin-bottom: 16px;
      padding: 14px 16px;
      border: 1px solid var(--adm-border, #e5e7eb);
      border-radius: 10px;
      background: var(--adm-bg-soft, #f8fafc);
    }

    .category-modal-intro__eyebrow {
      display: inline-flex;
      align-items: center;
      margin-bottom: 8px;
      padding: 5px 10px;
      border-radius: 999px;
      background: var(--cat-accent-soft, #e8f1ff);
      color: var(--cat-accent, #1d4ed8);
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
    }

    .category-modal-intro__text {
      margin-bottom: 0;
      color: var(--adm-muted);
      line-height: 1.55;
    }

    .category-image-box {
      border: 1px dashed var(--adm-border, #cbd5e1);
      border-radius: 12px;
      background: var(--adm-bg-soft, #f8fafc);
      padding: 16px;
    }

    .category-image-box__header {
      margin-bottom: 12px;
    }

    .category-image-box__title {
      display: block;
      margin-bottom: 4px;
      color: var(--adm-ink-strong, #0f172a);
      font-weight: 700;
    }

    .category-image-box__text {
      color: var(--adm-muted);
      font-size: 13.5px;
      line-height: 1.55;
    }

    .category-image-box__body {
      display: flex;
      align-items: center;
      gap: 14px;
      flex-wrap: wrap;
    }

    .category-image-box .thumb-preview {
      margin-bottom: 0;
    }

    .category-image-box .thumb-preview img {
      width: 96px;
      height: 96px;
      object-fit: cover;
      border-radius: 10px;
      border: 1px solid var(--adm-border, #dbe5f3);
      background: var(--adm-card, #fff);
      padding: 3px;
    }

    .category-image-upload {
      position: relative;
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 12px 14px;
      border-radius: 12px;
      background: linear-gradient(135deg, #c2410c 0%, #f97316 100%);
      color: #fff;
      cursor: pointer;
      overflow: hidden;
      margin-bottom: 0;
      box-shadow: 0 12px 24px rgba(249, 115, 22, .22);
    }

    .category-image-upload input {
      position: absolute;
      inset: 0;
      opacity: 0;
      cursor: pointer;
    }

    .category-image-upload__icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, .18);
      font-size: 18px;
    }

    .category-image-upload__copy {
      display: flex;
      flex-direction: column;
      line-height: 1.35;
    }

    .category-image-upload__copy strong {
      font-size: 14px;
      font-weight: 700;
    }

    .category-image-upload__copy span {
      font-size: 12px;
      opacity: .92;
    }

    .category-form-group label {
      color: var(--adm-ink-strong, #0f172a);
      font-weight: 700;
      margin-bottom: 8px;
    }

    .category-form-group .form-control {
      border-radius: 10px;
      min-height: 44px;
    }

    /* Dark: theme coverage + contraste AA */
    html[data-theme="dark"] .admin-category-index {
      --cat-ink: #e5e5e5;
      --cat-ink-strong: #ffffff;
      --cat-muted: #a3a3a3;
      --cat-border: #3d4354;
      --cat-soft: #1f2838;
      --cat-card: #2a3040;
      --cat-filters-bg: linear-gradient(180deg, #252b38 0%, #1f2838 100%);
      --cat-stat-border: #3d4354;
      --cat-accent-soft: #2a3656;
      --cat-accent: #93c5fd;
    }

    html[data-theme="dark"] .admin-category-index .category-index-name {
      color: #ffffff !important;
    }

    html[data-theme="dark"] .admin-category-index .category-index-table tbody td,
    html[data-theme="dark"] .admin-category-index .category-index-table thead th {
      color: var(--cat-ink) !important;
      border-color: var(--cat-border) !important;
    }

    html[data-theme="dark"] #createModal .modal-content,
    html[data-theme="dark"] #editEventCategoryModal .modal-content,
    html[data-theme="dark"] #createModal .modal-body,
    html[data-theme="dark"] #editEventCategoryModal .modal-body {
      background: var(--adm-card, #2a3040) !important;
      color: var(--adm-ink, #e5e5e5) !important;
    }

    html[data-theme="dark"] #createModal .modal-header,
    html[data-theme="dark"] #editEventCategoryModal .modal-header,
    html[data-theme="dark"] #createModal .modal-footer,
    html[data-theme="dark"] #editEventCategoryModal .modal-footer,
    html[data-theme="dark"] .category-modal-intro,
    html[data-theme="dark"] .category-image-box {
      background: var(--adm-bg-soft, #2a303e) !important;
      border-color: var(--adm-border, #3d4354) !important;
    }

    html[data-theme="dark"] #createModal .modal-title,
    html[data-theme="dark"] #editEventCategoryModal .modal-title,
    html[data-theme="dark"] .category-image-box__title,
    html[data-theme="dark"] .category-form-group label {
      color: var(--adm-ink-strong, #ffffff) !important;
    }

    html[data-theme="dark"] .category-modal-intro__eyebrow {
      background: #2a3656;
      color: #93c5fd;
    }
  </style>
@endsection


