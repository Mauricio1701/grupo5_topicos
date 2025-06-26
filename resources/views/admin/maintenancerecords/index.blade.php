@extends('adminlte::page')

@section('title', 'Prog. Actividades')

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
        <h3 class="card-title">Lista de Actividades - {{$maintenance->name}} - {{$maintenanceschedule->day_of_week}}</h3>
        <div class="card-tools">
            <a href="{{route('admin.maintenanceschedule.getSchedule', ['id' => $maintenance->id])}}" class="btn btn-info">Volver</a>
            <button id="btnNewMaintenance" class="btn btn-primary" ><i class="fas fa-plus"></i> Agregar Actividad</button>    
        </div>
    </div>
    <div class="card-body table-responsive">
            <table class="table table-striped" id="datatable" style="width:100%">
                <thead >
                    <tr>
                        <th>FECHA</th>
                        <th>DESCRIPCION</th>
                        <th>IMAGEN</th>
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
            url: "{{ route('admin.maintenancerecord.getSchedule', ['id' => $maintenanceschedule->id]) }}",
        },
        columns: [
            { data: 'maintenance_date', name: 'maintenance_date' },
            { data: 'description', name: 'description' },
            { data: 'image_url', name:'image_url'},
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']]
    });

    const id = '{{ $maintenanceschedule->id }}';  

    function setMaintenanceDateRange() {
        const startDate = '{{ $maintenance->start_date }}';  
        const endDate = '{{ $maintenance->end_date }}';     
        const maintenanceDateInput = document.getElementById('maintenance_date');
        
        maintenanceDateInput.setAttribute('min', startDate);  
        maintenanceDateInput.setAttribute('max', endDate);    
    }

    function validateAllowedDay(input) {
        const selectedDate = new Date(input.value);
        const dayOfWeek = selectedDate.getDay();

        const allowedDayText = '{{ $maintenanceschedule->day_of_week }}'.toLowerCase();

        const dias = {
            'domingo': 6,
            'lunes': 0,
            'martes': 1,
            'miércoles': 2,
            'miercoles': 2,
            'jueves': 3,
            'viernes': 4,
            'sábado': 5,
        };

        const allowedDayNumber = dias[allowedDayText];

        if (dayOfWeek !== allowedDayNumber) {
            $('#maintenance_date_error').show();
            $('#maintenance_date_error').text(`Solo se permiten fechas que caen en ${allowedDayText.charAt(0).toUpperCase() + allowedDayText.slice(1)}.`);
            input.value = ''; 
        }else{
            $('#maintenance_date_error').hide();
        }
    }


    

    // Nuevo empleado - abrir modal
    $('#btnNewMaintenance').click(function() {
        $.ajax({
            url: "{{ route('admin.maintenancerecord.create') }}",
            type: "GET",
            success: function(response) {
                $('#ModalLongTitle').text('Nueva Actividad');
                $('#modalMaintenance .modal-body').html(response);
                $('#modalMaintenance').modal('show');
                setMaintenanceDateRange();
                $('#maintenance_date').on('change', function () {
                    validateAllowedDay(this);
                });

                // Enviar formulario AJAX para crear
                $('#modalMaintenance form').submit(function(e) {
                    console.log('Formulario enviado');
                    e.preventDefault();
                    var form = $(this);
                    var formData = new FormData(this);
                    formData.append('schedule_id', id);

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
            url: "{{ route('admin.maintenancerecord.edit', ':id') }}".replace(':id', attendanceId),
            type: "GET",
            success: function(response) {
                $('#ModalLongTitle').text('Editar Actividad');
                $('#modalMaintenance .modal-body').html(response);
                $('#modalMaintenance').modal('show');
                setMaintenanceDateRange();
                $('#maintenance_date').on('change', function () {
                    validateAllowedDay(this);
                });
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