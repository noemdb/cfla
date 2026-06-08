<?php
$arr_rol = ['DIRECTOR'=>'DIRECTOR','AUTORIDAD1'=>'AUTORIDAD1','AUTORIDAD2'=>'AUTORIDAD2','AUTORIDAD3'=>'AUTORIDAD3','AUTORIDAD4'=>'AUTORIDAD4','ADMINISTRADOR'=>'ADMINISTRADOR','SUPERVISOR'=>'SUPERVISOR','PROFESOR'=>'PROFESOR','ASISTENTE'=>'ASISTENTE','USUARIO'=>'USUARIO','ESTUDIANTE'=>'ESTUDIANTE','REPRESENTANTE'=>'REPRESENTANTE','INIVITADO'=>'INIVITADO'];
foreach ($arr_rol as $key => $value) {
    DB::table('select_opts')->insert([
        'name' => "rol",
        'key' => $key,
        'value' => $value,
        'table' => "rols",
        'view' => "rol.create",
    ]);
}        

$arr_area= ['SISTEMA'=>'SISTEMA','DIRECCION'=>'DIRECCION','AUTORIDAD'=>'AUTORIDAD','ADMINISTRACION'=>'ADMINISTRACION','CONTROL ESTUDIO'=>'CONTROL ESTUDIO','PROFESORADO'=>'PROFESORADO','ESTUDIANTIL'=>'ESTUDIANTIL','REPRESENTANTE'=>'REPRESENTANTE'];
foreach ($arr_area as $key => $value) {
    DB::table('select_opts')->insert([
        'name' => "area",
        'key' => $key,
        'value' => $value,
        'table' => "rols",
        'view' => "rol.create",
    ]);
}
?>
