@extends('layouts.main')
@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>{{ $titulo }}</h1>
    </div>

    <section class="section">
        <div class="row mb-4">
            <div class="col-md-3" id="kpiUsuarios" style="cursor: pointer;">
                <div class="card shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Usuarios</h5>
                        <h6>{{ $kpis['usuarios_activos'] }} Activos / {{ $kpis['total_usuarios'] }}</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-3" id="kpiMaestros" style="cursor: pointer;">
                <div class="card shadow-sm border-start border-success border-4">
                    <div class="card-body">
                        <h5 class="card-title text-success">Maestros</h5>
                        <h6>{{ $kpis['total_maestros'] }} Registrados</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-3" id="kpiEsclavos" style="cursor: pointer;">
                <div class="card shadow-sm border-start border-warning border-4">
                    <div class="card-body">
                        <h5 class="card-title text-warning">Esclavos</h5>
                        <h6>{{ $kpis['total_esclavos'] }} Vinculados</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <button class="btn btn-dark w-100 h-100 shadow-sm" id="btnInventario">
                    <i class="bi bi-box-seam"></i> Ver Inventario Global
                </button>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Consulta de Historial (InfluxDB)</h5>
                <form id="formReporteAdmin" class="row g-3">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label">Cliente</label>
                        <select id="selectUsuario" class="form-select select2">
                            <option value="">Selecciona...</option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}">[{{ $u->rol }}] - {{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Maestro</label>
                        <select id="selectMaestro" class="form-select" disabled>
                            <option value="">Esperando usuario...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Esclavo</label>
                        <select id="selectEsclavo" name="esclavo_id" class="form-select" disabled>
                            <option value="">Esperando maestro...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Formato</label>
                        <select name="visual" class="form-select">
                            <option value="tabla">Tabla de Datos</option>
                            <option value="lineas">Gráfica de Líneas</option>
                        </select>
                    </div>
                    <div class="col-12" id="contenedorComponentes" style="display:none;">
                        <label class="form-label fw-bold">Sensores disponibles:</label>
                        <div id="listadoCheckboxes" class="d-flex flex-wrap gap-3 p-3 border rounded bg-light"></div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary px-4" id="btnGenerar">
                            <i class="bi bi-search"></i> Consultar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="seccionResultados" class="mt-4" style="display:none;">
            <div class="card">
                <div class="card-body pt-3">
                    <div id="contGrafica" style="display:none; height:400px;"><canvas id="canvasReporte"></canvas></div>
                    <div id="contTabla" style="display:none;" class="table-responsive">
                        <table class="table table-hover w-100" id="tablaInflux">
                            <thead class="table-light"><tr><th>Fecha</th><th>Sensor</th><th>Valor</th></tr></thead>
                            <tbody id="tbodyReporte"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<div class="modal fade" id="modalKpiDetalles" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-white" id="modalKpiHeader">
                <h5 class="modal-title" id="modalKpiTitle">Detalle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive" id="contenedorTablaKpi"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalInventario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title"><i class="bi bi-box-seam me-2"></i>Infraestructura Global IoT</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light" id="contenedorInventario">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Sincronizando telemetría...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const lenguajeEspanol = {
    "emptyTable": "No hay información",
    "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
    "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
    "infoFiltered": "(Filtrado de _MAX_ entradas totales)",
    "lengthMenu": "Mostrar _MENU_ entradas",
    "search": "Buscar:",
    "paginate": { "first": "Primero", "last": "Último", "next": "Siguiente", "previous": "Anterior" }
};

// Función para KPIs simples
function abrirModalKpi(tipo) {
    const modalEl = document.getElementById('modalKpiDetalles');
    const modal = new bootstrap.Modal(modalEl);
    const titulo = document.getElementById('modalKpiTitle');
    const header = document.getElementById('modalKpiHeader');
    const contenedor = document.getElementById('contenedorTablaKpi');

    header.className = "modal-header text-white";
    let url = "";
    let htmlTabla = "";

    if (tipo === 'usuarios') {
        header.classList.add("bg-primary");
        titulo.innerText = "Listado de Usuarios y Administradores";
        url = "/admin/reportes/kpi-usuarios";
        htmlTabla = `<table class="table w-100" id="tablaDinamicaKpi">
            <thead><tr><th>Nombre</th><th>Usuario</th><th>Email</th><th>Rol</th><th>Estado</th></tr></thead>
            <tbody id="bodyKpi"></tbody></table>`;
    } else if (tipo === 'maestros') {
        header.classList.add("bg-success");
        titulo.innerText = "Maestros Vinculados a Clientes";
        url = "/admin/reportes/kpi-maestros";
        htmlTabla = `<table class="table w-100" id="tablaDinamicaKpi">
            <thead><tr><th>Nombre Asignado</th><th>Modelo</th><th>Serie</th><th>Cliente</th></tr></thead>
            <tbody id="bodyKpi"></tbody></table>`;
    } else if (tipo === 'esclavos') {
        header.classList.add("bg-warning", "text-dark");
        titulo.innerText = "Esclavos en Red";
        url = "/admin/reportes/kpi-esclavos";
        htmlTabla = `<table class="table w-100" id="tablaDinamicaKpi">
            <thead><tr><th>Nombre Red</th><th>Modelo</th><th>Serie</th><th>Maestro Padre</th><th>Cliente</th></tr></thead>
            <tbody id="bodyKpi"></tbody></table>`;
    }

    contenedor.innerHTML = htmlTabla;

    fetch(url)
        .then(res => res.json())
        .then(res => {
            const body = document.getElementById('bodyKpi');
            let filas = "";

            if (tipo === 'usuarios') {
                filas = res.data.map(u => `
                    <tr>
                        <td>${u.name} ${u.apellido}</td>
                        <td>${u.usuario}</td>
                        <td>${u.email}</td>
                        <td><span class="badge ${u.rol === 'Admin' ? 'bg-dark' : 'bg-info text-dark'}">${u.rol}</span></td>
                        <td><span class="badge ${u.activo ? 'bg-success' : 'bg-danger'}">${u.activo ? 'Activo' : 'Inactivo'}</span></td>
                    </tr>`).join('');
            } else if (tipo === 'maestros') {
                filas = res.data.map(m => `
                    <tr>
                        <td>${m.nombre_asignado}</td>
                        <td>${m.modelo}</td>
                        <td>${m.numero_serie}</td>
                        <td>${m.cliente}</td>
                    </tr>`).join('');
            } else if (tipo === 'esclavos') {
                filas = res.data.map(e => `
                    <tr>
                        <td>${e.nombre_vinculo}</td>
                        <td>${e.modelo}</td>
                        <td>${e.numero_serie}</td>
                        <td>${e.maestro_padre}</td>
                        <td>${e.cliente}</td>
                    </tr>`).join('');
            }

            body.innerHTML = filas;
            if ($.fn.DataTable.isDataTable('#tablaDinamicaKpi')) {
                $('#tablaDinamicaKpi').DataTable().destroy();
            }
            $('#tablaDinamicaKpi').DataTable({ language: lenguajeEspanol, responsive: true });
            modal.show();
        });
}

document.addEventListener('DOMContentLoaded', function() {
    // Eventos KPI
    document.getElementById('kpiUsuarios').onclick = () => abrirModalKpi('usuarios');
    document.getElementById('kpiMaestros').onclick = () => abrirModalKpi('maestros');
    document.getElementById('kpiEsclavos').onclick = () => abrirModalKpi('esclavos');

    // REFERENCIA AL MODAL DE INVENTARIO
    const mInvEl = document.getElementById('modalInventario');
    const modalInv = new bootstrap.Modal(mInvEl);
    const contenedorInv = document.getElementById('contenedorInventario');

    document.getElementById('btnInventario').onclick = function() {
        contenedorInv.innerHTML = `
            <div class="text-center p-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Consultando InfluxDB y MySQL...</p>
            </div>`;
        modalInv.show();

       fetch('/admin/reportes/inventario-global')
    .then(res => res.json())
    .then(res => {
        if (!res.data || res.data.length === 0) {
            contenedorInv.innerHTML = '<div class="alert alert-info">No hay registros de infraestructura.</div>';
            return;
        }

        // --- Estructura jerárquica ---
        const dataAgrupada = res.data.reduce((acumulador, item) => {
            // 1. Buscar si el cliente ya existe en el acumulador
            let cliente = acumulador.find(c => c.email === item.email);
            if (!cliente) {
                cliente = { 
                    cliente: item.cliente, 
                    email: item.email, 
                    activo: item.activo, 
                    maestros: [] 
                };
                acumulador.push(cliente);
            }

            // 2. Buscar si el maestro ya existe dentro del cliente
            let maestro = cliente.maestros.find(m => m.nombre === item.maestro);
            if (!maestro) {
                maestro = { nombre: item.maestro, esclavos: [] };
                cliente.maestros.push(maestro);
            }

            // 3. Insertar el esclavo en el maestro correspondiente
            maestro.esclavos.push({
                nombre: item.esclavo,
                serie: item.serie,
                sensores: item.sensores || 0,
                actuadores: item.actuadores || 0,
                modelo: item.modelo || 'N/A',
                ultima_actividad: item.ultima_actividad || 'Sin datos',
                online: item.online || false
            });

            return acumulador;
        }, []);

        contenedorInv.innerHTML = dataAgrupada.map(cliente => `
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h5 class="mb-0 text-primary fw-bold">${cliente.cliente}</h5>
                        <small class="text-muted">${cliente.email}</small>
                    </div>
                    <span class="badge ${cliente.activo ? 'bg-success' : 'bg-danger'} p-2">
                        ${cliente.activo ? 'CLIENTE ACTIVO' : 'CLIENTE SUSPENDIDO'}
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Maestro / Grupo</th>
                                    <th>Esclavo (S/N)</th>
                                    <th>Configuración Hardware</th>
                                    <th>Última Actividad</th>
                                    <th class="text-center">Status Red</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${cliente.maestros.map(maestro => `
                                    ${maestro.esclavos.map((esclavo, eIdx) => `
                                        <tr>
                                            ${eIdx === 0 ? `
                                                <td rowspan="${maestro.esclavos.length}" class="ps-4 fw-bold border-end bg-light" style="width: 200px;">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-hdd-rack me-2 text-secondary h5 mb-0"></i>
                                                        <span>${maestro.nombre}</span>
                                                    </div>
                                                </td>` : ''}
                                            <td>
                                                <span class="fw-bold d-block">${esclavo.nombre}</span>
                                                <code class="text-primary small">${esclavo.serie}</code>
                                                <br><span class="badge bg-secondary" style="font-size: 0.65rem">${esclavo.modelo}</span>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    <i class="bi bi-thermometer-half text-warning me-1"></i>Sensores: <b>${esclavo.sensores}</b><br>
                                                    <i class="bi bi-toggle-on text-primary me-1"></i>Actuadores: <b>${esclavo.actuadores}</b>
                                                </div>
                                            </td>
                                            <td class="small text-muted">
                                                ${esclavo.ultima_actividad}
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="spinner-grow spinner-grow-sm ${esclavo.online ? 'text-success' : 'text-danger'}" role="status"></div>
                                                    <span class="small fw-bold ${esclavo.online ? 'text-success' : 'text-danger'} mt-1">
                                                        ${esclavo.online ? 'ONLINE' : 'OFFLINE'}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    `).join('')}
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `).join('');
    })
    .catch(err => {

        console.error("Error capturado en el frontend:", err);
        contenedorInv.innerHTML = '<div class="alert alert-danger">Error al procesar los datos de la API. Revisa la consola.</div>';
    });
    };
});
</script>
@endpush