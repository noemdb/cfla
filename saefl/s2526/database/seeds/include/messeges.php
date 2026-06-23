<?php 

unset($arr);
$arr = ['sent'=>'Enviado','received'=>'Recibido','read'=>'Leído'];
foreach ($arr as $key => $value) {
    DB::table('select_opts')->insert([
        'table' => "messeges",
        'name' => "estado",
        'key' => $key,
        'value' => $value,
        'view' => "messeges.create",
    ]);
}
unset($arr);
$arr = ['primary'=>'Primario', 'secondary'=>'Secundario', 'success'=>'Alternativo', 'info'=>'Informativo', 'warning'=>'De alerta', 'danger'=>'Importante'];
foreach ($arr as $key => $value) {
    DB::table('select_opts')->insert([
        'table' => "messeges",
        'name' => "tipo",
        'key' => $key,
        'value' => $value,
        'view' => "messeges.create",
    ]);
}

?>