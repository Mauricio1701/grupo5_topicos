<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programación</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

    <style>
        body {
            background-color: #f4f6f9;
            color: #333;
            padding: 40px;
        }

        .summary-row {
            background-color: #fff;
            border-left: 5px solid #17a2b8;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .summary-row .item {
            font-weight: 500;
        }

        .legend {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #fff;
            border-left: 5px solid #007bff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
            max-width: 600px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .color-box {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            border-radius: 3px;
        }

        .green-box { background-color: #28a745; }
        .red-box { background-color: #dc3545; }

        .card.vehicle-card {
            height: 160px;
            border-width: 2px;
            border-style: solid;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out;
        }

        .card.vehicle-card:hover {
            transform: translateY(-4px);
        }

        .card.vehicle-card.green {
            border-color: #28a745;
        }

        .card.vehicle-card.red {
            border-color: #dc3545;
        }

        .vehicle-title {
            font-weight: bold;
            font-size: 1.2rem;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    </style>
</head>
<body>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h1 class="h4">Programación - Turno Mañana</h1>
        <button class="btn btn-primary">Asignar Ayudantes</button>
    </div>

    <div class="row mb-4">
        <!-- Resumen en cuadritos -->
        <div class="col-md-6">
            <div class="row">
                <div class="col-6 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body p-3">
                            <div class="h4 mb-1">👥 24</div>
                            <small class="text-muted">Asistieron</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="card text-center shadow-sm ">
                        <div class="card-body p-3">
                            <div class="h4 mb-1">🚚 2</div>
                            <small class="text-muted">Grupos completos</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card text-center shadow-sm ">
                        <div class="card-body p-3">
                            <div class="h4 mb-1">🧍 5</div>
                            <small class="text-muted">Apoyos disponibles</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card text-center shadow-sm ">
                        <div class="card-body p-3">
                            <div class="h4 mb-1">❌ 3</div>
                            <small class="text-muted">Faltan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="legend h-100">
                <strong>Leyenda de colores:</strong>
                <div class="legend-item mt-2">
                    <span class="color-box green-box"></span> Grupo completo y listo para operar
                </div>
                <div class="legend-item">
                    <span class="color-box red-box"></span> Faltan integrantes por llegar o confirmar asistencia
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card vehicle-card green">
                <div class="card-body text-center">
                    <div class="vehicle-title">Placa: ABC-123</div>
                    <div>Grupo completo y listo para operar</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card vehicle-card red">
                <div class="card-body text-center">
                    <div class="vehicle-title">Placa: XYZ-789</div>
                    <div>Faltan integrantes por registrar asistencia</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card vehicle-card green">
                <div class="card-body text-center">
                    <div class="vehicle-title">Placa: DEF-456</div>
                    <div>Grupo completo y listo para operar</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card vehicle-card red">
                <div class="card-body text-center">
                    <div class="vehicle-title">Placa: LMN-321</div>
                    <div>Faltan integrantes por registrar asistencia</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
