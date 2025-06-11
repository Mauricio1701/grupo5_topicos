<!-- Fila 1: Código y Nombre -->
<div class="row">
    <div class="col-md-6">
    <div class="form-group">
        {!! Form::label('code', 'Código *') !!}
        {!! Form::text('code', null, [
            'class' => 'form-control',
            'placeholder' => 'Ingrese el código',
            'required',
            'pattern' => '^[A-Za-z0-9\-]+$',
            'title' => 'Solo letras mayúsculas, números y guiones son permitidos (ej. VEH-UKONW)',
            'style' => 'text-transform:uppercase;'
        ]) !!}
    </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('type_id', 'Tipo de Vehículo *') !!}
            {!! Form::select('type_id', $types, null, ['class' => 'form-control', 'placeholder' => 'Seleccione un tipo', 'required']) !!}
        </div>
    </div>
</div>

<!-- Fila 2: Nombre y Placa -->
<div class="row">
    <div class="col-md-6">
    <div class="form-group">
        {!! Form::label('name', 'Nombre del Vehículo *') !!}
        {!! Form::text('name', null, [ 'class' => 'form-control', 'placeholder' => 'Ingrese el nombre', 'required', 'pattern' => '^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ0-9\- ]+$','title' => 'Solo letras, números, espacios y guiones son permitidos', 'style' => 'text-transform:capitalize;'
        ]) !!}
    </div>
    </div>
    <div class="col-md-6">
    <div class="form-group">
        {!! Form::label('plate', 'Placa *') !!}
        {!! Form::text('plate', null, [ 'class' => 'form-control', 'placeholder' => '(Ej: ABC-123)', 'required', 'pattern' => '^[A-Z]{3}-[0-9]{3}$','title' => 'Debe ingresar una placa válida con el formato ABC-123', 'style' => 'text-transform:uppercase;'
        ]) !!}
    </div>
    </div>

</div>

<!-- Fila 3: Año y Color -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('year', 'Año *') !!}
            {!! Form::number('year', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el año', 'required', 'min' => 1900, 'max' => date('Y')]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('color_id', 'Color *') !!}
            {!! Form::select('color_id', $colors, null, ['class' => 'form-control', 'placeholder' => 'Seleccione un color', 'required']) !!}
        </div>
    </div>
</div>

<!-- Fila 4: Marca y Modelo -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('brand_id', 'Marca *') !!}
            {!! Form::select('brand_id', $brands, null, [ 'class' => 'form-control',  'placeholder' => 'Seleccione una marca',   'required',  'id' => 'brandSelect', 'onchange' => 'loadModels(this.value)'
            ]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('model_id', 'Modelo *') !!}
            {!! Form::select('model_id', [], null, ['class' => 'form-control',  'placeholder' => 'Seleccione un modelo',  'required',  'id' => 'modelSelect'
            ]) !!}
        </div>
    </div>
</div>

<!-- Fila 5: Capacidades -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('load_capacity', 'Capacidad de Carga (kg) *') !!}
            {!! Form::number('load_capacity', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la capacidad de carga', 'required', 'step' => '0.01', 'min' => 0]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('fuel_capacity', 'Capacidad de Combustible (L) *') !!}
            {!! Form::number('fuel_capacity', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la capacidad de combustible', 'required', 'step' => '0.01', 'min' => 0]) !!}
        </div>
    </div>
</div>

<!-- Fila 6: Más Capacidades -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('compactation_capacity', 'Capacidad de Compactación (kg) *') !!}
            {!! Form::number('compactation_capacity', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la capacidad de compactación', 'required', 'step' => '0.01', 'min' => 0]) !!}
        </div>
    </div>
    <div class="col-md-6">
    <div class="form-group">
        {!! Form::label('people_capacity', 'Capacidad de Personas *') !!}
        {!! Form::number('people_capacity', 3, [ 'class' => 'form-control', 'placeholder' => 'Ingrese la capacidad de personas', 'required', 'min' => 0, 'step' => 1
        ]) !!}
    </div>
</div>

</div>

<!-- Descripción (campo completo) -->
<div class="form-group">
    {!! Form::label('description', 'Descripción') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la descripción', 'rows' => 4]) !!}
</div>

<!-- Estado (como checkbox) -->
<div class="form-group">
    <div class="form-check">
       {!! Form::checkbox('status', 1, isset($vehicle) ? $vehicle->status == 1 : true, ['class' => 'form-check-input', 'id' => 'status']) !!}
        {!! Form::label('status', 'Vehículo activo', ['class' => 'form-check-label']) !!}
    </div>
</div>