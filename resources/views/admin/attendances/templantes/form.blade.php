<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('employee_id', 'Empleado') !!} <span class="text-danger">*</span>
            {!! Form::select('employee_id', $employees->pluck('full_name', 'id'), null, ['class' => 'form-control', 'placeholder' => 'Seleccione un empleado', 'required']) !!}
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('attendance_date', 'Fecha de Asistencia') !!} <span class="text-danger">*</span>
            {!! Form::date('attendance_date', now()->toDateString(), ['class' => 'form-control', 'required']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('status', 'Estado de Asistencia') !!} <span class="text-danger">*</span>
    {!! Form::select('status', [
        1 => 'Presente',
        0 => 'Ausente',
        2 => 'Justificado'
    ], 1, ['class' => 'form-control', 'required']) !!}
</div>

<div class="form-group">
    {!! Form::label('notes', 'Observaciones') !!}
    {!! Form::textarea('notes', null, ['class' => 'form-control', 'placeholder' => 'Notas u observaciones adicionales...', 'rows' => 3]) !!}
</div>

