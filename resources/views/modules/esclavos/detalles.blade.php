@extends('layouts.main')

@section('titulo', "Monitor - " . $esclavo->nombre)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Monitor de Nodo: {{ $esclavo->nombre }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('esclavos.catalogo.index') }}">Esclavos</a></li>
                <li class="breadcrumb-item active">Monitor Tiempo Real</li>
            </ol>
        </nav>
    </div>

    <section class="section profile">
        <div class="row">
            {{-- Columna de Información del Hardware --}}
            <div class="col-xl-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                        <div class="icon-circle bg-primary text-white mb-3" style="font-size: 2.5rem; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="bi bi-cpu"></i>
                        </div>
                        <h4 class="fw-bold">{{ $esclavo->nombre }}</h4>
                        <p class="text-muted mb-0 small">{{ $esclavo->modelo }}</p>
                        <span class="badge {{ $esclavo->activo ? 'bg-success' : 'bg-danger' }} mt-2 px-3">
                            {{ $esclavo->activo ? 'SISTEMA ONLINE' : 'SISTEMA OFFLINE' }}
                        </span>
                    </div>
                    <div class="card-body border-top mt-3">
                        <h6 class="card-title p-0 small text-uppercase fw-bold">Detalles de Instalación</h6>
                        <div class="row mb-2">
                            <div class="col-4 text-muted small">Ubicación:</div>
                            <div class="col-8 small fw-bold">{{ $esclavo->nombre_ubicacion }}</div>
                        </div>
                        <div class="row">
                            <div class="col-4 text-muted small">Descripción:</div>
                            <div class="col-8 small text-muted">{{ $esclavo->descripcion }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna de Componentes (Sensores y Actuadores) --}}
            <div class="col-xl-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 p-0 fs-6"><i class="bi bi-broadcast text-primary me-2"></i>Lecturas de Dispositivos Conectados</h5>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-3">
                            @forelse($componentes as $comp)
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100 mb-0 shadow-none border-start border-primary border-4">
                                        <div class="card-body py-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $comp->nombre }}</h6>
                                                    <span class="badge {{ $comp->tipo == 'Sensor' ? 'bg-info text-dark' : 'bg-secondary' }}" style="font-size: 0.6rem;">
                                                        {{ strtoupper($comp->tipo) }}
                                                    </span>
                                                </div>
                                                <div class="fs-4 text-primary">
                                                    @php
                                                        $iconos = [
                                                            'Temperatura' => 'bi-thermometer-half',
                                                            'Humedad' => 'bi-droplet-half',
                                                            'Relay' => 'bi-toggle-on',
                                                            'Luz' => 'bi-brightness-high'
                                                        ];
                                                        $icono = $iconos[$comp->nombre] ?? 'bi-gear';
                                                    @endphp
                                                    <i class="bi {{ $icono }}"></i>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-3 text-center">
                                                <h2 class="mb-0 fw-bold display-6">
                                                    {{ $comp->ultimo_valor ?? '--' }}
                                                    <small class="text-muted fs-5">{{ $comp->unidad }}</small>
                                                </h2>
                                                <p class="small text-muted mb-0 mt-1" style="font-size: 0.75rem;">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $comp->fecha_lectura ? date('H:i:s - d/m/y', strtotime($comp->fecha_lectura)) : 'Sin registros' }}
                                                </p>
                                            </div>
                                            
                                            @if($comp->tipo == 'Actuador')
                                                <div class="mt-3 d-grid">
                                                    <form action="{{ route('esclavos.comando') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="componente_id" value="{{ $comp->id }}">
                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-power me-1"></i> Alternar Estado
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <i class="bi bi-exclamation-triangle text-warning display-4"></i>
                                    <p class="text-muted mt-2">No hay componentes vinculados a este esclavo.</p>
                                    <a href="{{ route('esclavos.catalogo.edit', $esclavo->id) }}" class="btn btn-sm btn-outline-primary">Ir a configuración</a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

{{-- Script de Auto-Refresco --}}
<script>
    // Refresca la vista cada 30 segundos para actualizar lecturas sin F5
    setTimeout(() => { location.reload(); }, 30000);
</script>
@endsection