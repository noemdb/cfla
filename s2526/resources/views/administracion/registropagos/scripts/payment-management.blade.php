<script>
$(document).ready(function() {

    // Show payment details modal
    $('.btn-modal-details').click(function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var modal = '#modal_payment_details';
        var container = '#container_modal';
        var ajaxurl = '{{ route("administracion.ajax.registropago.details", "_id_") }}';
        ajaxurl = ajaxurl.replace('_id_', id);

        $.ajax({
            url: ajaxurl,
            type: "GET",
            beforeSend: function() {
                $(container).html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>');
            },
            success: function(data) {
                $(container).html(data);
                $(modal).modal('show');
            },
            error: function(xhr) {
                showAlert('Error al cargar los detalles del pago', 'error');
            }
        });
    });

    // Show cancellation form modal
    $('.btn-cancel-payment').click(function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var modal = '#modal_cancellation_form';
        var container = '#container_modal';
        var ajaxurl = '{{ route("administracion.ajax.registropago.cancellation-form", "_id_") }}';
        ajaxurl = ajaxurl.replace('_id_', id);

        $.ajax({
            url: ajaxurl,
            type: "GET",
            beforeSend: function() {
                $(container).html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>');
            },
            success: function(data) {
                $(container).html(data);
                $(modal).modal('show');
            },
            error: function(xhr) {
                showAlert('Error al cargar el formulario de anulación', 'error');
            }
        });
    });

    // Handle cancellation form submission
    $(document).on('click', '#btn-confirm-cancel', function(e) {
        e.preventDefault();
        var form = $('#form-cancel-payment');
        var id = form.data('id');
        var reason = $('#cancellation_reason').val().trim();

        if (!reason) {
            showAlert('Por favor ingrese el motivo de la anulación', 'warning');
            return;
        }

        var url = '{{ route("administracion.registropagos.cancel", "_id_") }}';
        url = url.replace('_id_', id);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                cancellation_reason: reason
            },
            beforeSend: function() {
                $('#btn-confirm-cancel').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Procesando...');
            },
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    $('#modal_cancellation_form').modal('hide');
                    updatePaymentRow(id, 'pending_approval');
                } else {
                    showAlert(response.message, 'error');
                }
            },
            error: function(xhr) {
                var message = 'Error al procesar la anulación';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showAlert(message, 'error');
            },
            complete: function() {
                $('#btn-confirm-cancel').prop('disabled', false).html('<i class="fas fa-times mr-1"></i> Anular Pago');
            }
        });
    });

    // Approve cancellation
    $('.btn-approve-cancel').click(function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        Swal.fire({
            title: '¿Aprobar anulación?',
            text: '¿Está seguro que desea aprobar la anulación de este pago?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                var url = '{{ route("administracion.registropagos.approve-cancel", "_id_") }}';
                url = url.replace('_id_', id);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            updatePaymentRow(id, 'cancelled');
                        } else {
                            showAlert(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        var message = 'Error al aprobar la anulación';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showAlert(message, 'error');
                    }
                });
            }
        });
    });

    // Update payment row status
    function updatePaymentRow(id, status) {
        var row = $('tr[data-id="' + id + '"]');
        var statusCell = row.find('td').eq(5); // Status column
        var actionCell = row.find('td').eq(10); // Action column

        // Update status badge
        var statusBadge = '';
        switch(status) {
            case 'pending_approval':
                statusBadge = '<span class="status-badge status-pending"><i class="fas fa-clock mr-1"></i>Pendiente Aprobación</span>';
                break;
            case 'cancelled':
                statusBadge = '<span class="status-badge status-cancelled"><i class="fas fa-times-circle mr-1"></i>Anulado</span>';
                break;
        }
        statusCell.html(statusBadge);

        // Update action buttons
        if (status === 'pending_approval') {
            actionCell.find('.btn-cancel-payment').remove();
            actionCell.find('.btn-group').append(
                '<button title="Aprobar anulación" class="btn-approve-cancel btn btn-success btn-sm" data-id="' + id + '">' +
                '<i class="fa fa-check"></i></button>'
            );
        } else if (status === 'cancelled') {
            actionCell.find('.btn-cancel-payment, .btn-approve-cancel').remove();
        }

        // Update row data attribute
        row.attr('data-status', status);
    }

    // Show alert function
    function showAlert(message, type) {
        var icon = type === 'success' ? 'success' : (type === 'warning' ? 'warning' : 'error');

        Swal.fire({
            title: type === 'success' ? '¡Éxito!' : (type === 'warning' ? '¡Atención!' : '¡Error!'),
            text: message,
            icon: icon,
            confirmButtonText: 'Entendido'
        });
    }

    // Historical modal (existing functionality)
    $('.btn-modal-historico').click(function (e) {
        e.preventDefault();
        var row = $(this).parents('tr');
        var id = row.data('representant_id');
        var modal = '#modal_historico';
        var container = '#container_modal';
        var ajaxurl = '{{route("administracion.ajax.fill.modal.representant_historico_pago", "_id_")}}';
        ajaxurl = ajaxurl.replace('_id_', id);

        $.ajax({
            url: ajaxurl,
            type: "GET",
            success: function(data){
                $(container).html(data);
                $(modal).modal('toggle');
            }
        });
    });

});
</script>
