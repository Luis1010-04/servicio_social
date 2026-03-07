@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>{{ $titulo }}</h1>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-8"> <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Información del Hardware</h5>

                        <form action="{{ route('esclavos.catalogo.store') }}" method="POST">
                            @csrf
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Nombre del Equipo</label>
                                <div class="col-sm-9">
                                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Estación Meteorológica Pro" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Modelo / SKU</label>
                                <div class="col-sm-9">
                                    <input type="text" name="modelo" class="form-control" placeholder="Ej: ESP32-WROOM-32" required>
                                    <small class="text-muted">Debe ser único en el sistema.</small>
                                </div>
                            </div>


                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Estado</label>
                                <div class="col-sm-9">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo" id="activo" checked>
                                        <label class="form-check-label" for="activo">Equipo Activo</label>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h5 class="card-title">Vincular Componentes Internos</h5>
                            <p class="small text-muted">Seleccione los sensores o actuadores que este equipo tiene conectados físicamente.</p>

                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="list-group">
                                        @forelse($componentes_disponibles as $comp)
                                            <label class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <input class="form-check-input me-3" type="checkbox" name="componentes[]" value="{{ $comp->id }}">
                                                    <strong>{{ $comp->nombre }}</strong>
                                                    <span class="ms-2 text-muted small">({{ $comp->tipo }})</span>
                                                </div>
                                                <i class="fa-solid fa-microchip text-secondary"></i>
                                            </label>
                                        @empty
                                            <div class="alert alert-warning">
                                                No hay componentes registrados en el sistema. 
                                                <a href="#">Ir a Catálogo de Componentes</a>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save"></i> Guardar Esclavo
                                </button>
                                <a href="{{ route('esclavos.catalogo.index') }}" class="btn btn-secondary">
                                    Cancelar
                                </a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-info"><i class="fa-solid fa-circle-info"></i> Ayuda</h5>
                        <p class="small text-muted">
                            <strong>¿Qué es un Esclavo?</strong><br>
                            Es el dispositivo físico (como un Arduino o ESP32) que recolecta datos.
                        </p>
                        <p class="small text-muted">
                            <strong>Componentes:</strong><br>
                            Un mismo esclavo puede tener varios componentes. Por ejemplo, un solo dispositivo puede medir humedad y temperatura al mismo tiempo.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection