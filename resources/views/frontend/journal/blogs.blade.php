@extends('frontend.layout')

@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->blog_page_title ?? __('Blog') }}
  @else
    {{ __('Blog') }}
  @endif
@endsection

@php
  $metaKeywords    = !empty($seo->meta_keyword_blog) ? $seo->meta_keyword_blog : '';
  $metaDescription = !empty($seo->meta_description_blog) ? $seo->meta_description_blog : '';
  $pageTitle       = !empty($pageHeading) ? ($pageHeading->blog_page_title ?? __('Blog')) : __('Blog');
  $activeCategory  = request()->input('category', '');
  $searchTitle     = request()->input('title', '');
@endphp
@section('meta-keywords', "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")

@section('custom-style')
<style>
  .main-header .header-upper { background: rgb(30,37,50); }
  .main-header .logo img, .main-header .logo-mobile img { filter: brightness(0) invert(1); }
  .main-header .main-menu .navigation li a { color: rgba(255,255,255,0.85); }
  .main-header .main-menu .navigation > li > a:hover { color: var(--primary-color); }
  .main-header .main-menu .navigation > li > a::after { background: var(--primary-color); }
</style>
@endsection

@section('content')

{{-- HEADER --}}
<div class="bl-header">
  <div class="container">
    <div class="bl-header__inner">
      <div>
        <h1 class="bl-header__title">{{ $pageTitle }}</h1>
        <p class="bl-header__sub">Ideas, novedades y consejos para organizadores y asistentes.</p>
      </div>
      <form class="bl-search" action="{{ route('blogs') }}" method="GET">
        @if($activeCategory)
          <input type="hidden" name="category" value="{{ $activeCategory }}">
        @endif
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="title" placeholder="Buscar artículos..." value="{{ $searchTitle }}">
        <button type="submit">Buscar</button>
      </form>
    </div>

    {{-- Category pills --}}
    <div class="bl-filters">
      <a href="{{ route('blogs', $searchTitle ? ['title' => $searchTitle] : []) }}"
         class="bl-filter{{ !$activeCategory ? ' bl-filter--active' : '' }}">
        Todos <span>{{ $allBlogs }}</span>
      </a>
      @foreach($categories as $cat)
        <a href="{{ route('blogs', array_filter(['category' => $cat->slug, 'title' => $searchTitle])) }}"
           class="bl-filter{{ $activeCategory === $cat->slug ? ' bl-filter--active' : '' }}">
          {{ $cat->name }} <span>{{ $cat->blogCount }}</span>
        </a>
      @endforeach
    </div>
  </div>
</div>

{{-- GRID --}}
<section class="bl-section">
  <div class="container">
    @if(count($blogs) === 0)
      <div class="bl-empty">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        <p>No se encontraron artículos.</p>
      </div>
    @else
      <div class="bl-grid">
        @foreach($blogs as $blog)
          @php
            $readTime = max(1, round(str_word_count(strip_tags($blog->content)) / 200));
          @endphp
          <a href="{{ route('blog_details', ['slug' => $blog->slug]) }}" class="bl-card">
            <div class="bl-card__visual">
              <div class="bl-card__img">
                <img data-src="{{ asset('assets/admin/img/blogs/' . $blog->image) }}"
                     src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                     class="lazy" alt="{{ $blog->title }}">
                <div class="bl-card__gradient"></div>
              </div>
              <div class="bl-card__overlay" aria-hidden="true">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
                <span class="bl-card__overlay-label">Leer artículo</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
              </div>
            </div>
            <div class="bl-card__body">
              <span class="bl-card__cat">{{ $blog->categoryName }}</span>
              <h2 class="bl-card__title">{{ $blog->title }}</h2>
              <p class="bl-card__excerpt">{{ Str::limit(strip_tags($blog->content), 120) }}</p>
            </div>
          </a>
        @endforeach
      </div>

      <div class="bl-pagination">
        {{ $blogs->appends(['title' => $searchTitle, 'category' => $activeCategory])->links() }}
      </div>
    @endif
  </div>
</section>

{{-- Hidden form para compatibilidad con blog.js --}}
<form class="d-none" action="{{ route('blogs') }}" method="GET">
  <input type="hidden" name="title" value="{{ $searchTitle }}">
  <input type="hidden" id="categoryKey" name="category">
  <button type="submit" id="submitBtn"></button>
</form>

@endsection

@section('script')
  <script type="text/javascript" src="{{ asset('assets/admin/js/blog.js') }}"></script>
@endsection
