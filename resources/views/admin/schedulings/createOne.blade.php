@extends('adminlte::page')

@section('title', 'Programaciones')

@section('css')
<style>
/* Estilo para los botones pequeños, transparentes y sin bordes */
#localData button {
    background-color: transparent; /* Fondo transparente */
    border: 1px solid transparent; /* Borde transparente por defecto */
    border-radius: 15px; /* Bordes redondeados */
    font-size: 14px; /* Tamaño de texto pequeño */
    padding: 5px 15px; /* Espaciado interno del botón */
    cursor: pointer; /* Cambia el cursor al pasar por encima */
    transition: all 0.3s ease; /* Efecto suave para el hover */
}

/* Estilo para el hover del botón */
#localData button:hover {
    background-color: #ffc107; /* Fondo amarillo en hover */
    border: 1px solid #ffc107; /* Borde amarillo en hover */
    color: 000; /* Texto blanco en hover */
}

/* Estilo para los botones en estado seleccionado (si es necesario) */
#localData button.selected {
    background-color: #ffc107; /* Fondo amarillo cuando está seleccionado */
    border: 1px solid #ffc107; /* Borde amarillo cuando está seleccionado */
    color: white; /* Texto blanco cuando está seleccionado */
}

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
@stop
@section('content')
<div class="p-2"></div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Registrar programacion</h3>
      
        <div class="card-tools">
            <a href="{{route('admin.schedulings.index')}}" class="btn btn-link"> Volver</a>
        </div>
    </div>
    

    <div class="card-body">
            <div class="row">
                <div class="col-md-10" id="localData">

                </div>
                <div class="col-md-2" >
                    <button type="button" style="display: none;" class="btn btn-outline-danger btn-sm" id="btnClearFailedGroups">
                        <i class="fas fa-trash-alt"></i> Limpiar
                    </button>
                </div>
            </div>
            <hr>
            
            <!-- Modal unificado -->
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

<div class="row">

    <!-- Grupo de Personal -->
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('employeegroups_id', 'Grupo de Personal') !!} <span class="text-danger">*</span>
            {!! Form::select('employeegroups_id', $employeeGroups->pluck('name', 'id'),null, ['class' => 'form-control', 'required','placeholder' => 'Seleccione un grupo', 'id' => 'employeegroups_id']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('shift_id', 'Turno') !!} <span class="text-danger">*</span>
            {!! Form::text('shift_id_text', '', ['class' => 'form-control', 'id' => 'shift_id_text', 'placeholder' => 'Turno seleccionado', 'readonly' => true]) !!}
            {!! Form::hidden('shift_id', '', ['id' => 'shift_id']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('vehicle_id', 'Vehículo') !!} <span class="text-danger">*</span>
            {!! Form::text('vehicle_id_text', '', ['class' => 'form-control', 'id' => 'vehicle_id_text', 'placeholder' => 'Vehículo seleccionado', 'readonly' => true]) !!}
            {!! Form::hidden('vehicle_id', '', ['id' => 'vehicle_id']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('zone_id', 'Zona') !!} <span class="text-danger">*</span>
            {!! Form::text('zone_id_text', '', ['class' => 'form-control', 'id' => 'zone_id_text', 'placeholder' => 'Zona seleccionada', 'readonly' => true]) !!}
            {!! Form::hidden('zone_id', '', ['id' => 'zone_id']) !!}
        </div>
    </div>


    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('days', 'Días de trabajo') !!} <span class="text-danger">*</span><br>
            @php
                $dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            @endphp
            @foreach ($dias_semana as $dia)
                <label class="mr-3">
                    {!! Form::checkbox('days[]', $dia, false) !!} {{ $dia }}
                </label>
            @endforeach
        </div>

    </div>

    <div class="col-md-12 employee-selects">
        <div class="row" id="employeeColumns">
            <!-- Aquí se generarán las columnas para conductor y ayudantes -->
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group d-flex justify-content-end">
            <button type="button" disabled=true class="btn btn-success" id="saveSchedulingBtn">Guardar Programación</button>
        </div>
    </div>
</div>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $('#employeegroups_id').on('change', function() {
        var groupId = $(this).val();

        if (groupId) {
            $.ajax({
                url: "{{ route('admin.schedulings.createOne') }}",  // Ajusta la ruta si es necesario
                method: "GET",
                data: {
                    employeegroups_id: groupId
                },
                success: function(data) {

                    // Actualizar el input de Turnos
                    $('#shift_id_text').val(data.group.shift.name || '');  
                    $('#shift_id').val(data.group.shift_id);

                    // Actualizar el input de Vehículos
                    $('#vehicle_id_text').val(data.group.vehicle.code || '');
                    $('#vehicle_id').val(data.group.vehicle_id);

                    // Actualizar el input de Zonas
                    $('#zone_id_text').val(data.group.zone.name || '');
                    $('#zone_id').val(data.group.zone_id);

                    // Marcar los días de trabajo seleccionados
                    var diasSeleccionados = data.diasTrabajo;  // Ya es un array, no es necesario usar .split()

                    // Marcar los días de trabajo seleccionados
                    $('input[name="days[]"]').each(function() {
                        if (diasSeleccionados.includes($(this).val())) {
                            $(this).prop('checked', true);  // Marcar el checkbox si está en el array
                        } else {
                            $(this).prop('checked', false);  // Desmarcar el checkbox si no está en el array
                        }
                    });

                        var peopleCapacity = data.group.vehicle.people_capacity || 0;

                        // Limpiar el contenido de los divs dinámicos
                        $('#employeeColumns').empty();

                        // Siempre 1 conductor, y los demás son ayudantes, restando 1 de la capacidad
                        var numConductores = 1; // Siempre hay 1 conductor
                        var numAyudantes = Math.max(0, peopleCapacity - 1); // Restar 1 para los ayudantes

                        // Agregar el select de conductor (si hay conductores)
                        if (numConductores > 0) {
                            $('#employeeColumns').append(`
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_conductor_id">Conductor</label>
                                        <select class="form-control" id="employee_conductor_id" name="employee_conductor_id[]">
                                            <option value="">Seleccione un conductor</option>
                                        </select>
                                    </div>
                                </div>
                            `);

                            data.employeesConductor.forEach(function(conductor) {
                                $('#employee_conductor_id').append(`
                                    <option value="${conductor.id}">${conductor.names + ' '+conductor.lastnames}</option>
                                `);
                            });
                        }

                        // Agregar los selects de ayudantes (restando 1 para el conductor)
                        for (var i = 0; i < numAyudantes; i++) {
                                $('#employeeColumns').append(`
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="employee_helper_id_${i}">Ayudante ${i+1}</label>
                                            <select class="form-control" id="employee_helper_id_${i}" name="employee_helper_id[]">
                                                <option value="">Seleccione un ayudante</option>
                                            </select>
                                        </div>
                                    </div>
                                `);

                            data.employeesAyudantes.forEach(function(ayudante) {
                                $('#employee_helper_id_' + i).append(`
                                    <option value="${ayudante.id}">${ayudante.names + ' '+ayudante.lastnames}</option>
                                `);
                            });
                        }

                

                    if (Array.isArray(data.group.configgroup) && data.group.configgroup.length > 0) {
                    const ayudantesAsignados = [];

                    data.group.configgroup.forEach((config, index) => {
                        if (config.employee.employee_type.name === 'Conductor') {
                            $('#employee_conductor_id').val(config.employee_id);
                        } else if (config.employee.employee_type.name === 'Ayudante') {
                            // Buscar el primer select de ayudante vacío
                            for (let i = 0; i < numAyudantes; i++) {
                                const selectEl = $(`#employee_helper_id_${i}`);
                                if (selectEl.length && !selectEl.val()) {
                                    selectEl.val(config.employee_id);
                                    ayudantesAsignados.push(config.employee_id);
                                    break;
                                }
                            }
                        }
                    });
                }
                    initializeDynamicSelects();
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar los datos: " + error);
                }
            });
        }
    });

    $('#saveSchedulingBtn').on('click', function() {
        // Recoger los datos de la programación

        if (!validarDates()) {
            return;
        }


        if (!validarCamposDeSeleccion()) {
            Swal.fire({
                icon: 'warning',
                title: '¡Verifica los campos!',
                text: 'Asegúrate de que todos los selects estén llenos y sin valores repetidos.'
            });
            return;
        }

        const schedulingData = getSchedulingData();

        schedulingData._token = '{{ csrf_token() }}';

        // Enviar los datos al servidor
        $.ajax({
            url: "{{ route('admin.schedulings.storeOne') }}",
            type: "POST",
            data: schedulingData,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Programación guardada correctamente.',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    window.location.href = "{{ route('admin.schedulings.index') }}"; // Redirigir a la lista de programaciones
                });
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

    function getSchedulingData() {
        const schedulingData = {
            // Recoger los datos de las fechas
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val() || null, // Si no hay end_date, lo dejamos como null

            // Recoger el grupo de personal seleccionado
            employee_group_id: $('#employeegroups_id').val(),
            
            // Recoger el turno, vehículo y zona seleccionados
            shift_id: $('#shift_id').val(),
            vehicle_id: $('#vehicle_id').val(),
            zone_id: $('#zone_id').val(),

            // Recoger los días de trabajo seleccionados
            days: [],
            
            // Recoger los datos de conductores y ayudantes
            helpers: []
        };

        // Obtener los días seleccionados (checkboxes)
        $('input[name="days[]"]:checked').each(function() {
            schedulingData.days.push($(this).val());
        });

        // Obtener los datos de los conductores y ayudantes
        const groupId = $('#employeegroups_id').val();
        
        const driverId = $('#employee_conductor_id').val();  // Obtener el conductor
        schedulingData.driver_id = (driverId);

        // Obtener los ayudantes
        $('select[name^="employee_helper_id"]').each(function() {
            const helperId = $(this).val();
            if (helperId) {
                schedulingData.helpers.push(helperId);
            }
        });

        // Imprimir los datos para verificar
        console.log(schedulingData);

        return schedulingData;
    }

    function getHelpersData() {
        const data = [];

        // Obtener el ID del conductor
        const driverId = $('#employee_conductor_id').val();

        // Obtener los ayudantes
        const helpers = $('select[name^="employee_helper_id"]').map(function() {
            return $(this).val();
        }).get();

        // Usamos un "group_id" genérico (por ejemplo 1 o null)
        data.push({
            group_id: null, // o null si no tienes un grupo real
            employees: [driverId, ...helpers].filter(Boolean) // filtra vacíos
        });

        return data;
    }

    function formatDate(dateString) {
        const [year, month, day] = dateString.split('T')[0].split('-');
        return `${day}/${month}/${year}`;
    }

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

    $('#btnValidar').on('click', function () {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        const shiftId =  $('#shift_id').val();
        const zoneId = $('#zone_id').val();

        const work_days = $('input[name="days[]"]:checked').map(function() {
            return $(this).val();
        }).get();

        if (!validarDates()) {
            return;
        }

        if (!validarCamposDeSeleccion()) return;

        const data ={
            start_date: startDate,
            end_date: endDate,
            shift_id: shiftId,
            zone_id: zoneId,
            work_days: work_days,
            helpers: getHelpersData(),

        }


        $.ajax({
            url: '{{ route('admin.schedulings.validationVacations') }}',
            type: 'GET',
            data: data ,
            success: function(response) {
                const no_disponibles = response.no_disponibles;
                // --- Limpiar estados previos ---
                $('#vacaciones-tab, #contratos-tab, #conflictos-tab').removeClass('text-danger text-success');

                if(Array.isArray(no_disponibles)){
                        resetSelect2Style('#employee_conductor_id, select[name^="employee_helper_id"]');
                        if(no_disponibles.length === 0) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Todos disponibles!',
                                text: 'Los conductores y ayudantes seleccionados están disponibles para las fechas indicadas.',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                             $('#saveSchedulingBtn').removeAttr('disabled');
                            return;
                        }

                        no_disponibles.forEach(id => {
                            // Marcar el campo correspondiente como inválido (borde rojo)
                            if (parseInt($('#employee_conductor_id').val()) === parseInt(id)) {
                                const selectEl = $('#employee_conductor_id');

                                    if (selectEl.hasClass('select2-hidden-accessible')) {
                                        selectEl.next('.select2-container')
                                            .find('.select2-selection')
                                            .css('border', '2px solid red');
                                    } else {
                                        selectEl.css('border', '2px solid red');
                                    }
                                }

                                $('select[name^="employee_helper_id"]').each(function() {
                                    if (parseInt($(this).val()) === parseInt(id)) {
                                        if ($(this).hasClass('select2-hidden-accessible')) {
                                            $(this).next('.select2-container')
                                                .find('.select2-selection')
                                                .css('border', '2px solid red');
                                        } else {
                                            $(this).css('border', '2px solid red');
                                        }
                                    }
                                });

                        });
                }

                // Mostrar vacaciones aprobadas (si hay)
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

                if(no_disponibles.length == 0 && vacaciones.length == 0){
                    $('#saveSchedulingBtn').removeAttr('disabled');
                }

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


    function loadFailedGroups() {
        const failedGroupsData = localStorage.getItem('failedGroups');
        
        if (failedGroupsData) {
            $('#btnClearFailedGroups').show();
            // Parsear los datos guardados en localStorage
            const failedGroupsJson = JSON.parse(failedGroupsData);
            const failedGroups = failedGroupsJson.failed_groups;
            
            // Limpiar el contenido previo de #localData
            $('#localData').empty();

            // Autollenar los campos start_date y end_date con los datos de localStorage
            $('#start_date').val(failedGroupsJson.start_date);
            $('#end_date').val(failedGroupsJson.end_date || '');  // Si no hay end_date, lo dejamos vacío

            // Mostrar los grupos no disponibles como botones pequeños y transparentes
            failedGroups.forEach(group => {
                const groupButton = `
                    <button type="button" class="btn btn-warning btn-sm m-2" style="background-color: transparent; border: 1px solid #ffc107;" data-group-id="${group.employee_group_id}">
                        Grupo ${group.employee_group_id}
                    </button>`;
                $('#localData').append(groupButton);
            });

            // Agregar evento a los botones para seleccionar el grupo en el select
        $(document).on('click', '#localData button', function() {
                const selectedGroupId = $(this).data('group-id');
                
                // Establecer el valor de #employeegroups_id con el id del grupo
                $('#employeegroups_id').val(selectedGroupId);
                
                // Activar el evento change para cargar los datos del grupo
                $('#employeegroups_id').trigger('change');
            });
        }
    }

    // Cargar los grupos no registrados al cargar la página
    $(document).ready(function() {
        loadFailedGroups(); // Cargar los datos almacenados en localStorage si existen
    });

    function initializeDynamicSelects() {
         $('select[name="employee_conductor_id[]').select2({
            placeholder: 'Seleccione un conductor',
            
        });

        $('select[name="employee_helper_id[]"]').select2({
            placeholder: 'Seleccione un ayudante',
            
        });
    }

   

    function validarCamposDeSeleccion() {
        let esValido = true;
        let idsSeleccionados = new Set();
        
        // Limpiar bordes anteriores
        $('#employee_conductor_id').css('border', '');
        $('select[name^="employee_helper_id"]').css('border', '');

        const conductorId = $('#employee_conductor_id').val();
        
        // Validar conductor
        if (!conductorId) {
            $('#employee_conductor_id').css('border', '2px solid red');
            esValido = false;
        } else if (idsSeleccionados.has(conductorId)) {
            $('#employee_conductor_id').css('border', '2px solid red');
            esValido = false;
        } else {
            idsSeleccionados.add(conductorId);
        }

        // Validar ayudantes
        $('select[name^="employee_helper_id"]').each(function () {
            const helperId = $(this).val();
            if (!helperId) {
                $(this).css('border', '2px solid red');
                esValido = false;
            } else if (idsSeleccionados.has(helperId)) {
                $(this).css('border', '2px solid red');
                esValido = false;
            } else {
                idsSeleccionados.add(helperId);
            }
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
                text: 'Por favor, selecciona al menos la fecha de inicio para filtrar.',
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

    $('#btnClearFailedGroups').on('click', function () {
        localStorage.removeItem('failedGroups');
        $('#localData').empty();
        $('#start_date').val('');
        $('#end_date').val('');
        $('#employeegroups_id').val('').trigger('change');
        $('#btnClearFailedGroups').hide(); // Ocultar el botón después de limpiar

        Swal.fire({
            icon: 'success',
            title: 'Limpieza completada',
            text: 'Se han eliminado los grupos fallidos.'
        });
    });


</script>
@stop


