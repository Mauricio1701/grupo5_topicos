@extends('adminlte::page')

@section('title', 'Contratos')

@section('content_header')
@stop

@section('content')
<div class="p-2"></div>

<!-- Modal -->
<div class="modal fade" id="modalContract" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ModalLongTitle" aria-hidden="true">
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
        <h3 class="card-title">Lista de Contratos</h3>
        <div class="card-tools">
            <button id="btnNewContract" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar Nuevo Contrato</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-striped" id="datatable">
            <thead>
                <tr>
                    <th>EMPLEADO</th>
                    <th>TIPO DE CONTRATO</th>
                    <th>FECHA INICIO</th>
                    <th>FECHA FIN</th>
                    <th>SALARIO</th>
                    <th>POSICIÓN</th>
                    <th>DEPARTAMENTO</th>
                    <th>ESTADO</th>
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
                "url": "{{ route('admin.contracts.index') }}",
                "cache": false
            },
            "columns": [{
                    "data": "employee_name"
                },
                {
                    "data": "contract_type"
                },
                {
                    "data": "start_date"
                },
                {
                    "data": "end_date"
                },
                {
                    "data": "salary"
                },
                {
                    "data": "position"
                },
                {
                    "data": "department"
                },
                {
                    "data": "status"
                },
                {
                    "data": "action",
                    "orderable": false,
                    "searchable": false
                }
            ]
        });

        function initContractForm() {
            var contractTypeSelect = $('#contract_type');
            var endDateField = $('#end_date');
            var endDateContainer = $('#end_date_container');
            var vacationDaysField = $('#vacation_days_per_year');

            $('.vacation-days-notice').remove();

            function updateEndDateField() {
                if (contractTypeSelect.val() === 'Tiempo completo') {
                    endDateField.prop('disabled', true);
                    endDateField.val('');
                    if (endDateContainer) {
                        endDateContainer.addClass('d-none');
                    }
                } else {
                    endDateField.prop('disabled', false);
                    if (endDateContainer) {
                        endDateContainer.removeClass('d-none');
                    }
                }
            }

            function updateVacationDays() {
                var specialContractTypes = ['Temporal', 'Por proyecto', 'Prácticas'];

                $('.vacation-days-notice').remove();

                if (specialContractTypes.includes(contractTypeSelect.val())) {
                    vacationDaysField.val(0);
                    vacationDaysField.prop('disabled', true);
                    vacationDaysField.parent().append('<small class="text-info d-block vacation-days-notice">Los contratos de este tipo no tienen días de vacaciones.</small>');
                } else {
                    vacationDaysField.prop('disabled', false);

                    if (!vacationDaysField.val() || vacationDaysField.val() == '0') {
                        vacationDaysField.val(15); 
                    }
                }
            }

            function toggleTerminationReason() {
                if ($('#is_active').length && $('#termination_reason_container').length) {
                    if ($('#is_active').prop('checked')) {
                        $('#termination_reason_container').addClass('d-none');
                        $('#termination_reason').val('');
                    } else {
                        $('#termination_reason_container').removeClass('d-none');
                    }
                }
            }

            updateEndDateField();
            if (vacationDaysField.length) {
                updateVacationDays();
            }
            toggleTerminationReason();

            contractTypeSelect.on('change', function() {
                updateEndDateField();
                if (vacationDaysField.length) {
                    updateVacationDays();
                }
            });

            $('#is_active').on('change', toggleTerminationReason);
        }

        function setupFormSubmit(form) {
            form.off('submit').on('submit', function(e) {
                e.preventDefault();

                var disabledFields = form.find(':disabled').prop('disabled', false);

                var formData = form.serialize();

                disabledFields.prop('disabled', true);

                if ($('#is_active').length) {
                    formData = formData.replace(/&is_active=1/, '');
                    formData += '&is_active=' + ($('#is_active').prop('checked') ? 1 : 0);
                }

                var contractType = $('#contract_type').val();
                var specialContractTypes = ['Temporal', 'Por proyecto', 'Prácticas'];

                if (specialContractTypes.includes(contractType)) {
                    formData = formData.replace(/&vacation_days_per_year=\d+/, '');
                    formData += '&vacation_days_per_year=0';
                }

                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#modalContract').modal('hide');
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
                        } else {
                            errorMessage = 'Ha ocurrido un error al guardar el contrato.';
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

        $('#btnNewContract').click(function() {
            $('#modalContract .modal-body').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Cargando...</p></div>');

            $.ajax({
                url: "{{ route('admin.contracts.create') }}",
                type: "GET",
                cache: false,
                success: function(response) {
                    $('#ModalLongTitle').text('Nuevo Contrato');
                    $('#modalContract .modal-body').html(response);
                    $('#modalContract').modal('show');

                    initContractForm();
                    setupFormSubmit($('#modalContract form'));
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'No se pudo cargar el formulario de contrato.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        });

        $(document).on('click', '.btnEditar', function() {
            var contractId = $(this).attr('id');
            $('#modalContract .modal-body').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Cargando...</p></div>');

            $.ajax({
                url: "{{ route('admin.contracts.edit', 'id') }}".replace('id', contractId),
                type: "GET",
                cache: false,
                data: {
                    _t: Date.now()
                },
                success: function(response) {
                    $('#ModalLongTitle').text('Editar Contrato');
                    $('#modalContract .modal-body').html(response);
                    $('#modalContract').modal('show');

                    initContractForm();
                    setupFormSubmit($('#modalContract form'));
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'No se pudo cargar el formulario para editar el contrato.',
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
                            text: 'Ha ocurrido un error al eliminar el contrato.',
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