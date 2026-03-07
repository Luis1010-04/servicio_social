@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Editar Dispositivo Esclavo</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.maestros.index') }}">Maestros</a></li>
                <li class="breadcrumb-item active">Editar Esclavo</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-sm border-top border-info border-3">
                    <div class="card-body">
                        <h5 class="card-title text-info mb-3">Configuración de: {{ $esclavo->nombre }}</h5>

                        <form action="{{ route('user.esclavos.update', $esclavo->id) }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="maestro_id" value="{{ $esclavo->maestro_id }}">

                            <div class="col-md-6">
                                <label class="form-label text-muted">Número de Serie (S/N)</label>
                                <input type="text" class="form-control bg-light" value="{{ $esclavo->numero_serie }}" readonly>
                                <small class="text-secondary">Vínculo físico inalterable.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Nombre del Dispositivo</label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre', $esclavo->nombre) }}" placeholder="Ej: Sensor Balcón" required>
                                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label font-weight-bold">Ubicación</label>
                                <select name="ubicacion_id" class="form-select @error('ubicacion_id') is-invalid @enderror" required>
                                    @foreach($ubicaciones as $ubi)
                                        <option value="{{ $ubi->id }}" {{ $esclavo->ubicacion_id == $ubi->id ? 'selected' : '' }}>
                                            {{ $ubi->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ubicacion_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-info text-white px-4">Actualizar Dispositivo</button>
                                <a href="{{ route('user.maestros.administrar', $esclavo->maestro_id) }}" class="btn btn-secondary px-4">Cancelar</a>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="alert alert-light border-0 shadow-sm mt-3 small text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Recuerda que cambiar el nombre o ubicación aquí solo afecta a la organización en el panel, no altera el funcionamiento del hardware físico.
                </div>
            </div>
        </div>
    </section>
</main>
@endsection