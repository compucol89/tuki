@extends('backend.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Configuración de facturación') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Configuración Básica') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Facturación ARCA') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <form action="{{ route('admin.basic_settings.billing_settings.update') }}" method="post" enctype="multipart/form-data">
          @csrf
          <div class="card-header">
            <div class="card-title">{{ __('Facturación ARCA') }}</div>
          </div>

          <div class="card-body">
            <div class="row">
              <div class="col-lg-8 mx-auto">
                <div class="alert alert-warning">
                  {{ __('La emisión automática ARCA permanece desactivada hasta completar la validación contable y la integración de pagos.') }}
                </div>

                <div class="form-group">
                  <label>{{ __('Emisión automática') }}</label>
                  <select name="enabled" class="form-control">
                    <option value="0" {{ old('enabled', (int) $billingSettings->enabled) == 0 ? 'selected' : '' }}>
                      {{ __('Desactivada') }}
                    </option>
                    <option value="1" {{ old('enabled', (int) $billingSettings->enabled) == 1 ? 'selected' : '' }}>
                      {{ __('Activada') }}
                    </option>
                  </select>
                  @if ($errors->has('enabled'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('enabled') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Ambiente') . '*' }}</label>
                  <select name="environment" class="form-control">
                    <option value="testing" {{ old('environment', $billingSettings->environment) == 'testing' ? 'selected' : '' }}>
                      {{ __('Testing') }}
                    </option>
                    <option value="production" {{ old('environment', $billingSettings->environment) == 'production' ? 'selected' : '' }}>
                      {{ __('Producción') }}
                    </option>
                  </select>
                  @if ($errors->has('environment'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('environment') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('CUIT emisor') }}</label>
                  <input type="text" name="issuer_cuit" class="form-control"
                    value="{{ old('issuer_cuit', $billingSettings->issuer_cuit) }}">
                  @if ($errors->has('issuer_cuit'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('issuer_cuit') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Condición IVA emisor') }}</label>
                  <input type="text" name="issuer_iva_condition" class="form-control"
                    value="{{ old('issuer_iva_condition', $billingSettings->issuer_iva_condition) }}">
                  @if ($errors->has('issuer_iva_condition'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('issuer_iva_condition') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Punto de venta') }}</label>
                  <input type="number" min="1" name="point_of_sale" class="form-control"
                    value="{{ old('point_of_sale', $billingSettings->point_of_sale) }}">
                  @if ($errors->has('point_of_sale'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('point_of_sale') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Comisión de servicio (%)') . '*' }}</label>
                  <input type="number" min="0" max="100" step="0.0001" name="service_fee_percentage"
                    class="form-control" value="{{ old('service_fee_percentage', $billingSettings->service_fee_percentage) }}">
                  @if ($errors->has('service_fee_percentage'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('service_fee_percentage') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Tratamiento IVA comisión') . '*' }}</label>
                  <select name="service_fee_tax_mode" class="form-control">
                    <option value="no_vat_added"
                      {{ old('service_fee_tax_mode', $billingSettings->service_fee_tax_mode) == 'no_vat_added' ? 'selected' : '' }}>
                      {{ __('Sin IVA agregado') }}
                    </option>
                    <option value="vat_added"
                      {{ old('service_fee_tax_mode', $billingSettings->service_fee_tax_mode) == 'vat_added' ? 'selected' : '' }}>
                      {{ __('IVA agregado') }}
                    </option>
                    <option value="vat_included"
                      {{ old('service_fee_tax_mode', $billingSettings->service_fee_tax_mode) == 'vat_included' ? 'selected' : '' }}>
                      {{ __('IVA incluido') }}
                    </option>
                  </select>
                  @if ($errors->has('service_fee_tax_mode'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('service_fee_tax_mode') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('IVA (%)') . '*' }}</label>
                  <input type="number" min="0" max="100" step="0.0001" name="vat_percentage" class="form-control"
                    value="{{ old('vat_percentage', $billingSettings->vat_percentage) }}">
                  @if ($errors->has('vat_percentage'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('vat_percentage') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Tipo de comprobante por defecto') }}</label>
                  <select name="default_invoice_type" class="form-control">
                    <option value="">{{ __('Seleccionar tipo de comprobante') }}</option>
                    <option value="6" {{ (string) old('default_invoice_type', $billingSettings->default_invoice_type) === '6' ? 'selected' : '' }}>
                      {{ __('Factura B — Consumidor final (6)') }}
                    </option>
                    <option value="2" {{ (string) old('default_invoice_type', $billingSettings->default_invoice_type) === '2' ? 'selected' : '' }}>
                      {{ __('Factura A — Responsable inscripto (2)') }}
                    </option>
                    <option value="11" {{ (string) old('default_invoice_type', $billingSettings->default_invoice_type) === '11' ? 'selected' : '' }}>
                      {{ __('Factura C — Monotributo (11)') }}
                    </option>
                  </select>
                  @if ($errors->has('default_invoice_type'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('default_invoice_type') }}</p>
                  @endif
                </div>

                <hr>
                <h5 class="mb-3">{{ __('Plantilla de factura') }}</h5>

                <div class="form-group">
                  <label>{{ __('Descripción del item de factura') }}</label>
                  <input type="text" name="invoice_item_description" class="form-control"
                    value="{{ old('invoice_item_description', $billingSettings->invoice_item_description) }}"
                    placeholder="Comisión por servicio de gestión de compra de entradas TukiPass">
                  <small class="form-text text-muted">{{ __('Podés usar las variables {evento} y {reserva} que se reemplazarán automáticamente.') }}</small>
                  @if ($errors->has('invoice_item_description'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('invoice_item_description') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="invoice_item_include_event" class="custom-control-input"
                      id="invoice_item_include_event" value="1"
                      {{ old('invoice_item_include_event', $billingSettings->invoice_item_include_event) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="invoice_item_include_event">{{ __('Incluir nombre del evento') }}</label>
                  </div>
                  @if ($errors->has('invoice_item_include_event'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('invoice_item_include_event') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="invoice_item_include_booking" class="custom-control-input"
                      id="invoice_item_include_booking" value="1"
                      {{ old('invoice_item_include_booking', $billingSettings->invoice_item_include_booking) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="invoice_item_include_booking">{{ __('Incluir número de reserva') }}</label>
                  </div>
                  @if ($errors->has('invoice_item_include_booking'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('invoice_item_include_booking') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Nombre del emisor') }}</label>
                  <input type="text" name="issuer_name" class="form-control"
                    value="{{ old('issuer_name', $billingSettings->issuer_name) }}">
                  @if ($errors->has('issuer_name'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('issuer_name') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Dirección fiscal del emisor') }}</label>
                  <input type="text" name="issuer_address" class="form-control"
                    value="{{ old('issuer_address', $billingSettings->issuer_address) }}">
                  @if ($errors->has('issuer_address'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('issuer_address') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Texto condición IVA del emisor') }}</label>
                  <input type="text" name="issuer_iva_condition_text" class="form-control"
                    value="{{ old('issuer_iva_condition_text', $billingSettings->issuer_iva_condition_text) }}">
                  @if ($errors->has('issuer_iva_condition_text'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('issuer_iva_condition_text') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Logo para PDF fiscal') }}</label>
                  <input type="file" name="pdf_logo" class="form-control-file" accept="image/png,image/jpeg">
                  @if ($billingSettings->pdf_logo_path)
                    <p class="mt-2 mb-0">
                      <img src="{{ asset('storage/' . $billingSettings->pdf_logo_path) }}" alt="{{ __('Logo') }}" style="max-height: 60px;">
                    </p>
                  @endif
                  @if ($errors->has('pdf_logo'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('pdf_logo') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="send_arca_invoice_email" class="custom-control-input"
                      id="send_arca_invoice_email" value="1"
                      {{ old('send_arca_invoice_email', $billingSettings->send_arca_invoice_email) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="send_arca_invoice_email">{{ __('Enviar email de factura fiscal') }}</label>
                  </div>
                  @if ($errors->has('send_arca_invoice_email'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('send_arca_invoice_email') }}</p>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <div class="card-footer">
            <div class="row">
              <div class="col-12 text-center">
                <button type="submit" class="btn btn-success">
                  {{ __('Update') }}
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
