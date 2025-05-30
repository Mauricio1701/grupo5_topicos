<form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="dni">DNI <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="dni" name="dni" value="{{ old('dni', $employee->dni) }}" placeholder="Ingrese el DNI" maxlength="10" required>
                <small class="form-text text-muted">Documento Nacional de Identidad</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="type_id">Tipo de Empleado <span class="text-danger">*</span></label>
                <select class="form-control" id="type_id" name="type_id" required>
                    <option value="">Seleccione un tipo</option>
                    @foreach($employeeTypes as $type)
                        <option value="{{ $type->id }}" {{ $employee->type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="names">Nombres <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="names" name="names" value="{{ old('names', $employee->names) }}" placeholder="Ingrese los nombres" maxlength="100" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="lastnames">Apellidos <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="lastnames" name="lastnames" value="{{ old('lastnames', $employee->lastnames) }}" placeholder="Ingrese los apellidos" maxlength="200" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="birthday">Fecha de Nacimiento <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="birthday" name="birthday" value="{{ old('birthday', $employee->birthday ? $employee->birthday->format('Y-m-d') : '') }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="license">Licencia</label>
                <input type="text" class="form-control" id="license" name="license" value="{{ old('license', $employee->license) }}" placeholder="Número de licencia" maxlength="20">
                <small class="form-text text-muted">Licencia de conducir (opcional)</small>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="address">Dirección <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $employee->address) }}" placeholder="Ingrese la dirección completa" maxlength="200" required>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $employee->email) }}" placeholder="ejemplo@correo.com" maxlength="100">
                <small class="form-text text-muted">Correo electrónico (opcional)</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="phone">Teléfono</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" placeholder="Número de teléfono" maxlength="20">
                <small class="form-text text-muted">Teléfono de contacto (opcional)</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="password">Nueva Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Dejar vacío para mantener la actual">
                <small class="form-text text-muted">Solo llenar si desea cambiar la contraseña</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="photo">Foto</label>
                <input type="file" class="form-control-file" id="photo" name="photo" accept="image/*">
                @if($employee->photo)
                    <small class="form-text text-muted">
                        Foto actual: {{ $employee->photo }}
                        <br>Seleccionar nueva foto para reemplazar
                    </small>
                @else
                    <small class="form-text text-muted">Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                @endif
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ $employee->status ? 'checked' : '' }}>
            <label class="form-check-label" for="status">
                Empleado activo
            </label>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </div>
</form>