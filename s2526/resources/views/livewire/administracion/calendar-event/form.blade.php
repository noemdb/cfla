{{-- resources/views/livewire/administracion/calendar-event/form.blade.php --}}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="date">Fecha del Evento *</label>
            <input type="date" 
                   class="form-control @error('calendar_event.date') is-invalid @enderror" 
                   id="date"
                   wire:model="calendar_event.date"
                   min="{{ date('Y-m-d') }}"
                   required>
            @error('calendar_event.date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="status_holidays">¿Es día feriado? *</label>
            <select class="form-control @error('calendar_event.status_holidays') is-invalid @enderror" 
                    id="status_holidays"
                    wire:model.defer="calendar_event.status_holidays"
                    required>
                <option value="1">Sí - Día Feriado (Afecta tasa de cambio)</option>
                <option value="0">No - Evento Regular</option>
            </select>
            @error('calendar_event.status_holidays')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">Nombre del Evento</label>
            <input type="text" 
                   class="form-control @error('calendar_event.name') is-invalid @enderror" 
                   id="name"
                   wire:model="calendar_event.name"
                   placeholder="Ingrese el nombre del evento"
                   maxlength="255">
            @error('calendar_event.name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="icon">Icono del Evento</label>
            <select class="form-control @error('calendar_event.icon') is-invalid @enderror" 
                    id="icon"
                    wire:model="calendar_event.icon">
                <option value="">Seleccione un icono</option>
                @foreach($iconosDisponibles as $iconClass => $iconDescription)
                    <option value="{{ $iconClass }}">
                        <i class="{{ $iconClass }}"></i> {{ $iconDescription }}
                    </option>
                @endforeach
            </select>
            @error('calendar_event.icon')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            
            {{-- Vista previa del icono seleccionado --}}
            @if($calendar_event->icon)
                <div class="mt-2 p-2 border rounded bg-light">
                    <small class="text-muted">Vista previa:</small>
                    <div class="d-flex align-items-center mt-1">
                        <i class="{{ $calendar_event->icon }} fa-lg text-primary mr-2"></i>
                        <span>{{ $iconosDisponibles[$calendar_event->icon] ?? 'Icono personalizado' }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="form-group">
    <label for="description">Descripción del Evento</label>
    <textarea class="form-control @error('calendar_event.description') is-invalid @enderror" 
              id="description"
              wire:model="calendar_event.description"
              rows="3"
              placeholder="Describa el evento o día feriado..."
              maxlength="500"></textarea>
    <small class="form-text text-muted">
        {{ strlen($calendar_event->description) }}/500 caracteres
    </small>
    @error('calendar_event.description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="observations">Observaciones Internas</label>
    <textarea class="form-control @error('calendar_event.observations') is-invalid @enderror" 
              id="observations"
              wire:model="calendar_event.observations"
              rows="2"
              placeholder="Observaciones adicionales para uso interno..."
              maxlength="300"></textarea>
    <small class="form-text text-muted">
        {{ strlen($calendar_event->observations) }}/300 caracteres
    </small>
    @error('calendar_event.observations')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Información sobre días feriados --}}
@if($calendar_event->status_holidays)
<div class="alert alert-warning mt-3">
    <i class="fas fa-info-circle"></i>
    <strong>Nota:</strong> Este evento está marcado como <strong>Día Feriado</strong>. 
    El sistema utilizará automáticamente la tasa de cambio del último día hábil anterior para los cálculos financieros.
</div>
@endif