@extends('frontend.layout')

@section('body-class', 'blog-site')

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

@section('content')

{{-- HEADER — hero editorial + búsqueda (premium) --}}
<div class="bl-header">
  <div class="bl-header__ambient" aria-hidden="true"></div>
  <div class="bl-header__grain" aria-hidden="true"></div>
  <div class="container bl-header__container">
    <div class="bl-header__inner">
      <div class="bl-header__intro">
        <p class="bl-header__kicker">{{ __('Blog') }}</p>
        <h1 class="bl-header__title">{{ $pageTitle }}</h1>
        <p class="bl-header__sub">Ideas, novedades y consejos para organizadores y asistentes.</p>
      </div>
      <form class="bl-search" action="{{ route('blogs') }}" method="GET" role="search" aria-label="Buscar artículos del blog">
        @if($activeCategory)
          <input type="hidden" name="category" value="{{ $activeCategory }}">
        @endif
        <div class="bl-search__shell">
          <span class="bl-search__icon-wrap" aria-hidden="true">
            <svg class="bl-search__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </span>
          <input type="search" name="title" class="bl-search__input" placeholder="Buscar artículos…" value="{{ $searchTitle }}" autocomplete="off" inputmode="search">
          <button type="submit" class="bl-search__btn">Buscar</button>
        </div>
      </form>
    </div>

    {{-- Category pills --}}
    <nav class="bl-filters" aria-label="Categorías del blog">
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
    </nav>
  </div>
</div>

{{-- GRID — tarjetas editoriales premium --}}
<section class="bl-section" aria-label="Artículos del blog">
  <div class="bl-section__bg" aria-hidden="true"></div>
  <div class="container bl-section__container">
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
            <span class="bl-card__glow" aria-hidden="true"></span>
            <div class="bl-card__visual">
              <div class="bl-card__img">
                <img data-src="{{ asset('assets/admin/img/blogs/' . $blog->image) }}"
                     src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                     class="lazy bl-card__photo" alt="{{ $blog->title }}">
                <div class="bl-card__gradient" aria-hidden="true"></div>
              </div>
              <div class="bl-card__overlay" aria-hidden="true">
                <span class="bl-card__overlay-inner">
                  <svg class="bl-card__overlay-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
                  <span class="bl-card__overlay-label">Leer artículo</span>
                  <svg class="bl-card__overlay-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </span>
              </div>
            </div>
            <div class="bl-card__body">
              <div class="bl-card__eyebrow">
                <span class="bl-card__cat">{{ $blog->categoryName }}</span>
                <span class="bl-card__meta-sep" aria-hidden="true"></span>
                <span class="bl-card__time">{{ $readTime }} min</span>
              </div>
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

    @if (!empty(showAd(3)))
      <aside class="bl-ad-slot" aria-label="{{ __('Publicidad') }}">
        <p class="bl-ad-slot__eyebrow">{{ __('Patrocinado') }}</p>
        {!! showAd(3) !!}
      </aside>
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
