<div class="dropdown btn btn-{{ $class ?? 'default'}} p-0 m-0" title="{{ $title ?? 'default'}}">

  <button class="btn btn-{{ $class ?? 'default'}} dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="{{ $icon ?? 'default'}}"></i>
  </button>

  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

    {{ $dropdown ?? 'dropdown' }}

  </div>

</div>