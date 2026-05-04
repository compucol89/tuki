@extends('organizer.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Report') }}</h4>
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
        <a href="#">{{ __('Event Bookings') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Report') }}</a>
      </li>
    </ul>
  </div>

  @php
    $fromDateValue = request()->filled('from_date') ? \Carbon\Carbon::parse(request()->input('from_date'))->format('Y-m-d') : '';
    $toDateValue = request()->filled('to_date') ? \Carbon\Carbon::parse(request()->input('to_date'))->format('Y-m-d') : '';
  @endphp

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title mb-1">{{ __('Booking Report') }}</h4>
          <p class="card-category mb-0">{{ __('Filtra reservas por rango de fechas, metodo y estado de pago.') }}</p>
        </div>

        <div class="card-body border-bottom">
          <form action="{{ route('organizer.event_booking.report') }}" method="GET">
            <div class="row align-items-end">
              <div class="col-xl-3 col-md-6">
                <div class="form-group">
                  <label for="from_date">{{ __('Desde') }}</label>
                  <input id="from_date" class="form-control" type="date" name="from_date" value="{{ $fromDateValue }}"
                    required>
                </div>
              </div>
              <div class="col-xl-3 col-md-6">
                <div class="form-group">
                  <label for="to_date">{{ __('Hasta') }}</label>
                  <input id="to_date" class="form-control" type="date" name="to_date" value="{{ $toDateValue }}"
                    required>
                </div>
              </div>
              <div class="col-xl-3 col-md-6">
                <div class="form-group">
                  <label for="payment_method">{{ __('Metodo de pago') }}</label>
                  <select id="payment_method" name="payment_method" class="form-control">
                    <option value="">{{ __('Todos') }}</option>
                    @foreach ($onPms as $onPm)
                      <option value="{{ $onPm->keyword }}"
                        {{ request()->input('payment_method') == $onPm->keyword ? 'selected' : '' }}>
                        {{ $onPm->name }}
                      </option>
                    @endforeach
                    @foreach ($offPms as $offPm)
                      <option value="{{ $offPm->name }}"
                        {{ request()->input('payment_method') == $offPm->name ? 'selected' : '' }}>
                        {{ $offPm->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-xl-3 col-md-6">
                <div class="form-group">
                  <label for="payment_status">{{ __('Estado del pago') }}</label>
                  <select id="payment_status" name="payment_status" class="form-control">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="pending" {{ request()->input('payment_status') == 'pending' ? 'selected' : '' }}>
                      {{ __('Pending') }}
                    </option>
                    <option value="completed" {{ request()->input('payment_status') == 'completed' ? 'selected' : '' }}>
                      {{ __('Completed') }}
                    </option>
                    <option value="rejected" {{ request()->input('payment_status') == 'rejected' ? 'selected' : '' }}>
                      {{ __('Rejected') }}
                    </option>
                  </select>
                </div>
              </div>
              <div class="col-12">
                <div class="d-flex flex-wrap justify-content-end pt-2">
                  <button type="submit" class="btn btn-primary btn-sm mr-2 mb-2">{{ __('Filtrar') }}</button>
                  <a href="{{ route('organizer.event_booking.report') }}"
                    class="btn btn-light btn-sm mr-2 mb-2">{{ __('Limpiar') }}</a>
                  <button type="submit" class="btn btn-success btn-sm mb-2"
                    formaction="{{ route('organizer.event_bookings.export') }}" formmethod="GET"
                    title="CSV Format">{{ __('Export') }}</button>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($bookings) > 0)
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <div class="text-muted small">
                    {{ __('Resultados') }}: {{ $bookings->total() }}
                  </div>
                </div>

                <div class="table-responsive">
                  <table class="table table-striped align-middle">
                    <thead>
                      <tr>
                        <th scope="col">{{ __('Booking') }}</th>
                        <th scope="col">{{ __('Evento') }}</th>
                        <th scope="col">{{ __('Cliente') }}</th>
                        <th scope="col">{{ __('Contacto') }}</th>
                        <th scope="col">{{ __('Importes') }}</th>
                        <th scope="col">{{ __('Pago') }}</th>
                        <th scope="col">{{ __('Fecha') }}</th>
                        <th scope="col">{{ __('Comprobante') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($bookings as $booking)
                        <tr>
                          <td>
                            <strong>#{{ $booking->booking_id }}</strong><br>
                            <small class="text-muted">{{ __('Cantidad') }}: {{ $booking->quantity }}</small>
                          </td>
                          <td>
                            <a href="{{ route('event.details', ['slug' => $booking->slug, 'id' => $booking->event_id]) }}"
                              target="_blank">
                              {{ strlen($booking->title) > 45 ? mb_substr($booking->title, 0, 45, 'utf-8') . '...' : $booking->title }}
                            </a>
                          </td>
                          <td>
                            <strong>{{ $booking->customerfname }} {{ $booking->customerlname }}</strong><br>
                            <small class="text-muted">{{ $booking->fname }} {{ $booking->lname }}</small>
                          </td>
                          <td>
                            <div>{{ $booking->email }}</div>
                            <small class="d-block text-muted">{{ $booking->phone ?: '-' }}</small>
                            <small class="d-block text-muted">
                              {{ collect([$booking->city, $booking->state, $booking->country])->filter()->implode(', ') ?: '-' }}
                            </small>
                          </td>
                          <td>
                            <div>
                              {{ __('Total') }}:
                              {{ $abs->base_currency_symbol_position == 'left' ? $abs->base_currency_symbol : '' }}{{ round($booking->price, 2) }}{{ $abs->base_currency_symbol_position == 'right' ? $abs->base_currency_symbol : '' }}
                            </div>
                            <small class="d-block text-muted">
                              {{ __('Discount') }}:
                              {{ $abs->base_currency_symbol_position == 'left' ? $abs->base_currency_symbol : '' }}{{ round($booking->discount, 2) }}{{ $abs->base_currency_symbol_position == 'right' ? $abs->base_currency_symbol : '' }}
                            </small>
                            <small class="d-block text-muted">
                              {{ __('Early Bird') }}:
                              {{ $abs->base_currency_symbol_position == 'left' ? $abs->base_currency_symbol : '' }}{{ round($booking->early_bird_discount, 2) }}{{ $abs->base_currency_symbol_position == 'right' ? $abs->base_currency_symbol : '' }}
                            </small>
                          </td>
                          <td>
                            <div>{{ ucfirst($booking->paymentMethod) }}</div>
                            @if ($booking->paymentStatus == 'pending')
                              <span class="badge badge-warning">{{ __('Pending') }}</span>
                            @elseif ($booking->paymentStatus == 'completed')
                              <span class="badge badge-success">{{ __('Completed') }}</span>
                            @else
                              <span class="badge badge-danger">{{ __('Rejected') }}</span>
                            @endif
                          </td>
                          <td>{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') }}</td>
                          <td>
                            <button type="button" class="btn btn-info btn-sm receiptPreviewBtn"
                              data-toggle="modal" data-target="#receiptPreviewModal"
                               data-receipt="{{ route('booking.ticket.download', $booking->id) }}">
                              {{ __('View') }}
                            </button>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @elseif(request()->filled('from_date') && request()->filled('to_date'))
                <div class="alert alert-info mb-0">{{ __('No bookings were found for the selected filters.') }}</div>
              @else
                <div class="alert alert-light mb-0">{{ __('Select a date range and optional filters to generate the report.') }}</div>
              @endif
            </div>
          </div>
        </div>

        @if (!empty($bookings))
          <div class="card-footer">
            <div class="row">
              <div class="d-inline-block mx-auto">
                {{ $bookings->appends([
                        'from_date' => request()->input('from_date'),
                        'to_date' => request()->input('to_date'),
                        'payment_method' => request()->input('payment_method'),
                        'payment_status' => request()->input('payment_status'),
                    ])->links() }}
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="modal fade" id="receiptPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Receipt Image') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <iframe src="" frameborder="0" class="receipt" id="receiptPreviewFrame"></iframe>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script>
    'use strict';

    $(document).on('click', '.receiptPreviewBtn', function() {
      $('#receiptPreviewFrame').attr('src', $(this).data('receipt'));
    });

    $('#receiptPreviewModal').on('hidden.bs.modal', function() {
      $('#receiptPreviewFrame').attr('src', '');
    });
  </script>
@endsection
