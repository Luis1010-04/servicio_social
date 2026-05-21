@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Configurar Componentes: {{ $maestro->nombre }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('maestros_catalogo.index') }}">Catálogo</a></li>
                <li class="breadcrumb-item active">Administrar Esclavos</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Esclavos vinculados al modelo {{ $maestro->modelo }}</h5>
                            <a href="{{ route('maestros.esclavos.crear', $maestro->id) }}" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-plus"></i> Vincular Nuevo Esclavo
                            </a>
                        </div>

                        <table class="table table-hover mt-3">
                            <thead>
                                <tr>
                                    <th>Hardware (Modelo)</th>
                                    <th>Alias en este Maestro</th>
                                    <th>Ubicación Sugerida</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($esclavos as $esclavo)
                                <tr>
                                    <td>{{ $esclavo->modelo }}</td>
                                    <td><strong>{{ $esclavo->alias_en_maestro }}</strong></td>
                                    <td><span class="badge bg-info text-dark">{{ $esclavo->nombre_ubicacion }}</span></td>
                                    <td>
                                        @if($esclavo->activo)
                                            <span class="text-success"><i class="fa-solid fa-check"></i> Disponible</span>
                                        @else
                                            <span class="text-danger"><i class="fa-solid fa-xmark"></i> Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                       <form action="{{ route('maestros.esclavos.destroy', $esclavo->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Desvincular?')">
                                                <i class="fa-solid fa-link-slash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Este modelo de maestro no tiene esclavos asignados todavía.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        <div class="text-end mt-3">
                            <a href="{{ route('maestros_catalogo.index') }}" class="btn btn-secondary">Volver al Catálogo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection