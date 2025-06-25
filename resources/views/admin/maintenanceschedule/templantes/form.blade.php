<div class="row">
    <div class="col-8">
            <!-- Vehículo -->
      <div class="row">
            <div class="col-6">
                <div class="form-group">
                    {!! Form::label('vehicle_id', 'Vehículo') !!}
                    {!! Form::select('vehicle_id', $vehicles->pluck('name','id'), null, ['class' => 'form-control', 'placeholder' => 'Seleccione un vehículo', 'required']) !!}
                </div>
            </div>

            <!-- Conductor -->
            <div class="col-6">
                <div class="form-group">
                    {!! Form::label('driver_id', 'Conductor') !!}
                    {!! Form::select('driver_id', $drivers->pluck('names','id'), null, ['class' => 'form-control', 'placeholder' => 'Seleccione un conductor', 'required']) !!}
                </div>
            </div>
      </div>

       <div class="row">
             <!-- Día de la semana -->
            <div class="col-6">
                <div class="form-group">
                    {!! Form::label('day_of_week', 'Día de la semana') !!}
                    {!! Form::select('day_of_week', ['Lunes' => 'Lunes', 'Martes' => 'Martes', 'Miércoles' => 'Miércoles', 'Jueves' => 'Jueves', 'Viernes' => 'Viernes', 'Sábado' => 'Sábado'], null, ['class' => 'form-control', 'placeholder' => 'Seleccione un dia', 'required']) !!}
                </div>
            </div>

            <!-- Hora de inicio -->
            <div class="col-6">
                <div class="form-group">
                    {!! Form::label('start_time', 'Hora de inicio') !!}
                    {!! Form::time('start_time', null, ['class' => 'form-control', 'required']) !!}
                </div>
            </div>
       </div>

       <div class="row">
                <!-- Hora de fin -->
        <div class="col-6">
            <div class="form-group">
                {!! Form::label('end_time', 'Hora de fin') !!}
                {!! Form::time('end_time', null, ['class' => 'form-control', 'required']) !!}
            </div>
        </div>

        <!-- Tipo de mantenimiento -->
        <div class="col-6">
            <div class="form-group">
                {!! Form::label('maintenance_type', 'Tipo de mantenimiento') !!}
                {!! Form::select('maintenance_type', ['Limpieza' => 'Limpieza', 'Reparación' => 'Reparación'], null, ['class' => 'form-control', 'placeholder' => 'Seleccione el tipo de mantenimiento', 'required']) !!}
            </div>
        </div>
       </div>

        <div>
            <!-- Fecha de mantenimiento (para la tabla `maintenance_records`) -->
        <div class="col-6">
            <div class="form-group">
                {!! Form::label('maintenance_date', 'Fecha del mantenimiento') !!}
                {!! Form::date('maintenance_date',  $maintenancerecords->maintenance_date ?? null, [
                    'class' => 'form-control', 
                    'required'
                ]) !!}
            </div>
        </div>

        <!-- Descripción -->
        <div class="col-12">
            <div class="form-group">
                {!! Form::label('description', 'Descripción') !!}
                {!! Form::textarea('description', $maintenancerecords->description ?? null, ['class' => 'form-control', 'placeholder' => 'Ingrese la descripción', 'rows' => 3, 'required']) !!}
            </div>
        </div>
        </div>

        <!-- Imagen (si aplica) -->
        <div class="col-6">
            <div class="form-group">
                {!! Form::file('image_url', ['id' => 'image_url','accept' => 'image/*','class' => 'form-control','hidden' => true]) !!}
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            <div id="imageButton" style="cursor: pointer; width: 100%; text-align: center; padding: 10px;">
                <img style="cursor: pointer; width: 100%; height: 180px;" 
                src="{{ isset($maintenancerecords) && $maintenancerecords->image_url != '' ? asset($maintenancerecords->image_url) : asset('storage/brand_logo/producto_var.webp') }}" 
                alt="Logo" width="50">
                <p>Haga click para seleccionar un imagen</p>
            </div>
        </div>
    </div>

</div>


<script>
     $('#image_url').change(function() {
        var file = this.files[0];
        if(file){
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imageButton img').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });

    $('#imageButton').click(function() {
        $('#logo').click();
    });
</script>