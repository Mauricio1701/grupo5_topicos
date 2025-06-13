
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="employee_id">Empleado</label>
            <select class="form-control" id="employee_id" name="employee_id" required>
                <option value="">Seleccione un empleado</option>
                @foreach($employees as $employee)
                <option value="{{ $employee->id }}"
                    {{ isset($vacation) && $vacation->employee_id == $employee->id ? 'selected' : '' }}
                    data-available-days="{{ $employee->available_days }}">
                    {{ $employee->name_with_last_name }} ({{ $employee->available_days }} días disponibles)
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="request_date">Fecha de Inicio</label>
            <?php
            $minDate = \Carbon\Carbon::now()->addDays(11)->format('Y-m-d');
            $defaultDate = isset($vacation) && $vacation->request_date > \Carbon\Carbon::now()->addDays(10)
                ? $vacation->request_date->format('Y-m-d')
                : $minDate;
            ?>
            <input type="date" class="form-control datepicker" id="request_date" name="request_date" required
                min="{{ $minDate }}"
                value="{{ $defaultDate }}">
            <small class="text-muted">Las solicitudes deben hacerse con al menos 10 días de anticipación</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="requested_days">Días Solicitados</label>
            <input type="number" class="form-control" id="requested_days" name="requested_days" min="1" required
                value="{{ isset($vacation) ? $vacation->requested_days : '1' }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="end_date">Fecha Final</label>
            <input type="date" class="form-control" id="end_date" name="end_date" readonly
                value="{{ isset($vacation) ? $vacation->end_date->format('Y-m-d') : '' }}">
            <small class="text-muted">Calculada automáticamente</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="available_days">Días Disponibles</label>
            <input type="number" class="form-control" id="available_days" name="available_days" readonly
                value="{{ isset($vacation) && isset($employees) ? $employees->where('id', $vacation->employee_id)->first()->available_days ?? '0' : '0' }}">
            <small class="text-muted">Basado en contrato del empleado</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="status">Estado</label>
            <select class="form-control" id="status" name="status" required>
                @foreach($statusOptions as $option)
                <option value="{{ $option }}" {{ isset($vacation) && $vacation->status == $option ? 'selected' : '' }}>
                    @switch($option)
                    @case('Pending')
                    Pendiente
                    @break
                    @case('Approved')
                    Aprobado
                    @break
                    @case('Rejected')
                    Rechazado
                    @break
                    @case('Cancelled')
                    Cancelado
                    @break
                    @case('Completed')
                    Completado
                    @break
                    @default
                    {{ $option }}
                    @endswitch
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="notes">Notas</label>
            <textarea class="form-control" id="notes" name="notes" rows="3">{{ isset($vacation) ? $vacation->notes : '' }}</textarea>
        </div>
    </div>
</div>

<input type="hidden" name="original_requested_days" value="{{ isset($vacation) ? $vacation->requested_days : '0' }}">