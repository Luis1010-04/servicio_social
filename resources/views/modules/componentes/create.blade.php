@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>{{ $titulo }}</h1>
    </div>

    <section class="section">
        <form action="{{ route('componentes.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Especificaciones del Dispositivo</h5>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Nombre del Modelo</label>
                                <div class="col-sm-9">
                                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Sensor Ultrasónico HC-SR04" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Tipo</label>
                                <div class="col-sm-9">
                                    <select name="tipo" id="tipo_dispositivo" class="form-select" required>
                                        <option value="Sensor">Sensor (Entrada)</option>
                                        <option value="Actuador">Actuador (Salida/Control)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3" id="contenedor_unidad">
                                <label class="col-sm-3 col-form-label">Unidad de Medida</label>
                                <div class="col-sm-9">
                                    <select name="unidad_id" id="unidad_id" class="form-select">
                                        <option value="">-- Seleccione Unidad --</option>
                                        @foreach($unidades as $u)
                                            <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Icono</label>
                                <div class="col-sm-9">
                                    <select name="ruta_incono" class="form-select">
                                        <option value="bi bi-cpu">Genérico (Chip)</option>
                                        <option value="bi bi-thermometer-half">Temperatura</option>
                                        <option value="bi bi-droplet">Líquidos / Humedad</option>
                                        <option value="bi bi-wind">Aire / Gas</option>
                                        <option value="bi bi-plug">Energía Eléctrica</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Descripción</label>
                                <div class="col-sm-9">
                                    <textarea name="descripcion" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Vinculación Inmediata <small class="text-muted text-capitalize">(Opcional)</small></h5>
                            <p class="small text-muted">Si este componente ya está instalado en algún equipo, selecciónalo aquí:</p>
                            
                            <div class="row px-3">
                                @forelse($esclavos as $esc)
                                    <div class="col-md-6 form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="esclavos[]" value="{{ $esc->id }}" id="esc_{{ $esc->id }}">
                                        <label class="form-check-label small" for="esc_{{ $esc->id }}">
                                            {{ $esc->nombre }} ({{ $esc->modelo }})
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted small">No hay esclavos disponibles para vincular.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body pt-3">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa-solid fa-save"></i> Guardar Todo
                                </button>
                                <a href="{{ route('componentes.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-arrow-left"></i> Regresar
                                </a>
                            </div>
                            <hr>
                            <div class="alert alert-info small border-0">
                                <i class="fa-solid fa-circle-info"></i> 
                                Al guardar, el componente estará disponible en el catálogo general para ser usado en cualquier equipo esclavo.
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
            // Ocultamos el contenedor y quitamos el requerimiento
            contenedorUnidad.style.display = 'none';
            unidadSelect.required = false;
            unidadSelect.value = ''; // Limpiamos la selección
        } else {
            // Mostramos y hacemos obligatorio para sensores
            contenedorUnidad.style.display = 'flex';
            unidadSelect.required = true;
        }
    }

    // Escuchar cambios
    tipoSelect.addEventListener('change', toggleUnidad);

    // Ejecutar al cargar por si hay valores previos
    toggleUnidad();
});
</script>

@endsection