<div class="row row-cols-1 row-cols-md-2 g-4">
    @forelse ($poll_options->sortBy('observations') as $option)
        <div class="col">
            <div class="card">

                @if ($option->image_url) <img src="{!! asset($option->image_url) !!}" class="card-img-top rounded-circle" alt="..."> @endif

                <div class="card-body small">
                    <div class="fw-light">{{$option->description}}.</div>
                    <div class="fw-bold">{{ Str::title(Str::lower($option->text)) ?? null}}</div>

                    <div class="card-footer">
                        <div class="d-grid gap-2 col-6 mx-auto">
                            <button type="button" class="btn btn-primary" wire:click="setOption({{$option->id}})" wire:loading.attr="disabled">Seleccionar</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @empty
        <div>No hay opciones</div>
    @endforelse
</div>

