@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">

  <div class="pagetitle">
    <h1>{{ $titulo }}</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
  </div>

  <section class="section dashboard">
    <div class="row">

      <div class="col-lg-12">
        <div class="row">

          <div class="col-xxl-3 col-md-6">
            <div class="card info-card customers-card">
              <div class="card-body">
                <h5 class="card-title">Usuarios <span>| Clientes</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                  </div>
                  <div class="ps-3">
                    <h6>{{ $stats['usuarios'] }}</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xxl-3 col-md-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <h5 class="card-title">Maestros <span>| Catálogo</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center text-primary">
                    <i class="bi bi-router"></i>
                  </div>
                  <div class="ps-3">
                    <h6>{{ $stats['maestros'] }}</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xxl-3 col-md-6">
            <div class="card info-card revenue-card">
              <div class="card-body">
                <h5 class="card-title">Esclavos <span>| Modelos</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center text-success">
                    <i class="bi bi-cpu"></i>
                  </div>
                  <div class="ps-3">
                    <h6>{{ $stats['esclavos'] }}</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xxl-3 col-md-6">
            <div class="card info-card">
              <div class="card-body">
                <h5 class="card-title">InfluxDB <span>| Conexión</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-hdd-network {{ $influxStatus == 'Online' ? 'text-success' : 'text-danger' }}"></i>
                  </div>
                  <div class="ps-3">
                    <span class="badge {{ $influxStatus == 'Online' ? 'bg-success' : 'bg-danger' }}">
                       {{ $influxStatus }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Resumen de Inventario</h5>
            <canvas id="adminLineChart" style="max-height: 300px;"></canvas>
            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new Chart(document.querySelector('#adminLineChart'), {
                  type: 'bar',
                  data: {
                    labels: ['Usuarios', 'Maestros', 'Esclavos', 'Unidades'],
                    datasets: [{
                      label: 'Total en Base de Datos',
                      data: [
                        {{ $stats['usuarios'] }}, 
                        {{ $stats['maestros'] }}, 
                        {{ $stats['esclavos'] }}, 
                        {{ $stats['unidades'] }}
                      ],
                      backgroundColor: ['#4154f1', '#2eca6a', '#ff771d', '#012970'],
                    }]
                  },
                  options: { 
                    responsive: true,
                    scales: { y: { beginAtZero: true } } 
                  }
                });
              });
            </script>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Equipos Recientes <span>| Maestros</span></h5>
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nombre/Modelo</th>
                  <th>Fecha Registro</th>
                </tr>
              </thead>
              <tbody>
                @forelse($ultimosMaestros as $maestro)
                <tr>
                  <td>
                    <strong>{{ $maestro->nombre ?? 'Sin nombre' }}</strong><br>
                    <small class="text-muted">{{ $maestro->modelo }}</small>
                  </td>
                  <td>
                    {{ $maestro->fecha_formateada }}
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="2" class="text-center">No hay equipos registrados.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </section>
</main>
@endsection