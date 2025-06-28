@extends('adminlte::page')

@section('title', 'Cambios')

@section('content_header')
    
@stop

@section('content')
<div class="p-2"></div>

<!-- Modal -->
<div class="modal fade " id="modalBrand" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ModalLongTitle" aria-hidden="true">
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
        <h3 class="card-title">
            <i class="fas fa-exchange-alt"></i> Cambios de Programaciones
        </h3>

        <div class="card-tools">
            <button id="btnNewBrand" class="btn btn-primary" ><i class="fas fa-plus"></i> Agregar Cambio</button>    
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Fecha de inicio: <span class="text-danger">*</span></label>
                <input type="date" value="{{$fechaActual}}" name="start_date" id="start_date" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">Fecha de fin: </label>
                <input type="date" name="end_date" id="end_date" class="form-control">
            </div>
            <div class="col-md-4">
                <button class="btn btn-outline-info" id="btnFilter">Filtrar</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped" id="datatable" style="width:100%">
                <thead >
                    <tr>
                        <th>FECHA CAMBIO</th>
                        <th>PROGRAMACIÓN</th>
                        <th>GRUPO</th>
                        <th>TIPO</th>
                        <th>VALOR ANTERIOR</th>
                        <th>VALOR NUEVO</th>
                        <th>ACCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
    
</div>
@stop

@section('css')

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
   
   <script>

    $('#btnNewBrand').click(function() {
        $.ajax({
            url: "{{ route('admin.changes.create') }}",
            type: 'GET',
            success: function(response) {
                $('#ModalLongTitle').text('Agregar Nueva Marca');
                $('#modalBrand .modal-body').html(response);
                $('#modalBrand').modal('show');

                $('#modalBrand form').submit(function(e) {
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
                            $('#modalBrand').modal('hide');
                            console.log(response);

                            refreshTable();

                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                            
                        },
                        error: function( xhr) {
                            var response = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: '¡Error!',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    })
                    
                })

            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    });

    $(document).on('click', '.btnEditar', function() {
        var brandId = $(this).attr('id');
        $.ajax({
            url: "{{ route('admin.changes.edit', 'id') }}".replace('id', brandId),
            type: 'GET',
            success: function(response) {
                $('#ModalLongTitle').text('Editar Cambio');
                $('#modalBrand .modal-body').html(response);
                $('#modalBrand').modal('show');

                $('#modalBrand form').submit(function(e){
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
                            
                            $('#modalBrand').modal('hide');
                            refreshTable();
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                            
                        },
                       error: function( xhr) {
                            var response = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: '¡Error!',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    })
                })
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    });

    $(document).ready(function() {
        $('#datatable').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.changes.index') }}",
                data: function (d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                },
                type: "GET"
            },
            columns: [
                { data: 'change_date' },
                { data: 'scheduled_date' },
                { data: 'group_employees' },
                { data: 'type' },
                { data: 'old_value' },
                { data: 'new_value' },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });


    $(document).on('submit','.delete',function(e){
        e.preventDefault();
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
                var form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    success: function(response) {
                        refreshTable();
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Aceptar'
                        });
                    },
                    error: function(xhr, status, error) {
                    var response = xhr.responseJSON;
                    Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: response.message,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    });
       

    function refreshTable() {
        var table = $('#datatable').DataTable();
        table.ajax.reload(null, false);
    }

    $('#btnFilter').on('click', function() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        if (startDate === '' || startDate === null) {
            Swal.fire({
                icon: 'warning',
                title: '¡Atención!',
                text: 'Por favor, selecciona al menos la fecha de inicio para filtrar.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        console.log('Fecha de inicio:', startDate);
        console.log('Fecha de fin:', endDate);


        if (endDate !== '' ) {
            
            if (startDate > endDate) {
                Swal.fire({
                    icon: 'warning',
                    title: '¡Atención!',
                    text: 'La fecha de fin no puede ser menor que la fecha de inicio.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            // Recargar el DataTable con las fechas
            refreshTable();
            return;
        }

        refreshTable();
       
    });

</script>

@stop