@php
  $breadcrumbSchemaItems = [
    ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => url('/')],
    ['@type' => 'ListItem', 'position' => 2, 'name' => !empty($title) ? $title : __('Página'), 'item' => url()->current()],
  ];
  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $breadcrumbSchemaItems,
  ];
@endphp
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
<!--====== PAGE TITLE PART START ======-->
<div class="page-title bg_cover pt-140 pb-140 lazy" @if (!empty($breadcrumb)) data-bg="{{ asset('assets/admin/img/' . $breadcrumb) }}" @endif>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-12">
        <div class="page-title-item text-center">
          <h3 class="title">{{ !empty($title) ? $title : '' }}</h3>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('Home') }}</a></li>
              <li class="breadcrumb-item active" aria-current="page">{{ !empty($title) ? $title : '' }}</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>
<!--====== PAGE TITLE PART END ======-->
