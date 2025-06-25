<div class="row">
    <div class="col-12">
        <div class="form-group">
            {!! Form::label('name', 'Nombre') !!}
            {!! Form::text('name', null, ['class' => 'form-control','placeholder' => 'Ingrese el nombre','required']) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {!! Form::label('start_date', 'Fecha de inicio') !!}
            {!! Form::date('start_date', null, ['class' => 'form-control','required']) !!}
        </div>
        <div id="start_date_error" class="text-danger" style="display: none;">La fecha de inicio es obligatoria.</div>

    </div>
     <div class="col-6">

        <div class="form-group">
            {!! Form::label('end_date', 'Fecha de fin') !!}
            {!! Form::date('end_date', null, ['class' => 'form-control','required']) !!}
        </div>
        <div id="end_date_error" class="text-danger" style="display: none;">La fecha de fin no puede ser menor a la de inicio.</div>

    </div>
   
</div>
<script>
    // Obtener los elementos de fecha de inicio y fin
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const startDateError = document.getElementById('start_date_error');
    const endDateError = document.getElementById('end_date_error');

    // Evento cuando la fecha de inicio cambia
    startDateInput.addEventListener('change', function () {
        // Si se selecciona una fecha de inicio, verificamos la fecha de fin
        if (startDateInput.value && !endDateInput.value) {

        } else {
            // Si la fecha de fin está vacía y la de inicio está seleccionada
            if (endDateInput.value && new Date(startDateInput.value) > new Date(endDateInput.value)) {
                endDateInput.style.borderColor = "red"; // Marcar el campo de fecha fin
                endDateError.style.display = 'block'; // Mostrar el error
            } else {
                endDateInput.style.borderColor = ""; // Limpiar el borde si la validación pasa
                endDateError.style.display = 'none'; // Ocultar el mensaje de error
            }
        }
    });

    // Evento cuando la fecha de fin cambia
    endDateInput.addEventListener('change', function () {
        // Si hay una fecha de inicio y se selecciona la fecha de fin
        if (startDateInput.value && new Date(startDateInput.value) > new Date(endDateInput.value)) {
            endDateInput.style.borderColor = "red"; // Marcar la fecha fin
            endDateError.style.display = 'block'; // Mostrar el error
        } else {
            endDateInput.style.borderColor = ""; // Limpiar el borde
            endDateError.style.display = 'none'; // Ocultar el mensaje de error
        }
    });
</script>