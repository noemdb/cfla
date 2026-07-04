<div class="border rounded">
	<div class="alert alert-dark" role="alert">
	  <div class="fw-bold text-center">Paso {{$step ?? null}}</div>
		<div class="progress my-0 py-0" style="height: 4px">
			@php $valuenow = ($limit) ? round(100 * $step / $limit) : null; @endphp
			<div class="progress-bar bg-secondary" role="progressbar" aria-label="Basic example" style="width: {{$valuenow ?? null}}%" aria-valuenow="{{$valuenow ?? null}}" aria-valuemin="0" aria-valuemax="100"></div>
		</div>
	</div>

	@switch($step)
	    @case('1')@include('livewire.administracion.enrollment.form.enrollment.estudiant')@break
	    @case('2')@include('livewire.administracion.enrollment.form.enrollment.representant')@break
	    @case('3')@include('livewire.administracion.enrollment.form.student_records.partials.general')@break
	    @case('4')@include('livewire.administracion.enrollment.form.student_records.partials.illness')@break
	    @case('5')@include('livewire.administracion.enrollment.form.student_records.partials.parents')@break
	    @default
	@endswitch

	@include('livewire.administracion.enrollment.form.button')
	
</div>

{{-- views/livewire/general/enrollment/form/student_records/fields.blade.php --}}
{{-- views/livewire/general/enrollment/form/student_records/partials/general.blade.php --}}
