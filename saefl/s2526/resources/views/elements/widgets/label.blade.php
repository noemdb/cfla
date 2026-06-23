{{-- variables de entrada class,id,panelTitle,badge,panelBody,panelFooter --}}

<div class="card card-{{ $class ?? 'default'}} ">

  <div class="card-header">

    <h5 class="card-title">

        <div class="container">
          <div class="row">
            <div class="col-sm-4">
              <i class="{{ $iconTitle ??  'default' }} text-{{ $class ?? 'default'}}"></i>
            </div>
            <div class="col-sm-8 text-{{ $class ??  'default'}}">
              <div class="row">
                <div class="col-sm-12">
                  <h2><span class="badge badge-{{ $class ?? 'default'}} float-right">{{ $total ?? ''}}</span></h2>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12 text-right ">
                  {{ $title ?? ''}}
                </div>
              </div>
            </div>
          </div>
        </div>

    </h5>

  </div>

  <div class="card-body p-1 m-1 ">

    {{-- <div class="card-header" id="headingOne"> --}}
      <a class="btn btn-link" data-toggle="collapse" href="#{{ $id ?? ''}}-bodycollapse" role="button" aria-expanded="false" aria-controls="{{ $id ?? ''}}-bodycollapse" style="text-decoration: none;">
        {{ $subtitle ??  ''}}...
      </a>
    {{-- </div> --}}

    @isset($body)

        <div class="collapse" id="{{ $id ?? ''}}-bodycollapse">
          <div class="card card-body p-1 m-1 border-0">
            {{ $body ?? ''}}
          </div>
        </div>

    @endisset

  </div>

  @isset($footer)
      <div class="card-footer text-muted">
        {{ $footer ?? ''}}
      </div>
  @endisset

</div>