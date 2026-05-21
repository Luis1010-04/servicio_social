@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1>{{ $titulo }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Componentes</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('componentes.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nuevo Componente
        </a>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Listado de Sensores y Actuadores</h5>
                
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Icono</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Unidad</th>
                                <th class="text-center">Uso en Esclavos</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datos as $comp)
                            <tr>
                                <td class="text-center">
                                    <i class="{{ $comp->ruta_incono ?? 'bi bi-cpu' }} fs-4 text-primary"></i>
                                </td>
                                <td>
                                    <strong>{{ $comp->nombre }}</strong>
                                    @if($comp->descripcion)
                                        <br><small class="text-muted">{{ Str::limit($comp->descripcion, 40) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $comp->tipo == 'Sensor' ? 'bg-info' : 'bg-warning' }}">
                                        {{ $comp->tipo }}
                                    </span>
                                </td>
                                <td>
                                    @if($comp->tipo == 'Sensor')
                                        <code class="fw-bold">{{ $comp->unidad_medida }}</code>
                                    @else
                                        <span class="text-muted small">N/A (Actuador)</span>
                                    @endif
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $comp->total_esclavos > 0 ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $comp->total_esclavos }} asignados
                                    </span>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('componentes.edit', $comp->id) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Editar componente">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        
                                        <form action="{{ route('componentes.destroy', $comp->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Eliminar componente"
                                                    onclick="return confirm('¿Estás seguro de eliminar este componente? Se borrarán sus vinculaciones.')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection