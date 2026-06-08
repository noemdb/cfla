@foreach ($pestudios as $pestudio)

<div class="card mb-3">
  <div class="card-header alert-info">
    {{$pestudio->name ?? ''}}
  </div>
  <div class="card-body p-1">
    @foreach ($pestudio->grados as $grado)

    <div class="card">
      <div class="card-header alert-success">
        {{$grado->name ?? ''}}
      </div>

      <div class="card-body p-1">
        <div class="row pb-2 ">
          @foreach ($grado->seccions as $seccion)

          <div class="col-sm-6 col-md-6 col-lg-6">
            <ul class="list-group">
              <li class="list-group-item list-group-item-secondary font-weight-bold">
                Sección {{$seccion->name ?? ''}}
              </li>
              @foreach ($seccion->inscripcions as $inscripcion)
              <li class="list-group-item">
                <a href="#" class="badge badge-info float-right p-2">
                  <i class="fa fa-edit" aria-hidden="true"></i>
                </a>
                <span>{{$inscripcion->estudiant->fullname}} </span>
                <br />
                <span class="text-muted"
                  >CI: {{$inscripcion->estudiant->ci_estudiant}}
                </span>
              </li>
              @endforeach
            </ul>
          </div>

          @endforeach
        </div>
      </div>
    </div>

    <hr />

    @endforeach
  </div>
</div>

@endforeach