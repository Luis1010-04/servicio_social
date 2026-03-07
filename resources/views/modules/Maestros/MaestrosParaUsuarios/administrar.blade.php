@extends('layouts.main')

@section('titulo',$titulo)

@section('contenido')
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Administrar Maestro:</h1>
        <hr> 
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{route('maestros_catalogo.index')}}">Maestros</a>
            <li class="breadcrumb-item active">{{$maestro->nombre}}</li>
            </ol>
        </nav>
        </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Administrar maestro: {{$maestro->nombre}}</h5>
              <p>Adminstrar todos los esclavos asociados al maestro seleccionado</p>
                <div class="d-flex justify-content-end">
                    <a href="{{route('maestro.esclavo.asignarNuevoEsclavo', $maestro->id)}}" class="btn btn-info"><i class="fa-solid fa-circle-plus"></i> Asignar nuevo esclavo</a>
              </div>
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Id</th>
                    <th>Nombre esclavo</th>
                    <th>Modelo</th>
                    <th>Ubicación</th>
                    <th>Activo</th>
                    <th>Administrar</th>{{--Chequedo de estado del esclavo en tiempo real--}}
                    <th>Acciones</th>
                  </tr>
                </thead>
               <tbody>
                @foreach ($esclavos as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            {{-- Usamos el alias de la tabla intermedia --}}
                                            <td>{{ $item->nombre }}</td>
                                            <td>{{ $item->modelo }}</td>
                                            <td>{{ $item->nombre_ubicacion ?? 'No asignada' }}</td>
                                            <td>
                                                @if($item->activo)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{route('maestro.esclavo.administrar', $item->id)}}" class="btn btn-sm btn-outline-info">
                                                    <i class="fa-solid fa-microchip"></i> Administrar
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-warning" title="Editar vínculo">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                {{-- Sugerencia: El botón X debería llamar a una función de desvincular --}}
                                                <a href="#" class="btn btn-sm btn-danger" title="Desvincular">
                                                    <i class="fa-solid fa-link-slash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
               </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
      
      {{-- Modal para asignar nuevo esclavo al maestro --}}
    </section>

    </main>
@endsection  