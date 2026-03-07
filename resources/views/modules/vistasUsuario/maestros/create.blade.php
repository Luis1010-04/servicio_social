@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Vincular Nuevo Equipo Maestro</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.maestros.index') }}">Maestros</a></li>
                <li class="breadcrumb-item active">Nuevoooo</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-center">Formulario de Registro de Hardware</h5>
                        <p class="text-muted text-center small">Introduce los datos que aparecen en la etiqueta de tu dispositivo.</p>
                        
                        <form action="{{ route('user.maestros.store') }}" method="POST" class="row g-3 needs-validation">
                            @csrf

                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Modelo de Hardware</label>
                                <select name="maestro_id" class="form-select @error('maestro_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Selecciona el modelo...</option>
                                    @foreach($modelosDisponibles as $modelo)
                                        <option value="{{ $modelo->id }}" {{ old('maestro_id') == $modelo->id ? 'selected' : '' }}>
                                            {{ $modelo->nombre }} ({{ $modelo->modelo }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('maestro_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Número de Serie (S/N)</label>
                                <input type="text" name="numero_serie" class="form-control @error('numero_serie') is-invalid @enderror" 
                                       placeholder="Ej: ESP32-GATE-XXXX" value="{{ old('numero_serie') }}" required>
                                <div class="form-text">Este número identifica únicamente a tu equipo físico.</div>
                                @error('numero_serie') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Nombre del Equipo</label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                       placeholder="Ej: Maestro de la Sala" value="{{ old('nombre') }}" required>
                                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">¿Dónde estará ubicado?</label>
                                <div class="input-group">
                                    <select name="ubicacion_id" class="form-select @error('ubicacion_id') is-invalid @enderror" required>
                                        <option value="" disabled selected>Selecciona ubicación...</option>
                                        @foreach($ubicaciones as $ubi)
                                            <option value="{{ $ubi->id }}" {{ old('ubicacion_id') == $ubi->id ? 'selected' : '' }}>
                                                {{ $ubi->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('user.ubicaciones.index') }}" class="btn btn-outline-secondary" title="Agregar nueva ubicación">
                                        <i class="bi bi-plus"></i>
                                    </a>
                                </div>
                                @error('ubicacion_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <hr class="my-4">
                            <h6 class="text-primary mb-3">Configuración de Comunicación (MQTT)</h6>

                            <div class="col-md-6">
                                <label class="form-label">Tópico Base</label>
                                <input type="text" name="topico" class="form-control @error('topico') is-invalid @enderror" 
                                       placeholder="casa/planta-baja/sala" value="{{ old('topico') }}" required>
                                @error('topico') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Dirección del Broker (IP/Host)</label>
                                <input type="text" name="Broker" 
                                    class="form-control @error('Broker') is-invalid @enderror" {{-- Agregamos la clase de error --}}
                                    placeholder="192.168.1.10" 
                                    value="{{ old('Broker', '192.168.1.10') }}">
                                
                                {{-- Agregamos el mensaje de error --}}
                                @error('Broker')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="text-center mt-4 pt-3">
                                <button type="submit" class="btn btn-primary btn-lg px-5">Vincular Ahora</button>
                                <a href="{{ route('user.maestros.index') }}" class="btn btn-link text-secondary">Cancelar</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection