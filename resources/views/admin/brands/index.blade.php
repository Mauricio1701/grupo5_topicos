@extends('adminlte::page')

@section('title', 'Marcas')

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
        <h3 class="card-title">Lista de Marcas</h3>
        <div class="card-tools">
            <button id="btnNewBrand" class="btn btn-primary" ><i class="fas fa-plus"></i> Agregar Nueva Marca</button>    
        </div>
    </div>
    <div class="card-body">
            <table class="table table-striped" id="datatable">
                <thead >
                    <tr>
                        <th>LOGO</th>
                        <th>NOMBRE</th>
                        <th>DESCRIPCIÓN</th>
                        <th>CREADO</th>
                        <th>ACTUALIZADO</th>
                        <th>ACCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($brands as $brand)
                        <tr>
                            <td>
                                <img src="{{ $brand->logo == '' ? asset('storage/brand_logo/producto_var.webp') : asset($brand->logo) }}" alt="Logo" width="50">
                            </td>
                            <td>{{ $brand->name }}</td>
                            <td>{{ $brand->description }}</td>
                            <td>{{ $brand->created_at }}</td>
                            <td>{{ $brand->updated_at }}</td>
                            <td>
                                <button type="button" class="btn btn-warning btnEditar" id="{{ $brand->id }}"> <i class="fas fa-edit"></i></button>
                                <form action="{{ route('admin.brands.destroy', $brand->id) }}" id="delete-form-{{ $brand->id }}" method="POST" class="d-inline formDelete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"> <i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    
                    @endforeach
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

    $('#btnNewBrand').click(function() {
        $.ajax({
            url: '{{ route('admin.brands.create') }}',
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
            url: '{{ route('admin.brands.edit', 'id') }}'.replace('id', brandId),
            type: 'GET',
            success: function(response) {
                $('#ModalLongTitle').text('Editar Marca');
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
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('admin.brands.index') }}",
                "type": "GET"
            },
            "columns": [
                { data: 'logo' },
                { data: 'name' },
                { data: 'description' },
                { data: 'created_at' },
                { data: 'updated_at' },
                { data: 'action' }
            ]
        });
    });

    $(document).on('submit','.formDelete',function(e){
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
</script>

@stop