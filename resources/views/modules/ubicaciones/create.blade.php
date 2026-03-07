@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>{{ $titulo }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ubicaciones.index') }}">Ubicaciones</a></li>
                <li class="breadcrumb-item active">Nueva</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="card-title mb-0 text-white">Registrar Nueva Ubicación Física</h5>
                    </div>
                    <div class="card-body pt-4">
                        <form action="{{ route('ubicaciones.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="nombre" class="form-label fw-bold">Nombre del Área / Zona</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control" name="nombre" id="nombre" 
                                           placeholder="Ej: Planta Alta - Sector A" value="{{ old('nombre') }}" required>
                                </div>
                                <div class="form-text mt-2">
                                    Identifique claramente el lugar donde se instalarán los dispositivos.
                                </div>
                            </div>

                            <div class="col-12 border-top pt-3 mt-4 d-flex justify-content-end gap-2">
                                <a href="{{ route('ubicaciones.index') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fa-solid fa-ban me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">
                                    <i class="fa-regular fa-floppy-disk me-1"></i> Guardar Ubicación
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection