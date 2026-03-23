@extends('organizer.layout')

@section('content')
  @php
    $balanceAmount = Auth::guard('organizer')->user()->amount;
    $pendingCount = $collection->where('status', 0)->count();
    $pendingAmount = $collection->where('status', 0)->sum('amount');
    $approvedAmount = $collection->where('status', 1)->sum('payable_amount');
  @endphp
  <div class="page-header">
    <h4 class="page-title">{{ __('Withdraws') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('organizer.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('My Withdraws') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
        <div class="mb-3 mb-lg-0">
          <h4 class="mb-1">{{ __('Tus retiros') }}</h4>
          <p class="text-muted mb-0">{{ __('Desde aqui puedes revisar solicitudes, ver su estado y pedir un nuevo retiro.') }}</p>
        </div>
        <div class="d-flex flex-wrap">
          <a href="{{ route('organizer.withdraw.create', ['language' => $defaultLang->code]) }}"
            class="btn btn-primary btn-sm mr-2 mb-2">
            <i class="fas fa-plus"></i> {{ __('Solicitar retiro') }}
          </a>
          <button class="btn btn-danger btn-sm mb-2 d-none bulk-delete"
            data-href="{{ route('organizer.witdraw.bulk_delete_withdraw') }}">
            <i class="flaticon-interface-5"></i> {{ __('Cancelar seleccionados') }}
          </button>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="card ev-section-card">
            <div class="card-body">
              <small class="text-muted d-block mb-2">{{ __('Saldo disponible') }}</small>
              <h3 class="mb-1">
                {{ $currencyInfo->base_currency_symbol_position == 'left' ? $currencyInfo->base_currency_symbol : '' }}{{ number_format($balanceAmount, 2) }}{{ $currencyInfo->base_currency_symbol_position == 'right' ? $currencyInfo->base_currency_symbol : '' }}
              </h3>
              <p class="text-muted mb-0">{{ __('Es el monto que hoy puedes retirar.') }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card ev-section-card">
            <div class="card-body">
              <small class="text-muted d-block mb-2">{{ __('Pendientes') }}</small>
              <h3 class="mb-1">{{ $pendingCount }}</h3>
              <p class="text-muted mb-0">
                {{ $currencyInfo->base_currency_symbol_position == 'left' ? $currencyInfo->base_currency_symbol : '' }}{{ number_format($pendingAmount, 2) }}{{ $currencyInfo->base_currency_symbol_position == 'right' ? $currencyInfo->base_currency_symbol : '' }}
                {{ __('todavia en revision.') }}
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card ev-section-card">
            <div class="card-body">
              <small class="text-muted d-block mb-2">{{ __('Aprobado historico') }}</small>
              <h3 class="mb-1">
                {{ $currencyInfo->base_currency_symbol_position == 'left' ? $currencyInfo->base_currency_symbol : '' }}{{ number_format($approvedAmount, 2) }}{{ $currencyInfo->base_currency_symbol_position == 'right' ? $currencyInfo->base_currency_symbol : '' }}
              </h3>
              <p class="text-muted mb-0">{{ __('Total aprobado en retiros ya procesados.') }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card ev-section-card">
        <div class="card-header ev-section-header">
          <h4 class="card-title">{{ __('Historial de retiros') }}</h4>
        </div>
        <div class="card-body">
          @if (session()->has('course_status_warning'))
            <div class="alert alert-warning">
              <p class="text-dark mb-0">{{ session()->get('course_status_warning') }}</p>
            </div>
          @endif

          @if ($collection->isEmpty())
            <div class="alert alert-light border mb-0">
              <strong>{{ __('Aun no tienes retiros cargados.') }}</strong>
              {{ __('Cuando envies una solicitud, la veras aqui con su estado.') }}
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-striped mt-3" id="basic-datatables">
                <thead>
                  <tr>
                    <th scope="col">
                      <input type="checkbox" class="bulk-check" data-val="all">
                    </th>
                    <th scope="col">{{ __('Retiro') }}</th>
                    <th scope="col">{{ __('Metodo') }}</th>
                    <th scope="col">{{ __('Solicitado') }}</th>
                    <th scope="col">{{ __('Comision') }}</th>
                    <th scope="col">{{ __('Cobras') }}</th>
                    <th scope="col">{{ __('Estado') }}</th>
                    <th scope="col">{{ __('Acciones') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($collection as $item)
                    <tr>
                      <td>
                        <input type="checkbox" class="bulk-check" data-val="{{ $item->id }}">
                      </td>
                      <td>
                        {{ $item->withdraw_id }}
                      </td>
                      <td>
                        {{ optional($item->method)->name }}
                      </td>
                      <td>
                        {{ $currencyInfo->base_currency_symbol_position == 'left' ? $currencyInfo->base_currency_symbol : '' }}
                        {{ $item->amount }}
                        {{ $currencyInfo->base_currency_symbol_position == 'right' ? $currencyInfo->base_currency_symbol : '' }}
                      </td>
                      <td>
                        {{ $currencyInfo->base_currency_symbol_position == 'left' ? $currencyInfo->base_currency_symbol : '' }}
                        {{ $item->total_charge }}
                        {{ $currencyInfo->base_currency_symbol_position == 'right' ? $currencyInfo->base_currency_symbol : '' }}
                      </td>
                      <td>
                        {{ $currencyInfo->base_currency_symbol_position == 'left' ? $currencyInfo->base_currency_symbol : '' }}
                        {{ $item->payable_amount }}
                        {{ $currencyInfo->base_currency_symbol_position == 'right' ? $currencyInfo->base_currency_symbol : '' }}
                      </td>
                      <td>
                        @if ($item->status == 0)
                          <span class="badge badge-warning">{{ __('Pendiente') }}</span>
                        @elseif($item->status == 1)
                          <span class="badge badge-success">{{ __('Aprobado') }}</span>
                        @elseif($item->status == 2)
                          <span class="badge badge-danger">{{ __('Rechazado') }}</span>
                        @endif
                      </td>
                      <td>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#withdrawModal{{ $item->id }}"
                          class="btn btn-primary btn-sm">{{ __('Ver') }}</a>
                        @if ($item->status == 0)
                          <form class="deleteForm d-inline-block"
                            action="{{ route('organizer.witdraw.delete_withdraw', ['id' => $item->id]) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm deleteBtn">{{ __('Cancelar') }}</button>
                          </form>
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
  </div>
  @foreach ($collection as $item)
    <div class="modal fade" id="withdrawModal{{ $item->id }}" tabindex="-1" role="dialog"
      aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLongTitle">{{ __('Withdraw Information') }}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            @php
              $d_feilds = json_decode($item->feilds, true);
            @endphp
            <div class="">
              <p>{{ __('Total Payable Amount') }} :
                {{ $currencyInfo->base_currency_symbol_position == 'left' ? $currencyInfo->base_currency_symbol : '' }}
                {{ $item->payable_amount }}
                {{ $currencyInfo->base_currency_symbol_position == 'right' ? $currencyInfo->base_currency_symbol : '' }}
              </p>
              @foreach ($d_feilds as $key => $d_feild)
                <p><strong>{{ str_replace('_', ' ', $key) }} : {{ $d_feild }}</strong></p>
              @endforeach
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
              {{ __('Close') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endsection
