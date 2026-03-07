@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Administrar Dispositivos</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('user.maestros.index') }}">Mis Maestros</a></li>
                <li class="breadcrumb-item active">{{ $maestro->nombre }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-left border-primary">
                    <div class="card-body pt-3">
                        <h5 class="card-title"><i class="bi bi-cpu"></i> Datos del Maestro</h5>
                        <p><strong>Modelo:</strong> {{ $maestro->modelo_nombre }}</p>
                        <p><strong>S/N:</strong> <code>{{ $maestro->numero_serie }}</code></p>
                        <p><strong>Tópico:</strong> <code>{{ $maestro->topico }}</code></p>
                        <hr>
                        <button class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#modalAddEsclavo">
                            <i class="bi bi-plus-circle"></i> Agregar Sensor/Esclavo
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body pt-3">
                        <h5 class="card-title">Esclavos Vinculados</h5>
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>S/N</th>
                                        <th>Ubicación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($esclavos as $esc)
                                        <tr>
                                            <td>
                                                <strong>{{ $esc->nombre }}</strong><br>
                                                <small class="text-muted">{{ $esc->modelo_tecnico }}</small>
                                            </td>
                                            <td><code>{{ $esc->numero_serie }}</code></td>
                                            <td>{{ $esc->nombre_ubicacion }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('user.esclavos.monitor', $esc->id) }}" class="btn btn-sm btn-outline-info" title="Monitorear">
                                                        <i class="bi bi-graph-up"></i>
                                                    </a>
                                                    <a href="{{ route('user.esclavos.edit', $esc->id) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('user.esclavos.destroy', $esc->id) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este esclavo?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No hay esclavos conectados a este maestro.</td>
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

@include('modules.vistasUsuario.maestros.partials.modal_add_esclavo')

@endsection