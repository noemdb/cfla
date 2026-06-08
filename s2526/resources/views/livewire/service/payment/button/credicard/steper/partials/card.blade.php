<div class="mb-3">

    <fieldset {{ $status_send_token_bank == true ? 'disabled' : null }} {{ $modeTest == true ? '' : null }}>

        <div class="border rounded shadow-sm p-1 mb-3 text-muted" style="border-color: #FFEEAF !important">

            <div class="fw-bold h6">Tarjeta </div>

            <div class="continer-fluid text-start ">

                {{-- <div class="text-danger text-end small fw-bold">{{ $modeTest == true ? 'Tarjeta de debito de prueba' : null }}</div> --}}

                <div class="row g-3 mb-2">
                    <div class="col-sm-8 px-1">
                        <div class="flex-fill w-100 px-2">
                            <label for="card_number" class="font-weight-bold mb-0 text-left">Número:</label>
                            <input type="number" wire:model="card_number" class="form-control"
                                placeholder="N. de tarjeta" id="card_number">
                            @error('card_number')
                                <span class="text-danger small d-block text-right">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-4 px-1">

                        <div class="flex-fill px-2">
                            <label for="cvc" class=" font-weight-bold mb-0" >CVC:</label>
                            <div class="input-group mb-3">
                                <input type="{{$cvc_type}}" wire:model="cvc" class="form-control" placeholder="Pin CVC" name="cvc" id="cvc">
                                {{-- <input type="{{$card_pin_type}}" wire:model="card_pin" class="form-control" placeholder="Clave de 6 dígitos"> --}}
                                <button class="input-group-text" wire:click="setCVCType()">
                                    <i class="{{ ($cvc_type=='password') ? 'bi-eye-slash' : 'bi-eye' }}" id="glyphicon"></i>
                                </button>
                            </div>
                            @error('cvc') <span class="text-danger small d-block text-right">{{ $message }}</span> @enderror
                        </div>

                    </div>
                </div>

                <div class="row g-3 mb-3">

                    <div class="col-sm-6 px-1">

                        <div class="flex-fill px-2">
                            <label for="card_type" class=" font-weight-bold mb-0">Tipo</label>
                            <select class="form-select" wire:model="card_type" id="card_type">
                                <option></option>
                                {{-- <option value="TDC">CREDITO</option> --}}
                                <option value="TDD">DEBITO</option>
                            </select>
                            @error('card_type')
                                <span class="text-danger small d-block text-right">{{ $message }}</span>
                            @enderror
                        </div>


                    </div>

                    <div class="col-sm-6 px-1">
                        <div class="flex-fill px-2">
                            <label for="date_expiration" class=" font-weight-bold mb-0">Fecha de expiración: MM/AA</label>
                            <input type="text" wire:model="date_expiration" class="form-control" placeholder="MM/AA">
                            @error('date_expiration')
                                <span class="text-danger small d-block text-right">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>

                <div class="row g-3 mb-3">

                    <div class="col-sm-6 px-1">
                        <div class="flex-fill px-2">
                            <label for="account_type" class=" font-weight-bold mb-0">Cuenta</label>
                            <select class="form-select" wire:model="account_type" id="account_type">
                                <option></option>
                                <option value="AHORRO">AHORRO</option>
                                <option value="CORRIENTE">CORRIENTE</option>
                                <option value="PRINCIPAL">PRINCIPAL</option>
                            </select>
                            @error('account_type')
                                <span class="text-danger small d-block text-right">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-6 px-1">

                        <div class="flex-fill px-2">
                            <label for="card_pin" class=" font-weight-bold mb-0" >PIN/Clave de la tarjeja:</label>
                            <div class="input-group mb-3">
                                <input type="{{$card_pin_type}}" wire:model="card_pin" class="form-control" placeholder="Clave de hasta 6 dígitos" name="card_pin" id="card_pin">
                                <button class="input-group-text" wire:click="setCardPinType()">
                                    <i class="{{ ($card_pin_type=='password') ? 'bi-eye-slash' : 'bi-eye' }}" id="glyphicon"></i>
                                </button>
                            </div>
                            @error('card_pin') <span class="text-danger small d-block text-right">{{ $message }}</span> @enderror
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="border rounded shadow-sm p-1 " style="border-color: #FFEEAF !important">
            <div class="fw-bold h6">Titular</div>
            <div class="continer-fluid text-start ">

                <div class="row  g-3">
                    <div class="col-sm-4 px-1">
                        <div class="w-100 px-2">
                            <label for="holder_id_doc" class="font-weight-bold mb-0 text-left">Tipo de Ident.</label>
                            {{-- <select class="form-control input-group-text" wire:model="holder_id_doc" required> --}}
                            <select class="form-select" wire:model="holder_id_doc" id="holder_id_doc">
                                <option></option>
                                <option value="CI">CI</option>
                                <option value="RIF">RIF</option>
                            </select>
                            @error('holder_id_doc') <span class="text-danger small d-block text-right">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-sm-8 px-1">

                        <div class="text-start px-2">
                            <label for="holder_id" class="font-weight-bold mb-0 text-left">N. RIF/CI:</label>
                            <div class="d-flex mt-0 pt-0">
                                <div class="pe-2">
                                    <select class="form-select" wire:model="holder_type" id="holder_type" style="width: 5rem !important">
                                        <option></option>
                                        <option value="V">V</option>
                                        <option value="J">J</option>
                                        <option value="C">C</option>
                                        <option value="E">E</option>
                                        <option value="G">G</option>
                                        <option value="P">P</option>
                                    </select>
                                </div>
                                <div class=" flex-grow-1">
                                    <input type="number" wire:model="holder_id" class="form-control" placeholder="N. RIF/CI" id="holder_id">
                                </div>
                            </div>
                            @error('holder_id') <span class="text-danger small d-block text-right">{{ $message }}</span> @enderror
                        </div>

                        {{-- <div class="flex-fill flex-shrink-1 px-2">
                            <label for="holder_id" class="font-weight-bold mb-0 text-left">N. RIF/CI:</label>
                            <div class="input-group-text p-0 m-0 border-0">
                                <select class="form-select input-group-text w-25" wire:model="holder_type" id="holder_type">
                                    <option></option>
                                    <option value="V">V</option>
                                    <option value="J">J</option>
                                    <option value="C">C</option>
                                    <option value="E">E</option>
                                    <option value="G">G</option>
                                    <option value="P">P</option>
                                </select>
                                <input type="text" wire:model="holder_id" class="form-control w-75" placeholder="N. RIF/CI" id="holder_id">
                            </div>
                            @error('holder_id') <span class="text-danger small d-block text-right">{{ $message }}</span> @enderror
                        </div> --}}
                    </div>
                </div>

                <div class="row  g-3">
                    <div class="col px-1">
                        <div class="flex-fill flex-shrink-1 px-2 mt-2">
                            <label for="holder_name" class="font-weight-bold mb-0 text-left">Nombre:</label>
                            <input type="text" wire:model="holder_name" id="holder_name" class="form-control"
                                placeholder="Nombre">
                            @error('holder_name')
                                <span class="text-danger small d-block text-right">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </fieldset>

</div>


