@extends('layouts.main')

@section('titulo',$titulo)

@section('contenido')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Mis Equipos</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Mis Maestrosss</li>
                </ol>
            </nav>
        </div><section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Mis Dispositivos Maestros</h5>
                            <p>Aquí puedes ver y administrar los maestros que tienes asignados en tus diferentes ubicaciones.</p>
                            
                            <div class="d-flex justify-content-end mb-3">
                                {{-- Cambiamos a la ruta del controlador de Usuario --}}
                                <a href="{{ route('maestros.user.create') }}" class="btn btn-success"> 
                                    <i class="fa-solid fa-link"></i> Asignar nuevo maestro 
                                </a>
                            </div>

                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Mi Nombre</th> {{-- El nombre que el usuario le puso --}}
                                        <th>Modelo Base</th> {{-- Viene del join con maestros_catalogo --}}
                                        <th>Localización</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($misMaestros as $dato)
                                    <tr>
                                        <td>{{ $dato->id }}</td>
                                        <td><strong>{{ $dato->nombre }}</strong></td>
                                        <td><span class="badge bg-secondary">{{ $dato->modelo_hardware }}</span></td>
                                        <td>{{ $dato->localizacion }}</td>
                                        <td>
                                            @if($dato->topico != 'vacio')
                                                <span class="badge bg-success">Configurado</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                {{-- Este botón llevará a la administración de esclavos --}}
                                                <a href="{{ route('maestros.user.manage', $dato->id) }}" class="btn btn-info btn-sm" title="Administrar Esclavos">
                                                    <i class="fa-solid fa-microchip"></i> Administrar
                                                </a>

                                                {{-- Formulario para desvincular (Eliminar de maestros_usuarios) --}}
                                                <form action="{{ route('maestros.user.destroy', $dato->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Deseas desvincular este maestro de tu cuenta?')">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection