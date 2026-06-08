<?php
$arr_estado = ['enable'=>'Activo','disable'=>'Desactivo'];
foreach ($arr_estado as $key => $value) {
	DB::table('select_opts')->insert([
		'table' => "users",
		'name' => "is_active",
		'key' => $key,
		'value' => $value,
		'view' => "users.index",
	]);
}
?>