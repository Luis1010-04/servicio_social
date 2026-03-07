@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1>{{ $titulo }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Esclavos</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Todos mis Sensores y Actuadores</h5>
                        <p class="small text-muted">Aquí puedes ver el estado general de tus dispositivos secundarios.</p>

                        <div class="table-responsive">
                            <table class="table table-hover datatable align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Dispositivo</th>
                                        <th>S/N</th>
                                        <th>Conectado a (Maestro)</th>
                                        <th>Ubicación</th>
                                        <th>Fecha Registro</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($esclavos as $esc)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-cpu-fill me-2 text-info"></i>
                                                    <div>
                                                        <span class="fw-bold d-block">{{ $esc->nombre }}</span>
                                                        <small class="badge bg-light text-dark border">{{ $esc->tipo_dispositivo }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><code>{{ $esc->numero_serie }}</code></td>
                                            <td>
                                                <span class="text-secondary"><i class="bi bi-hdd-network"></i> {{ $esc->nombre_maestro }}</span>
                                            </td>
                                            <td>
                                                <i class="bi bi-geo-alt text-danger small"></i> {{ $esc->nombre_ubicacion ?? 'Sin asignar' }}
                                            </td>
                                            <td class="small">{{ \Carbon\Carbon::parse($esc->created_at)->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                <div class="btn-group shadow-sm" role="group">
                                                    <a href="{{ route('user.esclavos.monitor', $esc->id) }}" class="btn btn-sm btn-outline-primary" title="Ver Monitoreo">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('user.esclavos.edit', $esc->id) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('user.esclavos.destroy', $esc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este dispositivo?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
                                                    <p class="mt-2">No tienes esclavos registrados aún.</p>
                                                    <a href="{{ route('user.maestros.index') }}" class="btn btn-sm btn-primary">Ir a mis Maestros para agregar uno</a>
                                                </div>
                                            </td>
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