<?php

namespace App\Models\app\Integration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',           // Mensaje general
        'ident',             // Identificación del usuario
        'phone',             // Teléfono del usuario
        'template',          // Plantilla utilizada
        'messaging_product', // Producto de mensajería (WhatsApp)
        'contact_input',     // Número de teléfono ingresado
        'contact_wa_id',     // Identificador en WhatsApp
        'message_id',        // ID del mensaje en WhatsApp
        'message_status',    // Estado del mensaje
        'json',              // JSON completo de la respuesta
    ];

    const COLUMN_COMMENTS = [
        'message'=>'Mensaje general',
        'ident'=>'Identificación del usuario',
        'phone'=>'Teléfono del usuario',
        'template'=>'Plantilla utilizada',
        'messaging_product'=>'Producto de mensajería (WhatsApp)',
        'contact_input'=>'Número de teléfono ingresado',
        'contact_wa_id'=>'Identificador en WhatsApp',
        'message_id'=>'ID del mensaje en WhatsApp',
        'message_status'=>'Estado del mensaje',
        'json'=>'JSON completo de la respuesta',
    ];

    public function scopeByMessageStatus($query, $status)
    {
        return $query->where('message_status', $status);
    }

    public function scopeByIdent($query, $ident)
    {
        return $query->where('ident', $ident);
    }

    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone', $phone);
    }

    public function getJsonData($key)
    {
        $data = json_decode($this->json, true);
        return $data[$key] ?? null;
    }

    public function getDescription()
    {
        return "Mensaje enviado a {$this->phone} con estado '{$this->message_status}'";
    }

    public function isAccepted()
    {
        return $this->message_status === 'accepted';
    }

    public function isRejected()
    {
        return $this->message_status === 'rejected';
    }

    public function isRecent($hours = 24)
    {
        return $this->created_at->diffInHours(now()) <= $hours;
    }

    public function saveJsonData(array $data)
    {
        $this->json = json_encode($data);
        return $this->save();
    }

    public static function createResponse(array $data)
    {
        return self::create([
            'message' => $data['message'],
            'ident' => $data['ident'],
            'phone' => $data['phone'],
            'template' => $data['template'] ?? null,
            'messaging_product' => $data['messaging_product'] ?? null,
            'contact_input' => $data['contact_input'] ?? null,
            'contact_wa_id' => $data['contact_wa_id'] ?? null,
            'message_id' => $data['message_id'] ?? null,
            'message_status' => $data['message_status'] ?? null,
            'json' => isset($data['json']) ? json_encode($data['json']) : null,
        ]);
    }

}
