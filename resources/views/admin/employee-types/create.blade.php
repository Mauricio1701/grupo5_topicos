<form action="{{ route('admin.employee-types.store') }}" method="POST">
    @csrf
    
    <div class="form-group">
        <label for="name">Nombre <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Ingrese el nombre del tipo de empleado" required>
        <small class="form-text text-muted">Ejemplo: Conductor, Ayudante, Supervisor</small>
    </div>

    <div class="form-group">
        <label for="description">Descripción</label>
        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Ingrese una descripción (opcional)"></textarea>
        <small class="form-text text-muted">Descripción de las funciones del tipo de empleado</small>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</form>