<div class="row">
    <div class="col-8">
            <!-- Fecha de mantenimiento (para la tabla `maintenance_records`) -->
        <div class="col-12">
            <div class="form-group">
                {!! Form::label('maintenance_date', 'Fecha del mantenimiento') !!}
                {!! Form::date('maintenance_date',  $maintenancerecord->maintenance_date ?? null, [
                    'class' => 'form-control', 
                    'required',
                ]) !!}

            <div id="maintenance_date_error" class="text-danger" style="display: none;">La fecha de fin no puede ser menor a la de inicio.</div>

            </div>
        </div>

        <!-- Descripción -->
        <div class="col-12">
            <div class="form-group">
                {!! Form::label('description', 'Descripción') !!}
                {!! Form::textarea('description', $maintenancerecord->description ?? null, ['class' => 'form-control', 'placeholder' => 'Ingrese la descripción', 'rows' => 3, 'required']) !!}
            </div>
        </div>
        

        <!-- Imagen (si aplica) -->
        <div class="col-12">
            <div class="form-group">
                {!! Form::file('image_url', ['id' => 'image_url','accept' => 'image/*','class' => 'form-control','hidden' => true]) !!}
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="form-group">
            <div id="imageButton" style="cursor: pointer; width: 100%; text-align: center; padding: 10px;">
                <img style="cursor: pointer; width: 100%; height: 180px;" 
                src="{{ isset($maintenancerecord) && $maintenancerecord->image_url != '' ? asset($maintenancerecord->image_url) : asset('storage/brand_logo/producto_var.webp') }}" 
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
        $('#image_url').click();
    });
</script>