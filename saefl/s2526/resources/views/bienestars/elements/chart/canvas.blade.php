{{-- ulpanel: panel de pestañas para manejar opciones en el chart (usuarios, roles, rangos de fechas, etc) --}}
{{ $nav ?? '' }}
<div id="div-{{ $id ?? 'default' }}" class="{{ $class ?? '' }}">
	<canvas id="{{ $id ?? 'default' }}" width="{{ $width ?? '350' }}" height="{{ $height ?? '220' }}"></canvas>
</div>
