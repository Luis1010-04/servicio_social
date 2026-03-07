@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1>Mis Equipos Maestros</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Maestros</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('user.maestros.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Vincular Nuevo Equipo
        </a>
    </div>

    <section class="section">
        <div class="row">
            @forelse($maestros as $maestro)
                <div class="col-lg-4 col-md-6">
                    <div class="card info-card sales-card border-top border-primary border-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mt-3">
                                <h5 class="card-title text-primary p-0 m-0">{{ $maestro->nombre }}</h5>
                                <span class="badge bg-info text-dark">{{ $maestro->modelo }}</span>
                            </div>

                            <div class="d-flex align-items-center mt-3">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-light">
                                    <i class="bi bi-cpu text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="mb-0" style="font-size: 0.9rem;">S/N: {{ $maestro->numero_serie }}</h6>
                                    <span class="text-muted small pt-2 ps-1">
                                        <i class="bi bi-geo-alt"></i> {{ $maestro->nombre_ubicacion ?? 'Sin ubicación' }}
                                    </span>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('user.maestros.administrar', $maestro->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-node-plus"></i> Sensores
                                    </a>
                                    <a href="{{ route('user.maestros.edit', $maestro->id) }}" class="btn btn-outline-secondary btn-sm" title="Editar Equipo">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>

                                <form action="{{ route('user.maestros.destroy', $maestro->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de desvincular este equipo? Se eliminarán también sus esclavos asociados.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center pb-3">
                            <small class="text-muted">Tópico: <code>{{ $maestro->topico }}</code></small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info border-light shadow-sm text-center" role="alert">
                        <i class="bi bi-info-circle me-1"></i>
                        Aún no tienes equipos vinculados. <a href="{{ route('user.maestros.create') }}" class="alert-link">¡Haz clic aquí para vincular tu primer Maestro!</a>
                    </div>
                </div>
            @endforelse
        </div>
    </section>
</main>
@endsection