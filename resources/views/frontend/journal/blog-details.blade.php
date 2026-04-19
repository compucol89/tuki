@extends('frontend.layout')

@section('body-class', 'blog-site blog-detail')

@section('pageHeading')
  {{ $details->title }}
@endsection

@section('meta-keywords', $details->meta_keywords)
@section('meta-description', $details->meta_description)

@php
  $og_title       = $details->title;
  $og_description = strip_tags($details->content);
  $og_image       = asset('assets/admin/img/blogs/' . $details->image);
  $readTime       = max(1, round(str_word_count(strip_tags($details->content)) / 200));
@endphp

@section('og-title', "$og_title")
@section('og-description', "$og_description")
@section('og-image', "$og_image")
@section('custom-style')
  <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-content.css') }}">
@endsection

@section('content')

<div class="bl-detail">
  <div class="bl-detail__ambient" aria-hidden="true"></div>
  <div class="bl-detail__grain" aria-hidden="true"></div>
  <div class="bl-detail__mesh" aria-hidden="true"></div>

  <div class="container bl-detail__shell">
    <article class="bl-article bl-article--premium" itemscope itemtype="https://schema.org/BlogPosting">
      <header class="bl-article__header">
        <a href="{{ route('blogs', ['category' => $details->blogSlug]) }}" class="bl-article__cat">
          <span class="bl-article__cat-pill">{{ $details->categoryName }}</span>
        </a>
        <h1 class="bl-article__title" itemprop="headline">{{ $details->title }}</h1>
        <div class="bl-article__meta">
          <span class="bl-article__meta-chip">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <time datetime="{{ \Carbon\Carbon::parse($details->created_at)->toIso8601String() }}" itemprop="datePublished">{{ \Carbon\Carbon::parse($details->created_at)->isoFormat('D [de] MMMM [de] YYYY') }}</time>
          </span>
          @if($details->author)
            <span class="bl-article__meta-chip">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <span itemprop="author">{{ $details->author }}</span>
            </span>
          @endif
          <span class="bl-article__meta-chip">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ $readTime }} {{ __('min de lectura') }}
          </span>
        </div>
        <nav class="bl-detail__crumb" aria-label="{{ __('Navegación') }}">
          <a href="{{ route('blogs') }}" class="bl-detail__crumb-link">{{ __('Blog') }}</a>
          <span class="bl-detail__crumb-sep" aria-hidden="true">/</span>
          <a href="{{ route('blogs', ['category' => $details->blogSlug]) }}" class="bl-detail__crumb-link">{{ $details->categoryName }}</a>
          <span class="bl-detail__crumb-sep" aria-hidden="true">/</span>
          <span class="bl-detail__crumb-current">{{ Str::limit($details->title, 48) }}</span>
        </nav>
      </header>

      @if($details->image)
        <figure class="bl-article__figure">
          <div class="bl-article__img-frame">
            <img data-src="{{ asset('assets/admin/img/blogs/' . $details->image) }}"
                 src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                 class="lazy bl-article__img" alt="{{ $details->title }}" itemprop="image">
          </div>
        </figure>
      @endif

      <div class="bl-article__body">
        <div class="summernote-content bl-article__content" itemprop="articleBody">
          {!! $details->content !!}
        </div>

        <div class="bl-share">
          <span class="bl-share__label">{{ __('Compartir') }}</span>
          <a href="//www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
             target="_blank" rel="noopener noreferrer" class="bl-share__btn bl-share__btn--fb">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            Facebook
          </a>
          <a href="//twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($details->title) }}"
             target="_blank" rel="noopener noreferrer" class="bl-share__btn bl-share__btn--tw">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
            Twitter
          </a>
          <a href="//www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($details->title) }}"
             target="_blank" rel="noopener noreferrer" class="bl-share__btn bl-share__btn--li">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
            LinkedIn
          </a>
        </div>
      </div>
    </article>
  </div>
</div>

@if(count($relatedBlogs) > 0)
  <section class="bl-related bl-related--detail" aria-labelledby="bl-related-heading">
    <div class="bl-related__bg" aria-hidden="true"></div>
    <div class="container bl-related__inner">
      <div class="bl-related__head">
        <h2 id="bl-related-heading" class="bl-related__title">{{ __('Artículos relacionados') }}</h2>
        <p class="bl-related__sub">{{ __('Seguí leyendo en la misma línea editorial.') }}</p>
      </div>
      <div class="bl-grid bl-grid--detail">
        @foreach($relatedBlogs as $rel)
          @php $relReadTime = max(1, round(str_word_count(strip_tags($rel->content)) / 200)); @endphp
          <a href="{{ route('blog_details', ['slug' => $rel->slug]) }}" class="bl-card">
            <span class="bl-card__glow" aria-hidden="true"></span>
            <div class="bl-card__visual">
              <div class="bl-card__img">
                <img data-src="{{ asset('assets/admin/img/blogs/' . $rel->image) }}"
                     src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                     class="lazy bl-card__photo" alt="{{ $rel->title }}">
                <div class="bl-card__gradient" aria-hidden="true"></div>
              </div>
              <div class="bl-card__overlay" aria-hidden="true">
                <span class="bl-card__overlay-inner">
                  <svg class="bl-card__overlay-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
                  <span class="bl-card__overlay-label">{{ __('Leer artículo') }}</span>
                  <svg class="bl-card__overlay-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </span>
              </div>
            </div>
            <div class="bl-card__body">
              <span class="bl-card__cat">{{ $rel->categoryName }}</span>
              <h3 class="bl-card__title">{{ $rel->title }}</h3>
              <p class="bl-card__excerpt">{{ Str::limit(strip_tags($rel->content), 100) }}</p>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </section>
@endif

@if($disqusInfo->disqus_status == 1)
  <div class="container bl-disqus">
    <div id="disqus_thread"></div>
  </div>
@endif

<form class="d-none" action="{{ route('blogs') }}" method="GET">
  <input type="hidden" id="categoryKey" name="category">
  <button type="submit" id="submitBtn"></button>
</form>

@endsection

@section('script')
<script>
  "use strict";
  const shortName = '{{ $disqusInfo->disqus_short_name }}';
</script>
<script type="text/javascript" src="{{ asset('assets/admin/js/blog.js') }}"></script>
@endsection
