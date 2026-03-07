@extends('layouts.main')

@section('titulo', 'Nuevo Modelo de Catálogo')

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Registrar Nuevo Modelo de Esclavo</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('esclavos.catalogo.index') }}">Catálogo</a></li>
                <li class="breadcrumb-item active">Nuevo Modelo</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-top border-primary border-3">
                    <div class="card-body">
                        <h5 class="card-title text-center">Información Técnica del Equipo</h5>
                        
                        <form action="{{ route('esclavos.catalogo.store') }}" method="POST" class="row g-3">
                            @csrf

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nombre del Equipo</label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                       placeholder="Ej: Nodo Sensor de Temperatura" value="{{ old('nombre') }}" required>
                                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Modelo (Único)</label>
                                <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror" 
                                       placeholder="Ej: TH-NODE-V1" value="{{ old('modelo') }}" required>
                                @error('modelo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="activo" id="activo" checked>
                                    <label class="form-check-label" for="activo">Modelo Activo para Usuarios</label>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg px-5">Guardar en Catálogo</button>
                                <a href="{{ route('esclavos.catalogo.index') }}" class="btn btn-link text-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection