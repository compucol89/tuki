@php $u = Auth::guard('customer')->user(); @endphp
<div class="col-lg-3">
  <div class="cd-sidebar">

    {{-- Avatar + nombre --}}
    <div class="cd-sidebar__profile">
      <div class="cd-sidebar__avatar">
        @if($u->image)
          <img src="{{ asset('assets/admin/img/customer/' . $u->image) }}" alt="{{ $u->username }}">
        @else
          <span class="cd-sidebar__avatar-initials">{{ strtoupper(substr($u->fname ?? $u->username, 0, 1)) }}</span>
        @endif
      </div>
      <div class="cd-sidebar__profile-info">
        <p class="cd-sidebar__name">{{ $u->fname ? $u->fname . ' ' . $u->lname : $u->username }}</p>
        <p class="cd-sidebar__email">{{ $u->email }}</p>
      </div>
    </div>

    {{-- Navegación --}}
    <nav class="cd-sidebar__nav">
      <a href="{{ route('customer.dashboard') }}"
         class="cd-nav-item @if(request()->routeIs('customer.dashboard')) active @endif">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Inicio
      </a>
      <a href="{{ route('customer.booking.my_booking') }}"
         class="cd-nav-item @if(request()->routeIs('customer.booking.my_booking') || request()->routeIs('customer.booking_details')) active @endif">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 010-6h20a3 3 0 010 6"/><path d="M2 9v11a2 2 0 002 2h16a2 2 0 002-2V9"/><path d="M10 14H8m8 0h-4"/></svg>
        Mis entradas
      </a>
      <a href="{{ route('customer.wishlist') }}"
         class="cd-nav-item @if(request()->routeIs('customer.wishlist')) active @endif">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
        Lista de deseos
      </a>
      <a href="{{ route('customer.support_tickert') }}"
         class="cd-nav-item @if(request()->routeIs('customer.support_tickert') || request()->routeIs('customer.support_tickert.create') || request()->routeIs('customer.support_ticket.message')) active @endif">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
        Soporte
      </a>

      <div class="cd-nav-divider"></div>

      <a href="{{ route('customer.edit.profile') }}"
         class="cd-nav-item @if(request()->routeIs('customer.edit.profile')) active @endif">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Editar perfil
      </a>
      <a href="{{ route('customer.change.password') }}"
         class="cd-nav-item @if(request()->routeIs('customer.change.password')) active @endif">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        Cambiar contraseña
      </a>
      <a href="{{ route('customer.logout') }}" class="cd-nav-item cd-nav-item--logout">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Cerrar sesión
      </a>
    </nav>

  </div>
</div>
