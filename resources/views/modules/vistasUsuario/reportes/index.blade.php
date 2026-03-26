@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Generar Reportes</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Reportes</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Filtros de Reporte</h5>

                            <form id="formReporte" class="row g-3">
                                @csrf
                                <div class="col-md-6">
                                    <label class="form-label">Mi Equipo Maestro</label>
                                    <select id="selectMaestro" name="maestro_id" class="form-select" required>
                                        <option value="" selected disabled>Selecciona un maestro...</option>
                                        @foreach($maestros as $maestro)
                                            <option value="{{ $maestro->id }}">
                                                {{ $maestro->nombre_asignado }} ({{ $maestro->modelo }})
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

                                <div class="col-md-3">
                                    <label class="form-label">Desde</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Hasta</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Visualización</label>
                                    <div class="d-flex gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="visual" value="tabla" id="v1" checked>
                                            <label class="form-check-label" for="v1">Tabla</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="visual" value="lineas" id="v2">
                                            <label class="form-check-label" for="v2">Gráfica</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12" id="contenedorComponentes" style="display: none;">
                                    <label class="form-label"><b>Componentes disponibles para este dispositivo:</b></label>
                                    <div id="listadoCheckboxes" class="d-flex flex-wrap gap-3 p-2 border rounded bg-light">
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex align-items-end justify-content-end">
                                    <button type="submit" class="btn btn-primary w-100" id="btnGenerar">
                                        <i class="bi bi-search"></i> Consultar InfluxDB
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12" id="seccionResultados" style="display: none;">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center bg-white">
                            <h5 class="card-title mb-0" id="txtResultado">Resultados del Nodo</h5>
                            <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                                <i class="bi bi-printer"></i> Imprimir
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="contGrafica" style="display: none; position: relative; height:40vh; width:100%">
                                <canvas id="canvasReporte"></canvas>
                            </div>

                            <div id="contTabla" style="display: none;" class="table-responsive mt-3">
                                <table class="table table-hover" id="tablaInflux">
                                    <thead>
                                        <tr>
                                            <th>Fecha (Local)</th>
                                            <th>Componente</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyReporte"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
@push('scripts')

<script>
    let miGrafica; 
    let tablaInstancia; 

    document.getElementById('selectMaestro').addEventListener('change', function() {
        const maestroId = this.value;
        if (!maestroId) return;

        const selectEsclavo = document.getElementById('selectEsclavo');
        const contenedorComp = document.getElementById('contenedorComponentes');
        const listadoCheck = document.getElementById('listadoCheckboxes');

        selectEsclavo.innerHTML = '<option value="" selected disabled>Cargando esclavos...</option>';
        selectEsclavo.disabled = true;
        
        if (contenedorComp) contenedorComp.style.display = 'none';
        if (listadoCheck) listadoCheck.innerHTML = '';

        const urlBase = "{{ route('user.reportes.getEsclavos', ':id') }}";
        const urlFinal = urlBase.replace(':id', maestroId);

        fetch(urlFinal)
            .then(response => response.json())
            .then(data => {
                selectEsclavo.innerHTML = '<option value="" selected disabled>Selecciona un esclavo...</option>';
                if (data.length === 0) {
                    selectEsclavo.innerHTML = '<option value="" disabled>No hay esclavos asignados</option>';
                } else {
                    data.forEach(esclavo => {
                        const option = document.createElement('option');
                        option.value = esclavo.id; 
                        option.textContent = `${esclavo.nombre || 'Esclavo'} (${esclavo.numero_serie || esclavo.modelo})`;
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

    document.getElementById('selectEsclavo').addEventListener('change', function() {
        const esclavoId = this.value;
        const contenedorComp = document.getElementById('contenedorComponentes');
        const listadoCheck = document.getElementById('listadoCheckboxes');

        if (!esclavoId) return;

        listadoCheck.innerHTML = '<small class="text-muted">Cargando sensores...</small>';
        contenedorComp.style.display = 'block';

        const urlBase = "{{ route('user.reportes.getComponentes', ':id') }}";
        const urlFinal = urlBase.replace(':id', esclavoId);

        fetch(urlFinal)
            .then(response => response.json())
            .then(data => {
                listadoCheck.innerHTML = ''; 
                if (data.length === 0) {
                    listadoCheck.innerHTML = '<span class="text-danger">Este dispositivo no tiene sensores vinculados.</span>';
                } else {
                    data.forEach(comp => {
                        const div = document.createElement('div');
                        div.className = 'form-check form-check-inline';
                        div.innerHTML = `
                            <input class="form-check-input" type="checkbox" name="componentes[]" value="${comp.id}" id="comp_${comp.id}">
                            <label class="form-check-label" for="comp_${comp.id}">
                                ${comp.nombre} <small class="text-secondary">(${comp.tipo})</small>
                            </label>
                        `;
                        listadoCheck.appendChild(div);
                    });
                }
            });
    });

    document.getElementById('formReporte').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btnGenerar');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Consultando...';

        const formData = new FormData(this);
        const params = new URLSearchParams(formData).toString();

        fetch("{{ route('user.reportes.generar') }}?" + params)
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('seccionResultados').style.display = 'block';
                    const visual = document.querySelector('input[name="visual"]:checked').value;

                    if (visual === 'lineas') {
                        mostrarGrafica(res.data);
                    } else {
                        mostrarTabla(res.data);
                    }
                } else {
                    alert("Error: " + res.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Ocurrió un error al consultar los datos.");
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-search"></i> Consultar InfluxDB';
            });
    });

    function mostrarGrafica(data) {
        if (data.length === 0) {
            alert("No se encontraron datos.");
            return;
        }

        document.getElementById('contGrafica').style.display = 'block';
        document.getElementById('contTabla').style.display = 'none';

        if (miGrafica) miGrafica.destroy();

        data.sort((a, b) => new Date(a._time) - new Date(b._time));
        const labels = data.map(d => new Date(d._time).toLocaleString());
        const systemKeys = ['_time', '_measurement', '_start', '_stop', 'esclavo_id', 'result', 'table', 'componente', 'valor'];
        const sensorKeys = Object.keys(data[0]).filter(k => !systemKeys.includes(k));

        const paleta = ['#4361ee', '#f72585', '#4cc9f0', '#7209b7', '#ff9f1c', '#2ec4b6', '#e71d36'];

        const datasets = sensorKeys.map((key, i) => {
            const color = paleta[i % paleta.length];
            return {
                label: key.replace('_', ' ').toUpperCase(),
                data: data.map(d => d[key]),
                borderColor: color,
                backgroundColor: color + '22',
                tension: 0.3,
                fill: false,
                pointRadius: 3
            };
        });

        const ctx = document.getElementById('canvasReporte').getContext('2d');
        miGrafica = new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { ticks: { callback: value => value.toFixed(1) } }
                }
            }
        });
    }

    // 5. Función Tabla (Con DataTables)
    function mostrarTabla(data) {
        document.getElementById('contGrafica').style.display = 'none';
        document.getElementById('contTabla').style.display = 'block';
        
        // Destruir instancia previa si existe
        if ($.fn.DataTable.isDataTable('#tablaInflux')) {
            $('#tablaInflux').DataTable().destroy();
        }

        const tbody = document.getElementById('tbodyReporte');
        tbody.innerHTML = '';

        const dataSorted = [...data].sort((a, b) => new Date(b._time) - new Date(a._time));

        dataSorted.forEach(d => {
            const exclude = ['_time', 'componente', 'valor', 'result', 'table'];
            Object.keys(d).forEach(key => {
                if (!exclude.includes(key) && !isNaN(d[key]) && d[key] !== null) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${new Date(d._time).toLocaleString()}</td>
                        <td><span class="badge bg-info text-dark">${key.replace('_', ' ').toUpperCase()}</span></td>
                        <td><strong>${Number(d[key]).toFixed(2)}</strong></td>
                    `;
                    tbody.appendChild(tr);
                }
            });
        });

        tablaInstancia = $('#tablaInflux').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "order": [[0, "desc"]],
            "pageLength": 10,
            "lengthMenu": [10, 25, 50, 100],
            "dom": 'lfrtip'
        });
    }
</script>
@endpush