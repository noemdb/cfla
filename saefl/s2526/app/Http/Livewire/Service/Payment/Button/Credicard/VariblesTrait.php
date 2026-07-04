<?php

namespace App\Http\Livewire\Service\Payment\Button\Credicard;


trait VariblesTrait
{
    public $step,$token;
    public $type_ci,$ci_representant,$full_ci_representant,$fill_ci_representant,$email_representant,$name_representant,$bills,$count_bills,$exchange_ammount_expire_bill,$ammount_expire_bill;
    public $card_type,$card_pin,$account_type,$date_expiration,$cvc,$card_number,$ammount_pay,$exchange_ammount,$ammount,$exchange_rate_ammount,$ammount_pay_exchange;
    public $currency,$commission_amount,$commission_type,$ammount_holder_commission,$bank_type,$product_name;
    public $cvc_type,$card_pin_type,$card_type_payment,$card_type_holder,$bank_card_validation;
    public $totalPay;
    public $holder_name,$holder_id_doc,$holder_id,$holder_type,$list_holder_type,$holder_id_con;
    public $access_token,$expires_in,$date_expires;
    public $status_connecting,$status_connected,$status_send_token_bank,$status_payment_error;
    public $modeAssistent,$modeConnected,$modeSendTokenBank,$modeTest,$modeTerms;
    public $bank_code,$token_bank,$bank_thumbnail,$card_emitter_thumbnail,$card_emitter_name,$holder_phone;
    public $expiration_month,$expiration_year;
    public $resultPayment,$resultPaymentCause;
    public $errorAuthorize,$connectTimeOutError,$connectTimeOutErrorMessege;
    public $status_payment_success,$credicard_trace,$credicard_created_at;
    public $ammount_unexpired,$exchange_ammount_unexpired_bill;
    public $status_transaction_report,$transaction_reports,$status_request_send_token_bank;
    public $credicard_message,$credicard_datetime,$credicard_approval,$credicard_amount_formatted;
    public $bill_number,$bill_created_at,$bill_ammount,$bill_ammount_exchange;
    public $cuentaxpagar_name,$abono_exchange_ammount,$abono_ingreso_ammount;
    public $exception_errors;
    public $ammount_pay_exchange_input;
    public $status_estudiants_formaly;

    public $result_bank_name; //Banco del adquiriente(collect_method.bank_collector.bank_name),
    public $result_bank_rif; //Banco del adquiriente(collect_method.bank_collector.bank_rif),
    public $result_card_emitter_name; // Marca de la Tarjeta (payment_method.card_emitter.name)
    public $result_affiliate_num; // Número Afiliado (credicard.affiliate)
    public $result_lot_number; // Número de Lote (credicard.lot)
    public $result_amount_formatted; // Monto Transacción (amount_formatted)
    public $result_collector_id; // Rif Establecimiento (collector.id_doc)
    public $result_collector_name; // Nombre establecimiento (collector.name)
    public $result_terminal_num; // Número Terminal (credicard.terminal)
    public $result_approval_num; // Numero Aprobación (credicard.approval)
    public $result_sequence_num; // Numero Referencia (credicard.sequence)
    public $result_account_numbe; // Número de tarjeta (payment_method.payment_card.masked_account_number)
    public $result_trace_num; // Número de Trace (credicard.trace)

}

?>

