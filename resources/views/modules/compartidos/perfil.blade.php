@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Perfil</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item">Usuarios</li>
                <li class="breadcrumb-item active">Perfil</li>
            </ol>
        </nav>
    </div>

    <section class="section profile">
        <div class="row">
            {{-- Columna Izquierda --}}
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                        {{-- Imagen Dinámica --}}
                        <img src="{{ auth()->user()->imagen_url ? asset('storage/' . auth()->user()->imagen_url) : asset('assets/img/profile-img.jpg') }}" 
                             alt="Profile" class="rounded-circle">
                        <h2>{{ auth()->user()->name }} {{ auth()->user()->apellido }}</h2>
                        <h3>{{ auth()->user()->rol }}</h3>
                    </div>
                </div>
            </div>

            {{-- Columna Derecha --}}
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body pt-3">
                        <ul class="nav nav-tabs nav-tabs-bordered">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Resumen</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Editar Perfil</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Cambiar Contraseña</button>
                            </li>
                        </ul>

                        <div class="tab-content pt-2">
                            {{-- Tab Resumen --}}
                            <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                <h5 class="card-title">Detalles del Perfil</h5>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label ">Nombre Completo</div>
                                    <div class="col-lg-9 col-md-8">{{ auth()->user()->name }} {{ auth()->user()->apellido }}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Nombre de Usuario</div>
                                    <div class="col-lg-9 col-md-8">{{ auth()->user()->usuario }}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Email</div>
                                    <div class="col-lg-9 col-md-8">{{ auth()->user()->email }}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Rol del Sistema</div>
                                    <div class="col-lg-9 col-md-8">{{ auth()->user()->rol }}</div>
                                </div>
                            </div>

                            {{-- Tab Editar --}}
                            <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
                                <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="row mb-3">
                                        <label class="col-md-4 col-lg-3 col-form-label">Imagen de Perfil</label>
                                        <div class="col-md-8 col-lg-9">
                                            <img src="{{ auth()->user()->imagen_url ? asset('storage/' . auth()->user()->imagen_url) : asset('assets/img/profile-img.jpg') }}" alt="Profile">
                                            <div class="pt-2">
                                                <input type="file" name="imagen_url" class="form-control" accept="image/*">
                                                <small class="text-muted">Formatos permitidos: JPG, PNG. Máx 2MB.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="name" class="col-md-4 col-lg-3 col-form-label">Nombre</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="name" type="text" class="form-control" id="name" value="{{ auth()->user()->name }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="apellido" class="col-md-4 col-lg-3 col-form-label">Apellido</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="apellido" type="text" class="form-control" id="apellido" value="{{ auth()->user()->apellido }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="usuario" class="col-md-4 col-lg-3 col-form-label">Usuario</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="usuario" type="text" class="form-control" id="usuario" value="{{ auth()->user()->usuario }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="email" type="email" class="form-control" id="Email" value="{{ auth()->user()->email }}">
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                    </div>
                                </form>
                            </div>

                            {{-- Tab Password --}}
                            <div class="tab-pane fade pt-3" id="profile-change-password">
                                <form action="{{ route('perfil.password') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row mb-3">
                                        <label for="current_password" class="col-md-4 col-lg-3 col-form-label">Contraseña Actual</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="current_password" type="password" class="form-control" id="current_password" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="newpassword" class="col-md-4 col-lg-3 col-form-label">Nueva Contraseña</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="newpassword" type="password" class="form-control" id="newpassword" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="renewpassword" class="col-md-4 col-lg-3 col-form-label">Repetir Nueva Contraseña</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="renewpassword" type="password" class="form-control" id="renewpassword" required>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection