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
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning py-3">
                        <h5 class="card-title mb-0 text-dark">Modificar Nombre de Ubicación</h5>
                    </div>
                    <div class="card-body pt-4">
                        <form action="{{ route('ubicaciones.update', $item->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <label for="nombre" class="form-label fw-bold">Nombre del Área / Zona</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-dark"><i class="bi bi-pencil-square"></i></span>
                                    <input type="text" class="form-control" name="nombre" id="nombre" 
                                           value="{{ old('nombre', $item->nombre) }}" required>
                                </div>
                            </div>

                            <div class="col-12 border-top pt-3 mt-4 d-flex justify-content-end gap-2">
                                <a href="{{ route('ubicaciones.index') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fa-solid fa-ban me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-warning px-4 fw-bold">
                                    <i class="fa-regular fa-floppy-disk me-1"></i> Actualizar Cambios
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