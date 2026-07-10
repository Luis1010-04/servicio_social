@extends('layouts.main')

@section('titulo',$titulo)

@section('contenido')
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Equipos - Maestros</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Maestros</li>
            </ol>
        </nav>
        </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Administrar maestros</h5>
              <p>Adminstrar todos los maestros existentes en el sistema</p>
              <div class="d-flex justify-content-end">
                <a href="{{route('maestros.catalogo.create')}}" class="btn btn-success"> 
                  <i class="fa-solid fa-circle-plus"></i> Crear nuevo maestro 
                </a>
              </div>
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Modelo</th>
                    <th>descripción</th>
                    <th>Activo</th>

                    <th>Acciones</th>
                  </tr>
                </thead>
               <tbody>
                @foreach($datos as $dato)
                <tr>
                    <td> {{ $dato->id }}</td>
                    <td> {{ $dato->nombre }}</td>
                    <td> {{ $dato->modelo }}</td>
                    <td> {{ $dato->descripcion }}</td>
                    <td>
                        @if($dato->activo)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    
                    <td>
                      <a href="{{ route('maestros.catalogo.edit', $dato->id) }}" class="btn btn-warning">
                        <i class="fa-solid fa-pencil"></i>
                      </a>
                      <form action="{{ route('maestros.catalogo.destroy', $dato->id) }}" method="POST" style="display:inline;">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este modelo?.')">
                              <i class="fa-solid fa-trash"></i>
                          </button>
                      </form>
                    </td>
                    {{-- Botones para las acciones --}}
                </tr>
                @endforeach
              
               </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

    </main>
@endsection  