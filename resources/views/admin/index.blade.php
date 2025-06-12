@extends('adminlte::page')

@section('title', 'Proyecto Rsu')

@section('content_header')
    <h1>Proyecto Rsu</h1>
@stop

@section('content')
    <p>Bienvenidos al panel de administración</p>
@stop

@section('css')
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Aceptar'
        });
    </script>
    @endif
@stop