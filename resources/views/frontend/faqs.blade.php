@extends('frontend.layout')
@section('body-class', 'faq-page-premium')

@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->faq_page_title ?? __('Preguntas frecuentes') }}
  @else
    {{ __('Preguntas frecuentes') }}
  @endif
@endsection


@php
  $rawMetaKeywords = !empty($seo->meta_keyword_faq) ? $seo->meta_keyword_faq : '';
  $rawMetaDescription = !empty($seo->meta_description_faq) ? $seo->meta_description_faq : '';
  $metaKeywords = str_contains($rawMetaKeywords, '<?') ? 'preguntas frecuentes, ayuda, entradas, eventos, tukipass' : $rawMetaKeywords;
  $metaDescription = str_contains($rawMetaDescription, '<?') ? 'Respondé tus dudas sobre compras, entradas, acceso a eventos y cuentas en Tukipass.' : $rawMetaDescription;
@endphp
@section('meta-keywords', "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")
@section('canonical', url()->current())
@section('og-url', url()->current())
@section('og-type', 'website')

@section('hero-section')
  <!-- Page Banner Start -->
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">
          @if (!empty($pageHeading))
            {{ $pageHeading->faq_page_title ?? __('Preguntas frecuentes') }}
          @else
            {{ __('Preguntas frecuentes') }}
          @endif
        </h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item active">
              @if (!empty($pageHeading))
                {{ $pageHeading->faq_page_title ?? __('Preguntas frecuentes') }}
              @else
                {{ __('Preguntas frecuentes') }}
              @endif
            </li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
  <!-- Page Banner End -->
@endsection

@section('content')
  @php
    /** Grupos temáticos — IA (scanning + carga cognitiva): una sección = un tema. */
    $fallbackFaqGroups = [
      [
        'slug' => 'sobre',
        'label' => 'Sobre Tukipass',
        'items' => [
          [
            'question' => '¿Qué es Tukipass?',
            'answer' => 'Tukipass es una plataforma para descubrir eventos, comprar entradas online y gestionar experiencias en un solo lugar. También permite a organizadores publicar eventos, vender entradas y seguir sus ventas en tiempo real.',
          ],
        ],
      ],
      [
        'slug' => 'compras',
        'label' => 'Compras y entradas',
        'items' => [
          [
            'question' => '¿Cómo compro una entrada?',
            'answer' => 'Elegí el evento que te interesa, entrá a la ficha, seleccioná la fecha si corresponde, el tipo de entrada y la cantidad. Después continuá al pago, completá tus datos y vas a recibir la confirmación por correo.',
          ],
          [
            'question' => '¿Cómo pago mis entradas?',
            'answer' => 'Podés pagar con los medios habilitados en el checkout de cada evento. Tukipass muestra las opciones disponibles antes de confirmar la compra para que elijas la que mejor te cierre.',
          ],
          [
            'question' => '¿Dónde recibo mis entradas?',
            'answer' => 'Cuando la compra se acredita, tus entradas quedan disponibles en tu cuenta y también se envían al correo que ingresaste durante la compra.',
          ],
          [
            'question' => '¿Cómo presento mis entradas en el evento?',
            'answer' => 'Podés presentar tu entrada digital desde el celular o llevarla descargada, según lo que indique el organizador. El acceso siempre se valida con el código único de tu ticket.',
          ],
          [
            'question' => '¿Qué hago si no encuentro mis entradas?',
            'answer' => 'Primero revisá tu correo, incluyendo spam o promociones. Si no aparecen, iniciá sesión en tu cuenta para ver tus compras. Si seguís con el problema, escribinos desde la página de contacto para ayudarte.',
          ],
          [
            'question' => '¿Puedo cambiar o devolver una entrada?',
            'answer' => 'Las condiciones de cambios, cancelaciones o devoluciones dependen de la política del evento y del organizador. En la ficha del evento podés ver esa información antes de comprar.',
          ],
        ],
      ],
      [
        'slug' => 'cuenta',
        'label' => 'Tu cuenta',
        'items' => [
          [
            'question' => 'Olvidé mi contraseña, ¿cómo recupero el acceso?',
            'answer' => 'Desde la pantalla de ingreso podés usar la opción de recuperación de contraseña. Te vamos a enviar un correo con los pasos para restablecerla.',
          ],
          [
            'question' => '¿Cómo actualizo mis datos?',
            'answer' => 'Ingresá a tu cuenta y desde tu perfil vas a poder revisar y actualizar tus datos personales, además de ver tus compras y entradas.',
          ],
        ],
      ],
      [
        'slug' => 'organizadores',
        'label' => 'Organizadores',
        'items' => [
          [
            'question' => 'Quiero publicar un evento en Tukipass, ¿qué tengo que hacer?',
            'answer' => 'Creá tu cuenta de organizador, completá la información del evento y configurá tus entradas. Cuando tengas todo listo, vas a poder publicarlo y empezar a vender online.',
          ],
        ],
      ],
    ];

    $demoFaqPatterns = [
      'solodev',
      'aws',
      'how do i',
      'how can i',
      'what is',
      'can i',
      'cloud infrastructure',
      'multi-site management',
    ];

    $dbFaqsLookDemo = $faqs->isNotEmpty() && $faqs->contains(function ($faq) use ($demoFaqPatterns) {
      $haystack = strtolower(($faq->question ?? '') . ' ' . ($faq->answer ?? ''));
      foreach ($demoFaqPatterns as $pattern) {
        if (str_contains($haystack, $pattern)) {
          return true;
        }
      }
      return false;
    });

    $mappedDbFaqs = $faqs->map(function ($faq) {
      return [
        'question' => $faq->question,
        'answer' => $faq->answer,
        'id' => $faq->id,
      ];
    });

    if ($faqs->isEmpty() || $dbFaqsLookDemo) {
      $faqGroups = $fallbackFaqGroups;
    } else {
      $faqGroups = [
        [
          'slug' => 'todas',
          'label' => null,
          'items' => $mappedDbFaqs->all(),
        ],
      ];
    }
  @endphp

@push('styles')
<style>
/* FAQ — premium + IA por grupos (solo .faq-page-premium) */
.faq-page-premium .faq-premium {
  --fp-ink: #0f172a;
  --fp-muted: #64748b;
  --fp-line: rgba(15, 23, 42, 0.08);
  --fp-accent: #ea580c;
  position: relative;
  z-index: 0;
  padding: clamp(56px, 8vw, 96px) 0 clamp(64px, 9vw, 104px);
  background:
    radial-gradient(ellipse 90% 55% at 8% -12%, rgba(249, 115, 22, 0.06) 0%, transparent 50%),
    radial-gradient(ellipse 70% 45% at 92% 20%, rgba(59, 130, 246, 0.04) 0%, transparent 45%),
    linear-gradient(185deg, #f8fafc 0%, #f1f5f9 45%, #eef2f7 100%);
}
.faq-page-premium .faq-premium::before {
  content: '';
  position: absolute;
  inset: 0;
  z-index: -1;
  pointer-events: none;
  opacity: 0.4;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.3'/%3E%3C/svg%3E");
  mix-blend-mode: multiply;
}
.faq-page-premium .faq-premium__intro {
  max-width: 46rem;
  margin: 0 auto clamp(36px, 5vw, 52px);
  text-align: center;
}
.faq-page-premium .faq-premium__eyebrow {
  margin: 0 0 10px;
  font-family: var(--heading-font), system-ui, sans-serif;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: #94a3b8;
}
.faq-page-premium .faq-premium__lede {
  margin: 0;
  font-size: clamp(1.02rem, 0.5vw + 0.95rem, 1.15rem);
  font-weight: 400;
  line-height: 1.6;
  color: var(--fp-muted);
}
.faq-page-premium .faq-premium__group {
  margin-bottom: clamp(36px, 5vw, 48px);
}
.faq-page-premium .faq-premium__group:last-of-type {
  margin-bottom: 0;
}
.faq-page-premium .faq-premium__group-title {
  margin: 0 0 18px;
  padding-bottom: 12px;
  font-family: var(--heading-font), system-ui, sans-serif;
  font-size: 13px;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: var(--fp-ink);
  border-bottom: 1px solid var(--fp-line);
}
.faq-page-premium .faq-premium__accordion .accordion {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.faq-page-premium .faq-premium__accordion .card {
  margin: 0;
  border: none;
  border-radius: 18px;
  overflow: hidden;
  background: linear-gradient(165deg, rgba(255, 255, 255, 0.97) 0%, rgba(248, 250, 252, 0.99) 100%);
  border: 1px solid var(--fp-line);
  box-shadow:
    0 1px 0 rgba(255, 255, 255, 1) inset,
    0 10px 36px rgba(15, 23, 42, 0.06),
    0 2px 8px rgba(15, 23, 42, 0.03);
  transition: box-shadow 0.3s ease, border-color 0.25s ease, transform 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}
.faq-page-premium .faq-premium__accordion .card:hover {
  border-color: rgba(249, 115, 22, 0.15);
}
.faq-page-premium .faq-premium__accordion .card:has(.faq-premium__trigger:not(.collapsed)) {
  border-color: rgba(249, 115, 22, 0.22);
  box-shadow:
    0 1px 0 rgba(255, 255, 255, 1) inset,
    0 18px 48px rgba(15, 23, 42, 0.08),
    0 8px 20px rgba(234, 88, 12, 0.06);
  transform: translateY(-2px);
}
.faq-page-premium .faq-premium__accordion .card-header {
  padding: 0;
  border: none;
  background: transparent;
}
.faq-page-premium .faq-premium__trigger {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 18px 20px 18px 22px;
  margin: 0;
  text-align: left;
  font-family: var(--heading-font), system-ui, sans-serif;
  font-size: 16px;
  font-weight: 700;
  letter-spacing: -0.025em;
  line-height: 1.35;
  color: var(--fp-ink);
  background: transparent;
  border: none;
  cursor: pointer;
  transition: color 0.2s ease;
}
.faq-page-premium .faq-premium__trigger:hover {
  color: #1e293b;
}
.faq-page-premium .faq-premium__trigger:focus-visible {
  outline: 2px solid rgba(249, 115, 22, 0.55);
  outline-offset: -2px;
}
.faq-page-premium .faq-premium__trigger-icon {
  flex-shrink: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  background: rgba(241, 245, 249, 0.95);
  border: 1px solid rgba(15, 23, 42, 0.06);
  color: var(--fp-accent);
  transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), background 0.2s ease;
}
.faq-page-premium .faq-premium__trigger:not(.collapsed) .faq-premium__trigger-icon {
  transform: rotate(180deg);
  background: rgba(255, 237, 213, 0.95);
  border-color: rgba(249, 115, 22, 0.2);
}
.faq-page-premium .faq-premium__accordion .card-body {
  padding: 0 22px 20px 22px;
  border-top: 1px solid rgba(15, 23, 42, 0.05);
  background: rgba(255, 255, 255, 0.45);
}
.faq-page-premium .faq-premium__accordion .card-body p {
  margin: 0;
  padding-top: 16px;
  font-size: 15px;
  line-height: 1.65;
  color: var(--fp-muted);
}
@supports not selector(:has(*)) {
  .faq-page-premium .faq-premium__accordion .card.faq-premium__card--open {
    border-color: rgba(249, 115, 22, 0.22);
    box-shadow:
      0 1px 0 rgba(255, 255, 255, 1) inset,
      0 18px 48px rgba(15, 23, 42, 0.08),
      0 8px 20px rgba(234, 88, 12, 0.06);
  }
}
@media (prefers-reduced-motion: reduce) {
  .faq-page-premium .faq-premium__accordion .card,
  .faq-page-premium .faq-premium__trigger-icon {
    transition: none;
  }
  .faq-page-premium .faq-premium__accordion .card:has(.faq-premium__trigger:not(.collapsed)),
  .faq-page-premium .faq-premium__accordion .card.faq-premium__card--open {
    transform: none;
  }
}
</style>
@endpush

  <!--====== FAQ PART START ======-->
  <section class="faq-area faq-premium" aria-labelledby="faq-page-heading">
    <div class="container">
      <header class="faq-premium__intro">
        <p class="faq-premium__eyebrow">{{ __('Centro de ayuda') }}</p>
        <h2 id="faq-page-heading" class="faq-premium__lede">{{ __('Encontrá respuestas rápidas ordenadas por tema. Si no resolvés tu duda, podés escribirnos desde contacto.') }}</h2>
      </header>

      @php
        $allItemsEmpty = collect($faqGroups)->sum(fn ($g) => count($g['items'] ?? [])) === 0;
      @endphp

      @if ($allItemsEmpty)
        <h3 class="text-center">{{ __('No encontramos preguntas frecuentes publicadas.') }}</h3>
      @else
        @foreach ($faqGroups as $group)
          @php
            $gIdx = $loop->index;
            $groupItems = $group['items'] ?? [];
            $accordionId = 'accordion-faq-' . ($group['slug'] ?? $gIdx);
          @endphp
          @if (count($groupItems))
            <section class="faq-premium__group" aria-label="{{ $group['label'] ?? __('Preguntas frecuentes') }}">
              @if (!empty($group['label']))
                <h2 class="faq-premium__group-title" id="faq-group-{{ $group['slug'] ?? $gIdx }}">{{ $group['label'] }}</h2>
              @endif

              <div class="faq-accordion faq-premium__accordion">
                <div class="accordion" id="{{ $accordionId }}">
                  @foreach ($groupItems as $faq)
                    @php
                      $baseId = isset($faq['id']) ? (string) $faq['id'] : (string) ($loop->index + 1);
                      $faqUid = $gIdx . '-q-' . $baseId . '-' . $loop->index;
                    @endphp
                    <div class="card">
                      <div class="card-header" id="{{ 'heading-' . $faqUid }}">
                        <button class="faq-premium__trigger {{ $loop->first && $gIdx === 0 ? '' : 'collapsed' }}"
                          type="button"
                          data-toggle="collapse"
                          data-target="{{ '#collapse-' . $faqUid }}"
                          aria-expanded="{{ $loop->first && $gIdx === 0 ? 'true' : 'false' }}"
                          aria-controls="{{ 'collapse-' . $faqUid }}">
                          <span class="flex-grow-1 pr-2">{{ $faq['question'] }}</span>
                          <span class="faq-premium__trigger-icon" aria-hidden="true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                          </span>
                        </button>
                      </div>

                      <div id="{{ 'collapse-' . $faqUid }}"
                        class="collapse {{ $loop->first && $gIdx === 0 ? 'show' : '' }}"
                        aria-labelledby="{{ 'heading-' . $faqUid }}"
                        data-parent="{{ '#' . $accordionId }}">
                        <div class="card-body">
                          <p>{{ $faq['answer'] }}</p>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </section>
          @endif
        @endforeach
      @endif

      @if (!empty(showAd(3)))
        <div class="text-center mt-30">
          {!! showAd(3) !!}
        </div>
      @endif
    </div>
  </section>
  <!--====== FAQ PART END ======-->
@endsection

@push('scripts')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => __('Inicio'),
            'item' => url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => !empty($pageHeading) ? ($pageHeading->faq_page_title ?? __('Preguntas frecuentes')) : __('Preguntas frecuentes'),
            'item' => url()->current(),
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
