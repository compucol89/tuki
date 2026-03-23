@extends('backend.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('backend.partials.rtl-style')

@section('content')
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

  {{-- create modal --}}
  @include('backend.event.category.create')

  {{-- edit modal --}}
  @include('backend.event.category.edit')
@endsection

@section('style')
  <style>
    .category-index-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
      flex-wrap: wrap;
    }

    .category-index-header__eyebrow {
      display: inline-flex;
      align-items: center;
      padding: 6px 10px;
      border-radius: 999px;
      background: #e8f1ff;
      color: #1d4ed8;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      margin-bottom: 10px;
    }

    .category-index-header__title {
      margin-bottom: 6px;
      color: #0f172a;
      font-size: 28px;
      font-weight: 700;
    }

    .category-index-header__text {
      margin-bottom: 0;
      max-width: 620px;
      color: #64748b;
      line-height: 1.7;
    }

    .category-index-toolbar__group {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      justify-content: flex-end;
    }

    .category-index-toolbar__group .form-control {
      min-width: 210px;
      border-radius: 12px;
    }

    .category-index-add-btn,
    .category-index-bulk-delete {
      border-radius: 12px;
      padding-inline: 16px;
    }

    .category-index-filters {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 18px;
      flex-wrap: wrap;
      margin-bottom: 18px;
      padding: 18px;
      border: 1px solid #e5e7eb;
      border-radius: 18px;
      background: linear-gradient(180deg, #fcfdff 0%, #f8fbff 100%);
    }

    .category-index-stats {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .category-index-stat {
      min-width: 140px;
      padding: 12px 14px;
      border: 1px solid #dbe5f3;
      border-radius: 14px;
      background: #fff;
    }

    .category-index-stat__label {
      display: block;
      margin-bottom: 4px;
      color: #64748b;
      font-size: 12px;
    }

    .category-index-stat__value {
      color: #0f172a;
      font-size: 16px;
      font-weight: 700;
    }

    .category-index-filters__hint {
      max-width: 420px;
      color: #64748b;
      line-height: 1.7;
    }

    .category-index-empty {
      padding: 40px 20px;
      border: 1px dashed #d6d9e6;
      border-radius: 18px;
      background: #f8fafc;
      color: #64748b;
    }

    .category-index-empty h3 {
      margin-bottom: 10px;
      color: #0f172a;
      font-size: 24px;
      font-weight: 700;
    }

    .category-index-table-wrap {
      border: 1px solid #e5e7eb;
      border-radius: 18px;
      background: #fff;
      padding: 18px 18px 10px;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .6);
    }

    .category-index-table-wrap .dataTables_wrapper {
      padding: 0;
    }

    .category-index-table-wrap .row:first-child,
    .category-index-table-wrap .row:last-child {
      margin-left: 0;
      margin-right: 0;
      padding-inline: 2px;
    }

    .category-index-table-wrap .dataTables_length,
    .category-index-table-wrap .dataTables_filter {
      margin-bottom: 14px;
    }

    .category-index-table-wrap .dataTables_filter {
      text-align: right;
    }

    .category-index-table-wrap .dataTables_filter input,
    .category-index-table-wrap .dataTables_length select {
      border-radius: 10px;
      border: 1px solid #dbe5f3;
      min-height: 40px;
    }

    .category-index-table-wrap .dataTables_info,
    .category-index-table-wrap .dataTables_paginate {
      margin-top: 14px;
      padding-inline: 2px;
    }

    .category-index-table-wrap .table-responsive {
      margin: 0 -18px;
      padding: 0 18px;
    }

    .category-index-table {
      margin-top: 0 !important;
      margin-bottom: 0;
    }

    .category-index-table thead th {
      border-top: 0;
      border-bottom: 1px solid #e5e7eb;
      background: #f8fafc;
      color: #475569;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: .04em;
      text-transform: uppercase;
      vertical-align: middle;
    }

    .category-index-table tbody td {
      vertical-align: middle;
      border-top: 1px solid #eef2f7;
    }

    .category-index-thumb {
      width: 72px;
      height: 72px;
      object-fit: cover;
      border-radius: 16px;
      background: #f8fafc;
      border: 1px solid #e5e7eb;
      padding: 4px;
    }

    .category-index-name {
      color: #0f172a;
      font-weight: 600;
    }

    .category-index-badge {
      font-size: 12px;
      font-weight: 700;
      padding: 8px 10px;
      border-radius: 999px;
    }

    .category-index-action-btn {
      border-radius: 10px;
      min-width: 34px;
    }

    #createModal .modal-content,
    #editEventCategoryModal .modal-content {
      border: 0;
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 30px 60px rgba(15, 23, 42, .18);
    }

    #createModal .modal-header,
    #editEventCategoryModal .modal-header {
      padding: 22px 24px 16px;
      border-bottom: 1px solid #eef2f7;
    }

    #createModal .modal-title,
    #editEventCategoryModal .modal-title {
      color: #0f172a;
      font-size: 22px;
      font-weight: 700;
    }

    #createModal .modal-body,
    #editEventCategoryModal .modal-body {
      padding: 24px;
    }

    #createModal .modal-footer,
    #editEventCategoryModal .modal-footer {
      padding: 18px 24px 24px;
      border-top: 1px solid #eef2f7;
    }

    .category-modal-intro {
      margin-bottom: 18px;
      padding: 16px 18px;
      border: 1px solid #e5e7eb;
      border-radius: 18px;
      background: linear-gradient(180deg, #fcfdff 0%, #f8fbff 100%);
    }

    .category-modal-intro__eyebrow {
      display: inline-flex;
      align-items: center;
      margin-bottom: 8px;
      padding: 6px 10px;
      border-radius: 999px;
      background: #e8f1ff;
      color: #1d4ed8;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
    }

    .category-modal-intro__text {
      margin-bottom: 0;
      color: #64748b;
      line-height: 1.7;
    }

    .category-image-box {
      border: 1px dashed #cbd5e1;
      border-radius: 20px;
      background: #f8fafc;
      padding: 18px;
    }

    .category-image-box__header {
      margin-bottom: 14px;
    }

    .category-image-box__title {
      display: block;
      margin-bottom: 4px;
      color: #0f172a;
      font-weight: 700;
    }

    .category-image-box__text {
      color: #64748b;
      font-size: 14px;
      line-height: 1.7;
    }

    .category-image-box__body {
      display: flex;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .category-image-box .thumb-preview {
      margin-bottom: 0;
    }

    .category-image-box .thumb-preview img {
      width: 110px;
      height: 110px;
      object-fit: cover;
      border-radius: 18px;
      border: 1px solid #dbe5f3;
      background: #fff;
      padding: 4px;
    }

    .category-image-upload {
      position: relative;
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 14px 16px;
      border-radius: 16px;
      background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
      color: #fff;
      cursor: pointer;
      overflow: hidden;
      margin-bottom: 0;
      box-shadow: 0 18px 34px rgba(37, 99, 235, .22);
    }

    .category-image-upload input {
      position: absolute;
      inset: 0;
      opacity: 0;
      cursor: pointer;
    }

    .category-image-upload__icon {
      width: 42px;
      height: 42px;
      border-radius: 14px;
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
      color: #0f172a;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .category-form-group .form-control {
      border-radius: 12px;
      min-height: 46px;
    }
  </style>
@endsection
