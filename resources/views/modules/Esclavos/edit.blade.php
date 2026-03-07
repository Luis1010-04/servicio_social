@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>{{ $titulo }}: {{ $esclavo->nombre }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('esclavos.catalogo.index') }}">Catálogo</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Modificar Información Técnica del Modelo</h5>

                        <form action="{{ route('esclavos.catalogo.update', $esclavo->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label fw-bold">Nombre del Equipo</label>
                                <div class="col-sm-9">
                                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $esclavo->nombre) }}" required>
                                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label fw-bold">Modelo / SKU</label>
                                <div class="col-sm-9">
                                    <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror" value="{{ old('modelo', $esclavo->modelo) }}" required>
                                    <small class="text-muted">Identificador único del hardware en el sistema.</small>
                                    @error('modelo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label fw-bold">Estado del Catálogo</label>
                                <div class="col-sm-9">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo" id="activo" {{ $esclavo->activo ? 'checked' : '' }}>
                                        <label class="form-check-label" for="activo">
                                            Permitir que los usuarios vinculen este modelo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="card-title text-primary">Configuración de Componentes (Sensores/Actuadores)</h5>
                            <p class="small text-muted mb-3">Selecciona los componentes técnicos que integran este modelo de esclavo.</p>

                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="list-group shadow-sm">
                                        @foreach($componentes_disponibles as $comp)
                                            <label class="list-group-item d-flex justify-content-between align-items-center list-group-item-action">
                                                <div>
                                                    <input class="form-check-input me-3" type="checkbox" name="componentes[]" value="{{ $comp->id }}" 
                                                        {{ in_array($comp->id, $componentes_actuales) ? 'checked' : '' }}>
                                                    <span class="fw-bold">{{ $comp->nombre }}</span>
                                                </div>
                                                <span class="badge bg-secondary-light text-secondary border">{{ $comp->tipo }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="text-end border-top pt-3">
                                <a href="{{ route('esclavos.catalogo.index') }}" class="btn btn-secondary px-4 me-2">Cancelar</a>
                                <button type="submit" class="btn btn-warning px-4">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="card bg-light shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title text-dark"><i class="fa-solid fa-circle-info me-2"></i>Resumen</h5>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2"><strong>ID Sistema:</strong> <span class="badge bg-white text-dark border">#{{ $esclavo->id }}</span></li>
                            <li class="mb-2"><strong>Registro:</strong> {{ date('d/m/Y', strtotime($esclavo->created_at)) }}</li>
                            <li><strong>Última mod:</strong> {{ date('d/m/Y H:i', strtotime($esclavo->updated_at)) }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection