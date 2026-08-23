{{-- attachment modal --}}
<div class="modal fade" id="attachmentModal-{{ $booking->id }}" tabindex="-1" role="dialog" aria-labelledby="attachmentModalLabel-{{ $booking->id }}" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="attachmentModalLabel-{{ $booking->id }}">
          {{ __('Comprobante') }}
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Cerrar comprobante') }}">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <img src="{{ asset('assets/admin/file/attachments/' . $booking->attachmentFile) }}" alt="{{ __('Comprobante de la reserva') }} #{{ $booking->booking_id }}" width="100%">
      </div>

      <div class="modal-footer"></div>
    </div>
  </div>
</div>
