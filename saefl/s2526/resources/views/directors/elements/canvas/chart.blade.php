{{-- ulpanel: panel de pestañas para manejar opciones en el chart (usuarios, roles, rangos de fechas, etc) --}}
{{ $nav ?? '' }}

<div id="div-{{ isset($id) ?  $id:''}}" class="{{ isset($class) ?  $class:'default'}}">
	<canvas id="{{ isset($id) ?  $id:''}}" width="{{ isset($width) ?  $width:'320'}}" height="{{ isset($height) ?  $width:'220' }}"></canvas>
</div>
