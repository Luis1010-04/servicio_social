@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
    <main id="main" class="main">
        
        <div class="pagetitle">
            <h1>Panel de Control Global</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item">Administración</li>
                    <li class="breadcrumb-item active">Panel Global</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="row">

                <div class="col-xxl-3 col-md-6 mb-4">
                    <div class="card info-card sales-card h-100 shadow-sm" style="cursor: pointer; transition: 0.3s;" onclick="abrirModal('usuarios')" onmouseover="this.classList.add('shadow')" onmouseout="this.classList.remove('shadow')">
                        <div class="card-body">
                            <h5 class="card-title">Usuarios <span>| Registrados</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 50px; height: 50px; font-size: 24px;">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="mb-0 fs-5 text-primary">Ver Listado</h6>
                                    <span class="text-muted small pt-2 ps-1">Directorio y Estatus</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6 mb-4">
                    <div class="card info-card revenue-card h-100 shadow-sm" style="cursor: pointer; transition: 0.3s;" onclick="abrirModal('maestros')" onmouseover="this.classList.add('shadow')" onmouseout="this.classList.remove('shadow')">
                        <div class="card-body">
                            <h5 class="card-title">Maestros <span>| Catálogo</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success text-white" style="width: 50px; height: 50px; font-size: 24px;">
                                    <i class="bi bi-hdd-network"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="mb-0 fs-5 text-success">Inventario</h6>
                                    <span class="text-muted small pt-2 ps-1">Modelos Operando</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6 mb-4">
                    <div class="card info-card customers-card h-100 shadow-sm" style="cursor: pointer; transition: 0.3s;" onclick="abrirModal('esclavos')" onmouseover="this.classList.add('shadow')" onmouseout="this.classList.remove('shadow')">
                        <div class="card-body">
                            <h5 class="card-title">Esclavos <span>| Catálogo</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-info text-white" style="width: 50px; height: 50px; font-size: 24px;">
                                    <i class="bi bi-cpu"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="mb-0 fs-5 text-info">Inventario</h6>
                                    <span class="text-muted small pt-2 ps-1">Modelos Operando</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6 mb-4">
                    <div class="card info-card h-100 shadow-sm border border-dark" style="cursor: pointer; transition: 0.3s;" onclick="abrirModal('tabla_maestra')" onmouseover="this.classList.add('shadow')" onmouseout="this.classList.remove('shadow')">
                        <div class="card-body bg-dark text-white rounded">
                            <h5 class="card-title text-white">GOD VIEW <span>| Monitor</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-white text-dark" style="width: 50px; height: 50px; font-size: 24px;">
                                    <i class="bi bi-globe"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="mb-0 fs-5">Tabla Maestra</h6>
                                    <span class="text-light small pt-2 ps-1">Relaciones y Telemetría</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <div class="modal fade" id="modalKpi" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold" id="modalTitulo">Cargando...</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="table-responsive">
                            <table id="tablaDinamica" class="table table-hover table-bordered w-100 align-middle">
                                <thead id="tablaHead" class="table-light"></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

    <script>
        let dataTableInstancia = null;

        // Configuración común para imprimir/exportar en DataTables
        const dtOptionsBase = {
            destroy: true, 
            processing: true,
            dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between mt-3"ip>',
            buttons: [
                { extend: 'excelHtml5', className: 'btn btn-sm btn-success', text: '<i class="bi bi-file-earmark-excel"></i> Excel' },
                { extend: 'pdfHtml5', className: 'btn btn-sm btn-danger', text: '<i class="bi bi-file-earmark-pdf"></i> PDF', orientation: 'landscape' },
                { extend: 'print', className: 'btn btn-sm btn-secondary', text: '<i class="bi bi-printer"></i> Imprimir' }
            ],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' }
        };

        function abrirModal(tipo) {
            const modal = new bootstrap.Modal(document.getElementById('modalKpi'));
            const modalTitulo = document.getElementById('modalTitulo');
            
            let url = '';
            let columns = [];

            // Limpiamos la tabla anterior para evitar cruce de datos
            if (dataTableInstancia) {
                dataTableInstancia.clear().destroy();
                $('#tablaDinamica').empty(); 
            }

            // Configuramos las rutas y columnas dependiendo de la tarjeta presionada
            switch(tipo) {
                case 'usuarios':
                    modalTitulo.innerHTML = "<i class='bi bi-people text-primary me-2'></i> Listado General de Usuarios";
                    url = "{{ route('admin.reportes.api.usuarios') }}";
                    columns = [
                        { data: 'name', title: 'Nombre de Usuario' },
                        { data: 'email', title: 'Correo Electrónico' },
                        { data: 'created_at', title: 'Fecha de Registro', render: data => new Date(data).toLocaleDateString() },
                        { data: 'activo', title: 'Estatus', render: data => data ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>' }
                    ];
                    break;

                case 'maestros':
                    modalTitulo.innerHTML = "<i class='bi bi-hdd-network text-success me-2'></i> Catálogo de Equipos Maestros";
                    url = "{{ route('admin.reportes.api.maestros') }}";
                    columns = [
                        { data: 'nombre', title: 'Nombre Catálogo' },
                        { data: 'modelo', title: 'Nombre del Modelo' },
                        { data: 'descripcion', title: 'Descripción' },
                        { data: 'operando', title: 'Equipos en Servicio', render: data => `<span class="badge border border-primary text-primary">${data} Operando</span>` },
                        { data: 'created_at', title: 'Fecha Alta', render: data => new Date(data).toLocaleDateString() },
                        { data: 'activo', title: 'Status', render: data => data ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' }
                    ];
                    break;

                case 'esclavos':
                    modalTitulo.innerHTML = "<i class='bi bi-cpu text-info me-2'></i> Catálogo de Dispositivos Esclavos";
                    url = "{{ route('admin.reportes.api.esclavos') }}";
                    columns = [
                        { data: 'nombre', title: 'Nombre Catálogo' },
                        { data: 'modelo', title: 'Nombre del Modelo' },
                        // Eliminamos la línea de descripción aquí porque no existe en BD
                        { data: 'operando', title: 'Equipos en Servicio', render: data => `<span class="badge border border-info text-info">${data} Operando</span>` },
                        { data: 'created_at', title: 'Fecha Alta', render: data => new Date(data).toLocaleDateString() },
                        { data: 'activo', title: 'Status', render: data => data ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' }
                    ];
                    break;

                case 'tabla_maestra':
                    modalTitulo.innerHTML = "<i class='bi bi-globe text-dark me-2'></i> God View - Relaciones y Telemetría en Vivo";
                    url = "{{ route('admin.reportes.api.tabla_maestra') }}";
                    columns = [
                        { data: 'usuario', title: 'Usuario Asignado' },
                        { data: 'nombre_maestro', title: 'Nombre Maestro' },
                        { data: 'modelo_maestro', title: 'Modelo Maestro' },
                        { data: 'nombre_esclavo', title: 'Nombre Esclavo' },
                        { data: 'numero_serie', title: 'No. Serie / TAG' },
                        { 
                            data: 'influx_time', // <- Ahora es una propiedad formal de la tabla
                            defaultContent: null,
                            title: 'Último Registro (InfluxDB)',
                            render: function(data, type, row) {
                                if (data === 'Sin TAG') return `<span class="badge bg-secondary">Sin TAG</span>`;
                                if (data === 'Error') return `<span class="badge bg-danger">Error de Conexión</span>`;
                                if (data) return `<span class="badge bg-light text-dark border"><i class="bi bi-clock-history"></i> ${data}</span>`;
                                
                                return `<span class="badge bg-light text-dark border"><i class="bi bi-hourglass-split"></i> Buscando...</span>`;
                            }
                        },
                        { 
                            data: 'influx_status', // <- Ahora es una propiedad formal de la tabla
                            defaultContent: null,
                            title: 'Status Real',
                            render: function(data, type, row) {
                                if (data === 'Online') return '<span class="badge bg-success text-white shadow-sm"><i class="bi bi-wifi"></i> Online</span>';
                                if (data === 'Offline') return '<span class="badge bg-danger text-white shadow-sm"><i class="bi bi-wifi-off"></i> Offline</span>';
                                if (data === 'Error') return '<span class="badge bg-secondary text-white"><i class="bi bi-exclamation-triangle"></i> Falló</span>';
                                if (data === 'Ignorado') return '<span class="badge bg-secondary">Ignorado</span>';
                                
                                return `<span class="badge bg-warning text-dark"><div class="spinner-border spinner-border-sm" role="status"></div> Analizando...</span>`;
                            }
                        }
                    ];
                    break;
            }

            // Inicializamos la tabla
            dataTableInstancia = $('#tablaDinamica').DataTable({
                ...dtOptionsBase,
                ajax: {
                    url: url,
                    type: 'GET',
                    dataSrc: function (json) {
                        if (json.error) {
                            console.error("🔥 Error desde MySQL/Laravel:", json.error);
                            alert("Ocurrió un error al cargar los datos. Revisa la consola.");
                            return [];
                        }
                        return json.data || [];
                    },
                    error: function (xhr, error, thrown) {
                        console.error("🔥 Error AJAX DataTables:", xhr.responseText);
                        alert("Error de conexión. Revisa la consola.");
                    }
                },
                columns: columns,
                // CAMBIO 1: Usar drawCallback en lugar de initComplete para que funcione al paginar
                drawCallback: function(settings) {
                    if(tipo === 'tabla_maestra') {
                        escanearInfluxDB();
                    }
                }
            });

            modal.show();
        }

        // CAMBIO 2: Función con rastreo en consola y mejor manejo de errores HTTP
        function escanearInfluxDB() {
            if (!dataTableInstancia) return;

            // Iteramos SOLO sobre las filas visibles en la página actual de DataTables
            dataTableInstancia.rows({ page: 'current' }).every(function () {
                let row = this;
                let rowData = row.data();
                let serie = rowData.numero_serie;

                // Si la fila ya tiene status (ya fue consultada), la saltamos para no gastar recursos
                if (rowData.influx_status) return;

                if (!serie || serie === 'null') {
                    rowData.influx_time = 'Sin TAG';
                    rowData.influx_status = 'Ignorado';
                    row.data(rowData); // Alimenta a DataTables y actualiza la vista visualmente
                    return;
                }

                fetch("{{ route('admin.reportes.api.influx') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ serie: serie })
                })
                .then(response => {
                    if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    // Le inyectamos los datos directamente a la memoria de DataTables
                    rowData.influx_time = data.ultima_actividad;
                    rowData.influx_status = data.online ? 'Online' : 'Offline';
                    
                    // Actualizamos la fila. Esto cambia el HTML automáticamente y lo deja listo para exportar/imprimir
                    row.data(rowData); 
                })
                .catch(error => {
                    console.error(`🔥 Error consultando InfluxDB para ${serie}:`, error);
                    rowData.influx_time = 'Error';
                    rowData.influx_status = 'Error';
                    row.data(rowData);
                });
            });
        }
    </script>
@endpush