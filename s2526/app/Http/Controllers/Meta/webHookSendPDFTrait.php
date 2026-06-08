<?php

namespace App\Http\Controllers\Meta;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Pescolar\Lapso;
use App\Models\sys\Cargo;
use Carbon\Carbon;

trait webHookSendPDFTrait
{

    //INI Administrativo

    private function sendFileConstanciaAdministrativa($phone, $messageBody)
    {
        $digits = substr($messageBody, 1); // Extraemos los dígitos después del primer caracter
        $estudiant = Estudiant::where('ci_estudiant', $digits)->first();

        // Verificar si el estudiante existe
        if (!$estudiant) {
            return $this->validateEstudiantCI($digits);
        }

        $representant = $estudiant->representant;
        // Verificar si el representante existe
        if (!$representant) {
            return $this->validateRepresentant();
        }

        // Verificar si el representante está solvente
        if (! $representant->status_solvent_exchange) {
            return $this->validateSolvent($representant);
        }

        // Verificar si el estudiante tiene inscripción
        if (!$administrativa = $estudiant->administrativa) {
            return $this->validateAdministrativa($estudiant);
        }

        // Generar PDF y enviar archivo
        $pdfPath = $administrativa->generatePdfPathConstanciaAdministrativa($estudiant->id);
        $this->senderFileMessage($phone, $pdfPath);

        return $this->improveMessageGemini('✅ *Constancia de Inscripción Administrativa* enviada éxitosamente');
    }

    private function sendFileSolvencia($phone, $messageBody)
    {
        $digits = substr($messageBody, 1); // Extraemos los dígitos después del primer caracter
        $estudiant = Estudiant::where('ci_estudiant', $digits)->first(); //dd($estudiant);

        // Verificar si el estudiante existe
        if (!$estudiant) {
            return $this->validateEstudiantCI($digits);
        }

        $representant = $estudiant->representant;
        // Verificar si el representante existe
        if (!$representant) {
            return $this->validateRepresentant();
        }

        // Verificar si el representante está solvente
        if (! $representant->status_solvent_exchange) {
            return $this->validateSolvent($representant);
        }

        // Verificar si el estudiante tiene inscripción
        if (!$administrativa = $estudiant->administrativa) {
            return $this->validateAdministrativa($estudiant);
        }

        // Generar PDF y enviar archivo
        $pdfPath = $administrativa->generatePdfPathSolvenciaAdministrativa($estudiant->id);
        $this->senderFileMessage($phone, $pdfPath);

        return $this->improveMessageGemini('✅ *Solvenvia Administrativa* enviada éxitosamente');
    }
    //FIN Administrativo

    //INI Academico

    private function sendFileBoletin($phone, $messageBody)
    {
        $digits = substr($messageBody, 1); // Extraemos los dígitos después del primer caracter
        $estudiant = Estudiant::where('ci_estudiant', $digits)->first();

        // Verificar si el estudiante existe
        if (!$estudiant) {
            return '❌ *Cédula (' . $digits . ') no encontrada*';
            // return $this->validateEstudiantCI($digits);
        }

        // Verificar si el representante existe
        $representant = $estudiant->representant;
        if (!$representant) {
            return $this->validateRepresentant();
        }

        // $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill, 2);
        // if ($exchange_ammount_expire_bill > 0) {
        //     return "❌ El representante *" . $representant->name . "* no está solvente";
        // }

        // Verificar si el estudiante tiene inscripción
        $inscripcion = $estudiant->inscripcion;
        if (!$inscripcion) {
            return "❌ El estudiante *' . $estudiant->fullname . '* no está inscrito formalmente";
        }
        //return ($inscripcion) ? $inscripcion->id : 'no encontrado';

        // Verificar si existe un lapso_anterior
        $now = Carbon::now()->format('Y-m-d');
        $lapso = Lapso::where('ffinal', '<=', $now)->orderBy('ffinal', 'desc')->first(); //dd($lapso);
        if (! $lapso) {
            return "❌ No hay un momento de evaluación activo";
        }

        $now = Carbon::now()->format('Y-m-d');
        $lapsos = Lapso::where('ffinal', '<=', $now)->orderBy('ffinal', 'desc')->get();
        $lapso_id = null;
        $isSolvent = false;
        foreach ($lapsos as $lapso) {
            if (!$estudiant->getStatusBillDate($lapso->ffinal)) {
                $isSolvent = true;
                $lapso_id = $lapso->id;
                break;
            } else {
                $isSolvent = false;
                $lapso_id = null;
            }
        }

        if (!$isSolvent) {
            return "❌ El representante *" . $representant->name . "* presenta insolvencia.";
        }

        if (! $lapso_id) {
            return "❌ No hay un momento de evaluación activo";
        }

        // Generar PDF y enviar archivo
        $pdfPath = $inscripcion->generatePdfPathBoletinPDF($estudiant->id, $lapso_id);
        $this->senderFileMessage($phone, $pdfPath);

        return $this->improveMessageGemini('✅ *Informe de Evaluación* enviado éxitosamente');
    }

    private function sendFileConstancia($phone, $messageBody)
    {
        $digits = substr($messageBody, 1); // Extraemos los dígitos después del primer caracter
        $estudiant = Estudiant::where('ci_estudiant', $digits)->first();

        // Verificar si el estudiante existe
        if (!$estudiant) {
            return $this->validateEstudiantCI($digits);
        }

        $representant = $estudiant->representant;
        // Verificar si el representante existe
        if (!$representant) {
            return $this->validateRepresentant();
        }

        // Verificar si el representante está solvente
        if (! $representant->status_solvent_exchange) {
            return $this->validateSolvent($representant);
        }

        // Verificar si el estudiante tiene inscripción
        if (!$inscripcion = $estudiant->inscripcion) {
            return $this->validateAcademico($representant);
        }

        // Generar PDF y enviar archivo
        $pdfPath = $inscripcion->generatePdfPathEstudioPDF($estudiant->id);
        $this->senderFileMessage($phone, $pdfPath);

        return $this->improveMessageGemini('✅ *Constancia de Estudios* enviada éxitosamente');
    }

    private function sendFileInscription($phone, $messageBody)
    {
        $digits = substr($messageBody, 1); // Extraemos los dígitos después del primer caracter
        $estudiant = Estudiant::where('ci_estudiant', $digits)->first();

        // Verificar si el estudiante existe
        if (!$estudiant) {
            return $this->validateEstudiantCI($digits);
        }

        $representant = $estudiant->representant;
        // Verificar si el representante existe
        if (!$representant) {
            return $this->validateRepresentant();
        }

        // Verificar si el representante está solvente
        if (! $representant->status_solvent_exchange) {
            return $this->validateSolvent($representant);
        }

        // Verificar si el estudiante tiene inscripción
        if (!$inscripcion = $estudiant->inscripcion) {
            return $this->validateAcademico($representant);
        }

        // Generar PDF y enviar archivo
        $pdfPath = $inscripcion->generatePdfPathConstanciaPDF($estudiant->id);
        $this->senderFileMessage($phone, $pdfPath);

        return $this->improveMessageGemini('✅ *Constancia de Inscripción* enviada éxitosamente');
    }

    private function improveMessageGemini($message)
    {
        // $messageGemini = $this->generateGeminiResponse($message);
        // if (is_array($messageGemini)) {
        //     if (array_key_exists("newMessege", $messageGemini)) {
        //         $message = $messageGemini['newMessege'] ?? $message;
        //     }
        // }
        return $message;
    }
}