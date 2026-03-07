@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Vincular Esclavo a: {{ $maestro->nombre }}</h1>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Configuración del Vínculo</h5>
                @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif
                <form action="{{ route('maestros.esclavos.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="maestro_id" value="{{ $maestro->id }}">

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Seleccionar Hardware</label>
                        <div class="col-sm-9">
                            <select name="esclavo_id" class="form-select" required>
                                <option value="">-- Seleccione un esclavo disponible --</option>
                                @foreach($disponibles as $disponible)
                                    <option value="{{ $disponible->id }}">
                                        {{ $disponible->nombre }} ({{ $disponible->modelo }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Solo aparecen esclavos que no están en otra red.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Nombre del Vínculo (Alias)</label>
                        <div class="col-sm-9">
                            <input type="text" name="nombre_vinculo" class="form-control" placeholder="Ej: Sensor de Temperatura Norte" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Ubicación Sugerida</label>
                        <div class="col-sm-9">
                            <select name="ubicacion_id" class="form-select" required>
                                <option value="">-- Seleccione ubicación --</option>
                                @foreach($ubicaciones as $ubi)
                                    <option value="{{ $ubi->id }}">{{ $ubi->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-link"></i> Crear Vínculo
                        </button>
                        <a href="{{ route('maestros.administrar', $maestro->id) }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </section>
</main>
@endsection