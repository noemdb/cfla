<?php

namespace App\Models\app\SenderMailer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mailed extends Model
{
    protected $fillable = [
        'mailer_id','representant_id','job_id','failed_job_id','status','available_at','service_provider','mail_log_id'
    ];
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'mailer_id' => 'Mensaje',
        'representant_id' => 'Representante',
        'job_id' => 'Trabajo asignado',
        'failed_job_id' => 'Trabajo fallado',
        'status' => 'Estado de envío',
        'available_at' => 'Marca de tiempo',
    ];

    public function maileds()
    {
        return $this->hasMany('App\Models\app\SenderMailer\Mailed');
    }

    public function mailLog()
    {
        return $this->belongsTo(SendMailLogs::class, 'mail_log_id');
    }
}

