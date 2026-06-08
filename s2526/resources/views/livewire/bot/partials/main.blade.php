<div class="w-100" wire:key="{{$area ?? null}}">
    <div class="p-1 mx-1 shadow-sm">
        <div class="bg-light">
            <div class="top-0 w-100" style="overflow: auto;display: flex; flex-direction:column;">
                @foreach ($history as $data)
                        <div class="d-flex justify-content-end m-2">
                            <div class="text-end alert alert-secondary position-relative m-2" style="background-color: rgb(240, 240, 240)">
                                {{ $data['messege'] }}
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-light p-2 shadow">
                                    <i class="bi bi-person-fill text-secondary" style="font-size: 1rem;"></i>
                                    <span class="visually-hidden">Usuario</span>
                                </span>
                            </div>
                        </div>
                    <code>
                        <div class="text-start overflow-auto">
                            <div class="d-flex justify-content-start  m-2 p-2">
                                <div class="text-start alert alert-success position-relative m-2">
                                    <pre style="white-space:pre-line; font-family: 'Roboto', sans-serif;">
                                        {!! $data['response'] !!}
                                    </pre>
                                    <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success p-2 shadow">
                                        <i class="bi bi-robot text-light" style="font-size: 1rem;"></i>
                                        <span class="visually-hidden">Usuario</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </code>

                @endforeach

            </div>
            <div class="w-100">
                <div class="input-group w-100">
                    <input wire:model="messege" wire:keydown.enter="send()" type="text" class="form-control" placeholder="Mensaje" aria-label="Mensaje" aria-describedby="button-addon2">
                    <button wire:click="send()" class="btn btn-outline-secondary" type="button" id="button-addon2">Enviar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="contentBot{{$area ?? null}}"></div>
</div>
