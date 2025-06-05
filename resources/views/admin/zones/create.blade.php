
{!! Form::open(['route' => 'admin.zones.store', 'method' => 'POST', 'id' => 'zoneForm']) !!}
<div class="form-group">
    {!! Form::label('name', 'Nombre:') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el nombre de la zona', 'required']) !!}
    <span class="text-danger error-text name_error"></span>
</div>

<div class="form-group">
    {!! Form::label('description', 'Descripción:') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la descripción de la zona', 'rows' => 3]) !!}
    <span class="text-danger error-text description_error"></span>
</div>

<div class="form-group">
    <label for="map">Dibuje la zona en el mapa:</label>
    <div id="map" style="height: 400px;"></div>
    <div class="mt-2">
        <button type="button" class="btn btn-secondary btn-sm" id="clearPolygon">Limpiar dibujo</button>
        <span class="text-muted ml-2">Haga clic en el mapa para agregar puntos y crear un polígono.</span>
    </div>
    <span class="text-danger error-text coords_error"></span>
</div>

<div id="coordinates-container">
</div>

<div class="d-flex justify-content-end gap-2">
    <button type="submit" class="btn btn-primary mr-2" id="btnSaveZone"><i class="fas fa-save"></i> Guardar</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-ban"></i> Cancelar</button>
</div>
{!! Form::close() !!}

<script>
    var polygonCoords = [];
    var polygon = null;
    var markers = [];
    var map;

    $(document).ready(function() {
        map = L.map('map').setView([-6.7711, -79.8430], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        function updatePolygon() {
            if (polygon) {
                map.removeLayer(polygon);
            }

            if (polygonCoords.length >= 3) {
                polygon = L.polygon(polygonCoords, {
                    color: 'blue'
                }).addTo(map);
            }

            updateHiddenFields();
        }

        function updateHiddenFields() {
            $('#coordinates-container').empty();

            polygonCoords.forEach(function(coord, index) {
                $('#coordinates-container').append(
                    `<input type="hidden" name="coords[${index}][latitude]" value="${coord.lat}">
                    <input type="hidden" name="coords[${index}][longitude]" value="${coord.lng}">`
                );
            });
        }

        map.on('click', function(e) {
            polygonCoords.push(e.latlng);

            var marker = L.marker(e.latlng).addTo(map);
            markers.push(marker);

            updatePolygon();
        });

        $('#clearPolygon').click(function() {
            polygonCoords = [];

            markers.forEach(function(marker) {
                map.removeLayer(marker);
            });
            markers = [];

            if (polygon) {
                map.removeLayer(polygon);
                polygon = null;
            }

            $('#coordinates-container').empty();
        });

        $('#modalZone').on('shown.bs.modal', function() {
            map.invalidateSize();
        });
    });

    $('#zoneForm').on('submit', function(e) {
        e.preventDefault();
        $('.error-text').text('');
        
        if (polygonCoords.length < 3) {
            $('.coords_error').text('Debe dibujar al menos 3 puntos en el mapa para formar un polígono válido.');
            return false;
        }
        
        updateHiddenFields();
        
        var form = $(this);
        var formData = form.serialize();
        
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: formData,
            beforeSend: function() {
                $('#btnSaveZone').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
            },
            success: function(response) {
                if (response.success) {
                    $('#modalZone').modal('hide');
                    table.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'No se pudo guardar la zona. Verifique los datos e intente nuevamente.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function(xhr) {
                $('#btnSaveZone').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
                
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('.' + key + '_error').text(value);
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error de validación!',
                        text: 'Por favor corrija los errores señalados en el formulario.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'No se pudo guardar la zona. Ha ocurrido un error en el servidor.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            complete: function() {
                $('#btnSaveZone').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });

    function updateHiddenFields() {
        $('#coordinates-container').empty();
        
        polygonCoords.forEach(function(coord, index) {
            $('#coordinates-container').append(
                `<input type="hidden" name="coords[${index}][latitude]" value="${coord.lat}">
                <input type="hidden" name="coords[${index}][longitude]" value="${coord.lng}">`
            );
        });
    }
</script>