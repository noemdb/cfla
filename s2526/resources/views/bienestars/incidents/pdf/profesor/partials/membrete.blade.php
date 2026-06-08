<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th scope="row" width="70px">
                <img width="70px" height="70px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
            </th>
            <th>
                <div class="title"><b>República Bolivariana de Venezuela</b></div>
                <div class="title"><b>Ministerio del Poder Popular para la Educación</b></div>
                <div class="title"><b>{{ $institucion->name }}</b></div>
                <div class="text-muted pt-0 pb-0"><b>Dirección de Académica</b></div>
            </th>
            <th scope="row" width="70px">
                <div style="height: 10rem; width:8rem; border: 1px #ccc solid">
                    {{-- <img style="width:3cm;height:4cm"  src="{{ asset($estudiant->photo_url) }}"> --}}
                    <img style="width:3cm;height:4cm"  src="{{ ($estudiant->photo_url) ? asset($estudiant->photo_url) : asset($estudiant->logo) }}">
                </div>
                {{-- <img width="100px" height="70px" class="card-img-top" src="{{ asset('images/avatar/amigoniano.png') }}"> --}}
            </th>
        </tr>
    </thead>
</table>
