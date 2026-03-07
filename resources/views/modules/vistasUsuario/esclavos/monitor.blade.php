@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Panel de Monitoreo</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('user.maestros.index') }}">Mis Maestros</a></li>
                <li class="breadcrumb-item active">Monitor: {{ $esclavo->nombre }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm text-center">
                    <div class="card-header bg-white border-0 pt-4">
                        <span class="badge bg-success">Conectado</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fs-4">{{ $esclavo->nombre }}</h5>
                        <p class="text-muted">{{ $esclavo->modelo }} | S/N: {{ $esclavo->numero_serie }}</p>
                        
                       <div class="py-4">
                        <div class="display-1 text-primary fw-bold" id="valor-sensor">
                            {{ $ultimaLectura->valor ?? '--.-' }}
                        </div>
                        <span class="fs-4 text-secondary">
                            {{ $ultimaLectura->unidad ?? 'Sin unidad definida' }}
                        </span>
                    </div>

                        <hr>
                        <p class="small text-muted">Tópico de escucha: <br> <code>{{ $esclavo->topico_maestro }}/{{ $esclavo->numero_serie }}</code></p>
                    </div>
                    <div class="card-footer bg-light border-0 py-3">
                        <a href="{{ route('user.maestros.administrar', $esclavo->maestro_id) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver al Maestro
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection