@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Panel de Monitoreo</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('user.maestros.index') }}">Mis Maestros</a></li>
                <li class="breadcrumb-item active">Monitor: {{ $esclavo->nombre }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8"> <div class="card shadow-sm text-center">
                    <div class="card-header bg-white border-0 pt-4">
                        <span class="badge bg-success animated-pulse">Conectado</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fs-4">{{ $esclavo->nombre }}</h5>
                        <p class="text-muted small">{{ $esclavo->modelo }} | S/N: {{ $esclavo->numero_serie }}</p>
                        
                        <div class="py-4">
                            <div class="row g-3" id="contenedor-lecturas">
                                <div class="col-12 text-muted">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    Cargando sensores...
                                </div>
                            </div>
                            
                            {{-- Fecha de actualización global --}}
                            <p class="mt-4 text-muted style="font-size: 0.8rem;">
                                <i class="bi bi-clock"></i> Última sincronización: <span id="fecha-lectura">---</span>
                            </p>
                        </div>

                        <hr>
                        <p class="small text-muted">Tópico de escucha: <br> 
                            <code>{{ $esclavo->topico_maestro }}/{{ $esclavo->numero_serie }}</code>
                        </p>
                    </div>
                    <div class="card-footer bg-light border-0 py-3">
                        <a href="{{ route('user.maestros.administrar', $esclavo->maestro_id) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver al Maestro
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@push('scripts')
<script>
    let intervaloActualizacion;
// --- 1. FUNCIÓN PARA CONTROLAR EL RELEVADOR (MQTT) ---
function cambiarEstadoActuador(nombreCampo, nuevoEstado, boton) {
    const esclavoId = "{{ $esclavo->id }}";
    const url = "{{ url('mis-equipos/componente') }}/" + esclavoId + "/controlar";


    const payload = {
        nombre_field: nombreCampo,
        estado: nuevoEstado
    };

    // LOG PARA VER EL MENSAJE EN LA CONSOLA
    console.log("%c ENVIANDO COMANDO MQTT", "color: #ff9900; font-weight: bold; font-size: 12px;");
    console.log("Destino (URL):", url);
    console.log("Cuerpo del mensaje (JSON):", payload);
    // 1. Efecto visual inmediato (Feedback)
    const cardBody = boton.closest('.card-body');
    const badge = cardBody.querySelector('.badge');
    const icono = cardBody.querySelector('i.bi');
    const textoOriginal = boton.innerHTML;

    boton.disabled = true;
    boton.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Enviando...`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            nombre_field: nombreCampo,
            estado: nuevoEstado
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Comando MQTT enviado:", data);

        // 2. CAMBIO MANUAL EN PANTALLA (Para no esperar los 5 segundos)
        if (typeof intervaloActualizacion !== 'undefined') {
            clearInterval(intervaloActualizacion); 
        }
        if (nuevoEstado == 1) {
            badge.className = "badge bg-success";
            badge.innerText = "ENCENDIDO";
            boton.className = "btn btn-outline-danger btn-sm w-100 shadow-sm fw-bold";
            boton.innerHTML = `<i class="bi bi-power"></i> Apagar Equipo`;
            // Cambiar evento para la próxima vez
            boton.setAttribute('onclick', `cambiarEstadoActuador('${nombreCampo}', 0, this)`);
            if(icono) icono.className = "bi bi-toggle-on text-success fs-2 opacity-75";
        } else {
            badge.className = "badge bg-danger";
            badge.innerText = "APAGADO";
            boton.className = "btn btn-outline-success btn-sm w-100 shadow-sm fw-bold";
            boton.innerHTML = `<i class="bi bi-power"></i> Encender Equipo`;
            // Cambiar evento para la próxima vez
            boton.setAttribute('onclick', `cambiarEstadoActuador('${nombreCampo}', 1, this)`);
            if(icono) icono.className = "bi bi-toggle-off text-secondary fs-2 opacity-75";
        }
       setTimeout(function() {
            actualizarLecturas(); // Confirmación única
            
            if (typeof intervaloActualizacion !== 'undefined') {
                 // Mantén el intervalo en 10000 (10 segundos) para no saturar
                 intervaloActualizacion = setInterval(actualizarLecturas, 10000);
            }
        }, 6000);
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Error al controlar el dispositivo");
        boton.innerHTML = textoOriginal;
    })
    .finally(() => {
        boton.disabled = false;
    });
}

// --- 2. FUNCIÓN ÚNICA PARA ACTUALIZAR LECTURAS (SENSORES) ---
function actualizarLecturas() {
    const esclavoId = "{{ $esclavo->id }}";

    const url = "{{ url('mis-equipos/esclavo') }}/" + esclavoId + "/ultima-lectura";
    
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('No se encontraron lecturas');
            return response.json();
        })
        .then(data => {
            const contenedor = document.getElementById('contenedor-lecturas');
            const fechaEl = document.getElementById('fecha-lectura');
            
            if (!data || data.length === 0) {
                contenedor.innerHTML = '<div class="col-12 text-warning">Esperando datos de los sensores...</div>';
                return;
            }

            contenedor.innerHTML = ''; 

            data.forEach(sensor => {
                let icono = 'bi-cpu';
                let colorIcono = 'text-primary';
                const nombreLower = sensor.nombre.toLowerCase();

                // Lógica de Iconos
                if(nombreLower.includes('temp')) { icono = 'bi-thermometer-half'; colorIcono = 'text-danger'; }
                else if(nombreLower.includes('hum')) { icono = 'bi-droplet-fill'; colorIcono = 'text-info'; }
                else if(nombreLower.includes('luz')) { icono = 'bi-sun-fill'; colorIcono = 'text-warning'; }
                else if(nombreLower.includes('rele')) {
                    icono = sensor.valor == 1 ? 'bi-toggle-on' : 'bi-toggle-off';
                    colorIcono = sensor.valor == 1 ? 'text-success' : 'text-secondary';
                }

                let botonesHTML = '';
                let valorMostrar = sensor.valor;

                // Lógica para Relevadores
                if(nombreLower.includes('rele')) {
                    const esEncendido = (sensor.valor == 1);
                    valorMostrar = esEncendido ? '<span class="badge bg-success">ENCENDIDO</span>' : '<span class="badge bg-danger">APAGADO</span>';
                    
                    const nuevoEstado = esEncendido ? 0 : 1;
                    const claseBoton = esEncendido ? 'btn-outline-danger' : 'btn-outline-success';
                    const textoBoton = esEncendido ? 'Apagar Equipo' : 'Encender Equipo';

                    botonesHTML = `
                        <div class="mt-3 border-top pt-2">
                            <button onclick="cambiarEstadoActuador('${sensor.nombre}', ${nuevoEstado}, this)" 
                                    class="btn ${claseBoton} btn-sm w-100 shadow-sm fw-bold">
                                <i class="bi bi-power"></i> ${textoBoton}
                            </button>
                        </div>
                    `;
                }

                contenedor.innerHTML += `
                    <div class="col-md-6 mb-3">
                        <div class="card border shadow-none h-100 bg-light">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="text-start">
                                        <h6 class="text-muted small mb-1 text-uppercase">${sensor.nombre.replace('_', ' ')}</h6>
                                        <div class="d-flex align-items-baseline">
                                            <h3 class="mb-0 fw-bold">${valorMostrar ?? '--'}</h3>
                                        </div>
                                    </div>
                                    <div class="ms-auto">
                                        <i class="bi ${icono} ${colorIcono} fs-2 opacity-75"></i>
                                    </div>
                                </div>
                                ${botonesHTML} 
                            </div>
                        </div>
                    </div>`;
                
                if(sensor.created_at) {
                    fechaEl.innerText = new Date(sensor.created_at).toLocaleString();
                }
            });
        })
        .catch(error => {
            console.error('Error al actualizar:', error);
        });
}

// --- 3. INICIO AUTOMÁTICO (CORREGIDO) ---
document.addEventListener('DOMContentLoaded', function() {
    actualizarLecturas(); // Carga inicial
    
    // Solo una vez y guardando la referencia
    intervaloActualizacion = setInterval(actualizarLecturas, 10000); 
});
</script>
@endsection