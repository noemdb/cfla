<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Estudiante\BoletinRevision;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;

trait BoletinRevisions
{

    public function getRevisions($pensum_id)
    {
        $revisions = BoletinRevision::orderBy('id')
            ->where('estudiant_id', $this->id)
            ->where('pensum_id', $pensum_id)
            ->get();

        return $revisions;
    }
    public function getRevisionNota($pensum_id, $status_literal = true)
    {
        $pensum                = Pensum::find($pensum_id);
        $pestudio              = $pensum->pestudio;
        $enable_academic_index = $pensum->asignatura->enable_academic_index;

        $revision = BoletinRevision::orderBy('created_at', 'desc')
            ->where('estudiant_id', $this->id)
            ->where('pensum_id', $pensum_id)
            ->first();

        $nota = ($revision) ? $revision->nota : null;
        if ($revision && $enable_academic_index == "false" && $status_literal) {

            // Determinar el lapso basado en la fecha de creación de la revisión
            $fechaRevision = $revision->created_at->format('Y-m-d');
            $lapso         = Lapso::whereDate('finicial', '<=', $fechaRevision)
                ->whereDate('ffinal', '>=', $fechaRevision)
                ->orderBy('id', 'desc')
                ->first();

            // Fallback al último lapso si no está dentro de ningún rango
            if (! $lapso) {
                $lapso = Lapso::orderBy('id', 'desc')->first();
            }

            $lapso_id = $lapso ? $lapso->id : null;

            $nota = Baremo::getLiteral($pestudio->id, $revision->nota, $lapso_id);
        }

        return $nota;
    }

}
