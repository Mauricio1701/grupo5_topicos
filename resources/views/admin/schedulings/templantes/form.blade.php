<!-- Modal -->
<style>
    /* Modificar la altura del contenedor de selección */
    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px) !important; /* Asegúrate de usar !important si es necesario */
        padding: 6px 12px;
    }

    /* Cambiar el color de fondo del dropdown */
    .select2-container--default .select2-dropdown {
        background-color: #f8f9fa !important;  /* Fondo claro */
        border-radius: 4px;
    }

    /* Cambiar el color de texto del ítem seleccionado */
    .select2-container--default .select2-selection__rendered {
        color: #333 !important;  /* Cambiar el color del texto */
    }
</style>
<div class="modal fade" id="validationModal" tabindex="-1" role="dialog" aria-labelledby="validationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="validationModalLabel">
          <i class="fas fa-clipboard-check"></i> Resultados de Validación
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="validationTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="vacaciones-tab" data-toggle="tab" data-target="#vacaciones" type="button" role="tab">
              <i class="fas fa-plane-departure"></i> Vacaciones
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="contratos-tab" data-toggle="tab" data-target="#contratos" type="button" role="tab">
              <i class="fas fa-file-signature"></i> Contratos
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="conflictos-tab" data-toggle="tab" data-target="#conflictos" type="button" role="tab">
              <i class="fas fa-exclamation-triangle"></i> Conflictos
            </button>
          </li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane fade show active" id="vacaciones" role="tabpanel">
            <ul class="list-group list-group-flush" id="vacationItems"></ul>
          </div>

          <div class="tab-pane fade" id="contratos" role="tabpanel">
            <ul class="list-group list-group-flush" id="nocontratoItem"></ul>
          </div>

          <div class="tab-pane fade" id="conflictos" role="tabpanel">
            <div class="accordion" id="conflicList">
              <ul class="list-group list-group-flush" id="conflicItem"></ul>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


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
<hr>
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="start_date" class="form-label">Fecha de inicio: <span class="text-danger">*</span></label>
        <input type="date" name="start_date" id="start_date" class="form-control">
    </div>
    <div class="col-md-4 mb-3">
        <label for="end_date" class="form-label">Fecha de fin: </label>
        <input type="date" name="end_date" id="end_date" class="form-control">
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-end">
        <button class="btn btn-outline-info w-100" id="btnValidar"> <i class="fas fa-calendar"></i> Validar Disponibilidad</button>
    </div>
</div>

<hr>
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

        <div class="col-md-4 mb-3 group-card" data-group-id="{{ $group->id }}">
            <div class="card border border-black shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong class="text-black">{{ $group->name }}</strong>
                    <button class="btn btn-sm btn-danger remove-card ml-auto"><i class="fas fa-trash"></i></button>
                </div>
                <div class="card-body">
                    <p><strong>Zona:</strong> {{ $zone->name ?? 'Sin asignar' }}</p>
                    <p><strong>Turno:</strong> {{ $shift->name }}</p>
                    <p><strong>Dias:</strong> {{ $group->days }}</p>
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

<div class="row justify-content-end">
    <button type="button" class="btn btn-success" id="submitAll">Registrar Programación</button>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function getGroupData() {
        const groupData = {
            groups: [] // Creamos un array de grupos
        };
        
        // Recorremos todos los grupos
        $('.group-card').each(function () {
            const groupId = $(this).data('group-id');
            const driverId = $(this).find(`select[name="driver_id[${groupId}]"]`).val();
            const helpers = $(this).find(`select[name="helpers[${groupId}][]"]`).map(function () {
                return $(this).val();
            }).get();
            
            // Almacenamos los datos de cada grupo en el array 'groups'
            groupData.groups.push({
                employee_group_id: groupId, // Aquí almacenamos el employee_group_id
                driver_id: driverId,
                helpers: helpers
            });
        });

        // Añadimos las fechas al objeto de datos
        groupData.start_date = $('#start_date').val();
        groupData.end_date = $('#end_date').val() || null;  // Si no hay end_date, lo ponemos como vacío

        // Imprimimos el JSON en consola para verificar
       

        return groupData;
    }

    function getHelpersData() {
        const data = [];

        $('.group-card').each(function () {
            const groupId = $(this).data('group-id');
            const driverId = $(this).find(`select[name="driver_id[${groupId}]"]`).val();
            const helpers = $(this).find(`select[name="helpers[${groupId}][]"]`).map(function () {
                return $(this).val();
            }).get();

            // Empuja un objeto con el grupo y sus empleados
            data.push({
                group_id: groupId,
                employees: [driverId, ...helpers].filter(Boolean) // quita nulls/vacíos
            });
        });

        return data;
    }


    $(document).on('click', '.remove-card', function () {
        $(this).closest('.group-card').remove();
    });

    initializeDynamicSelects();

    function initializeDynamicSelects() {
         $('select').select2({
            placeholder: 'Seleccione una opción',
        });
    }


    $('#submitAll').on('click', function () {
        if (!validarDates()) {
            return;
        }
        if (!validarSelects()) return;
        const data = getGroupData();
        data._token = '{{ csrf_token() }}';
        Swal.fire({
            title:'Estamos registrando la programación',
            text: 'Por favor, espere un momento...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        })

        console.log(JSON.stringify(data));
        $.ajax({
            url: '{{ route('admin.schedulings.store') }}',
            type: 'POST',
            data: data,
            success: function(response) {
                console.log('Datos enviados correctamente', response);
                const noregistros = response.noregistros;  // Lista de grupos no registrados
                const failedGroups = data.groups.filter(group =>
                    noregistros.includes(String(group.employee_group_id))
                );

                console.log('Tamaño:', noregistros.length);

                // Crear un JSON con los grupos que no se registraron
                const failedGroupsJson = {
                    failed_groups: failedGroups,
                    start_date: data.start_date,
                    end_date: data.end_date || null,  // Si no hay end_date, lo ponemos como vacío
                };

                console.log("Grupos no registrados: ", failedGroupsJson);

                if (noregistros.length > 0) {
                    Swal.close();
                    Swal.fire({
                        icon: 'warning',
                        title: '¡Atención!',
                        text: `Se han registrado programaciones pero no se han podido registrar algunos grupos debido a inconsistencias`,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            localStorage.setItem('failedGroups', JSON.stringify(failedGroupsJson));
                            window.location.href = '{{ route('admin.schedulings.createOne') }}';
                        }
                    });
                }else{
                    Swal.close();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Programación registrada correctamente',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route('admin.schedulings.index') }}';
                        }
                    });
                }

            },
            error: function(xhr) {
                let res = xhr.responseJSON;
                console.log(res);
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: res.message || 'Ocurrió un error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    $(document).on('click', '.btn-warning', function () {
        const groupId = $(this).closest('.group-card').data('group-id');
        console.log(groupId);
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

     function resetSelect2Style(selector) {
        // Para cada elemento select2 encontrado
        $(selector).each(function() {
            const selectEl = $(this);

            if (selectEl.hasClass('select2-hidden-accessible')) {
                // Si está inicializado con select2, resetea el contenedor visual
                selectEl.next('.select2-container')
                    .find('.select2-selection')
                    .css({
                        'border': '',
                        'background-color': '',
                        'color': ''
                    });
            } else {
                // Si es un select normal (por alguna razón)
                selectEl.css({
                    'border': '',
                    'background-color': '',
                    'color': ''
                });
            }
        });
    }

    function formatDate(dateString) {
        const [year, month, day] = dateString.split('T')[0].split('-');
        return `${day}/${month}/${year}`;
    }



    $('#btnValidar').on('click', function () {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        
        if (!validarDates()) {
            return;
        }

        if (!validarSelects()) return;
        $.ajax({
            url: '{{ route('admin.schedulings.validationVacations') }}',
            type: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate,
                helpers: getHelpersData(),

            },
            success: function(response) {
                const no_disponibles = response.no_disponibles;
                resetSelect2Style('select[name="driver_id[${groupId}]"], select[name^="employee_helper_id"]');
                $('#vacaciones-tab, #contratos-tab, #conflictos-tab').removeClass('text-danger text-success');

                $('.group-card').each(function () {
                    const groupId = $(this).data('group-id');
                    console.log('Validando grupo ID:', groupId);

                    // === CONDUCTOR ===
                    const $driverSelect = $(this).find(`select[name="driver_id[${groupId}]"]`);
                    const driverId = parseInt($driverSelect.val(), 10);
                    
                    if (no_disponibles.includes(driverId)) {
                        if ($driverSelect.hasClass('select2-hidden-accessible')) {
                            $driverSelect.next('.select2-container')
                                .find('.select2-selection')
                                .css('border', '2px solid red');
                        } else {
                            $driverSelect.css('border', '2px solid red');
                        }
                    } else {
                        if ($driverSelect.hasClass('select2-hidden-accessible')) {
                            $driverSelect.next('.select2-container')
                                .find('.select2-selection')
                                .css('border', '');
                        } else {
                            $driverSelect.css('border', '');
                        }
                    }

                    // === AYUDANTES ===
                    $(this).find(`select[name="helpers[${groupId}][]"]`).each(function () {
                        const $helperSelect = $(this);
                        const helperId = parseInt($helperSelect.val(), 10);

                        if (no_disponibles.includes(helperId)) {
                            if ($helperSelect.hasClass('select2-hidden-accessible')) {
                                $helperSelect.next('.select2-container')
                                    .find('.select2-selection')
                                    .css('border', '2px solid red');
                            } else {
                                $helperSelect.css('border', '2px solid red');
                            }
                        } else {
                            if ($helperSelect.hasClass('select2-hidden-accessible')) {
                                $helperSelect.next('.select2-container')
                                    .find('.select2-selection')
                                    .css('border', '');
                            } else {
                                $helperSelect.css('border', '');
                            }
                        }
                    });
                });

                const vacaciones = response.vacaciones || [];
                $('#vacationItems').empty();
                if (vacaciones.length > 0) {
                    $('#vacaciones-tab').addClass('text-danger');

                    vacaciones.forEach(v => {
                        // Convertir fechas a formato local (dd/mm/yyyy)
    
                        const requestDate = formatDate(v.request_date);
                        const endDate = formatDate(v.end_date);

                        $('#vacationItems').append(`
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><strong>${v.employee.names}</strong></span>
                                <span class="badge badge-warning">Del ${requestDate} al ${endDate}</span>
                            </li>
                        `);
                    });
                } else {                
                    $('#vacaciones-tab').addClass('text-success'); // verde si todo bien
                }

                const nocontrato = response.nocontrato || [];
                $('#nocontratoItem').empty();

                if (nocontrato.length > 0) {
                    $('#contratos-tab').addClass('text-danger');

                    nocontrato.forEach(v => {
                        // Convertir fechas a formato local (dd/mm/yyyy)
    
                        const requestDate = formatDate(v.contract.start_date);
                        const endDate = formatDate(v.contract.end_date);

                        $('#nocontratoItem').append(`
                            <li class="list-group-item d-flex flex-col justify-content-between align-items-center">
                                <span><strong>${v.employee.names}</strong></span>
                                <span>${v.message}</span>
                                <span class="badge badge-warning">Del ${requestDate} al ${endDate}</span>
                            </li>
                        `);
                    });
                } else {
                    $('#contratos-tab').addClass('text-success');
                }
            
                const conflictos = response.conflictos || [];
                $('#conflicList').empty();

                if (conflictos.length > 0) {
                    $('#conflictos-tab').addClass('text-danger');

                    conflictos.forEach((conflict, index) => {
                        const collapseId = `collapse-${conflict.employee_id}`;
                        const headingId = `heading-${conflict.employee_id}`;

                        let messageItems = '';
                       conflict.messages.forEach(msg => {
                            messageItems += `
                                <li class="list-group-item">
                                    Programación <strong>${msg.date}</strong> en zona ${msg.zone} (Turno: ${msg.shift})
                                </li>`;
                        });

                      const accordionItem = `
                        <div class="card accordion-card" data-toggle="collapse" data-target="#${collapseId}">
                            <div class="card-header" id="${headingId}">
                            <h6 class="mb-0 d-flex justify-content-between align-items-center">
                                <span>👤 ${conflict.employee_name}</span>
                                <i class="fas fa-chevron-down rotate-icon"></i>
                            </h6>
                            </div>
                            <div id="${collapseId}" 
                                class="collapse" 
                                aria-labelledby="${headingId}" 
                                data-parent="#conflicList">
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                   ${messageItems} 
                                </ul>
                            </div>
                            </div>
                        </div>
                        `;

                        $('#conflicList').append(accordionItem);
                    });

                } else {
                   $('#conflictos-tab').addClass('text-success');
                }

                $('#validationModal').modal('show');

                
                Swal.fire({
                    icon: 'info',
                    title: 'Validación Completa',
                    text: 'Los conductores y ayudantes seleccionados han sido validados. Los que tienen borde rojo no están disponibles.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar'
                });
            },
            error: function(xhr) {
                let res = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: res?.message || 'Ocurrió un error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });


    function validarSelects() {
        let esValido = true;
        let idsSeleccionados = [];

        // Limpiar estilos previos
        $('select').css('border', '');

        $('.group-card').each(function () {
            const groupId = $(this).data('group-id');

            // Validar conductor
            const driverSelect = $(this).find(`select[name="driver_id[${groupId}]"]`);
            const driverId = driverSelect.val();
            if (!driverId) {
                driverSelect.css('border', '2px solid red');
                esValido = false;
            } else if (idsSeleccionados.includes(driverId)) {
                driverSelect.css('border', '2px solid red');
                esValido = false;
            } else {
                idsSeleccionados.push(driverId);
            }

            // Validar ayudantes
            $(this).find(`select[name="helpers[${groupId}][]"]`).each(function () {
                const helperId = $(this).val();
                if (!helperId) {
                    $(this).css('border', '2px solid red');
                    esValido = false;
                } else if (idsSeleccionados.includes(helperId)) {
                    $(this).css('border', '2px solid red');
                    esValido = false;
                } else {
                    idsSeleccionados.push(helperId);
                }
            });
        });

        if (!esValido) {
            Swal.fire({
                icon: 'warning',
                title: '¡Atención!',
                text: 'Todos los campos deben estar seleccionados y sin duplicados.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Aceptar'
            });
        }

        return esValido;
    }

    function validarDates(){
        let esValido = true;
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        if (startDate === '' || startDate === null) {
            Swal.fire({
                icon: 'warning',
                title: '¡Atención!',
                text: 'Por favor, selecciona al menos la fecha de inicio.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Aceptar'
            });
            esValido = false;
        }



        if (endDate !== '' ) {
            
            if (startDate > endDate) {
                Swal.fire({
                    icon: 'warning',
                    title: '¡Atención!',
                    text: 'La fecha de fin no puede ser menor que la fecha de inicio.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar'
                });
                 esValido = false;
            }
            // Recargar el DataTable con las fechas
        }

        return esValido;

    }


</script>

