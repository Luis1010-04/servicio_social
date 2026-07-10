@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Generar Reportes</h1>
            <nav>
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('user.home') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Reportes</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="tarjetaFiltros">
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
        <label class="form-label">Intervalo</label>
        <select name="intervalo" id="intervalo" class="form-select" required>
            <option value="5m">5 Minutos</option>
            <option value="15m">15 Minutos</option>
            <option value="30m">30 Minutos</option>
            <option value="1h" selected>1 Hora</option>
            <option value="6h">6 Horas</option>
            <option value="12h">12 Horas</option>
            <option value="1d">1 Día</option>
        </select>
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

    <div class="col-md-12 d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary" id="btnGenerar" style="width: 250px;">
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
                            
                            <div class="d-flex gap-2">
                                <button id="btnDescargarPDF" class="btn btn-sm btn-outline-danger" style="display: none;" onclick="descargarPDF()">
                                    <i class="bi bi-file-earmark-pdf"></i> Descargar PDF
                                </button>
                                <button id="btnImprimirGrafica" class="btn btn-sm btn-outline-primary" style="display: none;" onclick="prepararImpresion()">
                                    <i class="bi bi-printer"></i> Previsuaización de Impresión
                                </button>
                            </div>
                        </div>
                        <div class="card-body mt-3">
                            
                            <div id="contGrafica" style="display: none; width:100%">
                                <div id="botonesFocusGrafica" class="d-flex flex-wrap gap-2 mb-3 justify-content-center"></div>
                                
                                <div style="position: relative; height:40vh; width:100%">
                                    <canvas id="canvasReporte"></canvas>
                                </div>
                            </div>

                            <div id="contTabla" style="display: none;" class="table-responsive">
                                <table class="table table-hover table-bordered w-100" id="tablaInflux">
                                    <thead class="table-light">
                                    </thead>
                                    <tbody id="tbodyReporte">
                                    </tbody>
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
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
    let miGrafica; 
    let tablaInstancia; 
    let sensorActualFoco = null;
    let metadatosReporte = null; // NUEVA VARIABLE PARA GUARDAR LA FICHA TÉCNICA

    // 1. Cargar Esclavos
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

        const urlFinal = "{{ route('user.reportes.getEsclavos', ':id') }}".replace(':id', maestroId);

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

    // 2. Cargar Componentes
    document.getElementById('selectEsclavo').addEventListener('change', function() {
        const esclavoId = this.value;
        const contenedorComp = document.getElementById('contenedorComponentes');
        const listadoCheck = document.getElementById('listadoCheckboxes');

        if (!esclavoId) return;

        listadoCheck.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Cargando sensores...';
        contenedorComp.style.display = 'block';

        const urlFinal = "{{ route('user.reportes.getComponentes', ':id') }}".replace(':id', esclavoId);

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
                            <input class="form-check-input" type="checkbox" name="componentes[]" value="${comp.id}" id="comp_${comp.id}" checked>
                            <label class="form-check-label" for="comp_${comp.id}">
                                ${comp.nombre} <small class="text-secondary">(${comp.tipo})</small>
                            </label>
                        `;
                        listadoCheck.appendChild(div);
                    });
                }
            });
    });

    // 3. Enviar Formulario
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
                if (res.success && res.data.length > 0) {
                    // GUARDAMOS LOS METADATOS QUE VIENEN DEL CONTROLADOR
                    metadatosReporte = res.metadata;

                    document.getElementById('seccionResultados').style.display = 'block';
                    
                    const datosProcesados = transformarDatosPivot(res.data);
                    const visual = document.querySelector('input[name="visual"]:checked').value;
                    
                    if (visual === 'lineas') {
                        mostrarGrafica(datosProcesados);
                    } else {
                        mostrarTabla(datosProcesados);
                    }
                } else if (res.success && res.data.length === 0) {
                    alert("No se encontraron registros para estos filtros.");
                    document.getElementById('seccionResultados').style.display = 'none';
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

    // --- FUNCIÓN HELPER: Transforma formato Largo a Ancho (Pivot) ---
    function transformarDatosPivot(dataRaw) {
        const agrupados = {};
        const sensoresSet = new Set();

        dataRaw.forEach(d => {
            if(!d.componente || d.valor === undefined) return;
            
            const objFecha = new Date(d._time);
            const timeStr = objFecha.toLocaleString();
            
            if (!agrupados[timeStr]) {
                agrupados[timeStr] = { objFecha: objFecha };
            }
            
            agrupados[timeStr][d.componente] = d.valor;
            sensoresSet.add(d.componente);
        });

        const fechasOrdenadas = Object.keys(agrupados).sort((a, b) => agrupados[a].objFecha - agrupados[b].objFecha);
        const sensores = Array.from(sensoresSet);

        return { agrupados, fechasOrdenadas, sensores };
    }

    // --- FUNCIÓN HELPER: Generar HTML de la Ficha Técnica ---
    function generarFichaTecnicaHTML() {
        if (!metadatosReporte) return '';
        
        let listaComponentes = metadatosReporte.componentes_analizados
            .map(c => `<li style="margin-bottom: 4px;"><b>${c.nombre}</b> <span style="color:#666; font-size:12px;">(${c.tipo})</span></li>`)
            .join('');

        return `
            <div style="font-family: Arial, sans-serif; color: #333; margin-bottom: 30px; width: 100%;">
                <div style="text-align: center; border-bottom: 2px solid #0d6efd; padding-bottom: 10px; margin-bottom: 20px;">
                    <h1 style="margin: 0; color: #0d6efd; font-size: 24px;">Ficha Técnica de Telemetría</h1>
                </div>
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 13px;">
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f8f9fa;"><b>Propietario / Usuario:</b></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${metadatosReporte.reporte_propietario}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f8f9fa;"><b>Fecha de Emisión:</b></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${metadatosReporte.fecha_generacion}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f8f9fa;"><b>Equipo Maestro:</b></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${metadatosReporte.maestro_nombre}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f8f9fa;"><b>Rango de Fechas:</b></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${metadatosReporte.rango_fechas.inicio} al ${metadatosReporte.rango_fechas.fin}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f8f9fa;"><b>Dispositivo Analizado:</b></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${metadatosReporte.equipo_nombre}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f8f9fa;"><b>Número de Serie:</b></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${metadatosReporte.numero_serie}</td>
                    </tr>
                </table>

                <div style="margin-bottom: 20px;">
                    <h3 style="margin-bottom: 10px; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Sensores / Componentes Evaluados</h3>
                    <ul style="margin: 0; padding-left: 20px; columns: 2; font-size: 13px;">
                        ${listaComponentes}
                    </ul>
                </div>
            </div>
        `;
    }

    // --- 4. FUNCIÓN GRÁFICA Y BOTONES INTERACTIVOS ---
    function mostrarGrafica(datosPivot) {
        document.getElementById('contGrafica').style.display = 'block';
        document.getElementById('contTabla').style.display = 'none';
        
        document.getElementById('btnImprimirGrafica').style.display = 'block';
        document.getElementById('btnDescargarPDF').style.display = 'block';

        if (miGrafica) miGrafica.destroy();
        sensorActualFoco = null;

        const { agrupados, fechasOrdenadas, sensores } = datosPivot;
        const paleta = ['#4361ee', '#f72585', '#4cc9f0', '#7209b7', '#ff9f1c', '#2ec4b6', '#e71d36'];

        const contenedorBotones = document.getElementById('botonesFocusGrafica');
        contenedorBotones.innerHTML = '';

        const datasets = sensores.map((sensor, i) => {
            const color = paleta[i % paleta.length];
            
            const btn = document.createElement('button');
            btn.className = 'btn btn-sm btn-outline-secondary';
            btn.style.borderColor = color;
            btn.innerHTML = `<span style="display:inline-block; width:10px; height:10px; background-color:${color}; border-radius:50%; margin-right:5px;"></span> ${sensor.toUpperCase()}`;
            
            btn.onclick = () => aplicarFocoGrafica(sensor, btn);
            contenedorBotones.appendChild(btn);

            return {
                label: sensor.toUpperCase(),
                sensorOriginalId: sensor, 
                data: fechasOrdenadas.map(f => agrupados[f][sensor] !== undefined ? agrupados[f][sensor] : null),
                borderColor: color,
                backgroundColor: color + '22',
                originalColor: color, 
                borderWidth: 2,
                borderDash: [], 
                tension: 0.3,
                fill: false,
                pointRadius: 3,
                spanGaps: true 
            };
        });

        const ctx = document.getElementById('canvasReporte').getContext('2d');
        miGrafica = new Chart(ctx, {
            type: 'line',
            data: { labels: fechasOrdenadas, datasets: datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false } },
                scales: { y: { ticks: { callback: value => value.toFixed(1) } } }
            }
        });
    }

    function aplicarFocoGrafica(sensorClickeado, botonElement) {
        const botones = document.getElementById('botonesFocusGrafica').children;

        if (sensorActualFoco === sensorClickeado) {
            sensorActualFoco = null;
            Array.from(botones).forEach(b => b.classList.remove('active', 'bg-light'));
            
            miGrafica.data.datasets.forEach(ds => {
                ds.borderColor = ds.originalColor;
                ds.borderWidth = 2;
                ds.borderDash = []; 
                ds.pointRadius = 3;
            });
        } else {
            sensorActualFoco = sensorClickeado;
            Array.from(botones).forEach(b => b.classList.remove('active', 'bg-light'));
            botonElement.classList.add('active', 'bg-light');

            miGrafica.data.datasets.forEach(ds => {
                if (ds.sensorOriginalId === sensorClickeado) {
                    ds.borderColor = ds.originalColor;
                    ds.borderWidth = 4;
                    ds.borderDash = [];
                    ds.pointRadius = 5;
                } else {
                    ds.borderColor = ds.originalColor + '40'; 
                    ds.borderWidth = 1; 
                    ds.borderDash = [5, 5]; 
                    ds.pointRadius = 0; 
                }
            });
        }
        miGrafica.update();
    }

    // --- 5. FUNCIÓN TABLA (Formato Horizontal/Wide) ---
    function mostrarTabla(datosPivot) {
        document.getElementById('contGrafica').style.display = 'none';
        document.getElementById('contTabla').style.display = 'block';
        
        document.getElementById('btnImprimirGrafica').style.display = 'none';
        document.getElementById('btnDescargarPDF').style.display = 'none';
        
        const dtDisponible = (typeof $ !== 'undefined' && $.fn.DataTable);

        if (dtDisponible && $.fn.DataTable.isDataTable('#tablaInflux')) {
            $('#tablaInflux').DataTable().destroy();
        }

        const contTabla = document.getElementById('contTabla');
        contTabla.innerHTML = `
            <table class="table table-hover table-bordered w-100" id="tablaInflux">
                <thead class="table-light"></thead>
                <tbody id="tbodyReporte"></tbody>
            </table>
        `;

        const thead = document.querySelector('#tablaInflux thead');
        const tbody = document.getElementById('tbodyReporte');
        
        const { agrupados, fechasOrdenadas, sensores } = datosPivot;

        let htmlThead = '<tr><th>Fecha y Hora</th>';
        sensores.forEach(s => { htmlThead += `<th>${s.toUpperCase()}</th>`; });
        htmlThead += '</tr>';
        thead.innerHTML = htmlThead;

        const fechasOrdenadasDesc = [...fechasOrdenadas].reverse();

        let htmlFilas = "";
        fechasOrdenadasDesc.forEach(fecha => {
            htmlFilas += `<tr><td><strong>${fecha}</strong></td>`;
            sensores.forEach(s => {
                let valor = agrupados[fecha][s];
                if (valor !== undefined && valor !== null && !isNaN(valor)) {
                    htmlFilas += `<td>${Number(valor).toFixed(2)}</td>`;
                } else {
                    htmlFilas += `<td class="text-muted">-</td>`; 
                }
            });
            htmlFilas += `</tr>`;
        });
        tbody.innerHTML = htmlFilas;

        if (dtDisponible) {
            tablaInstancia = $('#tablaInflux').DataTable({
                "ordering": false,
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                dom: 'lBfrtip', 
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                "language": {
                    "emptyTable": "No hay información",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                    "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                    "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                    "lengthMenu": "Mostrar _MENU_ Entradas",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "Sin resultados encontrados",
                    "paginate": { "first": "Primero", "last": "Ultimo", "next": "Siguiente", "previous": "Anterior" }
                }
            });
        }
    }

    // --- 6. FUNCIÓN PARA GENERAR IMÁGENES E IMPRIMIR EN VENTANA LIMPIA ---
    function prepararImpresion() {
        if (!miGrafica) {
            alert("Primero debes generar una gráfica para imprimir.");
            return;
        }

        const btn = document.getElementById('btnImprimirGrafica');
        const textoOriginal = btn.innerHTML;
        
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Preparando reporte...';
        btn.disabled = true;

        const imgGeneral = miGrafica.toBase64Image();
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = 800; // Reducimos el ancho para que quepa mejor en hoja
        tempCanvas.height = 300; // Reducimos el alto para que quepan varias por hoja
        const tempCtx = tempCanvas.getContext('2d');
        const datasets = miGrafica.data.datasets;
        const labels = miGrafica.data.labels;
        
        let htmlGraficasIndividuales = '';

        datasets.forEach((ds) => {
            const tempChart = new Chart(tempCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        ...ds, borderWidth: 2, borderDash: [], pointRadius: 2,
                        borderColor: ds.originalColor || ds.borderColor,
                        backgroundColor: (ds.originalColor || ds.borderColor) + '22'
                    }]
                },
                options: { responsive: false, animation: false, plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: value => value.toFixed(1) } } } }
            });

            const imgIndividual = tempChart.toBase64Image();
            
            // Usamos CSS `page-break-inside: avoid` en lugar de forzar un salto de página siempre
            htmlGraficasIndividuales += `
                <div class="grafica-seccion">
                    <h4 class="titulo-sensor" style="color: ${ds.originalColor || ds.borderColor};">
                        Comportamiento del Sensor: ${ds.label.toUpperCase()}
                    </h4>
                    <img src="${imgIndividual}" class="img-grafica">
                </div>
            `;
            tempChart.destroy(); 
        });

        const fichaTecnicaHtml = generarFichaTecnicaHTML();

        const ventanaImpresion = window.open('', '_blank', 'width=900,height=800');
        ventanaImpresion.document.write(`
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>Reporte Técnico de Sensores</title>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; background: #fff; }
                    .text-center { text-align: center; }
                    .img-grafica { width: 100%; max-width: 800px; height: auto; border: 1px solid #eee; padding: 15px; border-radius: 8px; box-sizing: border-box; }
                    .grafica-seccion { page-break-inside: avoid; text-align: center; margin-top: 25px; margin-bottom: 25px; }
                    .titulo-sensor { margin-bottom: 10px; font-size: 16px; margin-top: 0;}
                    /* Contenedor flexible para mostrar las gráficas más compactas si se desea */
                </style>
            </head>
            <body>
                ${fichaTecnicaHtml}
                
                <div class="grafica-seccion" style="border-top: 2px solid #ddd; padding-top: 20px;">
                    <h2 style="margin-bottom:15px; color:#333;">Vista General Interactiva</h2>
                    <img src="${imgGeneral}" class="img-grafica">
                </div>
                
                <hr style="border:1px dashed #ccc; margin: 30px 0;">
                
                ${htmlGraficasIndividuales}
                
                <script>
                    window.onload = function() {
                        setTimeout(() => { window.print(); window.close(); }, 800);
                    };
                <\/script>
            </body>
            </html>
        `);
        ventanaImpresion.document.close();

        btn.innerHTML = textoOriginal;
        btn.disabled = false;
    }

    // --- 7. NUEVA FUNCIÓN PARA DESCARGA DIRECTA DE PDF ---
    function descargarPDF() {
        if (!miGrafica) {
            alert("Primero debes generar una gráfica para descargar.");
            return;
        }

        const btn = document.getElementById('btnDescargarPDF');
        const textoOriginal = btn.innerHTML;
        
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generando PDF...';
        btn.disabled = true;

        const imgGeneral = miGrafica.toBase64Image();
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = 800; // Lienzo más estrecho
        tempCanvas.height = 300; // Lienzo más corto
        const tempCtx = tempCanvas.getContext('2d');
        const datasets = miGrafica.data.datasets;
        const labels = miGrafica.data.labels;
        
        let htmlGraficasIndividuales = '';

        datasets.forEach((ds) => {
            const tempChart = new Chart(tempCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        ...ds, borderWidth: 2, borderDash: [], pointRadius: 2,
                        borderColor: ds.originalColor || ds.borderColor,
                        backgroundColor: (ds.originalColor || ds.borderColor) + '22'
                    }]
                },
                options: { responsive: false, animation: false, plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: value => value.toFixed(1) } } } }
            });

            const imgIndividual = tempChart.toBase64Image();
            
            htmlGraficasIndividuales += `
                <div style="page-break-inside: avoid; text-align: center; margin-top: 20px; margin-bottom: 20px;">
                    <h4 style="color: ${ds.originalColor || ds.borderColor}; margin-bottom: 10px; font-size: 15px;">
                        Comportamiento del Sensor: ${ds.label.toUpperCase()}
                    </h4>
                    <img src="${imgIndividual}" style="width: 100%; height: auto; border: 1px solid #eee; padding: 10px; border-radius: 8px; box-sizing: border-box;">
                </div>
            `;
            tempChart.destroy(); 
        });

        const fichaTecnicaHtml = generarFichaTecnicaHTML();

        const elementoPDF = document.createElement('div');
        elementoPDF.innerHTML = `
            <div style="padding: 15px; font-family: Arial, sans-serif; color: #333; background: #fff; width: 100%; box-sizing: border-box;">
                ${fichaTecnicaHtml}
                
                <div style="page-break-inside: avoid; text-align: center; border-top: 2px solid #ddd; padding-top: 20px; margin-bottom: 30px;">
                    <h2 style="margin-bottom:15px; font-size: 18px; color:#333;">Vista General Interactiva</h2>
                    <img src="${imgGeneral}" style="width: 100%; height: auto; border: 1px solid #eee; padding: 10px; border-radius: 8px; box-sizing: border-box;">
                </div>
                
                ${htmlGraficasIndividuales}
            </div>
        `;

        const opt = {
            margin:       10,
            filename:     `Reporte_${metadatosReporte.equipo_nombre}_${metadatosReporte.fecha_generacion.split(' ')[0].replace(/\//g, '-')}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true }, 
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(elementoPDF).save().then(() => {
            btn.innerHTML = textoOriginal;
            btn.disabled = false;
        });
    }
</script>
@endpush