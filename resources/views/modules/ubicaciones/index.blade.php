@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Ubicaciones</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Ubicaciones</li>
            </ol>
        </nav>
    </div><section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
                            <div>
                                <h5 class="card-title mb-0">Administrar Ubicaciones</h5>
                                <p class="text-muted small mb-0">Gestiona las zonas físicas donde se despliegan tus nodos IoT</p>
                            </div>
                            <a href="{{ route('ubicaciones.create') }}" class="btn btn-success px-4">
                                <i class="bi bi-plus-circle me-1"></i> Nueva Ubicación
                            </a>
                        </div>

                        <table class="table table-hover datatable">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">ID</th>
                                    <th width="45%">Nombre de la ubicación</th>
                                    <th width="20%" class="text-center">Monitoreo</th>
                                    <th width="25%" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datos as $dato)
                                <tr>
                                    <td><span class="badge bg-secondary opacity-75">{{ $dato->id }}</span></td>
                                    <td>
                                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                        <span class="fw-bold">{{ $dato->nombre }}</span>
                                    </td>
                                    <td class="text-center"> 
                                        <a href="{{ route('ubicaciones.show', $dato->id) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-display me-1"></i> Ver Dispositivos
                                        </a> 
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm">
                                            <a href="{{ route('ubicaciones.edit', $dato->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('ubicaciones.destroy', $dato->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Seguro que deseas eliminar esta ubicación?')">
                                                    <i class="bi bi-trash"></i>
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
        </div>
    </section>
</main>
@endsection