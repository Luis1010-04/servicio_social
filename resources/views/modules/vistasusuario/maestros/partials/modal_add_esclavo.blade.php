<div class="modal fade" id="modalAddEsclavo" tabindex="-1" aria-labelledby="modalAddEsclavoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAddEsclavoLabel text-white">
                    <i class="bi bi-plus-circle me-2"></i>Nuevo Esclavo para {{ $maestro->nombre }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('user.esclavos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="maestro_id" value="{{ $maestro->id }}">

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Tipo de Sensor/Actuador</label>
                            <select name="esclavo_id" class="form-select" required>
                                <option value="" disabled selected>Selecciona del catálogo...</option>
                                @foreach($catalogoEsclavos as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }} ({{ $cat->modelo }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Número de Serie (S/N)</label>
                            <input type="text" name="numero_serie" class="form-control" placeholder="Ej: SN-TEMP-001" required>
                            <div class="form-text">Ubicado en la etiqueta del sensor.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Sensor Ventana" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ubicación</label>
                            <select name="ubicacion_id" class="form-select" required>
                                @foreach($ubicaciones as $ubi)
                                    {{-- Cambiamos $maestro->ubicacion_id por $maestro->localizacion --}}
                                    <option value="{{ $ubi->id }}" {{ $maestro->localizacion == $ubi->nombre ? 'selected' : '' }}>
                                        {{ $ubi->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Esclavo</button>
                </div>
            </form>
        </div>
    </div>
</div>