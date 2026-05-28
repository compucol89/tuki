{{-- Features + testimonios (antes en home; ahora en /organizadores) --}}
@php
  $secInfo = $secInfo ?? null;
  $featureEventSection = $featureEventSection ?? null;
  $featureEventItems = $featureEventItems ?? collect();
  $testimonialData = $testimonialData ?? null;
  $testimonials = $testimonials ?? collect();
  $partners = $partners ?? collect();
@endphp

@if ($secInfo && $secInfo->features_section_status == 1)
  <section class="feature-section bg-lighter reveal-on-scroll">
    <div class="container">
      <div class="feature-shell">
        <div class="feature-shell__intro">
          <div class="section-title text-center mb-55">
            <h2>{{ $featureEventSection ? $featureEventSection->title : '' }}</h2>
            <p>{{ $featureEventSection ? $featureEventSection->text : '' }}</p>
          </div>
        </div>
        @if ($featureEventItems->isEmpty())
          <h2>{{ __('Pronto vas a ver más razones para elegir Tukipass.') }}</h2>
        @endif
        <div class="row justify-content-center feature-grid">
          @foreach ($featureEventItems as $item)
            <div class="col-xl-4 col-md-6">
              <div class="feature-item">
                <div class="feature-item__icon" aria-hidden="true">
                  <i class="{{ $item->icon }}"></i>
                </div>
                <div class="feature-content">
                  <h5>{{ $item->title }}</h5>
                  <p>{{ $item->text }}</p>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>
@endif

@if ($secInfo && $secInfo->testimonials_section_status == 1)
  <section class="testimonial-section reveal-on-scroll">
    <div class="container">
      <div class="row pb-20 rpb-20">
        <div class="col-lg-4">
          <div class="testimonial-content pt-10 rmb-55">
            <div class="section-title mb-30">
              <h2>{{ $testimonialData ? $testimonialData->title : __('Lo que dicen quienes usan Tukipass') }}</h2>
            </div>
            <p>{{ $testimonialData ? $testimonialData->text : '' }}</p>
            <div class="total-client-reviews mt-40 bg-lighter">
              <div class="review-images mb-30">
                @if (!is_null($testimonialData))
                  <img class="lazy"
                    data-src="{{ \App\Services\FileUploadService::imageUrl('assets/admin/img/testimonial/', $testimonialData->image) }}"
                    loading="lazy"
                    alt="{{ __('Reseña destacada') }}">
                @else
                  <img class="lazy" data-src="{{ \App\Services\FileUploadService::imageUrl('assets/admin/img/testimonial/', 'clients.png') }}"
                    loading="lazy"
                    alt="{{ __('Reseña destacada') }}">
                @endif
                <span class="pluse"><i class="fas fa-plus"></i></span>
              </div>
              <h6>{{ $testimonialData ? $testimonialData->review_text : __('Opiniones de nuestra comunidad') }}</h6>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="testimonial-wrap">
            @if ($testimonials->isNotEmpty())
              <div class="row">
                @foreach ($testimonials as $item)
                  <div class="col-md-6">
                    <div class="testimonial-item">
                      <div class="author">
                        <img class="lazy" data-src="{{ \App\Services\FileUploadService::imageUrl('assets/admin/img/clients/', $item->image) }}"
                          loading="lazy"
                          alt="{{ __('Foto de quien dejó la reseña') }}">
                        <div class="content">
                          <h5>{{ $item->name }}</h5>
                          <span>{{ $item->occupation }}</span>
                          <div class="ratting">
                            @for ($i = 1; $i <= $item->rating; $i++)
                              <i class="fas fa-star"></i>
                            @endfor
                          </div>
                        </div>
                      </div>
                      <p>{{ $item->comment }}</p>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <h4 class="text-center">{{ __('Todavía no hay reseñas publicadas.') }}</h4>
            @endif
          </div>
        </div>
      </div>
      @if ($secInfo->partner_section_status == 1 && $partners->isNotEmpty())
        <div class="trust-partners" aria-label="{{ __('Aliados estratégicos') }}">
          <div class="trust-partners__intro">
            <h3>{{ __('También eligen Tukipass') }}</h3>
            <p>{{ __('Marcas y organizaciones que confían en nuestra plataforma para crecer.') }}</p>
          </div>
          <div class="client-logo-wrap trust-partners__logos">
            @foreach ($partners as $item)
              @php
                $partnerUrl = trim((string) ($item->url ?? ''));
              @endphp
              <div class="client-logo-item">
                @if ($partnerUrl !== '')
                  <a href="{{ $partnerUrl }}" target="_blank" rel="noopener noreferrer"
                    aria-label="{{ __('Visitar sitio del aliado estratégico') }}">
                    <img class="lazy" data-src="{{ \App\Services\FileUploadService::imageUrl('assets/admin/img/partner/', $item->image) }}"
                      loading="lazy"
                      alt="{{ $item->name ?? $item->title ?? __('Logo de aliado estratégico') }}">
                  </a>
                @else
                  <span aria-hidden="true">
                    <img class="lazy" data-src="{{ \App\Services\FileUploadService::imageUrl('assets/admin/img/partner/', $item->image) }}"
                      loading="lazy"
                      alt="{{ $item->name ?? $item->title ?? __('Logo de aliado estratégico') }}">
                  </span>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  </section>
@endif
