
<form id="vacationForm" method="POST" action="{{ route('admin.vacations.update', $vacation->id) }}">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label for="employee_id">Empleado <span class="text-danger">*</span></label>
        <select class="form-control" id="employee_id" name="employee_id" required>
            <option value="">Seleccione un empleado</option>
            @foreach($employeesForSelect as $id => $fullName)
                <option value="{{ $id }}" {{ $vacation->employee_id == $id ? 'selected' : '' }}>{{ $fullName }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="request_date">Fecha de Inicio <span class="text-danger">*</span></label>
        <input type="date" class="form-control" id="request_date" name="request_date" value="{{ $vacation->request_date->format('Y-m-d') }}" required>
    </div>

    <div class="form-group">
        <label for="end_date">Fecha de Fin <span class="text-danger">*</span></label>
        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $vacation->end_date->format('Y-m-d') }}" required>
    </div>

    <div class="form-group">
        <label for="requested_days">Días Solicitados <span class="text-danger">*</span></label>
        <input type="number" class="form-control" id="requested_days" name="requested_days" min="1" value="{{ $vacation->requested_days }}" readonly required>
    </div>

    <div class="form-group">
        <label for="available_days">Días Disponibles <span class="text-danger">*</span></label>
        <input type="number" class="form-control" id="available_days" name="available_days" min="0" value="{{ $vacation->available_days }}" readonly required>
    </div>

    <div class="form-group">
        <label for="status">Estado <span class="text-danger">*</span></label>
        <select class="form-control" id="status" name="status" required>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" {{ $vacation->status == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="notes">Notas</label>
        <textarea class="form-control" id="notes" name="notes" rows="3">{{ $vacation->notes }}</textarea>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </div>
</form>