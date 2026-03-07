@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Catálogo de Equipos Esclavos</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Catálogo de Esclavos</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">Modelos en Inventario Técnico</h5>
                            <a href="{{ route('esclavos.catalogo.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-plus"></i> Registrar Nuevo Modelo
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Equipo (Nombre / Modelo)</th>
                                        <th class="text-center">Capacidad</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($datos as $esclavo)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-primary">{{ $esclavo->nombre }}</span>
                                                <small class="text-muted"><i class="fa-solid fa-microchip me-1"></i> {{ $esclavo->modelo }}</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-info text-dark">
                                                {{ $esclavo->total_componentes }} Sensores/Actuadores
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($esclavo->activo)
                                                <span class="badge bg-success-light text-success border border-success">Activo</span>
                                            @else
                                                <span class="badge bg-danger-light text-danger border border-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group shadow-sm" role="group">
                                                <a href="{{ route('esclavos.catalogo.administrar', $esclavo->id) }}" class="btn btn-outline-primary btn-sm" title="Detalle Técnico">
                                                    <i class="fa-solid fa-gears"></i>
                                                </a>
                                                <a href="{{ route('esclavos.catalogo.edit', $esclavo->id) }}" class="btn btn-outline-warning btn-sm" title="Editar Modelo">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <form action="{{ route('esclavos.catalogo.destroy', $esclavo->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este modelo? Se borrarán las vinculaciones de los usuarios.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No hay modelos registrados en el catálogo.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection