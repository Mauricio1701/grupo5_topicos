@extends('adminlte::page')

@section('title', 'Solicitudes de Vacaciones')

@section('content')
<div class="p-2"></div>


<div class="modal fade" id="modalVacation" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- Contenido del formulario cargado por AJAX --}}
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Solicitudes de Vacaciones</h3>
        <div class="card-tools">
            <button id="btnNewVacation" class="btn btn-primary"><i class="fas fa-plus"></i> Nueva Solicitud</button>
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped" id="datatableVacations" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>EMPLEADO</th>
                    <th>FECHA INICIO</th>
                    <th>FECHA FIN</th>
                    <th>DÍAS SOLICITADOS</th>
                    <th>DÍAS DISPONIBLES</th>
                    <th>ESTADO</th>
                    <th>NOTAS</th>
                    <th>ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
                {{-- Datos cargados por DataTables --}}
            </tbody>
        </table>
    </div>
</div>
@stop
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        line-height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        var table = $('#datatableVacations').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
            },
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.vacations.index') }}",
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'employee_name',
                    name: 'employee_name'
                },
                {
                    data: 'request_date',
                    name: 'request_date'
                },
                {
                    data: 'end_date',
                    name: 'end_date'
                },
                {
                    data: 'requested_days',
                    name: 'requested_days'
                },
                {
                    data: 'available_days',
                    name: 'available_days'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'notes',
                    name: 'notes'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#btnNewVacation').click(function() {
            $.ajax({
                url: "{{ route('admin.vacations.create') }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#ModalLongTitle').text('Nueva Solicitud de Vacaciones');
                    $('#modalVacation .modal-body').html(response);
                    $('#modalVacation').modal('show');
                    
                    $('#employee_id').select2({
                        dropdownParent: $('#modalVacation'), 
                        placeholder: 'Seleccione un empleado',
                        width: '100%'
                    });
                    
                    setupDateCalculation();
                    setupEmployeeCheck();
                },
                error: function(xhr, status, error) {
                    
                }
            });
        });
        $('#btnNewVacation').click(function() {
            $.ajax({
                url: "{{ route('admin.vacations.create') }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#ModalLongTitle').text('Nueva Solicitud de Vacaciones');
                    $('#modalVacation .modal-body').html(response);
                    $('#modalVacation').modal('show');
                    setupDateCalculation();
                    setupEmployeeCheck();
                },
                error: function(xhr, status, error) {
                    let mensaje = 'No se pudo cargar el formulario. Por favor, intente nuevamente.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: mensaje
                    });
                }
            });
            setupEmployeeCheck();
        });

        $(document).on('click', '.btnEditar', function() {
            var vacationId = $(this).attr('id');
            $.ajax({
                url: "{{ route('admin.vacations.edit', ':id') }}".replace(':id', vacationId),
                type: "GET",
                success: function(response) {
                    $('#ModalLongTitle').text('Editar Solicitud de Vacaciones');
                    $('#modalVacation .modal-body').html(response);
                    $('#modalVacation').modal('show');
                    
                    $('#employee_id').select2({
                        dropdownParent: $('#modalVacation'),
                        placeholder: 'Seleccione un empleado',
                        width: '100%'
                    });
                    
                    setupDateCalculation();
                    setupEmployeeCheck();
                },
                error: function(xhr) {
                }
            });
        });

        $(document).on('submit', '.formDelete', function(e) {
            e.preventDefault();
            let form = $(this);

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            table.ajax.reload();
                            Swal.fire(
                                '¡Eliminado!',
                                response.message,
                                'success'
                            );
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error',
                                'Ocurrió un error al eliminar',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        $(document).on('submit', '#vacationForm', function(e) {
            e.preventDefault();
            let form = $(this);
            let url = form.attr('action');
            let method = form.attr('method');
            let formData = new FormData(form[0]);

            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#modalVacation').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = '';

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            errorMessage += errors[field][0] + '<br>';
                        }
                    }
                    else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    else {
                        errorMessage = 'Ha ocurrido un error al procesar la solicitud';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMessage
                    });
                }
            });
        });
    });

    function setupDateCalculation() {
        $('#request_date, #end_date').change(function() {
            let startDate = $('#request_date').val();
            let endDate = $('#end_date').val();

            if (startDate && endDate) {
                $.ajax({
                    url: "{{ route('admin.vacations.calculate-days') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#requested_days').val(response.days);
                        }
                    }
                });
            }
        });
    }

    function setupEmployeeCheck() {
        $('#employee_id').change(function() {
            let employeeId = $(this).val();

            if (employeeId) {
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
                        }
                    }
                });
            }
        });
    }
</script>
@stop