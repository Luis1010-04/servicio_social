@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>{{ $titulo }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('unidades.medida.index') }}">Unidades</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Modificar Unidad de Medida</h5>

                        <form action="{{ route('unidades.medida.update', $item->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre o Símbolo de la Unidad</label>
                                <input type="text" 
                                       name="nombre" 
                                       id="nombre"
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre', $item->nombre) }}" 
                                       required>
                                
                                @error('nombre')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">Ejemplo: °C, %, Amperios, Voltios.</div>
                            </div>

                            <div class="alert alert-warning border-0 bg-light small mb-4">
                                <i class="bi bi-exclamation-triangle me-1 text-warning"></i>
                                <strong>Nota:</strong> Al cambiar este nombre, se actualizará automáticamente la etiqueta de medida en todos los componentes que utilicen esta unidad.
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-warning px-4">
                                    <i class="bi bi-arrow-clockwise"></i> Actualizar
                                </button>
                                <a href="{{ route('unidades.medida.index') }}" class="btn btn-secondary px-4">
                                    Cancelar
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