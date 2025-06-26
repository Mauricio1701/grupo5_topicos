<div class="row">
    <div class="col-12">
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
                    {!! Form::label('driver_id', 'Responsable') !!}
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

     
    </div>
  
</div>

