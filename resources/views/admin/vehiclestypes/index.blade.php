
@extends('adminlte::page')

@section('title', 'Vehiclestypes')

@section('content_header')
    
@stop

@section('content')
<div class="p-2"></div>

<!-- Modal -->
<div class="modal fade" id="modalVehiclestype" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
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
        <h3 class="card-title">Lista de Tipos de Vehiculos</h3>
        <div class="card-tools">
            <button id="btnNewVehiclestype" class="btn btn-primary" ><i class="fas fa-plus"></i> Agregar Nuevo Tipo de Vehiculo</button> 
        </div>
    </div>
    <div class="card-body">
            <table class="table table-striped" id="datatable">
                <thead >
                    <tr>
                        <th>NOMBRE</th>
                        <th>DESCRIPCIÓN</th>
                        <th>CREADO</th>
                        <th>ACTUALIZADO</th>
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
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
        },
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.vehiclestypes.index') }}",
        columns: [
            { data: "name" },
            { data: "description" },
            {
                data: "created_at",
                render: function(data) {
                    if (!data) return '';
                    let fecha = new Date(data);
                    let dia = ('0' + fecha.getDate()).slice(-2);
                    let mes = ('0' + (fecha.getMonth() + 1)).slice(-2);
                    let anio = fecha.getFullYear();
                    return `${dia}-${mes}-${anio}`;
                }
            },
            {
                data: "updated_at",
                render: function(data) {
                    if (!data) return '';
                    let fecha = new Date(data);
                    let dia = ('0' + fecha.getDate()).slice(-2);
                    let mes = ('0' + (fecha.getMonth() + 1)).slice(-2);
                    let anio = fecha.getFullYear();
                    return `${dia}-${mes}-${anio}`;
                }
            },
            { data: "action", orderable: false, searchable: false }
        ]
    });
});


    $('#btnNewVehiclestype').click(function() {
        $.ajax({
            url: "{{ route('admin.vehiclestypes.create') }}",
            type: "GET",
            success: function(response) {
                $('#ModalLongTitle').text('Nuevo Tipo Vehiculo');
                $('#modalVehiclestype .modal-body').html(response);
                $('#modalVehiclestype').modal('show');

                $('#modalVehiclestype form').submit(function(e){
                    e.preventDefault();
                    var form = $(this);
                    var formData = form.serialize();
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: formData,
                        success: function(response) {
                            if(response.success){
                                $('#modalVehiclestype').modal('hide');
                                table.ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: response.message,
                                    confirmButtonVehiclestype: '#3085d6',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        },
                        error: function(xhr) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = '';
                            $.each(errors, function(key, value) {
                                errorMessage += value + '<br>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: '¡Error!',
                                html: errorMessage,
                                confirmButtonVehiclestype: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    });
                });
            }
        });
    });

    $(document).on('click', '.btnEditar', function() {
        var vehiclestypeId = $(this).attr('id');
        $.ajax({
            url: "{{ route('admin.vehiclestypes.edit', 'id') }}".replace('id', vehiclestypeId),
            type: "GET",
            success: function(response) {
                $('#ModalLongTitle').text('Editar Tipo de Vehiculo');
                $('#modalVehiclestype .modal-body').html(response);
                $('#modalVehiclestype').modal('show');

                $('#modalVehiclestype form').submit(function(e){
                    e.preventDefault();
                    var form = $(this);
                    var formData = form.serialize();
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: formData,
                        success: function(response) {
                            if(response.success){
                                $('#modalVehiclestype').modal('hide');
                                table.ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: response.message,
                                    confirmButtonVehiclestype: '#3085d6',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        },
                        error: function(xhr) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = '';
                            $.each(errors, function(key, value) {
                                errorMessage += value + '<br>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: '¡Error!',
                                html: errorMessage,
                                confirmButtonVehiclestype: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    });
                });
            }
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
                        if(response.success){
                            table.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                confirmButtonVehiclestype: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: 'Ha ocurrido un error al eliminar el Tipo de Vehiculo.',
                            confirmButtonVehiclestype: '#3085d6',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    }
</script>

@stop