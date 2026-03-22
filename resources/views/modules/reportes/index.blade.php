@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Generar Reportes</h1>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Filtros de Reporte</h5>

                            <form action="{{ route('reportes.generar') }}" method="GET" class="row g-3">
                                
                                <div class="col-md-6">
                                    <label class="form-label">Mi Equipo Maestro</label>
                                    <select id="selectMaestro" name="maestro_id" class="form-select" required>
                                        <option value="" selected disabled>Selecciona un maestro...</option>
                                        @foreach($maestros as $maestro)
                                            <option value="{{ $maestro->id }}">
                                                {{ $maestro->nombre }} - {{ $maestro->ubicacion->nombre ?? 'Sin ubicación' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Dispositivo Esclavo</label>
                                    <select id="selectEsclavo" name="esclavo_id" class="form-select" disabled required>
                                        <option value="" selected disabled>Primero selecciona un maestro...</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Desde</label>
                                    <input type="date" name="fecha_inicio" class="form-control" required>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Hasta</label>
                                    <input type="date" name="fecha_fin" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Tipo de Visualización</label>
                                    <div class="d-flex gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="visual" value="tabla" id="v1" checked>
                                            <label class="form-check-label" for="v1">Tabla</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="visual" value="lineas" id="v2">
                                            <label class="form-check-label" for="v2">Líneas</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="visual" value="barras" id="v3">
                                            <label class="form-check-label" for="v3">Barras</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Formato de Archivo</label>
                                    <select name="formato" class="form-select">
                                        <option value="pdf">PDF (Reporte Formal)</option>
                                        <option value="excel">Excel (Datos Crudos)</option>
                                    </select>
                                </div>

                                <div class="col-md-12 d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="bi bi-file-earmark-bar-graph"></i> Generar Reporte
                                    </button>
                                </div>
                            </form> </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

@push('scripts')
<script>
    document.getElementById('selectMaestro').addEventListener('change', function() {
        const maestroId = this.value;
        const selectEsclavo = document.getElementById('selectEsclavo');

        selectEsclavo.innerHTML = '<option value="" selected disabled>Cargando dispositivos...</option>';
        selectEsclavo.disabled = true;

        fetch(`/api/maestros/${maestroId}/esclavos`)
            .then(response => response.json())
            .then(data => {
                selectEsclavo.innerHTML = '<option value="" selected disabled>Selecciona un esclavo...</option>';
                if(data.length === 0) {
                    selectEsclavo.innerHTML = '<option value="" disabled>No hay esclavos asociados</option>';
                } else {
                    data.forEach(esclavo => {
                        const option = document.createElement('option');
                        option.value = esclavo.id;
                        option.textContent = `${esclavo.nombre} (${esclavo.modelo})`;
                        selectEsclavo.appendChild(option);
                    });
                    selectEsclavo.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                selectEsclavo.innerHTML = '<option value="" disabled>Error al cargar datos</option>';
            });
    });
</script>
@endpush
@endsection