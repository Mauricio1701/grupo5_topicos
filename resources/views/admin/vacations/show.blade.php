
@extends('adminlte::page')

@section('title', 'Detalles de Solicitud de Vacaciones')

@section('content_header')
    <h1>Detalles de Solicitud de Vacaciones</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Solicitud #{{ $vacation->id }}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Empleado:</label>
                    <p>{{ $vacation->employee->name ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Estado:</label>
                    @php
                        $statusClasses = [
                            'Pending' => 'badge badge-warning',
                            'Approved' => 'badge badge-success',
                            'Rejected' => 'badge badge-danger',
                            'Cancelled' => 'badge badge-secondary'
                        ];
                        
                        $class = isset($statusClasses[$vacation->status]) ? $statusClasses[$vacation->status] : 'badge badge-info';
                    @endphp
                    <p><span class="{{ $class }}">{{ $vacation->status }}</span></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Fecha de solicitud:</label>
                    <p>{{ \Carbon\Carbon::parse($vacation->request_date)->format('d/m/Y') }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Fecha de finalización:</label>
                    <p>{{ \Carbon\Carbon::parse($vacation->end_date)->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Días solicitados:</label>
                    <p>{{ $vacation->requested_days }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Días disponibles:</label>
                    <p>{{ $vacation->available_days }}</p>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Notas:</label>
            <p>{{ $vacation->notes ?? 'Sin notas' }}</p>
        </div>
        
        @if($vacation->status == 'Pending')
        <div class="row mt-4">
            <div class="col-md-12">
                <h4>Cambiar estado</h4>
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-change-status" data-status="Approved">Aprobar</button>
                    <button type="button" class="btn btn-danger btn-change-status" data-status="Rejected">Rechazar</button>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="card-footer">
        <a href="{{ route('admin.vacations.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('admin.vacations.edit', $vacation->id) }}" class="btn btn-primary">Editar</a>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('.btn-change-status').click(function() {
        var status = $(this).data('status');
        
        Swal.fire({
            title: '¿Cambiar estado?',
            text: "¿Estás seguro de que deseas cambiar el estado a " + status + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.vacations.change-status', $vacation->id) }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire(
                                '¡Cambiado!',
                                response.message,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error',
                            'Ocurrió un error al cambiar el estado',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@stop