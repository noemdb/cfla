<?php
namespace App\Imports\Functions\Prepagos;


trait PrepagoValidateCSV {

    public function fix_fecha($date,$separate='-')
    {
        $date = strtolower($date);

        $date = preg_replace("/[^a-z0-9\-\/]/", "",$date);
        $date = str_replace('/', $separate, $date);

        $mes_arr = array('ene','enero','feb','febrero','mar','marzo','abr','abril','may','mayo','jun','junio','jul','julio','ago','agosto','sep','septiembre','oct','octubre','nov','noviembre','dic','diciembre');
        $month_arr = array('01','01','02','02','03','03','04','04','05','05','06','06','07','07','08','08','09','09','10','10','11','11','12','12');
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

    public function fix_method_pay($method_pay_name)
    {
        $condicion_tr = strpos($method_pay_name,'Transferencia');
        if ($condicion_tr !== false) return 3;

        $condicion_pm = strpos($method_pay_name,'Pago M');
        if ($condicion_pm !== false) return 5;

        return null;
    }

    public function fix_banco_id($banco_name,$method_pay)
    {
        if ($method_pay == 5) return 6;

        $condicion_0114 = strpos($banco_name,'0114');
        if ($condicion_0114 !== false) return 3;

        $condicion_0163 = strpos($banco_name,'0163');
        if ($condicion_0163 !== false) return 2;

        return null;
    }

}
