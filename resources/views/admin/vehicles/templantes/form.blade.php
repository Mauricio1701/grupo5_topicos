<div class="form-group">
    {!! Form::label('name', 'Nombre del Vehículo') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el nombre', 'required']) !!}
</div>

<div class="form-group">
    {!! Form::label('code', 'Código') !!}
    {!! Form::text('code', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el código', 'required']) !!}
</div>

<div class="form-group">
    {!! Form::label('plate', 'Placa') !!}
    {!! Form::text('plate', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la placa', 'required']) !!}
</div>

<div class="form-group">
    {!! Form::label('year', 'Año') !!}
    {!! Form::number('year', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el año', 'required', 'min' => 1900, 'max' => date('Y')]) !!}
</div>

<div class="form-group">
    {!! Form::label('load_capacity', 'Capacidad de Carga (kg)') !!}
    {!! Form::number('load_capacity', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la capacidad de carga', 'required', 'step' => '0.01', 'min' => 0]) !!}
</div>

<div class="form-group">
    {!! Form::label('fuel_capacity', 'Capacidad de Combustible (L)') !!}
    {!! Form::number('fuel_capacity', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la capacidad de combustible', 'required', 'step' => '0.01', 'min' => 0]) !!}
</div>

<div class="form-group">
    {!! Form::label('compactation_capacity', 'Capacidad de Compactación (kg)') !!}
    {!! Form::number('compactation_capacity', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la capacidad de compactación', 'required', 'step' => '0.01', 'min' => 0]) !!}
</div>

<div class="form-group">
    {!! Form::label('people_capacity', 'Capacidad de Personas') !!}
    {!! Form::number('people_capacity', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la capacidad de personas', 'required', 'min' => 0]) !!}
</div>

<div class="form-group">
    {!! Form::label('description', 'Descripción') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la descripción', 'rows' => 4]) !!}
</div>

<div class="form-group">
    {!! Form::label('status', 'Estado') !!}
    {!! Form::select('status', [1 => 'Activo', 0 => 'Inactivo'], 1, ['class' => 'form-control', 'required']) !!}
</div>

<div class="form-group">
    {!! Form::label('color_id', 'Color') !!}
    {!! Form::select('color_id', $colors, null, ['class' => 'form-control', 'placeholder' => 'Seleccione un color', 'required']) !!}
</div>

<div class="form-group">
    {!! Form::label('brand_id', 'Marca') !!}
    {!! Form::select('brand_id', $brands, null, ['class' => 'form-control', 'placeholder' => 'Seleccione una marca', 'required', 'id' => 'brandSelect']) !!}
</div>


<div class="form-group">
    {!! Form::label('type_id', 'Tipo de Vehículo') !!}
    {!! Form::select('type_id', $types, null, ['class' => 'form-control', 'placeholder' => 'Seleccione un tipo', 'required']) !!}
</div>

<div class="form-group">
    {!! Form::label('model_id', 'Modelo') !!}
    {!! Form::select('model_id', [], null, ['class' => 'form-control', 'placeholder' => 'Seleccione un modelo', 'required', 'id' => 'modelSelect']) !!}
</div>

