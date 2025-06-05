<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('employee_id', 'Empleado:') !!}
            {!! Form::select('employee_id', $employees, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione un empleado', 'required', 'style' => 'width: 100%']) !!}
            @error('employee_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('contract_type', 'Tipo de Contrato:') !!}
            {!! Form::select('contract_type', $contractTypes, null, ['class' => 'form-control', 'placeholder' => 'Seleccione un tipo de contrato', 'required']) !!}
            @error('contract_type')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('start_date', 'Fecha de Inicio:') !!}
            {!! Form::date('start_date', null, ['class' => 'form-control', 'required']) !!}
            @error('start_date')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('end_date', 'Fecha de Fin:') !!}
            {!! Form::date('end_date', null, ['class' => 'form-control']) !!}
            <small class="form-text text-muted">Dejar en blanco si es contrato indefinido</small>
            @error('end_date')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('position_id', 'Posición:') !!}
            {!! Form::select('position_id', $positions->pluck('name', 'id'), null, ['class' => 'form-control', 'placeholder' => 'Seleccione una posición', 'required']) !!}
            @error('position_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('department_id', 'Departamento:') !!}
            {!! Form::select('department_id', $departments->pluck('name', 'id'), null, ['class' => 'form-control', 'placeholder' => 'Seleccione un departamento', 'required']) !!}
            @error('department_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('salary', 'Salario:') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">S/</span>
                </div>
                {!! Form::number('salary', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'required']) !!}
            </div>
            @error('salary')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('vacation_days_per_year', 'Días de Vacaciones por Año:') !!}
            {!! Form::number('vacation_days_per_year', 15, ['class' => 'form-control', 'min' => '0', 'max' => '30', 'required']) !!}
            @error('vacation_days_per_year')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('probation_period_months', 'Período de Prueba (meses):') !!}
            {!! Form::number('probation_period_months', 3, ['class' => 'form-control', 'min' => '0', 'max' => '12', 'required']) !!}
            @error('probation_period_months')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <div class="custom-control custom-switch">
                {!! Form::checkbox('is_active', 1, null, ['class' => 'custom-control-input', 'id' => 'is_active']) !!}
                {!! Form::label('is_active', 'Contrato Activo', ['class' => 'custom-control-label']) !!}
            </div>
            @error('is_active')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('termination_reason', 'Razón de Terminación:') !!}
    {!! Form::textarea('termination_reason', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la razón de terminación del contrato (si aplica)', 'rows' => 3]) !!}
    @error('termination_reason')
    <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

@section('js')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                },
                searching: function() {
                    return "Buscando...";
                }
            }
        });

        $('#end_date').on('change', function() {
            if ($(this).val()) {
                $('#termination_reason').closest('.form-group').show();
            } else {
                $('#termination_reason').closest('.form-group').hide();
            }
        }).trigger('change');
    });
</script>
@endsection