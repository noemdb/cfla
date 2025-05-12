<?php

namespace App\Livewire\App\Payment;

use App\Livewire\Forms\PaymentForm;
use App\Models\app\Admon\Banco;
use App\Models\app\Admon\MetodoPago;
use App\Models\app\Admon\Payment;
use App\Models\app\Learner\Representant;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use WireUi\Traits\Actions;

use App\Mail\WelcomeEmail;
use App\Models\app\Entity\Autoridad;
use App\Models\app\Entity\Institucion;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class IndexComponent extends Component
{
    use Actions;

    use WithFileUploads;
    #[Validate('image|max:1024|nullable')] // 1MB Max
    public $image;

    public $ci;
    public $step = 0, $limit = 2;
    public $modalStart, $modalOperOk, $modalSearch, $modalAssistent, $modalEmpty;
    public Representant $representant;
    public PaymentForm $payment;
    public $list_comment, $list_bank, $method_pay_list, $type_pay_list, $banco_emisor_list;
    public $toDate, $banco;

    public function loadTest()
    {
        $this->payment->ci_representant = '14442948';
        $this->payment->type_pay = 'Mensualidad actual';
        $this->payment->phone = '1234567890';
        $this->payment->comment = '#####################';
        $this->payment->phone_1 = '12345678';
        $this->payment->number_i_pay_1 = rand(1000000, 100000000);
        $this->payment->banco_id_1 = 2;
        $this->payment->banco_emisor_1 = 'BANCO DE VENEZUELA';
        $this->payment->method_pay_id_1 = 3;
        $this->payment->date_transaction_1 = '2024-01-25';
        $this->payment->ammount_1 = '10000';
        $this->payment->observation_1 = '#################';
    }

    public function upLoadImage($image)
    {
        $url = ($image) ? $image->store('images', 'payments') : null; //dd($url);
        return ($url) ? 'storage/payment/' . $url : null;
    }

    public function save()
    {
        $this->payment->image_1 = $this->upLoadImage($this->image);

        $this->validate();

        $payment = Payment::create($this->payment->all());
        $representant = $this->representant;

        if ($payment && $representant) {
            $title = "Datos guardados";
            $description = "Toda la información ha sido guardada éxitosamente!";
            $icon = "success";

            $inputs = (object) [
                'id' => $payment->id,
                'representant_name' => $representant->name,
                'ci_representant' => $representant->ci_representant,
                'number_i_pay' => $this->payment->number_i_pay_1,
                'ammount' => $this->payment->ammount_1,
                'type_pay' => $this->payment->type_pay,
                'date' => Carbon::now()->format('d-m-Y h:i'),
                'created_at' => $payment->created_at,
            ];

            $toDate = Date::now()->format('d F Y');
            $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
            $autoridad1 = Autoridad::getTipoAuthority('2'); //director
            $autoridad2 = Autoridad::getTipoAuthority('4'); //ADMINISTRADOR
            $data = (object) [
                'email' => $representant->email,
                'name' => $representant->name,
                'subject' => 'Notificaciones SAEFL',
                'representant' => $representant,
                'inputs' => $inputs,
                'institucion' => $institucion,
                'autoridad1' => $autoridad1,
                'autoridad2' => $autoridad2,
                'toDate' => $toDate,
                'view' => 'emails.welcome',
            ];

            try {
                // Renderizar la vista del email
                $html = View::make($data->view, ['data' => $data])->render();

                // Enviar email usando Resend API
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('RESEND_API_KEY'),
                    'Content-Type' => 'application/json',
                ])->post(env('RESEND_URL'), [
                    'from' => env('RESEND_FROM_NAME') . ' <' . env('RESEND_FROM') . '>',
                    'to' => $data->email,
                    'subject' => $data->subject,
                    'html' => $html,
                ]);
                dd($response);

                if ($response->successful()) {
                    $this->notification()->success(
                        $title = 'Excelente!',
                        $description = 'Se ha enviado la notificación a tu correo electrónico.'
                    );
                } else {
                    throw new \Exception('Error en la respuesta de Resend: ' . $response->body());
                }
            } catch (\Exception $e) {
                $this->notification()->error(
                    $title = 'Error !!!',
                    $description = 'No se pudo enviar el correo. Por favor, intenta nuevamente.'
                );
            }
        } else {
            $title = "No se han guardado los datos";
            $description = "Ocurrieron errores";
            $icon = "warning";
        }

        $this->modalAssistent = false;
        $this->image = null;
        $this->payment->image_1 = null;
        $this->payment->number_i_pay_1 = null;
        $this->payment->comment = null;

        $this->notification()->send([
            'title'       => $title,
            'description' => $description,
            'icon'        => $icon
        ]);
        $this->modalOperOk = true;
    }

    public function mount()
    {
        $this->banco_emisor_list = Payment::LIST_BANK_EMISOR; //dd($this->banco_emisor_list);
        $this->list_comment = Payment::COLUMN_COMMENTS;
        $this->list_bank = Banco::list_public_bancos();
        $this->method_pay_list = MetodoPago::method_pay_list(); //dd($this->method_pay_list);
        $this->type_pay_list = Payment::LIST_TYPE_PAY;
        $this->toDate = Carbon::now()->format('d F Y');

        $this->ci = '14608133';
        $this->loadTest();
    }

    public function render()
    {
        return view('livewire.app.payment.index-component');
    }

    public function search()
    {
        $this->resetValidation();
        $representant = Representant::where('ci_representant', $this->ci)->first(); //dd($representant);
        if ($representant) {
            $this->representant = $representant;
            $this->step = 1;
            $this->payment->ci_representant = $representant->ci_representant;
            $this->payment->representant_id = $representant->representant_id;
            $this->payment->name_representant = $representant->name;
            $this->modalAssistent = true;
        } else {
            $this->modalEmpty = true;
            $this->modalStart = true;
        }
        $this->modalSearch = false;
        $this->modalOperOk = false;
    }

    public function setStart()
    {
        $this->modalSearch = true;
        $this->modalStart = false;
        $this->modalAssistent = false;
        $this->modalEmpty = false;
        $this->modalOperOk = false;
    }

    public function validatedForStep($step)
    {


        switch ($step) {
            case '1':
                $this->validateOnly("payment.name_representant");
                $this->validateOnly("payment.ci_representant");
                $this->validateOnly("payment.type_pay");
                $this->validateOnly("payment.ammount_1");
                $this->validateOnly("payment.date_transaction_1");
                $this->validateOnly("payment.banco_emisor_1");
                $this->validateOnly("payment.banco_id_1");
                $this->validateOnly("payment.number_i_pay_1");
                $this->validateOnly("payment.method_pay_id_1");
                $this->validateOnly("payment.phone");
                $this->validateOnly("payment.phone_1");
                $this->validateOnly("payment.observation_1");
                $this->validateOnly("payment.comment");
                $this->validateOnly("payment.image_1");
                $this->next($step);
                break;
            case '2':
                $this->validate();
                $this->next($step);
                break;
        }

        $this->resetErrorBag();
    }

    public function next($step)
    {
        $this->step = ($step < $this->limit) ? $step + 1 : $this->limit;
    }

    public function back($step)
    {
        $this->step = ($step > 1) ? $step - 1 : 1;
    }

    public function close()
    {
        $this->modalStart = false;
        $this->modalOperOk = false;
        $this->modalSearch = false;
        $this->modalAssistent = false;
        $this->modalEmpty = false;
    }
}
