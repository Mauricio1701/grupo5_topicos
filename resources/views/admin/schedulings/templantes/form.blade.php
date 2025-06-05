<!-- Modal -->
<div class="modal fade" id="modalForm" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ModalLongTitle"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          {{-- Contenido del formulario cargado por AJAX --}}
        </div>
      </div>
    </div>
</div>

<div class="row">
    @foreach ($employeeGroups as $group)
        @php
            $vehicle = $vehicles->firstWhere('id', $group->vehicle_id);
            $zone = $zones->firstWhere('id', $group->zone_id);
            $conductor = $group->conductors->first();
            $helpers = $group->helpers;
            $capacity = optional($vehicle)->people_capacity ?? 1;
            $numHelpers = max(0, $capacity - 1);
        @endphp

        <div class="col-md-6 mb-3 group-card" data-group-id="{{ $group->id }}">
            <div class="card border border-black shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong class="text-black">{{ $group->name }}</strong>
                    <button class="btn btn-sm btn-danger remove-card ml-auto"><i class="fas fa-trash"></i></button>
                </div>
                <div class="card-body">
                    <p><strong>Zona:</strong> {{ $zone->name ?? 'Sin asignar' }}</p>
                    <p><strong>Turno:</strong> {{ $shift->name }}</p>
                    <p><strong>Vehículo:</strong> {{ $vehicle->code ?? 'Sin asignar' }} (Capacidad: {{ $capacity }})
                    <button class="btn btn-sm btn-warning"><i class="fas fa-recycle"></i></button></p>
                    
                    <div class="mb-2">
                        <label><strong>Conductor:</strong></label>
                        <select name="driver_id[{{ $group->id }}]" class="form-control">
                            <option value="">Seleccione un conductor</option>
                            @foreach ($employeesConductor as $emp)
                                <option value="{{ $emp->id }}" {{ $conductor && $conductor->id === $emp->id ? 'selected' : '' }}>
                                    {{ $emp->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @for ($i = 0; $i < $numHelpers; $i++)
                        <div class="mb-2">
                            <label>Ayudante {{ $i + 1 }}:</label>
                            <select name="helpers[{{ $group->id }}][]" class="form-control">
                                <option value="">Seleccione un ayudante</option>
                                @foreach ($employeesAyudantes as $ayudante)
                                    <option value="{{ $ayudante->id }}"
                                        {{ isset($helpers[$i]) && $helpers[$i]->id === $ayudante->id ? 'selected' : '' }}>
                                        {{ $ayudante->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
    $(document).on('click', '.remove-card', function () {
        $(this).closest('.group-card').remove();
    });

    $(document).on('click', '.btn-warning', function () {
        const groupId = $(this).closest('.group-card').data('group-id');
        $.ajax({
            url: '{{ route('admin.employee-groups.vehiclechange', 'GROUP_ID') }}'.replace('GROUP_ID', groupId),
            type: "GET",
            success: function(response) {
                $('#ModalLongTitle').text('Cambio de Vehículo');
                $('#modalForm .modal-body').html(response);
                $('#modalForm').modal('show');
                
            }
        })
    });
</script>

