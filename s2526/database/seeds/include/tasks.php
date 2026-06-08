<?php
$testados = ['INICIADA', 'REPROGRAMADA', 'FINALIZADA'];
foreach ($testados as $key => $value) {
	DB::table('select_opts')->insert([
		'table' => "tasks",
		'name' => "estado",
		'value' => $value,
		'view' => "tasks.create",
	]);
}

$ttipo = ['primary'=>'Primaria', 'secondary'=>'Secundaria', 'success'=>'Alternativa', 'info'=>'Informativa', 'warning'=>'De alerta', 'danger'=>'Importante'];
foreach ($ttipo as $key => $value) {
	DB::table('select_opts')->insert([
		'table' => "tasks",
		'name' => "tipo",
		'key' => $key,
		'value' => $value,
		'view' => "tasks.create",
	]);
}
?>