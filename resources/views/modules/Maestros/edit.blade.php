@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Editar Maestro: {{ $maestro->nombre }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('maestros_catalogo.index') }}">Catálogo Maestros</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div><section class="section">
        <div class="row">
            <div class="col-lg-8"> {{-- Centramos un poco el formulario --}}

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Información del Modelo de Hardware</h5>

                        <form action="{{ route('maestros.catalogo.update', $maestro->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <label for="nombre" class="col-sm-3 col-form-label">Nombre asignado</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nombre" id="nombre" value="{{ $maestro->nombre }}" required>
                                    <small class="text-muted">Ej: Gateway Industrial Pro</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="modelo" class="col-sm-3 col-form-label">Modelo / SKU</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="modelo" id="modelo" value="{{ $maestro->modelo }}" required>
                                    <small class="text-muted">Ej: ESP32-WROOM-32D</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="descripcion" class="col-sm-3 col-form-label">Descripción</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="descripcion" id="descripcion" rows="3" required>{{ $maestro->descripcion }}</textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="activo" class="col-sm-3 col-form-label">Estado</label>
                                <div class="col-sm-9">
                                    <select name="activo" id="activo" class="form-select">
                                        <option value="1" {{ $maestro->activo == 1 ? 'selected' : '' }}>🟢 Activo (Visible para usuarios)</option>
                                        <option value="0" {{ $maestro->activo == 0 ? 'selected' : '' }}>🔴 Inactivo (Oculto)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-regular fa-floppy-disk"></i> Guardar Cambios
                                </button>
                                <a href="{{ route('maestros_catalogo.index') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-ban"></i> Cancelar
                                </a>
                            </div>
                        </form>

                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ayuda técnica</h5>
                        <p class="small text-muted">
                            Recuerda que si marcas este equipo como <strong>Inactivo</strong>, los nuevos usuarios no podrán seleccionarlo en su panel, pero los que ya lo tengan asignado podrán seguir viéndolo.
                        </p>
                        <hr>
                        <p class="small"><strong>ID del registro:</strong> {{ $maestro->id }}</p>
                        <p class="small"><strong>Última actualización:</strong> {{ $maestro->updated_at }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection