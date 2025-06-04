{!! Form::open(['route' => 'admin.zones.store', 'method' => 'POST']) !!}
<div class="form-group">
    {!! Form::label('name', 'Nombre:') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el nombre de la zona', 'required']) !!}
    @error('name')
    <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    {!! Form::label('description', 'Descripción:') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la descripción de la zona', 'rows' => 3]) !!}
    @error('description')
    <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="map">Dibuje la zona en el mapa:</label>
    <div id="map" style="height: 400px;"></div>
    <div class="mt-2">
        <button type="button" class="btn btn-secondary btn-sm" id="clearPolygon">Limpiar dibujo</button>
        <span class="text-muted ml-2">Haga clic en el mapa para agregar puntos y crear un polígono.</span>
    </div>
    @error('coords')
    <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<div id="coordinates-container">
</div>

<div class="d-flex justify-content-end gap-2">
    <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-save"></i> Guardar</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-ban"></i> Cancelar</button>
</div>
{!! Form::close() !!}

<script>
    $(document).ready(function() {
        var map = L.map('map').setView([-6.7711, -79.8430], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var polygonCoords = [];

        var polygon = null;

        var markers = [];

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

            console.log('Actualizando campos ocultos. Puntos:', polygonCoords.length);

            polygonCoords.forEach(function(coord, index) {
                $('#coordinates-container').append(
                    `<input type="hidden" name="coords[${index}][latitude]" value="${coord.lat}">
            <input type="hidden" name="coords[${index}][longitude]" value="${coord.lng}">`
                );
                console.log(`Punto ${index}: lat=${coord.lat}, lng=${coord.lng}`);
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

    $('form').on('submit', function(e) {
        if (polygonCoords.length < 3) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Debe dibujar al menos 3 puntos en el mapa para formar un polígono.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Aceptar'
            });
            return false;
        }
        updateHiddenFields();
    });
</script>