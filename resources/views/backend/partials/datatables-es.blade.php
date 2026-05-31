<script>
  $(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#basic-datatables')) {
      $('#basic-datatables').DataTable().destroy();
    }
    $('#basic-datatables').DataTable({
      destroy: true,
      ordering: false,
      responsive: true,
      language: {
        decimal: '',
        emptyTable: 'No hay información',
        info: 'Mostrando _START_ a _END_ de _TOTAL_ entradas',
        infoEmpty: 'Mostrando 0 a 0 de 0 entradas',
        infoFiltered: '(Filtrado de _MAX_ entradas totales)',
        infoPostFix: '',
        thousands: ',',
        lengthMenu: 'Mostrar _MENU_ entradas',
        loadingRecords: 'Cargando...',
        processing: 'Procesando...',
        search: 'Buscar:',
        zeroRecords: 'Sin resultados encontrados',
        paginate: {
          first: 'Primero',
          last: 'Último',
          next: 'Siguiente',
          previous: 'Anterior'
        }
      }
    });
  });
</script>
