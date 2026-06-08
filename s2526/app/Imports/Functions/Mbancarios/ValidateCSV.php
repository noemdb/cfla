<?php
namespace App\Imports\Functions\Mbancarios;


trait ValidateCSV {

    public function fix_fecha($date,$separate='-')
    {
        $date = strtolower($date);

        $date = preg_replace("/[^a-z0-9\-\/]/", "",$date);
        $date = str_replace('/', $separate, $date);

        $mes_arr = array('ene','enero','feb','febrero','mar','marzo','abr','abril','may','mayo','jun','junio','jul','julio','ago','agosto','sep','septiembre','oct','octubre','nov','noviembre','dic','diciembre');
        $month_arr = array('01','01','02','02','03','03','04','04','05','05','06','06','07','07','08','08','09','09','10','10','11','11','12','12');
        // $month_arr = array('January','February','March','April','May','June','July','August','September','October','November','December');
        return str_replace($mes_arr,$month_arr,$date);
    }

    public function fix_referencia($referencia)
    {
        return preg_replace("/[^a-zA-Z0-9]/", "",$referencia);
    }

    public function fix_monto($monto)
    {
        $value = preg_replace("/[^0-9.,]/", "",$monto);
        $value = str_replace(".","",$value);
        $value = str_replace(",",".",$value);
        return is_numeric( $value ) ? $value : $monto;
    }

}
