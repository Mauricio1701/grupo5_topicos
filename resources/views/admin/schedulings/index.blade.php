@extends('adminlte::page')

@section('title', 'Programaciones')


@section('content')
<div class="p-2"></div>



<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Programaciones</h3>
        <div class="card-tools">
            <a href="{{route('admin.schedulings.create')}}" id="btnNewScheduling" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar Nueva Programación</a> 
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped" id="datatableSchedulings" style="width:100%">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>EMPLEADO</th>
                    <th>FECHA</th>
                    <th>ESTADO</th>
                    <th>NOTAS</th>
                    <th>CREADO</th>
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
        ajax: "{{ route('admin.attendances.index') }}",
        columns: [
            { data: 'employee_dni', name: 'employee_dni' },
            { data: 'employee_name', name: 'employee_name', orderable: false, searchable: false },
            { data: 'attendance_date', name: 'attendance_date' },
            { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
            { data: 'notes', name: 'notes' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[6, 'desc']]
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