{{-- Nombre y zona --}}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('group_name', 'Nombre del grupo') !!} <span class="text-danger">*</span>
            {!! Form::text('group_name', old('group_name', $employeeGroup->name ?? ''), ['class' => 'form-control', 'required']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('zone_id', 'Zona') !!} <span class="text-danger">*</span>
            {!! Form::select('zone_id', $zones->pluck('name', 'id'), null, ['class' => 'form-control', 'required', 'placeholder' => 'Seleccione una zona']) !!}
        </div>
    </div>
</div>

{{-- Turno y vehículo --}}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('shift_id', 'Turno') !!} <span class="text-danger">*</span>
            {!! Form::select('shift_id', $shifts->pluck('name', 'id'), null, ['class' => 'form-control', 'required', 'placeholder' => 'Seleccione un turno']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('vehicle_id', 'Vehículo') !!} <span class="text-danger">*</span>
            <select name="vehicle_id" id="vehicle_id" class="form-control" required>
                <option value="">Seleccione un vehículo</option>
                @foreach ($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}"
                    data-capacidad="{{ $vehicle->people_capacity }}"
                    {{ (isset($employeeGroup) && $employeeGroup->vehicle_id == $vehicle->id) ? 'selected' : '' }}>
                    {{ $vehicle->name }} (Capacidad: {{ $vehicle->people_capacity }})
                </option>
            @endforeach
            </select>
        </div>
    </div>
</div>

{{-- Días como checkboxes --}}
<div class="form-group">
    {!! Form::label('dias', 'Días de trabajo') !!} <span class="text-danger">*</span><br>
    @php
        $dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $diasSeleccionados = explode(',', $employeeGroup->days ?? '');
    @endphp
    @foreach ($dias_semana as $dia)
        <label class="mr-3">
            {!! Form::checkbox('days[]', $dia, in_array($dia, $diasSeleccionados)) !!} {{ $dia }}
        </label>
    @endforeach
</div>

{{-- Selección de conductor --}}
<div id="dataExtra" class="form-group d-none">
    <hr>
 <p>Estos datos son para pre configuración no son obligatorios</p>
 <div class="form-group">
    {!! Form::label('driver_id', 'Conductor') !!}
    {!! Form::select('driver_id', $employeesConductor->pluck('full_name', 'id'), null, ['class' => 'form-control', 'placeholder' => 'Seleccione un conductor']) !!}
</div>

{{-- Contenedor de ayudantes dinámicos --}}
<div id="ayudantes-container" class="row "></div>
</div>


{{-- Script para generar ayudantes dinámicamente --}}


<script>
    window.ayudantesData = @json($employeesAyudantes->map(function($a) {
        return [
            'id' => $a->id,
            'name' => $a->full_name
        ];
    }));
</script>

