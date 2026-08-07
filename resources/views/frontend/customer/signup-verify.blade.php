@extends('frontend.layout')

@section('content')
  <style>
    .verify-email-page {
      padding: 100px 0;
      background:
        radial-gradient(circle at top left, color-mix(in srgb, var(--primary) 12%, transparent), transparent 32%),
        radial-gradient(circle at bottom right, rgba(34, 197, 94, 0.10), transparent 28%),
        linear-gradient(180deg, #fffaf5 0%, #ffffff 52%, #f8fafc 100%);
    }

    .verify-email-shell {
      max-width: 760px;
      margin: 0 auto;
      padding: 0 15px;
    }

    .verify-email-card {
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(30, 37, 50, 0.08);
      border-radius: 28px;
      background: var(--card);
      box-shadow: 0 28px 70px rgba(30, 37, 50, 0.10);
      padding: 56px 44px;
      text-align: center;
    }

    .verify-email-card::before {
      content: "";
      position: absolute;
      inset: 0 0 auto;
      height: 6px;
      background: {{ $status === 'success' ? 'linear-gradient(90deg, var(--success) 0%, var(--success) 100%)' : 'linear-gradient(90deg, var(--primary) 0%, var(--primary-active) 100%)' }};
    }

    .verify-email-badge {
      width: 84px;
      height: 84px;
      margin: 0 auto 28px;
      border-radius: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 34px;
      color: var(--primary-foreground);
      background: {{ $status === 'success' ? 'linear-gradient(135deg, var(--success) 0%, var(--success) 100%)' : 'linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%)' }};
      box-shadow: 0 18px 35px {{ $status === 'success' ? 'rgba(34, 197, 94, 0.24)' : 'rgba(15, 23, 42, 0.08)' }};
    }

    .verify-email-eyebrow {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 8px 16px;
      border-radius: 999px;
      margin-bottom: 18px;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--foreground);
      background: rgba(30, 37, 50, 0.06);
    }

    .verify-email-title {
      margin-bottom: 16px;
      color: var(--foreground);
      font-size: 40px;
      line-height: 1.1;
      font-weight: 800;
    }

    .verify-email-text {
      max-width: 560px;
      margin: 0 auto 14px;
      color: #556070;
      font-size: 17px;
      line-height: 1.75;
    }

    .verify-email-actions {
      display: flex;
      justify-content: center;
      gap: 14px;
      flex-wrap: wrap;
      margin-top: 34px;
    }

    .verify-email-btn {
      min-width: 190px;
      padding: 14px 24px;
      border-radius: 14px;
      font-weight: 700;
      text-decoration: none !important;
      transition: all 0.2s ease;
    }

    .verify-email-btn--primary {
      color: var(--primary-foreground);
      background: var(--primary);
    }

    .verify-email-btn--primary:hover {
      color: var(--primary-foreground);
      transform: translateY(-1px);
      background: var(--primary);
    }

    .verify-email-btn--secondary {
      color: var(--foreground);
      background: var(--card);
      border: 1px solid rgba(30, 37, 50, 0.12);
    }

    .verify-email-btn--secondary:hover {
      color: var(--foreground);
      border-color: rgba(30, 37, 50, 0.2);
      background: var(--muted);
    }

    .verify-email-help {
      margin-top: 22px;
      color: #7a8595;
      font-size: 14px;
      line-height: 1.7;
    }

    @media (max-width: 767.98px) {
      .verify-email-page {
        padding: 72px 0;
      }

      .verify-email-card {
        padding: 42px 24px;
        border-radius: 24px;
      }

      .verify-email-title {
        font-size: 31px;
      }

      .verify-email-text {
        font-size: 16px;
      }

      .verify-email-actions {
        flex-direction: column;
      }

      .verify-email-btn {
        width: 100%;
      }
    }
  </style>

  <section class="verify-email-page">
    <div class="container">
      <div class="verify-email-shell">
        <div class="verify-email-card">
          @if ($status === 'success')
            <div class="verify-email-badge">
              <i class="fas fa-check"></i>
            </div>
            <div class="verify-email-eyebrow">Verificación completada</div>
            <h1 class="verify-email-title">¡Correo verificado correctamente!</h1>
            <p class="verify-email-text">
              Tu cuenta ya está activa. Ahora podés iniciar sesión y empezar a usar TukiPass.
            </p>
            <p class="verify-email-text">
              Desde tu cuenta vas a poder ver tus entradas, gestionar tus reservas y recibir novedades importantes sobre tus eventos.
            </p>
            <div class="verify-email-actions">
              <a href="{{ route('customer.login') }}" class="verify-email-btn verify-email-btn--primary">Iniciar sesión</a>
              <a href="{{ route('events') }}" class="verify-email-btn verify-email-btn--secondary">Ver eventos</a>
            </div>
            <p class="verify-email-help">
              Si ya tenías una sesión abierta, podés volver al inicio y continuar navegando.
            </p>
          @else
            <div class="verify-email-badge">
              <i class="fas fa-exclamation"></i>
            </div>
            <div class="verify-email-eyebrow">No se pudo verificar</div>
            <h1 class="verify-email-title">El enlace de verificación no es válido o ya fue utilizado.</h1>
            <p class="verify-email-text">
              Intentá iniciar sesión o solicitá un nuevo correo de verificación si la opción está disponible.
            </p>
            <p class="verify-email-text">
              Si acabás de verificar tu cuenta en otra pestaña, es posible que el enlace ya no esté activo.
            </p>
            <div class="verify-email-actions">
              <a href="{{ route('customer.login') }}" class="verify-email-btn verify-email-btn--primary">Iniciar sesión</a>
              <a href="{{ route('customer.signup') }}" class="verify-email-btn verify-email-btn--secondary">Crear cuenta</a>
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>
@endsection
