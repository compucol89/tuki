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
        <form action="{{ route('admin.basic_settings.billing_settings.update') }}" method="post">
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
                  <input type="number" min="1" name="default_invoice_type" class="form-control"
                    value="{{ old('default_invoice_type', $billingSettings->default_invoice_type) }}">
                  @if ($errors->has('default_invoice_type'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('default_invoice_type') }}</p>
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
