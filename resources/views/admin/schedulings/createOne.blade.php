@extends('adminlte::page')

@section('title', 'Programaciones')


@section('content')
<div class="p-2"></div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Registrar programacion</h3>
      
        <div class="card-tools">
            <a href="{{route('admin.schedulings.index')}}" class="btn btn-link"> Volver</a>
        </div>
    </div>
    

    <div class="card-body">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="start_date" class="form-label">Fecha de inicio: <span class="text-danger">*</span></label>
                <input type="date" name="start_date" id="start_date" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label for="end_date" class="form-label">Fecha de fin: </label>
                <input type="date" name="end_date" id="end_date" class="form-control">
            </div>
        </div>

<div class="row">
    <!-- Grupo de Personal -->
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('employeegroups_id', 'Grupo de Personal') !!} <span class="text-danger">*</span>
            {!! Form::select('employeegroups_id', $employeeGroups->pluck('name', 'id'),null, ['class' => 'form-control', 'required','placeholder' => 'Seleccione un grupo', 'id' => 'employeegroups_id']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('shift_id', 'Turno') !!} <span class="text-danger">*</span>
            {!! Form::text('shift_id_text', '', ['class' => 'form-control', 'id' => 'shift_id_text', 'placeholder' => 'Turno seleccionado', 'readonly' => true]) !!}
            {!! Form::hidden('shift_id', '', ['id' => 'shift_id']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('vehicle_id', 'Vehículo') !!} <span class="text-danger">*</span>
            {!! Form::text('vehicle_id_text', '', ['class' => 'form-control', 'id' => 'vehicle_id_text', 'placeholder' => 'Vehículo seleccionado', 'readonly' => true]) !!}
            {!! Form::hidden('vehicle_id', '', ['id' => 'vehicle_id']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('zone_id', 'Zona') !!} <span class="text-danger">*</span>
            {!! Form::text('zone_id_text', '', ['class' => 'form-control', 'id' => 'zone_id_text', 'placeholder' => 'Zona seleccionada', 'readonly' => true]) !!}
            {!! Form::hidden('zone_id', '', ['id' => 'zone_id']) !!}
        </div>
    </div>


    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('days', 'Días de trabajo') !!} <span class="text-danger">*</span><br>
            @php
                $dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            @endphp
            @foreach ($dias_semana as $dia)
                <label class="mr-3">
                    {!! Form::checkbox('days[]', $dia, false) !!} {{ $dia }}
                </label>
            @endforeach
        </div>

    </div>

    <div class="col-md-12 employee-selects">
        <!-- Los selects de conductores y ayudantes se generarán aquí -->
    </div>

    <div class="col-md-12">
        <div class="form-group d-flex justify-content-end">
            <button type="button" class="btn btn-success" id="saveSchedulingBtn">Guardar Programación</button>
        </div>
    </div>
</div>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
 $('#employeegroups_id').on('change', function() {
    var groupId = $(this).val();

    if (groupId) {
        $.ajax({
            url: "{{ route('admin.schedulings.createOne') }}",  // Ajusta la ruta si es necesario
            method: "GET",
            data: {
                employeegroups_id: groupId
            },
            success: function(data) {
                console.log(data);

                // Actualizar el input de Turnos
                $('#shift_id_text').val(data.group.shift.name || '');  
                $('#shift_id').val(data.group.shift_id);

                // Actualizar el input de Vehículos
                $('#vehicle_id_text').val(data.group.vehicle.code || '');
                $('#vehicle_id').val(data.group.vehicle_id);

                // Actualizar el input de Zonas
                $('#zone_id_text').val(data.group.zone.name || '');
                $('#zone_id').val(data.group.zone_id);

                // Marcar los días de trabajo seleccionados
                var diasSeleccionados = data.diasTrabajo;  // Ya es un array, no es necesario usar .split()

                // Marcar los días de trabajo seleccionados
                $('input[name="days[]"]').each(function() {
                    if (diasSeleccionados.includes($(this).val())) {
                        $(this).prop('checked', true);  // Marcar el checkbox si está en el array
                    } else {
                        $(this).prop('checked', false);  // Desmarcar el checkbox si no está en el array
                    }
                });

                    var peopleCapacity = data.group.vehicle.people_capacity || 0;

                    // Limpiar el contenido de los divs dinámicos
                    $('.employee-selects').empty();

                    // Siempre 1 conductor, y los demás son ayudantes, restando 1 de la capacidad
                    var numConductores = 1; // Siempre hay 1 conductor
                    var numAyudantes = Math.max(0, peopleCapacity - 1); // Restar 1 para los ayudantes

                    // Agregar el select de conductor (si hay conductores)
                    if (numConductores > 0) {
                        $('.employee-selects').append(`
                            <div class="form-group">
                                <label for="employee_conductor_id">Conductor</label>
                                <select class="form-control" id="employee_conductor_id" name="employee_conductor_id[]">
                                    <option value="">Seleccione un conductor</option>
                                    <!-- Opciones dinámicas de conductores se agregarán aquí -->
                                </select>
                            </div>
                        `);

                        data.employeesConductor.forEach(function(conductor) {
                            $('#employee_conductor_id').append(`
                                <option value="${conductor.id}">${conductor.names + ' '+conductor.lastnames}</option>
                            `);
                        });
                    }

                    // Agregar los selects de ayudantes (restando 1 para el conductor)
                    for (var i = 0; i < numAyudantes; i++) {
                        $('.employee-selects').append(`
                            <div class="form-group">
                                <label for="employee_helper_id_${i}">Ayudante ${i+1}</label>
                                <select class="form-control" id="employee_helper_id_${i}" name="employee_helper_id[]">
                                    <option value="">Seleccione un ayudante</option>
                                    <!-- Opciones dinámicas de ayudantes se agregarán aquí -->
                                </select>
                            </div>
                        `);

                        data.employeesAyudantes.forEach(function(ayudante) {
                            $('#employee_helper_id_' + i).append(`
                                <option value="${ayudante.id}">${ayudante.names + ' '+ayudante.lastnames}</option>
                            `);
                        });
                    }

             

                if (data.group.configgroup && data.group.configgroup.length > 0) {
                    $.each(data.group.configgroup, function(index, config) {
                        // Verificar el tipo de empleado (conductor o ayudante)
                        if (config.employee.employee_type.name === 'Conductor') {
                            // Si es conductor, pre-seleccionar el conductor en el select
                            $('#employee_conductor_id').val(config.employee_id);
                        } else if (config.employee.employee_type.name === 'Ayudante') {
                            // Si es ayudante, pre-seleccionar el ayudante en el select correspondiente
                            for (var i = 0; i < numAyudantes; i++) {
                                if ($('#employee_helper_id_' + i).length) { // Verificar si el select existe
                                    $('#employee_helper_id_' + i).val(config.employee_id);
                                }
                            }
                        }
                    });
                }

            },
            error: function(xhr, status, error) {
                console.error("Error al cargar los datos: " + error);
            }
        });
    }
});

$('#saveSchedulingBtn').on('click', function() {
    // Recoger los datos de la programación
    const schedulingData = getSchedulingData();

    // Validar que se hayan seleccionado los campos obligatorios
    if (!schedulingData.start_date || !schedulingData.employee_group_id || !schedulingData.shift_id || !schedulingData.vehicle_id || !schedulingData.zone_id || schedulingData.days.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor, complete todos los campos obligatorios.'
        });
        return;
    }

    console.log('Datos de programación:', schedulingData);
    schedulingData._token = '{{ csrf_token() }}';

    // Enviar los datos al servidor
    $.ajax({
        url: "{{ route('admin.schedulings.storeOne') }}",
        type: "POST",
        data: schedulingData,
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'Programación guardada correctamente.',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.href = "{{ route('admin.schedulings.index') }}"; // Redirigir a la lista de programaciones
            });
        },
        error: function(xhr) {
                let res = xhr.responseJSON;
                console.log(res);
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

function getSchedulingData() {
    const schedulingData = {
        // Recoger los datos de las fechas
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val() || null, // Si no hay end_date, lo dejamos como null

        // Recoger el grupo de personal seleccionado
        employee_group_id: $('#employeegroups_id').val(),
        
        // Recoger el turno, vehículo y zona seleccionados
        shift_id: $('#shift_id').val(),
        vehicle_id: $('#vehicle_id').val(),
        zone_id: $('#zone_id').val(),

        // Recoger los días de trabajo seleccionados
        days: [],
        
        // Recoger los datos de conductores y ayudantes
        helpers: []
    };

    // Obtener los días seleccionados (checkboxes)
    $('input[name="days[]"]:checked').each(function() {
        schedulingData.days.push($(this).val());
    });

    // Obtener los datos de los conductores y ayudantes
    const groupId = $('#employeegroups_id').val();
    
    const driverId = $('#employee_conductor_id').val();  // Obtener el conductor
    schedulingData.driver_id = (driverId);

    // Obtener los ayudantes
    $('select[name^="employee_helper_id"]').each(function() {
        const helperId = $(this).val();
        if (helperId) {
            schedulingData.helpers.push(helperId);
        }
    });

    // Imprimir los datos para verificar
    console.log(schedulingData);

    return schedulingData;
}

</script>
@stop


