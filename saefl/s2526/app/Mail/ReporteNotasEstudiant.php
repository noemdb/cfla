<?php

namespace App\Mail;

use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar;
use App\Models\app\Pescolar\Lapso;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReporteNotasEstudiant extends Mailable
{
    public $estudiant;
    public $filePath;
    public $fileName;
    public $lapso_id;

    public function __construct($estudiant, $filePath, $fileName, $lapso_id)
    {
        $this->estudiant = $estudiant;
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->lapso_id = $lapso_id;
    }

    public function build()
    {
        $lapso = Lapso::find($this->lapso_id);
        $pescolar = Pescolar::first();
        $institucion = $pescolar->institucion;
        $representant = $this->estudiant->representant;
        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad = Autoridad::getTipoAuthority('1'); //DIRECTOR ACADEMICO
        $director = Autoridad::getTipoAuthority('2'); //DIRECTOR GENERAL

        return $this->subject('Notificaciones SAEFL - Informe de Notas')
            ->cc(env('MAIL_CC_ADDRESS_CONTROL'))
            ->view('email.boletins.reporte_notas') // email.boletins.reporte_notas
            ->attach($this->filePath, [
                'as' => $this->fileName,
                'mime' => 'application/pdf',
            ])
            ->with([
                'estudiant' => $this->estudiant,
                'pescolar' => $pescolar,
                'institucion' => $institucion,
                'representant' => $representant,
                'lapso' => $lapso,
                'autoridad' => $autoridad,
                'director' => $director,
            ]);
    }
}
