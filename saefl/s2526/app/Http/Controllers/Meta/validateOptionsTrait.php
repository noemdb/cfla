<?php

namespace App\Http\Controllers\Meta;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;

trait validateOptionsTrait
{
    private function validateLapso()
    {
        $message = '❌ EL momento de evaluación actual aún no ha culminado, no es posible enviar la información solicitada';
        $messageGemini = $this->generateQwenResponseSend($message);
        if (is_array($messageGemini)) {
            if (array_key_exists("newMessege", $messageGemini)) {
                $message = '❌ ' . $messageGemini['newMessege'] ?? $message;
            }
        }
        return $message;
    }

    private function validateAcademico(Estudiant $estudiant)
    {
        $message = '❌ Estudiante *' . $estudiant->fullname . '* no está inscrito academicamente';
        $messageGemini = $this->generateQwenResponseSend($message);
        if (is_array($messageGemini)) {
            if (array_key_exists("newMessege", $messageGemini)) {
                $message = '❌ ' . $messageGemini['newMessege'] ?? $message;
            }
        }
        return $message;
    }

    private function validateAdministrativa(Estudiant $estudiant)
    {
        $message = '❌ Estudiante *' . $estudiant->fullname . '* no está inscrito administrativamente';
        $messageGemini = $this->generateQwenResponseSend($message);
        if (is_array($messageGemini)) {
            if (array_key_exists("newMessege", $messageGemini)) {
                $message = '❌ ' . $messageGemini['newMessege'] ?? $message;
            }
        }
        return $message;
    }

    private function validateEstudiantCI($digits)
    {
        $message = '❌ *Cédula (' . $digits . ') no encontrada*';
        // $messageGemini = $this->generateGeminiResponse($message);
        $messageGemini = $this->generateQwenResponseSend($message);
        if (is_array($messageGemini)) {
            if (array_key_exists("newMessege", $messageGemini)) {
                $message = '❌ ' . $messageGemini['newMessege'] ?? $message;
            }
        }
        return $message;
    }

    private function validateRepresentant()
    {
        $message = '❌ *Representante no fué encontrado*';
        $messageGemini = $this->generateQwenResponseSend($message);
        if (is_array($messageGemini)) {
            if (array_key_exists("newMessege", $messageGemini)) {
                $message = '❌ ' . $messageGemini['newMessege'] ?? $message;
            }
        }
        return $message;
    }

    private function validateSolvent(Representant $representant)
    {
        $message = '❌ El representante *' . $representant->name . '* no está solvente, la opción que ha seleccionado anteriormente se requiere la *SOLVENCIA ADMINISTRATIVA*. Para más información al respecto, por favor comuníquese con la administración de la institución.';
        $messageGemini = $this->generateQwenResponseSend($message);
        if (is_array($messageGemini)) {
            if (array_key_exists("newMessege", $messageGemini)) {
                $message = '❌ ' . $messageGemini['newMessege'] ?? $message;
            }
        }
        return $message;
    }
}