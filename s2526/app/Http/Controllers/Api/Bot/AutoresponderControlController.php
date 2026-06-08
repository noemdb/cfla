<?php

namespace App\Http\Controllers\Api\Bot;

use App\Http\Controllers\Controller;
use App\Jobs\Email\ProcessNotifyCollect;
use App\Jobs\Email\ProcessNotifyResetPassword;
use App\Models\app\Bot\Bmain;
use App\Models\app\Bot\Bmessege;
use App\Models\app\Bot\Boption;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Planpago\ExchangeRate;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

class AutoresponderControlController extends Controller
{
    public $bmain,$bmain_id,$json_message,$message,$area,$email;

    public function __construct()
    {
        $this->area = "CONTROL ESTUDIO";
        $this->bmain = Bmain::where('area',$this->area)->where('status_active','true')->orderBy('created_at','desc')->first();
        $this->bmain_id = ($this->bmain) ? $this->bmain->id : null ;
        $this->email = "noemdb@gmail.com";
    }

    public function main(Request $request)
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $this->json_message = null;

        $data = json_decode(json_encode($request->all()), FALSE); //dd($data);

        if(
            !empty($data->query) &&
            !empty($data->appPackageName) &&
            !empty($data->messengerPackageName) &&
            !empty($data->query->sender) &&
            !empty($data->query->message)
        ) {
            $this->message = $data->query->message;

            // $bmain = Bmain::where('status_active','true')->orderBy('created_at','desc')->first(); //dd($bmain);
            if ($this->bmain_id) {
                if ($this->message>0 && $this->message<=4) {
                    $boption = Boption::where('bmain_id',$this->bmain_id)->where('key',$this->message)->first(); //dd($boption);
                    if ($boption) {
                        $this->json_message = $this->getMenu();
                        $this->json_message = $boption->text;
                        //INI concatenar tasa de cambio
                            if ($this->message==4) {
                                $this->json_message .= "\n\n------------------------";
                                $exchange_rate = ExchangeRate::whereDate('date',Carbon::now())->first() ;
                                if ($exchange_rate) {
                                    $this->json_message .=
                                        "\n*Tasa de Cambio BCV*  [".Carbon::now()->format('d-m-y')."]".
                                        "\n ```Bs. ".f_float($exchange_rate->ammount)."```";
                                } else {
                                    $this->json_message .= "\n - Aún no hay tasa de cambio oficial registrada para el día de hoy";
                                    $this->json_message .= "\n - Las tasas de cambio se registran de lunes a viernes";
                                }
                            }
                        //FIN concatenar tasa de cambio
                    }
                    else {
                        $this->json_message = 'Opción no encontrada';
                    }
                } else {
                    $this->json_message = $this->getMenu();
                }
            }

            $this->saveMessage($data);

            // set response code - 200 success
            http_response_code(200);

            // send one or multiple replies to AutoResponder
            return json_encode(array("replies" => array(
                array("message" => $this->json_message)
            )));

            // or


        }  else {
            http_response_code(400);
            return json_encode(array("replies" => array(
                array("message" => "Error ❌"),
                array("message" => "JSON data is incomplete. Was the request sent by AutoResponder?")
            )));
        }

    }

    public function resquestResetPasswordName(Request $request)
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $json_message = null;

        $data = json_decode(json_encode($request->all()), FALSE); //dd($data);

        if( !empty($data->query)) {

            if (!empty($data->query->message)) {
                $message = $data->query->message;
                $name = str_replace('restablecer','',$message);
                $name = trim($name);
                $estudiants = Estudiant::search($name)->active('true')->get();

                if ($estudiants->count()==0) {
                    $json_message = 'Los datos ingresados no corresponden a un alumno de la institución*';
                }                                

                if ($estudiants->count()==1) {
                    $estudiant = $estudiants->first();
                    if ($estudiant->seccion) {
                        try {
                            $time = Carbon::now()->addMinutes(1);
                            $toDate = Date::now()->format('d F Y');
                            $institucion = Institucion::OrderBy('created_at','DESC')->first();
                            $autoridad1 = Autoridad::getTipoAuthority('2');//director
                            $autoridad2 = Autoridad::getTipoAuthority('3');//ADMINISTRADOR
                            $dataEmail = [
                                'subject' => "Solicitud de Reestablecimiento de Contraseña",
                                'address' => $this->email,
                                'mailCCAddress' => env('MAIL_CC_ADDRESS', 'hello@example.com'),
                                'estudiant' => $estudiant,
                                'toDate' => $toDate,
                                'message' => "Solicitud de Reestablecimiento de Contraseña para Classroom",
                                'institucion' => $institucion,
                                'autoridad1' => $autoridad1,
                                'autoridad2' => $autoridad2,
                            ];
                            // ProcessNotifyResetPassword::dispatchSync($dataEmail); //dispatch
                            ProcessNotifyResetPassword::dispatch($dataEmail)->delay($time);

                            $json_message .= "\n------------------------";
                            $json_message .= "\n".$estudiant->fullname;
                            $json_message .= "\n La contraseña será restablecida en los próximos dos días hábiles.";
                            $json_message .= "\n------------------------";

                            $this->saveMessage($data);

                        } catch (\Exception $e) {
                            $json_message .= "\n -Probremas al enviar el email-";
                        }
                    }
                    else {
                        $json_message = '*La información ingresada no corrsponde a un alumno inscrito*';
                    }
                }
                
                if ($estudiants->count()>1) {
                    $json_message = 'Los datos ingresados corresponden a varios alumnos, se requiere nombre completo*';
                }

                $this->saveMessage($data);
                http_response_code(200);
                return json_encode(array("replies" => array(
                    array("message" => $json_message)
                )));
            }
        }

        else{

            http_response_code(400);

            return json_encode(array("replies" => array(
                array("message" => "Error ❌"),
                array("message" => "JSON data is incomplete. Was the request sent by AutoResponder?")
            )));
        }
    }

    public function resquestResetPassword(Request $request)
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $json_message = null;

        $data = json_decode(json_encode($request->all()), FALSE); //dd($data);

        if( !empty($data->query)) {

            if (!empty($data->query->message)) {
                $message = $data->query->message;
                $estudiant = Estudiant::where('ci_estudiant',$message)->first();
                if ($estudiant) {
                    if ($estudiant->seccion) {
                        try {
                            $time = Carbon::now()->addMinutes(1);
                            $toDate = Date::now()->format('d F Y');
                            $institucion = Institucion::OrderBy('created_at','DESC')->first();
                            $autoridad1 = Autoridad::getTipoAuthority('2');//director
                            $autoridad2 = Autoridad::getTipoAuthority('3');//ADMINISTRADOR
                            $dataEmail = [
                                'subject' => "Solicitud de Reestablecimiento de Contraseña",
                                'address' => $this->email,
                                'mailCCAddress' => env('MAIL_CC_ADDRESS', 'hello@example.com'),
                                'estudiant' => $estudiant,
                                'toDate' => $toDate,
                                'message' => "Solicitud de Reestablecimiento de Contraseña para Classroom",
                                'institucion' => $institucion,
                                'autoridad1' => $autoridad1,
                                'autoridad2' => $autoridad2,
                            ];
                            // ProcessNotifyResetPassword::dispatchSync($dataEmail); //dispatch
                            ProcessNotifyResetPassword::dispatch($dataEmail)->delay($time);

                            $json_message .= "\n------------------------";
                            $json_message .= "\n La contraseña será reiniciada en las próximas 48 horas.";
                            $json_message .= "\n------------------------";

                            $this->saveMessage($data);

                        } catch (\Exception $e) {
                            $json_message .= "\n -Probremas la enviar el email-";
                        }
                    }

                    else {
                        $json_message = '*Cédula no corrsponde a un estudiante inscrito*';
                    }

                } else {
                    $json_message = '*Cédula no registrada*';
                }

                $this->saveMessage($data);
                http_response_code(200);
                return json_encode(array("replies" => array(
                    array("message" => $json_message)
                )));
            }
        }

        else{

            http_response_code(400);

            return json_encode(array("replies" => array(
                array("message" => "Error ❌"),
                array("message" => "JSON data is incomplete. Was the request sent by AutoResponder?")
            )));
        }
    }

    public function getMenu()
    {
        if ($this->bmain) {
            $boptions = Boption::where('bmain_id',$this->bmain->id)->get(); //dd($this->bmain->id,$boptions);
            $message = $this->bmain->description."\n \n";
            foreach ($boptions->sortBy('key') as $boption) {
                $message .= $boption->description."\n";
            }
            return $message;
        }
    }

    public function saveMessage($data)
    {
        $arr = [
            'bmain_id' => $this->bmain_id,
            'app_package_name' => $data->appPackageName ,
            'messenger_package_name' => $data->messengerPackageName ,
            'sender' => $data->query->sender ,
            'message' => $data->query->message ,
            'is_group' => (empty($data->isGroup)) ? null : $data->isGroup,
            'rule_id' => (empty($data->rule_id)) ? null : $data->rule_id,
        ];
        $messege = Bmessege::create($arr);
    }

}
