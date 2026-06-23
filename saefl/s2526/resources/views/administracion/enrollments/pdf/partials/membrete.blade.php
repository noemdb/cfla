<table cellspacing="1" cellpadding="1" class="table-sm" style="padding-top:0.4rem;font-size:0.6rem !important">
    <thead>
        <tr>
            <th scope="row" width="70px">
                <img width="70px" height="70px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
            </th>
            <th style="text-align: left;font-size:0.8rem !important;">
                <div class="title"><b>{{ $institucion->name }}</b></div>
                {{-- <div class="title"><b>República Bolivariana de Venezuela</b></div> --}}
                <div class="title"><b>Inscrito en el Ministerio del Poder Popular para la Educación</b></div>
                <div class="title"><b>Bajo el n°: S0427D221</b></div>
                <div class="title"><b>San Felipe, Estado Yaracuy</b></div>
                <div class="title"><b>Teléfonos: 0424-5891682 - 0414-5442298</b></div>
            </th>
            <th scope="row" width="70px">
                <div style="height: 10rem; width:8rem; border: 1px #ccc solid">

                    {{-- @php $photo_url = ($enrollment->photo_url) ? $enrollment->photo_url : $estudiant->logo ; @endphp --}}

                    {{-- <img style="width:3cm;height:4cm"  src="{{ asset($photo_url) }}"> --}}
                    @php $photo_url = ($estudiant) ? $estudiant->photo_url : 'images/avatar/estudiant/neutral.png' @endphp
                    @php $logo = ($estudiant) ? $estudiant->logo : 'images/avatar/estudiant/neutral.png' @endphp
                    <img style="width:3cm;height:4cm"  src="{{ ($photo_url) ? asset($photo_url) : asset($logo) }}">

                    <!-- width="100px" height="140px" -->
                </div>
            </th>
        </tr>
    </thead>
</table>
