<div>

    <div class="card card-primary mt-2 bd-callout bd-callout-gray">
        
        <div class="card-body p-1 m-1">

            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        @include('livewire.profesor.competition.table.index')
                    </div>

                    @if ($modeQuestion)
                        <div class="col-8">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="alert-danger py-3 px-2 text-dark font-weight-bolder rounded">
                                            <b>Áreas de Formación: {{$pensum->fullname}}</b>
                                            <button type="button" class="close" wire:click='close()'>
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        {{-- @include('livewire.profesor.competition.table.questions') --}}
                                        @php $key = Str::random().'-'.$debate_id; @endphp
                                        <livewire:profesor.competition.question-component :pensum_id="$pensum_id" :wire:key="$key"/>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    @endif
                    
                </div>
            </div>

            


        </div>

    </div>

</div>
