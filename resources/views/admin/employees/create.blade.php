<form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="dni">DNI <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="dni" name="dni" placeholder="Ingrese el DNI" maxlength="10" required>
                <small class="form-text text-muted">Documento Nacional de Identidad</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="type_id">Tipo de Empleado <span class="text-danger">*</span></label>
                <select class="form-control" id="type_id" name="type_id" required>
                    <option value="">Seleccione un tipo</option>
                    @foreach($employeeTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="names">Nombres <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="names" name="names" placeholder="Ingrese los nombres" maxlength="100" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="lastnames">Apellidos <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="lastnames" name="lastnames" placeholder="Ingrese los apellidos" maxlength="200" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="birthday">Fecha de Nacimiento <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="birthday" name="birthday" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="license">Licencia</label>
                <input type="text" class="form-control" id="license" name="license" placeholder="Número de licencia" maxlength="20">
                <small class="form-text text-muted">Licencia de conducir (opcional)</small>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="address">Dirección <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="address" name="address" placeholder="Ingrese la dirección completa" maxlength="200" required>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@correo.com" maxlength="100">
                <small class="form-text text-muted">Correo electrónico (opcional)</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="phone">Teléfono</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Número de teléfono" maxlength="20">
                <small class="form-text text-muted">Teléfono de contacto (opcional)</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="password">Contraseña <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña de acceso" required>
                <small class="form-text text-muted">Mínimo 6 caracteres</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="photo">Foto</label>
                <input type="file" class="form-control-file" id="photo" name="photo" accept="image/*">
                <small class="form-text text-muted">Formatos: JPG, PNG, GIF. Máximo 2MB</small>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="status" name="status" value="1" checked>
            <label class="form-check-label" for="status">
                Empleado activo
            </label>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</form>