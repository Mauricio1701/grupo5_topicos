<div class="container">
    <!-- Card con los datos generales -->
    <div class="card mt-4">
        <div class="card-header">
            <strong>Datos Generales</strong>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Zona</th>
                        <th>Turno</th>
                        <th>Vehículo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $scheduling->date ?? 'N/A' }}</td> <!-- Fecha -->
                        <td>
                            @if($scheduling->status == 1)
                                <span class="badge badge-secondary">Programado</span>
                            @elseif($scheduling->status == 2)
                                <span class="badge badge-success">Completado</span>
                            @else
                                <span class="badge badge-danger">Reprogramado</span>
                            @endif
                        </td> <!-- Estado -->
                        <td>{{ $scheduling->employeegroup->zone->name ?? 'N/A' }}</td> <!-- Zona -->
                        <td>{{ $scheduling->employeegroup->shift->name ?? 'N/A' }}</td> <!-- Turno -->
                        <td>{{ $scheduling->employeegroup->vehicle->name ?? 'N/A' }}</td> <!-- Vehículo -->
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Card con el personal asignado -->
    <div class="card mt-4">
        <div class="card-header">
            <strong>Personal Asignado</strong>
        </div>
        <div class="card-body">
            <!-- Tabla para los empleados asignados -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Iterar sobre los detalles del grupo (groupdetail) -->
                    @foreach($scheduling->groupdetail as $detail)
                        <!-- Mostrar el conductor -->
                        @if($detail->employee && $detail->employee->employeeType->name == 'Conductor')
                            <tr>
                                <td>Conductor</td>
                                <td>{{ $detail->employee->full_name }}</td>
                            </tr>
                        @endif

                        <!-- Mostrar los ayudantes -->
                        @if($detail->employee && $detail->employee->employeeType->name == 'Ayudante')
                            <tr>
                                <td>Ayudante</td>
                                <td>{{ $detail->employee->full_name }}</td>
                            </tr>
                        @endif
                    @endforeach

                    <!-- Si no hay detalles de empleados, muestra el mensaje -->
                    @if($scheduling->groupdetail->isEmpty())
                        <tr>
                            <td colspan="2">No se ha asignado ningún empleado.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-ban"></i> Cerrar</button>
    </div>
</div>
