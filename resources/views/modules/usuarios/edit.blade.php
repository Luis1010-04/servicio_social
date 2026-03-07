@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>{{ $titulo }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-center">Modificar Datos del Usuario</h5>
                        <hr>

                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nombre</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" id="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control @error('apellido') is-invalid @enderror" 
                                           name="apellido" id="apellido" value="{{ old('apellido', $user->apellido) }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="usuario" class="form-label">Nombre de Usuario</label>
                                    <input type="text" class="form-control @error('usuario') is-invalid @enderror" 
                                           name="usuario" id="usuario" value="{{ old('usuario', $user->usuario) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" id="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Nueva Contraseña</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           name="password" id="password">
                                    <div class="form-text">Dejar en blanco si no deseas cambiarla.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="rol" class="form-label">Rol del Sistema</label>
                                    <select name="rol" id="rol" class="form-select" required>
                                        <option value="Admin" {{ $user->rol == 'Admin' ? 'selected' : '' }}>Administrador</option>
                                        <option value="Usuario" {{ $user->rol == 'Usuario' ? 'selected' : '' }}>Usuario Estándar</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-warning px-4">
                                    <i class="fa-regular fa-floppy-disk me-1"></i> Actualizar Usuario
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary px-4">
                                    <i class="fa-solid fa-ban me-1"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection