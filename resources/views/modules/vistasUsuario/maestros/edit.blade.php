@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Editar Equipo Maestro</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.maestros.index') }}">Maestros</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-top border-secondary border-3">
                    <div class="card-body">
                        <h5 class="card-title">Modificar Configuración de {{ $maestro->nombre }}</h5>

                        <form action="{{ route('user.maestros.update', $maestro->id) }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')

                            <div class="col-md-6">
                                <label class="form-label text-muted">Número de Serie (S/N)</label>
                                <input type="text" class="form-control bg-light" value="{{ $maestro->numero_serie }}" readonly>
                                <small class="text-info">El S/N no se puede modificar por seguridad.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Nombre del Equipo</label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre', $maestro->nombre) }}" required>
                                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label font-weight-bold">Nueva Ubicación</label>
                                <select name="ubicacion_id" class="form-select @error('ubicacion_id') is-invalid @enderror" required>
                                    @foreach($ubicaciones as $ubi)
                                        <option value="{{ $ubi->id }}" {{ $maestro->ubicacion_id == $ubi->id ? 'selected' : '' }}>
                                            {{ $ubi->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ubicacion_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 mt-4">
                                <h6 class="text-primary"><i class="bi bi-gear"></i> Configuración Avanzada</h6>
                                <hr>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tópico MQTT</label>
                                <input type="text" name="topico" class="form-control" value="{{ $maestro->topico }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Broker</label>
                                <input type="text" name="Broker" class="form-control" value="{{ $maestro->Broker }}">
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success px-4">Guardar Cambios</button>
                                <a href="{{ route('user.maestros.index') }}" class="btn btn-secondary px-4">Regresar</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection