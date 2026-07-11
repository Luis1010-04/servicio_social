@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Administrar Recursos del Usuario</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
                <li class="breadcrumb-item active">{{ $user->usuario }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <div class="card-body pt-3">
                        <ul class="nav nav-tabs nav-tabs-bordered">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-ubicaciones">
                                    <i class="bi bi-geo-alt"></i> Ubicaciones
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-maestros">
                                    <i class="bi bi-cpu"></i> Maestros
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-esclavos">
                                    <i class="bi bi-diagram-3"></i> Esclavos
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content pt-4">
                            <div class="tab-pane fade show active" id="tab-ubicaciones">
                                <h5 class="card-title">Ubicaciones de {{ $user->name }}</h5>
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Creado el</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($ubicaciones as $u)
                                            <tr>
                                                <td>{{ $u->id }}</td>
                                                <td><span class="badge bg-info text-dark">{{ $u->nombre }}</span></td>
                                                <td>{{ date('d/m/Y', strtotime($u->created_at)) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center text-muted">No hay ubicaciones registradas.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane fade" id="tab-maestros">
                                <h5 class="card-title">Dispositivos Maestros</h5>
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Modelo Catálogo</th>
                                            <th>S/N</th>
                                            <th>Ubicación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($maestros as $m)
                                            <tr>
                                                <td><strong>{{ $m->nombre }}</strong></td>
                                                <td>{{ $m->modelo_base }}</td>
                                                <td><code>{{ $m->numero_serie }}</code></td>
                                                <td><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $m->nombre_ubicacion }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted">No tiene maestros vinculados.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane fade" id="tab-esclavos">
                                <h5 class="card-title">Nodos Esclavos (Sensores/Actuadores)</h5>
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Modelo</th>
                                            <th>Maestro Dueño</th>
                                            <th>Ubicación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($esclavos as $e)
                                            <tr>
                                                <td><strong>{{ $e->nombre }}</strong></td>
                                                <td>{{ $e->modelo_esclavo }}</td>
                                                <td><span class="text-primary">{{ $e->nombre_maestro }}</span></td>
                                                <td><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $e->nombre_ubicacion }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted">No tiene esclavos configurados.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div></div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection