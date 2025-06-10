@extends('adminlte::page')

@section('title', 'Programaciones')


@section('content')
<div class="p-2"></div>

<!-- Modal -->
<div class="modal fade" id="modalScheduling" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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
        <h3 class="card-title">Lista de Programaciones</h3>
      
        <div class="card-tools">
            <a href="{{route('admin.module')}}" class="btn btn-success"><i class="fas fa-calendar"></i> Ir a modulo</a> 
            <a href="{{route('admin.schedulings.create')}}" id="btnNewScheduling" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar Nueva Programación</a> 
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped" id="datatableSchedulings" style="width:100%">
            <thead>
                <tr>
                    <th>FECHA</th>
                    <th>ESTADO</th>
                    <th>ZONA</th>
                    <th>TURNOS</th>
                    <th>VEHICULO</th>
                    <th>GRUPO</th>
                    <th>ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
                {{-- Si usas serverSide, queda vacío --}}
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#datatableSchedulings').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
        },
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.schedulings.index') }}",
        columns: [
            { data: 'date', name: 'date' },
            { data: 'status_badge', name: 'status_badge' },
            { data: 'zone', name: 'zone' },
            { data: 'shift', name: 'shift' },
            { data: 'vehicle', name: 'vehicle' },
            { data: 'group', name: 'group' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
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
            confirmButtonText: 'Sí, Cancelar',
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

    $(document).on('click', '.btnEditar', function() {
        var schedulingId = $(this).attr('id');
        $.ajax({
            url: '{{ route('admin.schedulings.edit', 'id') }}'.replace('id', schedulingId),
            type: "GET",
            success: function(response) {
                $('#ModalLongTitle').text('Editar Programación');
                $('#modalScheduling .modal-body').html(response);
                $('#modalScheduling').modal('show');
            }
        });
    });



    $(document).on('click', '.btnVer', function() {
        var schedulingId = $(this).attr('id');
        $.ajax({
            url: '{{ route('admin.schedulings.show', 'id') }}'.replace('id', schedulingId),
            type: "GET",
            success: function(response) {
                $('#ModalLongTitle').text('Ver Programación');
                $('#modalScheduling .modal-body').html(response);
                $('#modalScheduling').modal('show');
            }
        });
    });
});
</script>
@stop