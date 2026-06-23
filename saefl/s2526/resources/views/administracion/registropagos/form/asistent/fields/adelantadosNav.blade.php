@php $estudiants = $representant->estudiants @endphp

<nav>
    <div class="nav nav-tabs  nav-fill" id="nav-tab" role="tablist">
        @foreach($estudiants as $estudiant)
            <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}}" id="nav-header-tab-estudiant-adelantados-{{$estudiant->id}}" data-toggle="tab" href="#nav-content-estudiant-adelantados-{{$estudiant->id}}" role="tab" aria-controls="nav-home" aria-selected="true"><b>{{$estudiant->short_name ?? ''}}</b></a>
        @endforeach
    </div>
</nav>

<div class="tab-content border border-top-0" id="nav-tabContent">
    @foreach($estudiants as $estudiant)
        <div class="tab-pane fade show {{($loop->iteration==1) ? 'active':''}}" id="nav-content-estudiant-adelantados-{{$estudiant->id}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$estudiant->id}}">
            <div class="p-2">
                <div class="row">
                    <div class="col">
                        @include('administracion.registropagos.form.asistent.fields.partials.adelantados')
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>


