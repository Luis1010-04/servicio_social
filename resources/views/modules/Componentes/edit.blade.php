@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>{{ $titulo }}</h1>
    </div>

    <section class="section">
        <form action="{{ route('componentes.update', $componente->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Editar Especificaciones</h5>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Nombre del Modelo</label>
                                <div class="col-sm-9">
                                    <input type="text" name="nombre" class="form-control" value="{{ $componente->nombre }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Tipo</label>
                                <div class="col-sm-9">
                                    <select name="tipo" id="tipo_dispositivo" class="form-select" required>
                                        <option value="Sensor" {{ $componente->tipo == 'Sensor' ? 'selected' : '' }}>Sensor (Entrada)</option>
                                        <option value="Actuador" {{ $componente->tipo == 'Actuador' ? 'selected' : '' }}>Actuador (Salida/Control)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3" id="contenedor_unidad">
                                <label class="col-sm-3 col-form-label">Unidad de Medida</label>
                                <div class="col-sm-9">
                                    <select name="unidad_id" id="unidad_id" class="form-select">
                                        <option value="">-- Seleccione Unidad --</option>
                                        @foreach($unidades as $u)
                                            <option value="{{ $u->id }}" {{ $componente->unidad_id == $u->id ? 'selected' : '' }}>{{ $u->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Icono</label>
                                <div class="col-sm-9">
                                    <select name="ruta_incono" class="form-select">
                                        @php $iconos = ['bi bi-cpu', 'bi bi-thermometer-half', 'bi bi-droplet', 'bi bi-wind', 'bi bi-plug']; @endphp
                                        @foreach($iconos as $ico)
                                            <option value="{{ $ico }}" {{ $componente->ruta_incono == $ico ? 'selected' : '' }}>{{ $ico }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Descripción</label>
                                <div class="col-sm-9">
                                    <textarea name="descripcion" class="form-control" rows="2">{{ $componente->descripcion }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Esclavos Vinculados</h5>
                            <div class="row px-3">
                                @forelse($esclavos as $esc)
                                    <div class="col-md-6 form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="esclavos[]" 
                                               value="{{ $esc->id }}" id="esc_{{ $esc->id }}"
                                               {{ in_array($esc->id, $esclavosVinculados) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="esc_{{ $esc->id }}">
                                            {{ $esc->nombre }}
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted small">No hay esclavos disponibles.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body pt-3">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="fa-solid fa-sync"></i> Actualizar
                                </button>
                                <a href="{{ route('componentes.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-arrow-left"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo_dispositivo');
    const contenedorUnidad = document.getElementById('contenedor_unidad');
    const unidadSelect = document.getElementById('unidad_id');

    function toggleUnidad() {
        if (tipoSelect.value === 'Actuador') {
            contenedorUnidad.style.display = 'none';
            unidadSelect.required = false;
        } else {
            contenedorUnidad.style.display = 'flex';
            unidadSelect.required = true;
        }
    }

    tipoSelect.addEventListener('change', toggleUnidad);
    toggleUnidad(); // Se ejecuta al cargar la página para evaluar el estado inicial
});
</script>
@endsection