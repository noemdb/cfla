<div>

    <div class="card border-0">

        <div class="card-body">

            @php
                $general = 'general-day-'.$assit_day->id;
                $days = 'days-'.$assit_day->id;
            @endphp

            <nav>
                <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-{{$general}}-tab" data-toggle="tab" href="#nav-{{$general}}" role="tab" aria-controls="nav-{{$general}}" aria-selected="true">Datos Generales <small class="text-muted"> - Semanas</small></a>
                    <a class="nav-item nav-link" id="nav-{{$days}}-tab" data-toggle="tab" href="#nav-{{$days}}" role="tab" aria-controls="nav-{{$days}}" aria-selected="false">Días</a>
                </div>
            </nav>
            <div class="tab-content border border-top-0" id="nav-tabContent">
                <div class="tab-pane fade show active p-4 shadow" id="nav-{{$general}}" role="tabpanel" aria-labelledby="nav-{{$general}}-tab">
                    <livewire:administracion.assist-control.assit-day.form-component :id="$assit_day->id" :key="'assit-days-form-'.$assit_day->id" />
                </div>
                <div class="tab-pane fade p-4 shadow" id="nav-{{$days}}" role="tabpanel" aria-labelledby="nav-profile-tab">
                    {{-- <livewire:administracion.assist-control.assit-day.main-component :id="$assit_day->id" :key="'assit-day-main-'.$assit_day->id" /> --}}
                </div>
            </div>

        </div>
    </div>

</div>
