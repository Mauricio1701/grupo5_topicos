<form action="{{ route('admin.shifts.update', $shift->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label for="name">Nombre del Turno <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $shift->name) }}" placeholder="Ingrese el nombre del turno" required>
        <small class="form-text text-muted">Ejemplo: Turno Mañana, Turno Tarde, Turno Noche</small>
    </div>

    <div class="form-group">
        <label for="description">Descripción</label>
        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Ingrese una descripción del turno (opcional)">{{ old('description', $shift->description) }}</textarea>
        <small class="form-text text-muted">Descripción de las características del turno</small>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Nota:</strong> Los turnos se configuran con nombre y descripción. Los horarios específicos se manejan en la programación.
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </div>
</form>