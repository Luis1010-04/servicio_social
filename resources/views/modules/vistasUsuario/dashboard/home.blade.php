@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Bienvenido</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('user.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            @forelse($esclavos as $esclavo)
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-cpu text-primary"></i> {{ $esclavo->nombre_esclavo }} 
                                <small class="text-muted">({{ $esclavo->modelo }})</small>
                            </h5>

                            <div style="height: 300px; position: relative;">
                                <canvas id="chart-{{ $esclavo->numero_serie }}"></canvas>
                            </div>

                            <hr>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Actuadores / Estados:</strong>
                                <div id="actuadores-{{ $esclavo->numero_serie }}">
                                    <span class="badge bg-light text-dark border">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info shadow-sm">
                        <i class="bi bi-info-circle me-2"></i> No tienes dispositivos vinculados actualmente.
                    </div>
                </div>
            @endforelse
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const charts = {}; 
    const paleta = ['#4361ee', '#f72585', '#4cc9f0', '#7209b7', '#ff9f1c', '#2ec4b6'];

    function fetchUpdate() {
        // Esta ruta debe devolver el JSON con { serie: { labels: [], datasets: {} } }
        fetch("{{ route('user.dashboard.data') }}") 
            .then(res => res.json())
            .then(data => {
                if (Object.keys(data).length === 0) return;

                Object.keys(data).forEach(serie => {
                    const info = data[serie];
                    actualizarGrafica(serie, info);
                    if (info.ultimo_estado) {
                        actualizarActuadores(serie, info.ultimo_estado);
                    }
                });
            })
            .catch(err => console.error("Error en Dashboard:", err));
    }

    function actualizarGrafica(serie, info) {
        const ctx = document.getElementById(`chart-${serie}`);
        if (!ctx || !info.datasets) return;

        const labels = info.labels;
        const datasetsKeys = Object.keys(info.datasets);

        if (!charts[serie]) {
            // Inicialización
            charts[serie] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasetsKeys.map((key, i) => ({
                        label: key.toUpperCase(),
                        data: info.datasets[key],
                        borderColor: paleta[i % paleta.length],
                        backgroundColor: paleta[i % paleta.length] + '22',
                        tension: 0.3,
                        pointRadius: 1,
                        fill: true
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { position: 'top', labels: { boxWidth: 10 } } 
                    },
                    scales: { 
                        x: { grid: { display: false } },
                        y: { beginAtZero: false } 
                    }
                }
            });
        } else {
            // Actualización dinámica
            charts[serie].data.labels = labels;
            datasetsKeys.forEach((key, i) => {
                if (charts[serie].data.datasets[i]) {
                    charts[serie].data.datasets[i].data = info.datasets[key];
                }
            });
            charts[serie].update('none'); // Update sin animaciones pesadas
        }
    }

    function actualizarActuadores(serie, ultimoEstado) {
        const container = document.getElementById(`actuadores-${serie}`);
        if (!container) return;

        let html = '';
        Object.keys(ultimoEstado).forEach(key => {
            const val = ultimoEstado[key];
            // Solo mostramos como Badge si es 0 o 1 (Actuadores)
            if (val === 0 || val === 1) {
                const color = val === 1 ? 'success' : 'danger';
                const text = val === 1 ? 'ON' : 'OFF';
                html += `<span class="badge bg-${color} shadow-sm me-1" style="font-size: 0.7rem;">
                            ${key.toUpperCase()}: ${text}
                         </span>`;
            }
        });
        
        if (html !== '') container.innerHTML = html;
    }

    // Ejecución
    document.addEventListener("DOMContentLoaded", () => {
        fetchUpdate(); // Primera carga
        setInterval(fetchUpdate, 15000); // Refresco cada 15s
    });
</script>
@endsection