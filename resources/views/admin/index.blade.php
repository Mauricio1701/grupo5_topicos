@extends('adminlte::page')

@section('title', 'Panel de Administración - RSU Municipalidad JLO')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-recycle text-primary"></i>
                    Panel de Administración RSU
                </h1>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card animate-fade-in">
                <div class="welcome-content">
                    <h2 class="welcome-title">
                        <i class="fas fa-star text-warning pulse-icon"></i>
                        ¡Bienvenido al Sistema RSU!
                    </h2>
                    <p class="welcome-subtitle">
                        Sistema de Gestión de Residuos Sólidos Urbanos - Municipalidad José Leonardo Ortiz
                    </p>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Utiliza el menú lateral para navegar entre los diferentes módulos del sistema
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline shadow-lg animate-slide-up">
                <div class="card-header bg-gradient-primary">
                    <h3 class="card-title text-white">
                        <i class="fas fa-info-circle"></i>
                        Sistema de Gestión de Residuos Sólidos Urbanos
                    </h3>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-5 text-center">
                            <div class="image-container">
                                <img src="https://www.munijlo.gob.pe/web/images_load/muni.jpg"
                                    alt="Municipalidad José Leonardo Ortiz"
                                    class="img-fluid rounded shadow-lg mb-3"
                                    style="max-height: 350px; width: 100%; object-fit: contain;">
                            </div>
                            <div class="badge badge-primary p-2 mt-2 scale-hover">
                                <i class="fas fa-building"></i>
                                Gobierno Local
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="info-section">
                                <h3 class="text-primary mb-3">
                                    <i class="fas fa-building"></i>
                                    Municipalidad Distrital de José Leonardo Ortiz
                                </h3>
                                <p class="lead text-justify mb-4">
                                    Sistema integral para la gestión eficiente de los residuos sólidos urbanos del distrito,
                                    optimizando rutas de recolección, administrando personal y mejorando la calidad del servicio
                                    hacia la comunidad josefina.
                                </p>

                                <div class="features-grid">
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <div class="feature-item gentle-hover">
                                                <i class="fas fa-users text-primary rotate-icon"></i>
                                                <span>Gestión de Personal</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <div class="feature-item gentle-hover">
                                                <i class="fas fa-truck text-info bounce-icon"></i>
                                                <span>Control de Vehículos</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <div class="feature-item gentle-hover">
                                                <i class="fas fa-route text-warning shake-icon"></i>
                                                <span>Planificación de Rutas</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <div class="feature-item gentle-hover">
                                                <i class="fas fa-clock text-success tick-icon"></i>
                                                <span>Seguimiento de Asistencia</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <div class="feature-item gentle-hover">
                                                <i class="fas fa-file-contract text-danger swing-icon"></i>
                                                <span>Gestión de Contratos</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <div class="feature-item gentle-hover">
                                                <i class="fas fa-umbrella-beach text-purple flip-icon"></i>
                                                <span>Control de Vacaciones</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <span class="badge badge-pill badge-primary p-2 mr-2 scale-hover">
                                        <i class="fas fa-leaf"></i> Eco-Friendly
                                    </span>
                                    <span class="badge badge-pill badge-info p-2 mr-2 scale-hover">
                                        <i class="fas fa-recycle"></i> Sostenible
                                    </span>
                                    <span class="badge badge-pill badge-warning p-2 scale-hover">
                                        <i class="fas fa-heart"></i> Responsable
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .content-header {
        padding: 15px 0;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-3px); }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-2px); }
        75% { transform: translateX(2px); }
    }

    @keyframes swing {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(5deg); }
        75% { transform: rotate(-5deg); }
    }

    @keyframes flip {
        0%, 100% { transform: rotateY(0deg); }
        50% { transform: rotateY(180deg); }
    }

    @keyframes gentleSlide {
        0% { transform: translateX(0px); }
        100% { transform: translateX(8px); }
    }

    .animate-fade-in {
        animation: fadeIn 1s ease-out;
    }

    .animate-slide-up {
        animation: slideUp 1.2s ease-out 0.3s both;
    }

    .welcome-card {
        background: linear-gradient(135deg, #007bff 0%, #17a2b8 100%);
        border-radius: 15px;
        padding: 25px;
        color: white;
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.2);
        position: relative;
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }

    .welcome-card:hover {
        box-shadow: 0 12px 35px rgba(0, 123, 255, 0.3);
    }

    .welcome-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: rotate(45deg);
    }

    .welcome-content {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .welcome-title {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 10px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .welcome-subtitle {
        font-size: 1.1rem;
        margin-bottom: 10px;
        opacity: 0.9;
    }

    .welcome-card .text-muted {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 500;
    }

    .card {
        border-radius: 15px;
    }

    .pulse-icon {
        animation: pulse 2s infinite;
    }

    .shadow-lg {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
    }

    .image-container {
        position: relative;
    }

    .info-section h3 {
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }

    .feature-item {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
    }

    .gentle-hover:hover {
        transform: translateX(8px);
        background-color: rgba(0, 123, 255, 0.03);
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
        border-left: 3px solid #007bff;
        padding-left: 15px;
    }

    .scale-hover {
        transition: transform 0.3s ease;
    }

    .scale-hover:hover {
        transform: scale(1.05);
    }

    .feature-item i {
        margin-right: 10px;
        font-size: 1.2rem;
        width: 20px;
        transition: all 0.3s ease;
    }

    .rotate-icon:hover {
        animation: rotate 1s ease-in-out;
    }

    .bounce-icon:hover {
        animation: bounce 0.6s ease-in-out;
    }

    .shake-icon:hover {
        animation: shake 0.6s ease-in-out;
    }

    .tick-icon:hover {
        animation: pulse 0.6s ease-in-out;
    }

    .swing-icon:hover {
        animation: swing 0.6s ease-in-out;
    }

    .flip-icon:hover {
        animation: flip 0.6s ease-in-out;
    }

    .features-grid {
        background: rgba(248, 249, 250, 0.8);
        border-radius: 10px;
        padding: 20px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: background-color 0.3s ease;
    }

    .features-grid:hover {
        background: rgba(248, 249, 250, 0.95);
    }

    .bg-gradient-primary {
        background: linear-gradient(87deg, #007bff 0, #17a2b8 100%) !important;
    }

    .text-purple {
        color: #6f42c1 !important;
    }

    .badge {
        font-size: 0.8rem;
    }

    @media (max-width: 768px) {
        .welcome-title {
            font-size: 1.5rem;
        }

        .welcome-subtitle {
            font-size: 1rem;
        }

        .image-container img {
            max-height: 250px;
        }

        .gentle-hover:hover {
            transform: translateX(4px);
            padding-left: 12px;
        }
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('.feature-item').each(function(index) {
            $(this).css('opacity', '0');
            $(this).delay(index * 200).animate({
                opacity: 1
            }, 500);
        });

        $('.badge').each(function(index) {
            $(this).css('opacity', '0');
            $(this).delay(2000 + (index * 300)).animate({
                opacity: 1
            }, 600);
        });
    });
</script>
@stop