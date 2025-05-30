@extends('adminlte::page')

@section('title', 'Vehículos')

@section('content')
<div class="p-2"></div>

<!-- Modal -->
<div class="modal fade" id="modalVehicle" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ModalLongTitle" aria-hidden="true">
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
        <h3 class="card-title">Lista de Vehículos</h3>
        <div class="card-tools">
            <button id="btnNewVehicle" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar Nuevo Vehículo</button> 
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped" id="datatableVehicles" style="width:100%">
            <thead>
                <tr>
                    <th>PLACA</th>
                    <th>NOMBRE</th>
                    <th>CÓDIGO</th>
                    <th>MARCA</th>
                    <th>MODELO</th>
                    <th>COLOR</th>
                    <th>TIPO</th>
                    <th>AÑO</th>
                    <th>CAPACIDAD DE CARGA</th>
                    <th>ESTADO</th>
                    <th>CREADO</th>
                    <th>ACTUALIZADO</th>
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

@section('css')
    <!-- FontAwesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#datatableVehicles').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
        },
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.vehicles.index') }}",
        columns: [
            { data: 'plate', name: 'plate' },
            { data: 'name', name: 'name' },
            { data: 'code', name: 'code' },
            { data: 'brand_name', name: 'brand_name' },
            { data: 'model_name', name: 'model_name' },
            { data: 'color_name', name: 'color_name' },
            { data: 'type_name', name: 'type_name' },
            { data: 'year', name: 'year' },
            { data: 'load_capacity', name: 'load_capacity' },
            { data: 'status', name: 'status', render: function(data, type, row) {
                return data ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>';
            }},
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[10, 'desc']]
    });

    // Nuevo vehículo - abrir modal
    $('#btnNewVehicle').click(function() {
        $.ajax({
            url: "{{ route('admin.vehicles.create') }}",
            type: "GET",
            success: function(response) {
                $('#ModalLongTitle').text('Nuevo Vehículo');
                $('#modalVehicle .modal-body').html(response);
                $('#modalVehicle').modal('show');

                // Enviar formulario AJAX para crear
                $('#modalVehicle form').off('submit').on('submit', function(e) {
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
                            $('#modalVehicle').modal('hide');
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
                                text: res.message || 'Ocurrió un error',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    });
                });
            }
        });

    });


    // Editar vehículo - abrir modal
    $(document).on('click', '.btnEditar', function() {
        var vehicleId = $(this).attr('id');
        $.ajax({
            url: "{{ route('admin.vehicles.edit', ':id') }}".replace(':id', vehicleId),
            type: "GET",
            success: function(response) {
                $('#ModalLongTitle').text('Editar Vehículo');
                $('#modalVehicle .modal-body').html(response);
                $('#modalVehicle').modal('show');

                // Enviar formulario AJAX para actualizar
                $('#modalVehicle form').off('submit').on('submit', function(e) {
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
                            $('#modalVehicle').modal('hide');
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
                                text: res.message || 'Ocurrió un error',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    });
                });
            }
        });
    });

// Eliminar vehículo con confirmación
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

 function loadModels(brandId) {
        let modelSelect = document.getElementById('modelSelect');

        // Limpiar modelos anteriores
        modelSelect.innerHTML = '<option value="">Seleccione un modelo</option>';

        if (brandId) {
            fetch(`/get-models/${brandId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(function(model) {
                        let option = document.createElement('option');
                        option.value = model.id;
                        option.text = model.name;
                        modelSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error al cargar modelos:', error);
                    alert('Error al cargar modelos');
                });
        }
    }

</script>
@stop
