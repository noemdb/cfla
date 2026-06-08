<?php

namespace App\Http\Controllers\Meta;

use App\Models\app\Bot\Bmain;
use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;
use App\Models\app\Educational\DebateCompetition as Competition;
use App\Models\app\Meta\WebhookMessage;
use App\Rules\MaxWords;

use Gemini;
use Gemini\Data\Content;
use Gemini\Enums\Role;

trait GeminiChatHookTrait
{ 
    public $context_primary = "Actúa como un experto en marketing, especialista en relaciones humánas, ámplia experiencia en atención al público. no uses más de 100 palabras, el tono es mucha amabilidad y cordialidad.";   
    
    public function getChat()
    {
        $client = Gemini::client(getenv('GEMINI_API_KEY'));
        $chat = $client
        ->geminiPro()
        ->startChat(history: [
            Content::parse(part: $this->context_primary),
            Content::parse(part: 'Sí, lo entiendo. tomaré ese rol', role: Role::MODEL)
        ]);       
        return $chat;
    } 

    public function generateGeminiResponse($messege,$from=null)
    {
        $chat = $this->getChat();
        $strinJson = $chat->sendMessage(
            'Mejora en menos de 35 palabras el siguiente mensaje, el tono es mucha amabilidad y cordialidad, omite saludos, usa emoji de la platoforma whatsapp, formatea el texto para un mensaje en esa plataforma: '.$messege.
            'La respuesta debe ser un string emulando la estructura de datos de un JSON que luego pueda ser convertido en un array en PHP usando la función json_decode. Responde de la siguiente forma:'.
            '
            {
                "oldMessege": "coloca aquí el mensaje recibido que se va a ser mejorado",
                "newMessege": "coloca aquí el mensaje mejorado"
            }
            '
        );

        $strinJson = $strinJson->text() ?? null;
        return $this->getChatGetJson($strinJson);
    } 

    public function geminiResponse($default,$body,$from=null)
    {
        $chat = $this->getChat();
        $context = $this->getContext($from); //dd($context);
        $strinJson = $chat->sendMessage(
            'EL siguiente texto conforma el contexto de información que necesitas usar para generar lo que necesito: '.$context.
            '- . - . EL mensaje recibido es el siguiente: '.$body.
            '- . - . El siguiente texto es una respuesta por defecto (default): '.$default.
            '- . - . Omite saludos, usa emoji para la platoforma whatsapp, formatea el texto (negritas, cursivas, tachado, espaciado, etc) para un mensaje en esa plataforma'.
            '- . - . Genera una respuesta (newMessege) a esta interacción, en atención al contexto y menú de opciones dado;'.
            '- . - . La respuesta que generes debe ser un string emulando la estructura de datos de un JSON que luego pueda ser convertido en un array en PHP usando la función json_decode. Responde de la siguiente forma:'.
            '- . - . En la respuesta que generes omite saludos (hola, adios, buenos día, etc), se directo y preciso, apegado al contexto y al menú de opciones'.
            '
            {
                "default": "coloca aquí respuesta por defecto",
                "newMessege": "coloca aquí el mensaje mejorado"
            }
            '
        );

        $message = $default;
        $strinJson = $strinJson->text();
        $messageGemini = $this->getChatGetJson($strinJson);
        if (is_array($messageGemini)) {
            if (array_key_exists("newMessege", $messageGemini)) {
                $message = $messageGemini['newMessege'] ?? '❌ '. $default;
            }
        }
        return $message;
    } 

    public function getChatGetJson($text)
    {
        $text = str_replace('json','',$text);
        $text = str_replace('```','',$text);
        $text = str_replace('"""','',$text);
        $text = str_replace('**', '*', $text);
        return json_decode($text, true);
    }

    public function getContext($from)
    {
        $menu = 'EL siguiente es el menú de opciones disponibles para esta interacción: '.$this->getDefaultMenuResponse();
        $response = '- . - Opciones preestablecidas: '. Bmain::getResponseContextString();
        $stringSql = WebhookMessage::where('from', $from)->where('type', 'text')->orderBy('id', 'desc')->limit(10)->get()->pluck('body')->implode(";\n"); //dd($stringSql);
        $historyChat = 'Los últimos mensaje recibidos del usuario: \n'. $stringSql;
        $unexpire_bills = null;
        $expire_bills = null;

        $representant = WebhookMessage::getRepresentantForFrom($from);
        if ($representant) {
            $unexpire_bills = ($representant) ? $representant->ExchangeUnexpireBillPendientes : null;
            $result = $unexpire_bills->map(function ($item) {
                return sprintf(
                    "Nombre: %s, Fecha de vencimiento: %s, Monto: USD %s",
                    $item['expire_bill_name'],
                    $item['date_expiration'],
                    $item['ammount']
                );
            })->implode("\n");

            $unexpire_bills = 'El usuario asociado a este chat tiene las siguiente cuotas que aún no estan vencidas'.$result;

            $expire_bill = $representant->expire_bill_pendientes ;
            $result = $expire_bill->map(function ($item) {
                return sprintf(
                    "Nombre: %s, Monto: USD %s",
                    $item['expire_bill_name'],
                    $item['ammount']
                );
            })->implode("\n");
            $expire_bills = 'El usuario asociado a este chat tiene las siguiente cuotas vencidas: '.$result;
        }

        return $menu.$response.$historyChat.$expire_bills.$unexpire_bills;
    }
}