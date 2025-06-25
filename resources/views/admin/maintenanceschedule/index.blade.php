@extends('adminlte::page')

@section('title', 'Prog. Mantenimientos')

@section('content_header')
    
@stop

@section('content')
<div class="p-2"></div>

<!-- Modal -->
<div class="modal fade " id="modalMaintenance" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ModalLongTitle" aria-hidden="true">
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
        <h3 class="card-title">Lista de Programación - {{$maintenance->name}}</h3>
        <div class="card-tools">
            <a href="{{route('admin.maintenance.index')}}" class="btn btn-info">Volver</a>
            <button id="btnNewMaintenance" class="btn btn-primary" ><i class="fas fa-plus"></i> Agregar Programación</button>    
        </div>
    </div>
    <div class="card-body table-responsive">
            <table class="table table-striped" id="datatable" style="width:100%">
                <thead >
                    <tr>
                        <th>DÍA</th>
                        <th>VEHÍCULO</th>
                        <th>CONDUCTOR</th>
                        <th>TIPO</th>
                        <th>INICIO</th>
                        <th>FIN</th>
                        <th>ACCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        
       
    
</div>
@stop

@section('css')

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#datatable').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
            emptyTable: "No hay registros disponibles"
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.maintenanceschedule.getSchedule', ['id' => $maintenance->id]) }}",
        },
        columns: [
            { data: 'day_of_week', name: 'day_of_week' },
            { data: 'vehicle_name', name: 'vehicle_name' },
            { data: 'employee_name', name:'employee_name'},
            { data: 'maintenance_type', name:'maintenance_type'},
            { data: 'formatted_start_time', name: 'formatted_start_time', orderable: false, searchable: false },
            { data: 'formatted_end_time', name: 'formatted_end_time' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']]
    });

    const id = '{{ $maintenance->id }}';  

    function setMaintenanceDateRange() {
        const startDate = '{{ $maintenance->start_date }}';  
        const endDate = '{{ $maintenance->end_date }}';     
        const maintenanceDateInput = document.getElementById('maintenance_date');
        console.log("holaaa")
        
        // Establecer el valor mínimo y máximo para el campo de fecha de mantenimiento
        maintenanceDateInput.setAttribute('min', startDate);  // La fecha mínima será la de inicio
        maintenanceDateInput.setAttribute('max', endDate);    // La fecha máxima será la de fin
    }

    // Nuevo empleado - abrir modal
    $('#btnNewMaintenance').click(function() {
        $.ajax({
            url: "{{ route('admin.maintenanceschedule.create') }}",
            type: "GET",
            success: function(response) {
                $('#ModalLongTitle').text('Nuevo Mantenimiento');
                $('#modalMaintenance .modal-body').html(response);
                $('#modalMaintenance').modal('show');
                setMaintenanceDateRange();

                // Enviar formulario AJAX para crear
                $('#modalMaintenance form').submit(function(e) {
                    console.log('Formulario enviado');
                    e.preventDefault();
                    var form = $(this);
                    var formData = new FormData(this);
                    formData.append('maintenance_id', id);

                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#modalMaintenance').modal('hide');
                            table.ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        },
                        error: function(xhr) {
                            let res = xhr.responseJSON;
                            let errors = res.errors || {};
                            let errorMessage = res.message || 'Ocurrió un error';
                            
                            if (Object.keys(errors).length > 0) {
                                errorMessage = Object.values(errors).flat().join('\n');
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: '¡Error!',
                                text: errorMessage,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    });
                });
            }
        });
    });

    // Editar empleado - abrir modal
    $(document).on('click', '.btnEditar', function() {
        var attendanceId = $(this).attr('id');
        $.ajax({
            url: "{{ route('admin.maintenanceschedule.edit', ':id') }}".replace(':id', attendanceId),
            type: "GET",
            success: function(response) {
                $('#ModalLongTitle').text('Editar Mantenimiento');
                $('#modalMaintenance .modal-body').html(response);
                $('#modalMaintenance').modal('show');
                setMaintenanceDateRange();
                // Enviar formulario AJAX para actualizar
                $('#modalMaintenance form').submit(function(e) {
                    e.preventDefault();
                    var form = $(this);
                    var formData = new FormData(this);
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#modalMaintenance').modal('hide');
                            table.ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        },
                        error: function(xhr) {
                            let res = xhr.responseJSON;
                            let errors = res.errors || {};
                            let errorMessage = res.message || 'Ocurrió un error';
                            
                            if (Object.keys(errors).length > 0) {
                                errorMessage = Object.values(errors).flat().join('\n');
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: '¡Error!',
                                text: errorMessage,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    });
                });
            }
        });
    });

    // Eliminar empleado con confirmación
    $(document).on('submit', '.delete', function(e) {
        e.preventDefault();
        let form = $(this);

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
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Aceptar'
                        });
                    },
                    error: function(xhr) {
                        let res = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: res?.message || 'Ocurrió un error',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
        

     


        
    });

    
});
</script>
@stop