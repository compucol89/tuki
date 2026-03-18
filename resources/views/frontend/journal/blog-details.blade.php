@extends('frontend.layout')

@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->blog_details_page_title ?? __('Blog Details') }}
  @else
    {{ __('Blog Details') }}
  @endif
@endsection

@section('metaKeywords') {{ $details->meta_keywords }} @endsection
@section('metaDescription') {{ $details->meta_description }} @endsection

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

{{-- ARTICLE --}}
<article class="bl-article">
  <div class="bl-article__header">
    <a href="{{ route('blogs', ['category' => $details->blogSlug]) }}" class="bl-article__cat">
      {{ $details->categoryName }}
    </a>
    <h1 class="bl-article__title">{{ $details->title }}</h1>
    <div class="bl-article__meta">
      <span>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        {{ \Carbon\Carbon::parse($details->created_at)->isoFormat('D [de] MMMM [de] YYYY') }}
      </span>
      @if($details->author)
        <span class="bl-article__dot"></span>
        <span>
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          {{ $details->author }}
        </span>
      @endif
      <span class="bl-article__dot"></span>
      <span>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        {{ $readTime }} min de lectura
      </span>
    </div>
  </div>
</article>

@if($details->image)
  <div class="bl-article__img-wrap">
    <img data-src="{{ asset('assets/admin/img/blogs/' . $details->image) }}"
         src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
         class="lazy bl-article__img" alt="{{ $details->title }}">
  </div>
@endif

<div class="bl-article">
  <div class="bl-article__body">
    <div class="summernote-content bl-article__content">
      {!! $details->content !!}
    </div>

    {{-- Share --}}
    <div class="bl-share">
      <span class="bl-share__label">Compartir</span>
      <a href="//www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
         target="_blank" class="bl-share__btn bl-share__btn--fb">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
        Facebook
      </a>
      <a href="//twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($details->title) }}"
         target="_blank" class="bl-share__btn bl-share__btn--tw">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
        Twitter
      </a>
      <a href="//www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($details->title) }}"
         target="_blank" class="bl-share__btn bl-share__btn--li">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
        LinkedIn
      </a>
    </div>
  </div>
</div>

{{-- Related posts --}}
@if(count($relatedBlogs) > 0)
  <section class="bl-related">
    <div class="container">
      <h2 class="bl-related__title">Artículos relacionados</h2>
      <div class="bl-grid">
        @foreach($relatedBlogs as $rel)
          @php $relReadTime = max(1, round(str_word_count(strip_tags($rel->content)) / 200)); @endphp
          <a href="{{ route('blog_details', ['slug' => $rel->slug]) }}" class="bl-card">
            <div class="bl-card__visual">
              <div class="bl-card__img">
                <img data-src="{{ asset('assets/admin/img/blogs/' . $rel->image) }}"
                     src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                     class="lazy" alt="{{ $rel->title }}">
                <div class="bl-card__gradient"></div>
              </div>
              <div class="bl-card__overlay" aria-hidden="true">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
                <span class="bl-card__overlay-label">Leer artículo</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
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
  <div class="container" style="max-width:720px;margin:0 auto;padding-bottom:80px">
    <div id="disqus_thread"></div>
  </div>
@endif

{{-- Hidden form para compatibilidad con blog.js --}}
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
