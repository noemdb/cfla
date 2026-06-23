<?php

namespace App\Http\Livewire\Service\Payment\Button\Credicard;
// app/Http/Livewire/Service/Payment/Button/Credicard/ValidateTrait.php

use App\Models\app\Planpago\ExchangeRate;

trait ErrorsApiTrait
{
    public function setErrorsException($messege)
    {
        switch ($messege) {
            case 'Unprocessable Entity': $messege = 'La entidad bancaria asociada a la tarjeta, no puede ser procesada.'; break;
        }
        session()->flash('messengeErrorException', $messege);
    }

    public function setErrorsApiCode($response)
    {
        $data = json_decode($response->getBody()); //dd($data);
        $cause = $data->cause;
        $status = $response->getStatusCode();
        $apiMessege =$data->message;
        $this->setErrorsApiCodeStatusCause($cause,$status,$apiMessege);
    }

    public function setErrorsApiCodeStatusCause($cause,$status,$apiMessege)
    {
        $messege = null;
        switch ($status) {
            case '202': $messege = 'Error, se detallará el mensaje de error de la ejecución del servicio.'; break;
            case '401': $messege = 'Acceso denegado.'; break;
            case '403': $messege = 'No está autorizado para acceder a algún recuerdo (la sesión es inválida).'; break;
            case '422': $messege = 'Solicitud incorrecta.'; break;
        }
        $messegeBank = (count($cause)) ? '<br>'.$this->setErrorsApiMessegeBank($cause) : null;
        session()->flash('messengeError', $messege.' '.$this->setErrorsApiMessege($apiMessege).$messegeBank);
    }

    public function setErrorsApiMessege($messege)
    {
        switch ($messege) {
            case 'COLLECTOR_NOT_FOUND': $messege = 'Adquiriente no encontrado'; break;
            case 'MUST_SPECIFY_ONE_PAYMENT_METHOD': $messege = 'Debe especificar un método de pago'; break;
            case 'MUST_SPECIFY_ONLY_ONE_PAYMENT_METHOD': $messege = 'Debe especificar solo un método de pago'; break;
            case 'CURRENCY_NOT_FOUND': $messege = 'Moneda no encontrada'; break;
            case 'BANK_CODE_DOES_NOT_MATCH_CARD': $messege = 'Código de banco no corresponde a la tarjeta'; break;
            case 'INVALID_CARD_NUMBER': $messege = 'Código de tarjeta inválido'; break;
            case 'NO_BIN_FOUND_ASSOCIATED_WITH_THAT_CARD_NUMBER': $messege = 'No existe el bin asociado a la tarjeta'; break;
            case 'BANK_IS_NOT_EMITTER': $messege = 'El banco no es emisor permitido'; break;
            case 'NO_COLLECT_METHODS_FOUND_FOR_THIS_BANK': $messege = 'Método de Adquiriencia no encontrado para este banco'; break;
            case 'NO_COLLECT_METHODS_ALLOWED_FOR_THIS_BANK': $messege = 'Método no permitido para este banco'; break;
            case 'NO_BANK_VALIDATOR_FOUND_FOR_THIS_BANK': $messege = 'No existe validador para este banco'; break;
            case 'PHONE_IS_NECESSARY_FOR_PAYMENT_CARD_BANK_VALIDATION': $messege = 'El teléfono es necesario para validar la identidad de la tarjeta'; break;
            case 'ID_DOC_IS_NECESSARY_FOR_PAYMENT_CARD_BANK_VALIDATION': $messege = 'El documento de identidad es necesario para validar la identidad de la tarjeta'; break;
            case 'INVALID_ID_DOC_VE': $messege = 'Documento invalido'; break;
            case 'INVALID_BANK_CARD_VALIDATOR_TOKEN': $messege = 'Token Invalido'; break;
            case 'INVALID_TOKEN_MUST_BE_ALL_NUMERIC': $messege = 'Token invalido, solo acepta caracteres numéricos'; break;
            case 'TOKEN_IS_NECESSARY_FOR_PAYMENT_CARD_BANK_VALIDATION': $messege = 'El token es necesario para validar la identidad de la tarjeta'; break;
            case 'CREDICARD_RESPONSE_UNSUCCESSFUL': $messege = 'Respuesta de Credicard fallida, datos inválidos'; break;
            case 'BANK_CARD_VALIDATOR_SERVICE_IS_UNAVAILABLE': $messege = 'Validación del banco no disponible'; break;
            case 'BANK_CARD_TOKEN_SERVICE_ERROR': $messege = 'Error en solicitud de código de autorización al banco emisor (BANK_CARD_TOKEN_SERVICE_ERROR)'; break;
            case 'BANK_CARD_VALIDATOR_SERVICE_ERROR': $messege = 'Error en validación de token'; break;
            case 'SESSION_EXPIRED': $messege = 'Autenticación expirada'; break;
            case 'ONE_CARD_MUST_BE_NOT_NULL': $messege = 'ONE_CARD_MUST_BE_NOT_NULL'; break;
            case 'ISO_SERVER_NOT_AVAILABLE': $messege = 'Servidor no disponible: ['.$messege.']'; break;
        }
        return $messege;
    }

    public function setErrorsApiMessegeBank($cause)
    {
        // dd($cause);
        $bank = (array_key_exists(0,$cause)) ? $cause[0] : null; ;
        $error = (array_key_exists(1,$cause)) ? $cause[1] : null; ;
        $code = (array_key_exists(2,$cause)) ? substr($cause[2],0,4) : null;
        $code = ($code) ? ltrim($code, "0") : null;

        $msg = null;

        $arrMessege = [
            'MIBANCO'=>[
                'BANK_CARD_SERVICE_INTERNAL_ERROR'=>[
                    '-1'=>'ERROR DE SISTEMA',
                    '0'=>'IDENTIDAD VALIDA',
                ],
                'BANK_CARD_SERVICE_DATA_ERROR'=>[
                    '1'=>'IDENTIDAD INVALIDA',
                ],
            ],

            'BANCRECER'=>[
                'BANK_CARD_SERVICE_INTERNAL_ERROR'=>[
                    '1'=>'ERROR EN PARÁMETROS',
                    '3'=>'ERROR GENERANDO CLAVE',
                    '4'=>'ERROR EN ENVÍO DE CLAVE',
                ],
                'BANK_CARD_SERVICE_DATA_ERROR'=>[
                    '2'=>'IDENTIDAD INVALIDA',
                ],
                'BANK_INVALID_TOKEN'=>[
                    '5'=>'ERROR VALIDANDO CLAVE',
                    '6'=>'NO EXISTE CLAVE ACTIVA',
                    '7'=>'CLAVE YA UTILIZADA',
                    '8'=>'CLAVE ERRADA',
                    '9'=>'CLAVE EXPIRADA',
                    '10'=>'INTENTOS SUPERADOS',
                    '11'=>'CLAVE USUARIO DEL SERVICIO INVALIDA',
                    '12'=>'USUARIO DE SERVICIO INVALIDO',
                    '13'=>'FALTA TELÉFONO O EMAIL OBLIGATORIO',
                ],
            ],

            'BANCAMIGA'=>[
                'BANK_INVALID_TOKEN'=>[
                    'FALSE'=>'El nro. de TOKEN no existe',
                ],
            ],

            'BANFANB'=>[
                'BANK_CARD_SERVICE_DATA_ERROR'=>[
                    '1'=>'TELEFONO NO VALIDO',
                    '2'=>'CEDULA/RIF NO VALIDO',
                    '3'=>'NACIONALIDAD NO VALIDA',
                    '4'=>'CEDULA/RIF SIN TLF ASOCIADO',

                ],
                'BANK_CARD_SERVICE_INTERNAL_ERROR'=>[
                    '5'=>'TELEFONO NO ES EL ASOCIADO',
                    '6'=>'ERROR EN ARCHIVO DE OTP',
                    '7'=>'ERROR GENERANDO TIMESTAMP',
                    '8'=>'ERROR GRABANDO ARCHIVO DE OTP',
                    '9'=>'Q MANAGER NO DISPONIBLE',
                    '10'=>'ERROR AL ABRIR QM DE SALIDA',
                    '11'=>'ERROR GRABANDO QM',
                    '12'=>'ERROR EN LECTURA DE PARAMETROS',
                    '13'=>'ERROR EN LECTURA DE ASOCIACION',
                ],
                'BANK_INVALID_TOKEN'=>[
                    '6'=>'ERROR EN ARCHIVO DE OTP',
                    '7'=>'ERROR GENERANDO TIMESTAMP',
                    '8'=>'ERROR OTP NO EXISTE',
                    '9'=>'ERROR LEYENDO OTP',
                    '10'=>'ERROR EVALUANDO TIMESTAMP',
                    '11'=>'ERROR OTP VENCIDO',
                    '14'=>'ERROR OTP NO COINCIDE',
                    '15'=>'NO EXISTE PARAMETRO VCTO OTP',

                ],
            ],

            'TESORO'=>[
                'BANK_CARD_SERVICE_DATA_ERROR'=>[
                    '130'=>'CLIENTE NO POSEE CUENTA',
                    '140'=>'CLIENTE NO EXISTE EN EL BANCO',
                ],
                'BANK_INVALID_TOKEN'=>[
                    '190'=>'TOKEN EXPIRADO',
                    '180'=>'TOKEN INVALIDO',
                    '170'=>'TOKEN PROCESADO',
                ],
            ],

            'BANCARIBE'=>[
                'BANK_CARD_SERVICE_DATA_ERROR'=>[
                    '2'=>'CEDULA/RIF NO VALIDO',
                    '3'=>'NACIONALIDAD NO VALIDA',
                    '4'=>'CEDULA/RIF SIN TLF ASOCIADO',
                    '5'=>'TELEFONO NO ES EL ASOCIADO',
                    '13'=>'ERROR EN LECTURA DE ASOCIACION',
                ],
                'BANK_INVALID_TOKEN'=>[
                    '6'=>'ERROR EN ARCHIVO DE OTP',
                    '7'=>'ERROR GENERANDO TIMESTAMP',
                    '8'=>'ERROR OTP NO EXISTE',
                    '9'=>'ERROR LEYENDO OTP',
                    '10'=>'ERROR EVALUANDO TIMESTAMP',
                    '11'=>'ERROR OTP VENCIDO',
                    '14'=>'ERROR OTP NO COINCIDE',
                    '15'=>'NO EXISTE PARAMETRO VCTO OTP',
                ],
                'BANK_CARD_SERVICE_INTERNAL_ERROR'=>[
                    '12'=>'ERROR EN LECTURA DE PARAMETROS',
                ],
            ],
        ];

        if (array_key_exists($bank,$arrMessege)) {
            if (array_key_exists($error,$arrMessege[$bank])) {
                if (array_key_exists($code,$arrMessege[$bank][$error])) {
                    $msg .= $arrMessege[$bank][$error][$code];
                }
            }
        }

        if ($bank == 0 )
            $msg .= "Tipo de documento de identidad inválido";

        return $msg;
    }

}


?>
