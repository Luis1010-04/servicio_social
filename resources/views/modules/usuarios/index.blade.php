@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1>{{ $titulo }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-user-plus"></i> Agregar Usuario
        </a>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Administrar Cuentas y Roles</h5>
                
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th>id</th>
                                <th>Email</th>
                                <th>Nombre Completo</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datos as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->name }} {{ $item->apellido }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $item->usuario }}</span></td>
                                <td>
                                    <span class="badge {{ $item->rol == 'Admin' ? 'bg-primary' : 'bg-info' }}">
                                        {{ $item->rol }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input switch-estado" type="checkbox" 
                                               id="user-{{ $item->id }}" 
                                               data-id="{{ $item->id }}"
                                               {{ $item->activo ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-center">
                                <div class="btn-group">
                                    {{-- NUEVO BOTÓN: Gestionar Recursos --}}
                                    <a href="{{ route('users.recursos', $item->id) }}" 
                                    class="btn btn-sm btn-outline-info" 
                                    title="Gestionar Recursos (Ubicaciones, Maestros, Esclavos)">
                                        <i class="fa-solid fa-folder-tree"></i>
                                    </a>

                                    <a href="{{ route('users.edit', $item->id) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <form action="{{ route('users.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar usuario?')">
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
    </section>
</main>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Detectar cambio en el switch de estado
        $('.switch-estado').on('change', function() {
            let id = $(this).data('id');
            let estado = $(this).is(':checked') ? 1 : 0;
            
            cambiar_estado(id, estado);
        });
    });

    function cambiar_estado(id, estado) {
        $.ajax({
            type: "GET",
            // Ajustamos la URL a la que definiste en tu controlador
            url: "{{ url('usuarios/cambiar-estado') }}/" + id + "/" + estado,
            success: function(respuesta) {
                if (respuesta.success) {
                    Swal.fire({
                        position: "top-end",
                        icon: 'success',
                        title: '¡Éxito!',
                        text: respuesta.message,
                        showConfirmButton: false,
                        timer: 1500,
                        toast: true // Lo hace ver más moderno como notificación
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cambiar el estado.'
                    });
                    // Si falla, revertimos el switch visualmente
                    let checkbox = $('#user-' + id);
                    checkbox.prop('checked', !checkbox.prop('checked'));
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo comunicar con el servidor.'
                });
            }
        });
    }
</script>
@endpush  