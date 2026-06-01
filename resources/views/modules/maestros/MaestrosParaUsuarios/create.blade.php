@extends('layouts.main')

@section('titulo', 'Asignar Nuevo Maestro')

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Asignar Nuevo Equipo</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.maestros.index') }}">Mis Maestros</a></li>
                <li class="breadcrumb-item active">Asignar</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Configuración del Dispositivo</h5>

                        {{-- Alerta general de errores (Opcional si usas validación por campo) --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Revisa los campos marcados en rojo.</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('user.maestros.store') }}" method="POST">
                            @csrf

                            {{-- 1. Selector de Catálogo --}}
                            <div class="row mb-3">
                                <label for="maestro_id" class="col-sm-3 col-form-label">Modelo Base</label>
                                <div class="col-sm-9">
                                    <select name="maestro_id" id="maestro_id" class="form-select @error('maestro_id') is-invalid @enderror" required>
                                        <option value="" selected disabled>Selecciona un hardware...</option>
                                      @foreach($modelosDisponibles as $item) {{-- Cambiado de $catalogo a $modelosDisponibles --}}
                                            <option value="{{ $item->id }}" {{ old('maestro_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->nombre }} - (Modelo: {{ $item->modelo }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('maestro_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- 2. Nombre Personalizado --}}
                            <div class="row mb-3">
                                <label for="nombre" class="col-sm-3 col-form-label">Nombre del Equipo</label>
                                <div class="col-sm-9">
                                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" placeholder="Ej: Maestro Sala 1" required>
                                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- 3. Número de Serie --}}
                            <div class="row mb-3">
                                <label for="numero_serie" class="col-sm-3 col-form-label">Número de Serie</label>
                                <div class="col-sm-9">
                                    <input type="text" name="numero_serie" value="{{ old('numero_serie') }}" class="form-control @error('numero_serie') is-invalid @enderror" placeholder="S/N Único del fabricante" required>
                                    @error('numero_serie') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- 4. Ubicación (Selector de tus ubicaciones creadas) --}}
                            <div class="row mb-3">
                                <label for="ubicacion_id" class="col-sm-3 col-form-label">Ubicación</label>
                                <div class="col-sm-9">
                                    <select name="ubicacion_id" id="ubicacion_id" class="form-select @error('ubicacion_id') is-invalid @enderror" required>
                                        <option value="" selected disabled>Selecciona una ubicación...</option>
                                        @foreach($ubicaciones as $ubi)
                                            <option value="{{ $ubi->id }}" {{ old('ubicacion_id') == $ubi->id ? 'selected' : '' }}>
                                                {{ $ubi->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ubicacion_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- 5. Configuración de Red (Broker e IP) --}}
                            <div class="row mb-3">
                                <label for="Broker" class="col-sm-3 col-form-label">Broker MQTT (IP)</label>
                                <div class="col-sm-9">
                                    <input type="text" name="Broker" value="{{ old('Broker', '192.168.1.10') }}" class="form-control @error('Broker') is-invalid @enderror" required>
                                    @error('Broker') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="topico" class="col-sm-3 col-form-label">Tópico Raíz</label>
                                <div class="col-sm-9">
                                    <input type="text" name="topico" value="{{ old('topico') }}" class="form-control @error('topico') is-invalid @enderror" placeholder="Ej: casa/sala/maestro1" required>
                                    @error('topico') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save"></i> Guardar Asignación
                                </button>
                                <a href="{{ route('user.maestros.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Ayuda Lateral --}}
            <div class="col-lg-4">
                <div class="card bg-light">
                    <div class="card-body text-center pt-4">
                        <i class="bi bi-info-circle text-primary" style="font-size: 2rem;"></i>
                        <h5 class="card-title">Ayuda con la Red</h5>
                        <p class="small text-muted">Asegúrate de que el <strong>Broker</strong> y el <strong>Tópico</strong> coincidan exactamente con la programación física de tu dispositivo para poder recibir datos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection