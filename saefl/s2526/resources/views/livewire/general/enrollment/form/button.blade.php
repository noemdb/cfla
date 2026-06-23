
<div class="d-flex justify-content-between p-2">
  <!-- <a href="#" wire:click="goToBack({{$step ?? null}})" class="btn btn-dark w-25 {{ ($step == 1) ? 'disabled' : null}}">Atrás</a> -->
  <!-- <a href="#" wire:click="goToNext({{$step ?? null}})" class="btn btn-primary  w-25 {{ ($step  == $limit) ? 'disabled' : null}}">Siguiente</a> -->
  <button wire:click="goToBack({{$step ?? null}})" class="btn btn-dark  w-25 {{ ($step  == $limit) ? 'disabled' : null}}">Atrás</button>
  <button wire:click="goToNext({{$step ?? null}})" class="btn btn-primary  w-25 {{ ($step  == $limit) ? 'disabled' : null}}">Siguiente</button>
</div>
