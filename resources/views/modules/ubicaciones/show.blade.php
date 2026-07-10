@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Explorador de Ubicación</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ubicaciones.index') }}">Ubicaciones</a></li>
                <li class="breadcrumb-item active">{{ $ubicacion->nombre }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            {{-- Tarjeta lateral de Info --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body pt-4">
                        <div class="text-center mb-3">
                            <div class="display-4 text-danger"><i class="bi bi-geo-alt-fill"></i></div>
                            <h4 class="fw-bold mt-2">{{ $ubicacion->nombre }}</h4>
                            <p class="text-muted small">Zona de despliegue IoT</p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between px-3">
                            <span class="text-muted">Nodos instalados:</span>
                            <span class="badge bg-primary fs-6">{{ $esclavos->count() }}</span>
                        </div>
                        <div class="mt-4 d-grid">
                            <a href="{{ route('ubicaciones.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> Volver al listado
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Listado de Esclavos en esta ubicación --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 p-0 fs-6">Dispositivos en esta Área</h5>
                    </div>
                    <div class="card-body pt-3">
                        @if($esclavos->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-cpu text-light display-1"></i>
                                <p class="text-muted mt-3">No hay equipos registrados en esta ubicación todavía.</p>
                                <a href="{{ route('esclavos.catalogo.create') }}" class="btn btn-primary btn-sm">Registrar un Esclavo aquí</a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Dispositivo</th>
                                            <th>Modelo</th>
                                            <th class="text-center">Componentes</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($esclavos as $esc)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-primary">{{ $esc->nombre }}</div>
                                            </td>
                                            <td><small class="text-muted">{{ $esc->modelo }}</small></td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill bg-info text-dark">
                                                    {{ $esc->total_comp }} items
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <i class="bi bi-circle-fill {{ $esc->activo ? 'text-success' : 'text-danger' }}" style="font-size: 0.7rem;"></i>
                                                <small>{{ $esc->activo ? 'Online' : 'Offline' }}</small>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('esclavos.catalogo.administrar', $esc->id) }}" class="btn btn-sm btn-outline-primary" title="Ver Monitor">
                                                    <i class="bi bi-speedometer2"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection