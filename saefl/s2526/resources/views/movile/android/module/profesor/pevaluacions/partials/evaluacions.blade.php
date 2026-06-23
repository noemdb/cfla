<ul class="list-group py-2">

	@forelse ($evaluacions as $evaluacion)
		@php 
			$boletins = $evaluacion->boletins;
			$goal = count($list_estudiants);
			$real = $boletins->count();
			$indicator = ($goal) ? (100 * $real/$goal) : null;
			$indicator = round($indicator,2);
			$indicator = ($indicator>100) ? 100 : $indicator ;
			$lapso = $evaluacion->lapso;
		@endphp
		<li class="list-group-item p-2 d-flex justify-content-between align-items-center small">
            <span class="fw-light mx-1">
            	{{$loop->iteration}}. {{$evaluacion->description ?? null}}            	
            </span>
            <div class="d-flex mx-1">

            	<span class="fw-bold text-secondary me-1">[{{$indicator}}%]</span>
            	
	            <a wire:click="setModeLoad('{{$evaluacion->id}}')" class="btn btn-outline-secondary btn-sm" href="#" role="button">
	                <i class="bi bi-box-arrow-down" style="font-size: 1rem"></i>
	            </a>
            	            	
            </div>
        </li>
	@empty
		<div class="ms-4 text-muted small">No hay evaluaciones registradas.</div>
	@endforelse

</ul>
