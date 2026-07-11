@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between">
        <h1>{{ $titulo }}</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddUbicacion">
            <i class="bi bi-plus-circle"></i> Nueva Ubicación
        </button>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-body pt-3">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre de la Ubicación / Área</th>
                                    <th>Fecha de Creación</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ubicaciones as $ubi)
                                    <tr>
                                        <td><strong>{{ $ubi->nombre }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($ubi->created_at)->format('d/m/Y') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('user.ubicaciones.edit', $ubi->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            <form action="{{ route('user.ubicaciones.destroy', $ubi->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar ubicación?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="modalEditUbicacion{{ $ubi->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="{{ route('user.ubicaciones.update', $ubi->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header"><h5>Editar Ubicación</h5></div>
                                                    <div class="modal-body">
                                                        <input type="text" name="nombre" class="form-control" value="{{ $ubi->nombre }}" required>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No has creado ubicaciones aún.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<div class="modal fade" id="modalAddUbicacion" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('user.ubicaciones.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white"><h5>Nueva Ubicación</h5></div>
                <div class="modal-body">
                    <label class="form-label">Nombre (Ej: Planta Baja, Almacén, Oficina)</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre de la ubicación" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Crear Ubicación</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection