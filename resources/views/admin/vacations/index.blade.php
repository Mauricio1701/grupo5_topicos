@extends('adminlte::page')

@section('title', 'Vacaciones')

@section('content_header')
@stop

@section('content')
<div class="p-2"></div>

<!-- Modal -->
<div class="modal fade" id="modalVacation" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Solicitudes de Vacaciones</h3>
        <div class="card-tools">
            <button id="btnNewVacation" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar Nueva Solicitud</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-striped" id="datatable">
            <thead>
                <tr>
                    <th>EMPLEADO</th>
            <th>FECHA SOLICITUD</th>
            <th>DÍAS SOLICITADOS</th>
            <th>FECHA FINAL</th>
            <th>DÍAS DISPONIBLES AL SOLICITAR</th>
            <th>DÍAS DISPONIBLES ACTUALES</th>
            <th>ESTADO</th>
            <th>NOTAS</th>
            <th>ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    let table;

    $(document).ready(function() {
        table = $('#datatable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('admin.vacations.index') }}",
                "type": "GET",
                "cache": false,
                "error": function(xhr, error, thrown) {
                    console.error('Error en la carga de datos:', error, thrown);
                }
            },
            "columns": [{
                    "data": "employee_name",
                    "name": "employee_name"
                },
                {
                    "data": "request_date_formatted",
                    "name": "request_date"
                },
                {
                    "data": "requested_days",
                    "name": "requested_days"
                },
                {
                    "data": "end_date_formatted",
                    "name": "end_date"
                },
                {
                    "data": "available_days",
                    "name": "available_days",
                    "title": "DÍAS DISPONIBLES AL SOLICITAR"
                },
                {
                    "data": "current_available_days",
                    "name": "current_available_days",
                    "title": "DÍAS DISPONIBLES ACTUALES"
                },
                {
                    "data": "status_badge",
                    "name": "status",
                    "orderable": false
                },
                {
                    "data": "notes",
                    "name": "notes"
                },
                {
                    "data": "action",
                    "name": "action",
                    "orderable": false,
                    "searchable": false
                }
            ],
            "order": [
                [1, "desc"]
            ], // Ordenar por fecha de solicitud descendente
            "pageLength": 10,
            "stateSave": false, // Evita problemas de caché
            "searching": true
        });

        $('#btnNewVacation').click(function() {
            $('#modalVacation .modal-body').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Cargando...</p></div>');

            $.ajax({
                url: "{{ route('admin.vacations.create') }}",
                type: "GET",
                cache: false,
                success: function(response) {
                    $('#ModalLongTitle').text('Nueva Solicitud de Vacaciones');
                    $('#modalVacation .modal-body').html(response);
                    $('#modalVacation').modal('show');

                    initVacationForm();
                    setupFormSubmit($('#modalVacation form'));
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'No se pudo cargar el formulario de vacaciones.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        });

        function initVacationForm() {
            // Inicializar datepicker
            // Dentro de la función initVacationForm()
            if ($.fn.datepicker) {
                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    language: 'es',
                    startDate: new Date(new Date().setDate(new Date().getDate() + 11)) // 11 días desde hoy (hoy + 10 días prohibidos + 1)
                });
            }
            $('#employee_id').off('change').on('change', function() {
                var employeeId = $(this).val();
                var isEdit = $('#vacationForm').attr('action').includes('update');
                var originalEmployeeId = isEdit ? '{{ isset($vacation) ? $vacation->employee_id : "" }}' : '';

                if (employeeId) {
                    // Mostrar mensaje de carga
                    $('.available-days-info').text('Verificando días disponibles...');

                    $.ajax({
                        url: "{{ route('admin.vacations.check-available-days') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            employee_id: employeeId,
                            is_edit: isEdit,
                            vacation_id: isEdit ? $('#vacationForm').attr('action').split('/').pop() : null
                        },
                        success: function(response) {
                            if (response.success) {
                                // Si estamos en modo edición y cambiamos a un nuevo empleado
                                if (isEdit && employeeId != originalEmployeeId) {
                                    // Restamos los días solicitados actualmente
                                    var requestedDays = parseInt($('#requested_days').val() || 0);
                                    $('#available_days').val(response.available_days - requestedDays);
                                } else {
                                    $('#available_days').val(response.available_days);
                                }
                                $('.available-days-info').text('Días disponibles: ' + response.available_days);
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Advertencia',
                                    text: response.message,
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'Aceptar'
                                });
                                $('#available_days').val(0);
                                $('.available-days-info').text('Días disponibles: 0');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error al verificar días disponibles:', xhr);
                            Swal.fire({
                                icon: 'error',
                                title: '¡Error!',
                                text: 'No se pudieron verificar los días disponibles.',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                            $('#available_days').val(0);
                            $('.available-days-info').text('Días disponibles: 0');
                        }
                    });
                } else {
                    $('#available_days').val(0);
                    $('.available-days-info').text('Días disponibles: 0');
                }
            });

            // Manejar cambio en días solicitados para actualizar días disponibles y fecha final
            $('#requested_days').off('change keyup').on('change keyup', function() {
                calculateEndDate();

                // Actualizar días disponibles si cambiamos los días solicitados en edición
                var isEdit = $('#vacationForm').attr('action').includes('update');
                if (isEdit) {
                    var originalDays = parseInt($('input[name="original_requested_days"]').val() || 0);
                    var newDays = parseInt($(this).val() || 0);
                    var currentAvailable = parseInt($('#available_days').val() || 0);
                    var difference = originalDays - newDays;

                    $('#available_days').val(currentAvailable + difference);
                }
            });

            // Manejar cambio en fecha de solicitud para actualizar fecha final
            $('#request_date').off('change').on('change', function() {
                calculateEndDate();
            });
        }

        function calculateEndDate() {
            var requestDate = $('#request_date').val();
            var requestedDays = $('#requested_days').val();

            if (requestDate && requestedDays) {
                $.ajax({
                    url: "{{ route('admin.vacations.calculate-days') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        request_date: requestDate,
                        requested_days: requestedDays
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#end_date').val(response.end_date);
                        }
                    }
                });
            }
        }

        function checkAvailableDays(employeeId) {
            $.ajax({
                url: "{{ route('admin.vacations.check-available-days') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    employee_id: employeeId
                },
                success: function(response) {
                    if (response.success) {
                        $('#available_days').val(response.available_days);
                        $('.available-days-info').text('Días disponibles: ' + response.available_days);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Advertencia',
                            text: response.message,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Aceptar'
                        });
                        $('#available_days').val(0);
                        $('.available-days-info').text('Días disponibles: 0');
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'No se pudieron verificar los días disponibles.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    });
                    $('#available_days').val(0);
                    $('.available-days-info').text('Días disponibles: 0');
                }
            });
        }

        function setupFormSubmit(form) {
            form.off('submit').on('submit', function(e) {
                e.preventDefault();

                var formData = form.serialize();

                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#modalVacation').modal('hide');
                            table.ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON?.errors;
                        var errorMessage = '';

                        if (errors) {
                            $.each(errors, function(key, value) {
                                errorMessage += value + '<br>';
                            });
                        } else if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else {
                            errorMessage = 'Ha ocurrido un error al guardar la solicitud de vacaciones.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            html: errorMessage,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            });
        }

        $(document).on('click', '.btnEditar', function() {
            var vacationId = $(this).attr('id');
            $('#modalVacation .modal-body').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Cargando...</p></div>');

            $.ajax({
                url: "{{ route('admin.vacations.edit', 'id') }}".replace('id', vacationId),
                type: "GET",
                cache: false,
                data: {
                    _t: Date.now()
                },
                success: function(response) {
                    $('#ModalLongTitle').text('Editar Solicitud de Vacaciones');
                    $('#modalVacation .modal-body').html(response);
                    $('#modalVacation').modal('show');

                    initVacationForm();
                    setupFormSubmit($('#modalVacation form'));
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'No se pudo cargar el formulario para editar la solicitud de vacaciones.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        });

        $('#btnRefresh').click(function() {
            table.ajax.reload(null, false);
        });
    });



    function confirmDelete(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Este cambio no se puede deshacer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $("#delete-form-" + id).attr('action'),
                    type: 'POST',
                    data: $("#delete-form-" + id).serialize(),
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: 'Ha ocurrido un error al eliminar la solicitud de vacaciones.',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    }
</script>
@stop