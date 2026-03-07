@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Crear un nuevo maestro</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('maestros_catalogo.index') }}">Maestros</a></li>
                <li class="breadcrumb-item active">Crear maestro</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Datos de Identificación del Hardware</h5>

                        <form action="{{ route('maestros.catalogo.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="nombre" class="form-label font-weight-bold">Nombre del maestro</label>
                                <input type="text" class="form-control" name="nombre" id="nombre" 
                                       placeholder="Ej: Gateway Central Planta 1" required>
                            </div>

                            <div class="mb-3">
                                <label for="modelo" class="form-label font-weight-bold">Modelo / SKU</label>
                                <input type="text" class="form-control" name="modelo" id="modelo" 
                                       placeholder="Ej: ESP32-WROOM-32U" required>
                                <small class="text-muted">Este código debe ser único para cada maestro.</small>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label font-weight-bold">Descripción</label>
                                <textarea class="form-control" name="descripcion" id="descripcion" 
                                          rows="3" placeholder="Breve detalle del propósito de este equipo..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="activo" id="activo" checked>
                                    <label class="form-check-label" for="activo">
                                        <strong>Estado:</strong> El equipo iniciará como <span class="text-success">Activo</span>
                                    </label>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('maestros_catalogo.index') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-ban"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-regular fa-floppy-disk"></i> Guardar Maestro
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