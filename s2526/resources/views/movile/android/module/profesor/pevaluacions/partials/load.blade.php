@php
    $estudiant_count = count($list_estudiants);
    $current = $index + 1;
@endphp
<div class="card shadow">
    <div class="alert alert-secondary alert-dismissible fade show border-0 py-2 my-0 shadow-sm small" role="alert">
        <div class="fw-bold">{{ $evaluacion->description }}</div>
        <button type="button" wire:click="close()" class="btn-close p-2 fw-bold" data-bs-dismiss="alert"
            aria-label="Close"></button>
    </div>
    <div class="d-flex justify-content-center p-2">
        <img class="card-img-top img-thumbnail"
            src="{{ isset($estudiant->logo) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}"
            alt="Card image cap" style="max-width: 5rem">
    </div>
    <div class="card-body px-2">

        <div class="card-title small fw-bold text-center text-muted">
            {{ $estudiant->fullname }}
            <div class=" fw-normal small">CI: {{ $estudiant->ci_estudiant }}</div>
            <div class=" fw-normal small">N. {{ $index + 1 }} / {{ $estudiant_count }}</div>
        </div>

        <div class="d-flex py-2 justify-content-center p-2 m-2">
            <div>
                @php
                    $class = empty($nota) ? 'border-danger' : null;
                    $lapso = $evaluacion->lapso;
                @endphp
                @if ($lapso->status_preclosing)
                    {!! Form::select('nota', $pevaluacion_list_nota, $nota, [
                        'wire:model' => 'nota',
                        'class' => 'form-select form-select-lg  fw-bold' . $class,
                    ]) !!}
                @else
                    <div class="border rounded p-4 m-4 text-center display-4 fw-bold">{{ $nota }}</div>
                    {{-- <div class="small text-muted">No se puede modificar</div> --}}
                @endif


                @if (Session::has('operp_ok'))
                    <div class="alert alert-success fade show p-1 m-1 mt-2 small fw-bold" role="alert">
                        {{-- <span class="badge rounded-pill text-bg-success p-2"><i class="bi bi-check"></i></span> --}}
                        {{ Session::get('operp_ok') }}
                    </div>
                @endif
            </div>

            {{-- <div wire:loading wire:target="updatedNota">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div> --}}
        </div>

        <div class="d-flex justify-content-around py-2">
            <div class="btn-group" role="group" aria-label="Basic example">
                <a wire:click="home()" class="btn btn-dark btn-lg {{ $current == 1 ? ' disabled ' : null }}"
                    href="#" role="button">
                    <i class="bi bi-skip-backward-fill" style="font-size: 1rem"></i>
                </a>
                <a wire:click="previus()" class="btn btn-primary btn-lg {{ $current == 1 ? ' disabled ' : null }}"
                    href="#" role="button">
                    <i class="bi bi-caret-left-fill" style="font-size: 1rem"></i>
                </a>
            </div>
            {{-- <a wire:click="home()" class="btn btn-dark  btn-lg" href="#" role="button">
                <i class="bi bi-house-door-fill" style="font-size: 1rem"></i>
            </a> --}}
            <div class="btn-group" role="group" aria-label="Basic example">
                <a wire:click="next()"
                    class="btn btn-primary btn-lg {{ $current == $estudiant_count ? ' disabled ' : null }}"
                    href="#" role="button">
                    <i class="bi bi-caret-right-fill" style="font-size: 1rem"></i>
                </a>
                <a wire:click="last()"
                    class="btn btn-dark btn-lg {{ $current == $estudiant_count ? ' disabled ' : null }}" href="#"
                    role="button">
                    <i class="bi bi-skip-forward-fill" style="font-size: 1rem"></i>
                </a>
            </div>
        </div>

        @php
            $width = $estudiant_count ? (100 * $current) / $estudiant_count : null;
            $width = round($width);
        @endphp
        <div class="progress mt-2" role="progressbar" aria-label="Default striped example"
            aria-valuenow="{{ $width }}" aria-valuemin="0" aria-valuemax="100" style="height: 0.2rem">
            <div class="progress-bar progress-bar-striped" style="width: {{ $width }}%"></div>
        </div>

    </div>
</div>
