
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('employee_id', 'Empleado') !!}
            {!! Form::select('employee_id', $employees, null, ['class' => 'form-control', 'placeholder' => 'Seleccione un empleado', 'required']) !!}
            @error('employee_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('status', 'Estado') !!}
            {!! Form::select('status', array_combine($statuses, $statuses), null, ['class' => 'form-control', 'required']) !!}
            @error('status')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('request_date', 'Fecha de solicitud') !!}
            {!! Form::date('request_date', null, ['class' => 'form-control', 'required']) !!}
            @error('request_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('end_date', 'Fecha de finalización') !!}
            {!! Form::date('end_date', null, ['class' => 'form-control', 'required']) !!}
            @error('end_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('requested_days', 'Días solicitados') !!}
            {!! Form::number('requested_days', null, ['class' => 'form-control', 'min' => '1', 'required', 'readonly']) !!}
            @error('requested_days')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('available_days', 'Días disponibles') !!}
            {!! Form::number('available_days', null, ['class' => 'form-control', 'min' => '0', 'required', 'readonly']) !!}
            @error('available_days')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('notes', 'Notas') !!}
    {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Ingrese notas o comentarios sobre esta solicitud']) !!}
    @error('notes')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>