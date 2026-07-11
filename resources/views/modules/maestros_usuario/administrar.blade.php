@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>{{ $titulo }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item">Usuarios</li>
                <li class="breadcrumb-item active">Asignación de Maestros</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Equipos de: <strong>{{ $usuario->name }} {{ $usuario->apellido }}</strong></h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAsignar">
                                <i class="bi bi-plus-circle me-1"></i> Vincular Nuevo Equipo
                            </button>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="table-responsive mt-3">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Alias / Nombre</th>
                                        <th>Modelo</th>
                                        <th>N° Serie</th>
                                        <th>Ubicación</th>
                                        <th>MQTT (Broker/Tópico)</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($datos as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('assets/img/' . $item->imagen_ruta) }}" alt="" style="width: 40px;" class="me-2 rounded shadow-sm">
                                                <div>
                                                    <strong>{{ $item->nombre }}</strong><br>
                                                    <small class="text-muted">{{ Str::limit($item->descripcion, 30) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-info text-dark">{{ $item->modelo }}</span></td>
                                        <td><code>{{ $item->numero_serie }}</code></td>
                                        <td>{{ $item->nombre_ubicacion }}</td>
                                        <td>
                                            <small><strong>B:</strong> {{ $item->Broker }}</small><br>
                                            <small><strong>T:</strong> {{ $item->topico }}</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil"></i></button>
                                            <form action="{{ route('maestros_usuarios.destroy', $item->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Desvincular este equipo?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">El usuario no tiene equipos vinculados.</td>
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

<div class="modal fade" id="modalAsignar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('maestros_usuarios.store') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $usuario->id }}">
                
                <div class="modal-header">
                    <h5 class="modal-title">Vincular Hardware a Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Seleccionar Modelo</label>
                            <select name="maestro_id" class="form-select" required>
                                <option value="" disabled selected>Elegir del catálogo...</option>
                                @foreach($disponibles as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }} ({{ $cat->modelo }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Número de Serie (ID Físico)</label>
                            <input type="text" name="numero_serie" class="form-control" placeholder="Ej: SN-2024-X100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alias del Equipo</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Maestro de Planta Alta" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ubicación</label>
                            <select name="ubicacion_id" class="form-select" required>
                                <option value="" disabled selected>Elegir ubicación...</option>
                                @foreach($ubicaciones as $ubi)
                                    <option value="{{ $ubi->id }}">{{ $ubi->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Broker MQTT</label>
                            <input type="text" name="Broker" class="form-control" value="broker.hivemq.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tópico de Suscripción</label>
                            <input type="text" name="topico" class="form-control" placeholder="usuario/equipo/dato" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas Adicionales</label>
                            <textarea name="descripcion" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Asignación</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection