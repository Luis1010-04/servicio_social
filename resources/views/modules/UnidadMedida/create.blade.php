@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>{{ $titulo }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('unidades.medida.index') }}">Unidades</a></li>
                <li class="breadcrumb-item active">Nuevo</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-center">Registro de Unidad</h5>
                        <p class="text-muted text-center small">Define las unidades que usarán tus sensores (ej. °C, %, Lux).</p>

                        <form action="{{ route('unidades.medida.store') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="nombre" class="form-label fw-bold">Nombre o Símbolo</label>
                                <input type="text" 
                                       name="nombre" 
                                       id="nombre"
                                       class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                                       placeholder="Ej: Metros" 
                                       value="{{ old('nombre') }}" 
                                       required 
                                       autofocus>
                                
                                @error('nombre')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">Asegúrate de que el nombre sea único para evitar duplicados.</div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('unidades.medida.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Regresar
                                </a>
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="bi bi-check-circle"></i> Guardar Unidad
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="alert alert-info border-0 shadow-sm small">
                    <i class="bi bi-lightbulb me-2 text-primary"></i>
                    <strong>Sugerencia:</strong> Puedes registrar tanto el nombre completo (ej. <em>Celsius</em>) como el símbolo (ej. <em>°C</em>). Elige el que prefieras mostrar en los reportes.
                </div>
            </div>
        </div>
    </section>
</main>
@endsection