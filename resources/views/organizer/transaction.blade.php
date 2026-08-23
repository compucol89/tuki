@extends('organizer.layout')


@section('content')
  <div class="organizer-transactions oc-page">
  <div class="page-header">
    <h1 class="page-title oc-page__title">{{ __('Transacciones') }}</h1>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('organizer.dashboard') }}" aria-label="{{ __('Ir al panel') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow" aria-hidden="true"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Transacciones') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card oc-panel">
        <div class="card-header oc-panel__header">
          <div class="row">
            <div class="col-lg-4">
              <div class="card-title oc-panel__title d-inline-block">{{ __('Transacciones') }}</div>
            </div>

            <div class="col-lg-4">
              <form action="" method="get" class="ot-filter">
                <label for="transSearch" class="visually-hidden">{{ __('Buscar por ID de transacción') }}</label>
                <div class="input-group">
                  <input id="transSearch" type="text" value="{{ request()->input('transcation_id') }}"
                    name="transcation_id" placeholder="{{ __('Buscar por ID de transacción') }}" class="form-control">
                  <div class="input-group-append">
                    <button type="submit" class="btn btn-primary oc-btn oc-btn--primary" aria-label="{{ __('Buscar transacción') }}">
                      <i class="fas fa-search" aria-hidden="true"></i>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-body oc-panel__body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($transcations) == 0)
                <div class="ot-empty oc-empty" role="status">
                  <i class="fas fa-receipt" aria-hidden="true"></i>
                  <h2>{{ __('No encontramos transacciones') }}</h2>
                  <p>{{ __('Probá con otro ID o limpiá la búsqueda para ver todo el historial.') }}</p>
                </div>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3 oc-table">
                    <thead>
                      <tr>
                        <th scope="col" class="tuki-data">{{ __('ID de transacción') }}</th>
                        <th scope="col">{{ __('Tipo') }}</th>
                        <th scope="col">{{ __('Medio de pago') }}</th>
                        <th scope="col" class="tuki-data">{{ __('Saldo anterior') }}</th>
                        <th scope="col" class="tuki-data">{{ __('Importe') }}</th>
                        <th scope="col" class="tuki-data">{{ __('Saldo posterior') }}</th>
                        <th scope="col">{{ __('Estado') }}</th>
                        <th scope="col">{{ __('Acciones') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($transcations as $transcation)
                        <tr>
                          <td class="tuki-data tuki-data-id">#{{ $transcation->transcation_id }}</td>
                          <td>
                            @if ($transcation->transcation_type == 1)
                              {{ __('Reserva de evento') }}
                            @elseif ($transcation->transcation_type == 2)
                              {{ __('Pedido de producto') }}
                            @elseif ($transcation->transcation_type == 3)
                              {{ __('Retiro') }}
                            @elseif ($transcation->transcation_type == 4)
                              {{ __('Carga de saldo') }}
                            @elseif ($transcation->transcation_type == 5)
                              {{ $transcation->payment_method == 'event_settlement' ? __('Liquidación de evento') : __('Descuento de saldo') }}
                            @endif
                          </td>
                          <td>
                            @if ($transcation->transcation_type == 3)
                              @php
                                $method = $transcation->method()->first();
                              @endphp
                              @if ($method)
                                {{ $method->name }}
                              @else
                                {{ '-' }}
                              @endif
                            @else
                              {{ $transcation->payment_method == 'event_settlement' ? __('Liquidación de evento') : ($transcation->payment_method != null ? $transcation->payment_method : '-') }}
                            @endif
                          </td>
                          <td class="tuki-data-money">
                            {{ $transcation->currency_symbol_position == 'left' ? $transcation->currency_symbol : '' }}
                            {{ $transcation->pre_balance }}
                            {{ $transcation->currency_symbol_position == 'right' ? $transcation->currency_symbol : '' }}
                          </td>
                          <td class="tuki-data-money">
                            @if ($transcation->transcation_type == 3 || $transcation->transcation_type == 5)
                              <span class="text-danger">{{ '(-) ' }}</span>
                            @else
                              <span class="text-success">{{ '(+) ' }}</span>
                            @endif

                            {{ $transcation->currency_symbol_position == 'left' ? $transcation->currency_symbol : '' }}
                            {{ $transcation->grand_total - $transcation->commission }}
                            {{ $transcation->currency_symbol_position == 'right' ? $transcation->currency_symbol : '' }}
                          </td>
                          <td class="tuki-data-money">
                            {{ $transcation->currency_symbol_position == 'left' ? $transcation->currency_symbol : '' }}
                            {{ $transcation->after_balance }}
                            {{ $transcation->currency_symbol_position == 'right' ? $transcation->currency_symbol : '' }}
                          </td>
                          <td>
                            @if ($transcation->payment_status == 1)
                            <span class="badge badge-success oc-pill">{{ __('Pagado') }}</span>
                          @elseif ($transcation->payment_status == 2)
                              <span class="badge badge-warning text-dark oc-pill">{{ __('Rechazado') }}</span>
                            @else
                              <span class="badge badge-danger oc-pill">{{ __('Pendiente') }}</span>
                            @endif
                          </td>

                          <td>
                            @if ($transcation->transcation_type == 1)
                              @php
                                $t_invoice = $transcation->event_booking()->first();
                              @endphp
                              @if ($t_invoice)
                                  <a target="_blank" class="btn btn-secondary btn-sm mr-1 oc-btn"
                                  href="{{ route('booking.ticket.download', $t_invoice->id) }}"
                                  aria-label="{{ __('Ver comprobante') }}">
                                  <i class="fas fa-eye" aria-hidden="true"></i>
                                </a>
                              @endif
                            @elseif ($transcation->transcation_type == 2)
                              @php
                                $t_invoice = $transcation->product_order()->first();
                              @endphp
                              @if ($t_invoice)
                                <a target="_blank" class="btn btn-secondary btn-sm mr-1 oc-btn"
                                  href="{{ asset('assets/admin/file/order/invoices/' . $t_invoice->invoice) }}"
                                  aria-label="{{ __('Ver factura') }}">
                                  <i class="fas fa-eye" aria-hidden="true"></i>
                                </a>
                              @endif
                            @endif
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

        @if (count($transcations) > 0)
        <div class="card-footer text-center oc-panel__footer">
          <div class="d-inline-block mt-3">
            {{ $transcations->appends([
                    'transcation_id' => request()->input('transcation_id'),
                ])->links() }}
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
  </div>
@endsection
