@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1>Ficha Técnica: {{ $esclavo->nombre }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('esclavos.catalogo.index') }}">Catálogo</a></li>
                    <li class="breadcrumb-item active">Monitor de Hardware</li>
                </ol>
            </nav>
        </div>
        <span class="badge {{ $esclavo->activo ? 'bg-success' : 'bg-secondary' }} p-2 shadow-sm">
            <i class="fa-solid fa-microchip"></i> {{ $esclavo->activo ? 'MODELO DISPONIBLE' : 'MODELO DESCONTINUADO' }}
        </span>
    </div>

    <section class="section">
        <div class="row">
            {{-- Columna lateral: Detalles del Modelo --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center text-center">
                        <div class="bg-light p-3 rounded-circle mb-3">
                            <i class="fa-solid fa-memory fa-3x text-primary"></i>
                        </div>
                        <h2 class="h4 fw-bold">{{ $esclavo->modelo }}</h2>
                        <p class="text-muted small">Referencia de Catálogo</p>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-body pt-3">
                        <h5 class="card-title text-dark">Información de Registro</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">ID Sistema:</span>
                            <span class="fw-bold">#{{ $esclavo->id }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Fecha Alta:</span>
                            <span class="small">{{ date('d/m/Y H:i', strtotime($esclavo->created_at)) }}</span>
                        </div>
                        <hr>
                        <a href="{{ route('esclavos.catalogo.edit', $esclavo->id) }}" class="btn btn-warning btn-sm w-100">
                            <i class="fa-solid fa-pen-to-square"></i> Editar Especificaciones
                        </a>
                    </div>
                </div>
            </div>

            {{-- Columna principal: Componentes Integrados --}}
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body pt-3">
                        <h5 class="card-title text-primary">Arquitectura de Componentes</h5>
                        <p class="text-muted small">Este modelo cuenta con los siguientes sensores y actuadores pre-configurados:</p>
                        
                        <div class="row">
                            @forelse($componentes as $comp)
                                <div class="col-lg-6 col-md-12 mb-3">
                                    <div class="card border border-light shadow-none bg-light mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <i class="fa-solid fa-gear text-secondary"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0 fw-bold">{{ $comp->nombre }}</h6>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-primary">{{ $comp->tipo }}</small>
                                                        <span class="badge bg-white text-dark border extra-small">
                                                            {{ $comp->nombre_unidad }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <div class="mb-3">
                                        <i class="fa-solid fa-layer-group fa-3x text-light"></i>
                                    </div>
                                    <h6 class="text-muted">Sin componentes definidos</h6>
                                    <p class="small text-muted">Este modelo de hardware está vacío en el catálogo.</p>
                                    <a href="{{ route('esclavos.catalogo.edit', $esclavo->id) }}" class="btn btn-sm btn-outline-primary">
                                        Vincular Componentes Técnicos
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="{{ route('esclavos.catalogo.index') }}" class="btn btn-secondary px-5">Regresar al Listado</a>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection