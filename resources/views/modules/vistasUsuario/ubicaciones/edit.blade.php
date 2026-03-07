@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>{{ $titulo }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.ubicaciones.index') }}">Ubicaciones</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-top border-primary border-3">
                    <div class="card-body">
                        <h5 class="card-title">Actualizar nombre del área</h5>

                        <form action="{{ route('user.ubicaciones.update', $ubicacion->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="nombre" class="form-label fw-bold">Nombre de la Ubicación</label>
                                <input type="text" 
                                       name="nombre" 
                                       id="nombre" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre', $ubicacion->nombre) }}" 
                                       placeholder="Ej: Almacén Norte" 
                                       required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save"></i> Guardar Cambios
                                </button>
                                <a href="{{ route('user.ubicaciones.index') }}" class="btn btn-secondary px-4">
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="alert alert-warning mt-3 small" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <strong>Nota:</strong> Cambiar el nombre de la ubicación se reflejará automáticamente en todos los equipos (Maestros y Esclavos) que ya estén asignados a esta área.
                </div>
            </div>
        </div>
    </section>
</main>
@endsection