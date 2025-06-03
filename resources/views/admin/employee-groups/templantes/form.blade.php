{{-- Nombre y zona --}}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('name', 'Nombre del grupo') !!} <span class="text-danger">*</span>
            {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('zona_id', 'Zona') !!} <span class="text-danger">*</span>
            {!! Form::select('zona_id', $zones->pluck('name', 'id'), null, ['class' => 'form-control', 'required', 'placeholder' => 'Seleccione una zona']) !!}
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
            {!! Form::label('vehiculo_id', 'Vehículo') !!} <span class="text-danger">*</span>
            <select name="vehiculo_id" id="vehiculo_id" class="form-control" required>
                <option value="">Seleccione un vehículo</option>
                @foreach ($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" data-capacidad="{{ $vehicle->people_capacity }}">
                        {{ $vehicle->name }} (Capacidad: {{ $vehicle->people_capacity }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- Días como checkboxes --}}
<div class="form-group">
    {!! Form::label('dias', 'Días de trabajo') !!}<br>
    @php
        $dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    @endphp
    @foreach ($dias_semana as $dia)
        <label class="mr-3">
            {!! Form::checkbox('dias[]', $dia) !!} {{ $dia }}
        </label>
    @endforeach
</div>

{{-- Selección de conductor --}}
<div class="form-group">
    {!! Form::label('conductor_id', 'Conductor') !!} <span class="text-danger">*</span>
    {!! Form::select('conductor_id', $employees->pluck('full_name', 'id'), null, ['class' => 'form-control', 'required', 'placeholder' => 'Seleccione un conductor']) !!}
</div>

{{-- Contenedor de ayudantes dinámicos --}}
<div id="ayudantes-container"></div>

{{-- Script para generar ayudantes dinámicamente --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const vehiculoSelect = document.getElementById('vehiculo_id');
        const ayudantesContainer = document.getElementById('ayudantes-container');

        vehiculoSelect.addEventListener('change', function () {
            const selectedOption = vehiculoSelect.options[vehiculoSelect.selectedIndex];
            const capacidad = parseInt(selectedOption.getAttribute('data-capacidad')) || 1;

            ayudantesContainer.innerHTML = ''; // limpiar antes de crear nuevos campos

            const numAyudantes = capacidad - 1;

            for (let i = 1; i <= numAyudantes; i++) {
                const div = document.createElement('div');
                div.classList.add('form-group');

                const label = document.createElement('label');
                label.textContent = `Ayudante ${i} *`;

                const select = document.createElement('select');
                select.name = 'ayudantes[]';
                select.required = true;
                select.classList.add('form-control');

                // Clonar opciones del select de conductor
                const conductorSelect = document.querySelector('select[name="conductor_id"]');
                select.innerHTML = conductorSelect.innerHTML;

                div.appendChild(label);
                div.appendChild(select);
                ayudantesContainer.appendChild(div);
            }
        });
    });
</script>
