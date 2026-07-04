<?php

namespace App\Http\Livewire\Service\Payment\Button\Credicard;
// app/Http/Livewire/Service/Payment/Button/Credicard/ValidateTrait.php

use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\app\Estudiante\Representant;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;

trait PaymentTrait
{

    private function getDebitCard ()
    {
        $this->expiration_month = substr($this->date_expiration,0,2);
        $this->expiration_year = substr($this->date_expiration,3,2);
        //$this->loadlderIdCon();
        //dd($this->holder_id_con,$this->holder_type,$this->holder_id);

        return [
            'card_number'=>$this->card_number, // $this->card_number String Número de tarjeta
            'expiration_month'=>$this->expiration_month, // $this->expiration_month integer($int32) Mes de expiración (valor entre 1 y 12)
            'expiration_year'=>$this->expiration_year, //$this->expiration_year Año de expiración (valor entre 0 y 99)
            'holder_name'=> $this->holder_name,  // $this->holder_name, // String Nombre del titular
            'holder_id_doc'=>$this->holder_id_doc, // $this->holder_id_doc String identificación del tipo de persona Patrón: RIF/CI únicos valores aceptados
            'holder_id'=> $this->holder_id_con, //$this->holder_id,
            /* String
            Documento de identidad patrón: primer carácter  debe ser el identificador de tipo de documento V|J|C|E|G|P y luego 9 caracteres numéricos
            Nota: si el documento tiene menos de 9 caracteres numéricos se debe rellenar con ceros a la izquierda */
            'card_type'=>$this->card_type_payment, // $this->card_type String Tipo de tarjeta Patrón: “DEBIT”
            'account_type'=>$this->account_type, // $this->account_type String Tipo de cuenta Patrón: “PRINCIPAL”, “CORRIENTE” o “AHORRO”
            //'pin'=>$this->card_pin, // String contraseña de la tarjeta,
            'pin'=>$this->encrypt($this->card_pin), // String contraseña de la tarjeta,
            'cvc'=>$this->cvc, //$this->cvc String Código de validación de la tarjeta
            'bank_card_validation'=> $this->setBankCardValidation(), // Validación bancaria
        ];
    }


    private function getDebitCardTest ()
    {
        $this->expiration_month = substr($this->date_expiration,0,2);
        $this->expiration_year = substr($this->date_expiration,3,2);

        return [
            'card_number'=>'5859480000000146871', // $this->card_number String Número de tarjeta
            'expiration_month'=>'6', // $this->expiration_month integer($int32) Mes de expiración (valor entre 1 y 12)
            'expiration_year'=>'24', //$this->expiration_year Año de expiración (valor entre 0 y 99)
            'holder_name'=> "ADQUIRIENTE",  // $this->holder_name, // String Nombre del titular
            'holder_id_doc'=>"RIF", // $this->holder_id_doc String identificación del tipo de persona Patrón: RIF/CI únicos valores aceptados
            'holder_id'=> 'V016673906', //$this->holder_id,
            /* String
            Documento de identidad patrón: primer carácter  debe ser el identificador de tipo de documento V|J|C|E|G|P y luego 9 caracteres numéricos
            Nota: si el documento tiene menos de 9 caracteres numéricos se debe rellenar con ceros a la izquierda */
            'card_type'=>'DEBIT', // $this->card_type String Tipo de tarjeta Patrón: “DEBIT”
            'account_type'=>'CORRIENTE', // $this->account_type String Tipo de cuenta Patrón: “PRINCIPAL”, “CORRIENTE” o “AHORRO”
            // 'pin'=>$this->getPinEncrip($this->card_pin), // String contraseña de la tarjeta,
            'pin'=>$this->encrypt($this->card_pin), // String contraseña de la tarjeta,
            'cvc'=>'941', //$this->cvc String Código de validación de la tarjeta
            'bank_card_validation'=> $this->setBankCardValidation(), // Validación bancaria
        ];
    }

    private function setBody()
    {
        return [
            'reason'=> 'Pago de cuotas', // String Motivo
            'amount'=> $this->ammount_pay, // number Monto (1)
            // 'amount'=> 1, // number Monto (1)
            'currency'=> 'VED', // String Moneda (VES)
            //'order_number'=> 'string', // String Identificador (opcional)
            //'payer_name'=> 'ADQUIRIENTE', // String Nombre del pagador (opcional)
            'payer_name'=> $this->holder_name, // String Nombre del pagador (opcional)
            'payer_email'=> $this->email_representant, // String Correo electrónico opcional
            //'card_bank_code'=> $this->bank_code, // String Código del banco (Solo numérico) opcional

            'debit_card' => $this->getDebitCard(), // Objeto (array) tarjeta de débito
            'country'=> 'VE', // String País (VE) (Opcional)
        ];
    }

    private function setBankCardValidation()
    {
        switch ($this->bank_code) {
            case '0102': //BcoVnz
                $data = [
                    //'rif'=>$this->holder_id,
                    'rif'=>$this->holder_id_con,
                    'token'=>$this->token_bank,
                ];
                break;
            case '0172':
                $data = [
                    'rif'=>$this->holder_id,
                    'token'=>$this->token_bank,
                ];
                break;
            case '0169':
                $data = [
                    'phone'=>$this->holder_phone,
                    'rif'=>$this->holder_id_con,
                    'token'=>$this->token_bank,
                ];
                break;
            case '0168':
                $data = [
                    'rif'=>$this->holder_id_con,
                    'token'=>$this->token_bank,
                ];
                break;
            case '0177':
                $data = [
                    'rif'=>$this->holder_id_con,
                    'token'=>$this->token_bank,
                ];
                break;
            case '0163': //Tesoro
                    $data = [
                        'rif'=>$this->holder_id_con,
                        'token'=>$this->token_bank,
                    ];
                    break;
            case '0114': //Bancaribe
                $data = [
                    'rif'=>$this->holder_id_con,
                    'token'=>$this->token_bank,
                ];
                break;

            default:
                $data = [
                    'rif'=>$this->holder_id_con,
                    'token'=>$this->token_bank,
                ];
                break;
        }

        return $data;

    }

    private function getPinEncrip($cleartext)
    {
        return "lB7iA8HM3CSqkYXzXx1zaHM55qMkoRXF1j8dUErN0Weab/lrKLT1tVvZypW5vToD+erseTMfyJX56iZSK4atQgbgbdhXceAze6B2ITS5ZI0oYlANdahBS/h18tMS6iy5ZvtFPsEEOthFxvRM8HIacCPE77svU8EW+jfsr9JyxLVrkXJ8qy6jvHKoF3sQcDq1omfsw3SzojF0DH/hhifrrVjWcfhsQvB7rC8iuWej5Tw/CRUIXGKylh1QF60GK7yCh1NPcAuLQ2UG97yTFvo6BY2nePI04xtA+Kvjw4V/rqD6jt5RJDKq2DpmDBpXL/A1xNuUd/SDgygaeheglzaOzhZkF+2chh6KzaXvlDB09VrAfkjeNdTU7p30QgqKCztzakNcrE33M933eJmsmcHfzxe2OHOlVHGN2paXuxbJZ9QWM+lhAGpX6n5V6esqxAytWNr1ZVEDl685IqelgLVLyduP/oXnpQ/qP61AdTAYhvo2C8qhC6gioznGrdozmd3M3K/oGR0D06WlhHIVUAH3hgvKk1fdHDmjYpJkauHau89g9aOQk1IExcj4gJ/dYuBzL1/Qv44cwlaPC/r02vH8Mov3FAVfC4160P3BE4fnrDdA2axMtViMWHU5nlzRwLOYOqrDbqtmFDvNL6ot5fGsVTa66kz2XX504zJB1bT52TY=";
    }

    public function encrypt($data)
    {
        $pubkey = $this->getPublicKey();

        if ($pubkey) {
            if (openssl_public_encrypt($data, $cryptdata, $pubkey ))
            {
                $data = base64_encode($cryptdata); //dd($data);
            }
            else{
                throw new Exception('Unable to encrypt data. Perhaps it is bigger than the key size?');
            }
        } else {
            throw new Exception('No se pueden cifrar los datos. Tal vez es mayor que el tamaño de la clave?');
        }

        return $data;
    }

    private function getPublicKey()
    {
        return "-----BEGIN PUBLIC KEY-----\n" . wordwrap($this->getPublicKeyRaw(), 64, "\n", true) . "\n-----END PUBLIC KEY-----";
    }

    private function getPublicKeyRaw()
    {
        return
        "MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAlZsOvWornKePU+ssZl3V".
        "hOy8vExZTRSosd4bgmsj4dRCAs9Uosw4i47go+aABkmbVW1wrvNxhJmUvtbBk9IH".
        "ueunWR7bd4ZmRQvxldPeo1QQBaFdR9a9xGGLvpLdHJHf8EqQeJj85a5+kKmjjNA3".
        "pXUZejiAOR6c7LnzVImaZbgSSvghmeN7jg6gJ+yL4a3t6xK9CcBD8EKkVnD7Ry4/".
        "6nhV9v8r1lfRgECSBPdpNCSdQxJeCGUz0Zrb7QIp6ccjNRGCQga6F/XuPAoG/5qo".
        "kPrjW6FD35DxUx5DGKWGj9VnBAKsD13cW8rcTZB60BzZX39QTbNRNJ6o+Am/dhcZ".
        "VNzv9F6lJJ0T50kVzUsN8tDDDnW8LCe7U1O1vLGN8/kLVFW4XjaJDJmKISLqEmS+".
        "ydLM/9zHLdOG/gHHXn8yVK8/TpXI8rOKo/B8VRHVqGKeVSWEmuFM4FxsgUD9xLMi".
        "kZFIzd4pJVJBePU8GihrjhtBs8Xve/NWg2i8HN3qm7E7Z8E5iwM1R5YFSLVbfIRz".
        "E4QDCAVQgUhNk+WkM4sYVXjOSdzg8w8qVedOsNH6REpZsN5+u5Xof9+/KogujsVb".
        "EiOVmxrty2hXh73G5yfLlIaxQZO3YwUoE/UGN7qx2HnNChdP/LsexthuIjLcDdXP".
        "uESdH/bsClMcj7N/gC7gRO0CAwEAAQ=="
        ;
    }
}

/*

"Valor encriptado", "lB7iA8HM3CSqkYXzXx1zaHM55qMkoRXF1j8dUErN0Weab/lrKLT1tVvZypW5vToD+erseTMfyJX56iZSK4atQgbgbdhXceAze6B2ITS5ZI0oYlANdahBS/h18tMS6iy5ZvtFPsEEOthFxvRM8HIacCPE77svU8EW+jfsr9JyxLVrkXJ8qy6jvHKoF3sQcDq1omfsw3SzojF0DH/hhifrrVjWcfhsQvB7rC8iuWej5Tw/CRUIXGKylh1QF60GK7yCh1NPcAuLQ2UG97yTFvo6BY2nePI04xtA+Kvjw4V/rqD6jt5RJDKq2DpmDBpXL/A1xNuUd/SDgygaeheglzaOzhZkF+2chh6KzaXvlDB09VrAfkjeNdTU7p30QgqKCztzakNcrE33M933eJmsmcHfzxe2OHOlVHGN2paXuxbJZ9QWM+lhAGpX6n5V6esqxAytWNr1ZVEDl685IqelgLVLyduP/oXnpQ/qP61AdTAYhvo2C8qhC6gioznGrdozmd3M3K/oGR0D06WlhHIVUAH3hgvKk1fdHDmjYpJkauHau89g9aOQk1IExcj4gJ/dYuBzL1/Qv44cwlaPC/r02vH8Mov3FAVfC4160P3BE4fnrDdA2axMtViMWHU5nlzRwLOYOqrDbqtmFDvNL6ot5fGsVTa66kz2XX504zJB1bT52TY="


*/

/*


ambiente produccion
"MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAlZsOvWornKePU+ssZl3V".
"hOy8vExZTRSosd4bgmsj4dRCAs9Uosw4i47go+aABkmbVW1wrvNxhJmUvtbBk9IH".
"ueunWR7bd4ZmRQvxldPeo1QQBaFdR9a9xGGLvpLdHJHf8EqQeJj85a5+kKmjjNA3".
"pXUZejiAOR6c7LnzVImaZbgSSvghmeN7jg6gJ+yL4a3t6xK9CcBD8EKkVnD7Ry4/".
"6nhV9v8r1lfRgECSBPdpNCSdQxJeCGUz0Zrb7QIp6ccjNRGCQga6F/XuPAoG/5qo".
"kPrjW6FD35DxUx5DGKWGj9VnBAKsD13cW8rcTZB60BzZX39QTbNRNJ6o+Am/dhcZ".
"VNzv9F6lJJ0T50kVzUsN8tDDDnW8LCe7U1O1vLGN8/kLVFW4XjaJDJmKISLqEmS+".
"ydLM/9zHLdOG/gHHXn8yVK8/TpXI8rOKo/B8VRHVqGKeVSWEmuFM4FxsgUD9xLMi".
"kZFIzd4pJVJBePU8GihrjhtBs8Xve/NWg2i8HN3qm7E7Z8E5iwM1R5YFSLVbfIRz".
"E4QDCAVQgUhNk+WkM4sYVXjOSdzg8w8qVedOsNH6REpZsN5+u5Xof9+/KogujsVb".
"EiOVmxrty2hXh73G5yfLlIaxQZO3YwUoE/UGN7qx2HnNChdP/LsexthuIjLcDdXP".
"uESdH/bsClMcj7N/gC7gRO0CAwEAAQ=="



ambiente calidad
"MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAlZsOvWornKePU+ssZl3V".
"hOy8vExZTRSosd4bgmsj4dRCAs9Uosw4i47go+aABkmbVW1wrvNxhJmUvtbBk9IH".
"ueunWR7bd4ZmRQvxldPeo1QQBaFdR9a9xGGLvpLdHJHf8EqQeJj85a5+kKmjjNA3".
"pXUZejiAOR6c7LnzVImaZbgSSvghmeN7jg6gJ+yL4a3t6xK9CcBD8EKkVnD7Ry4/".
"6nhV9v8r1lfRgECSBPdpNCSdQxJeCGUz0Zrb7QIp6ccjNRGCQga6F/XuPAoG/5qo".
"kPrjW6FD35DxUx5DGKWGj9VnBAKsD13cW8rcTZB60BzZX39QTbNRNJ6o+Am/dhcZ".
"VNzv9F6lJJ0T50kVzUsN8tDDDnW8LCe7U1O1vLGN8/kLVFW4XjaJDJmKISLqEmS+".
"ydLM/9zHLdOG/gHHXn8yVK8/TpXI8rOKo/B8VRHVqGKeVSWEmuFM4FxsgUD9xLMi".
"kZFIzd4pJVJBePU8GihrjhtBs8Xve/NWg2i8HN3qm7E7Z8E5iwM1R5YFSLVbfIRz".
"E4QDCAVQgUhNk+WkM4sYVXjOSdzg8w8qVedOsNH6REpZsN5+u5Xof9+/KogujsVb".
"EiOVmxrty2hXh73G5yfLlIaxQZO3YwUoE/UGN7qx2HnNChdP/LsexthuIjLcDdXP".
"uESdH/bsClMcj7N/gC7gRO0CAwEAAQ=="+

*/
