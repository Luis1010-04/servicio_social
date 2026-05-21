@extends('layouts.main')

@section('titulo',$titulo)

@section('contenido')
<main id="main" class="main">

    <div class="pagetitle">
      <h1> Eliminar Componente</h1>
      
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Eliminar Componente</h5>
            
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                    
              <form action="{{route('componentes.destroy', $componente->id)}}" method="POST">
                @csrf
                @method('DELETE') {{-- Método correcto para eliminar --}}
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre del componente</label>
                    <input type="text" class="form-control" value="{{ $componente->nombre }}" readonly>
                </div>
                
                <div class="mb-3">
                    <label for="esclavo_id" class="form-label">Pertenece al Esclavo:</label>
                    {{-- Usamos disabled porque readonly no aplica a select --}}
                    <select class="form-select" disabled>
                        @foreach($esclavos as $esc)
                            <option value="{{ $esc->id }}" {{ $componente->esclavo_id == $esc->id ? 'selected' : '' }}>
                                {{ $esc->nombre }} ({{ $esc->modelo }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="unidad_id" class="form-label">Unidad de medida</label>
                    <select class="form-select" disabled>
                        @foreach($unidades as $uni)
                            <option value="{{ $uni->id }}" {{ $componente->unidad_id == $uni->id ? 'selected' : '' }}>
                                {{ $uni->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo (Entrada/Salida)</label>
                    <input type="text" class="form-control" value="{{ $componente->tipo }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción del componente</label>
                    <textarea class="form-control" rows="3" readonly>{{ $componente->descripcion }}</textarea>
                </div>

                <div class="mt-3">
                    {{-- Cambiamos el estilo a Peligro (Rojo) para indicar eliminación --}}
                    <button type="submit" class="btn btn-danger"> 
                        <i class="fa-solid fa-trash-can"></i> Confirmar Eliminación
                    </button>
                    <a href="{{ route('componentes.index') }}" class="btn btn-secondary"> 
                        <i class="fa-solid fa-ban"></i> Cancelar
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
