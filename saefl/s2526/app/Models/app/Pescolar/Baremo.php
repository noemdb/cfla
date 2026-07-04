<?php
namespace App\Models\app\Pescolar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Baremo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pestudio_id', 'lapso_id', 'pensum_id', 'minimo', 'maxima', 'valoracion', 'description',
    ];

    public function pestudio()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pestudio');
    }
    public function pensum()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pensum');
    }

    public function lapso()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Lapso');
    }

    /**
     * Obtener valoración priorizando baremo por lapso_id
     */
    public static function getValoracion($pestudio_id, $nota, $lapso_id = null)
    {
        $query = Baremo::select('baremos.valoracion', 'baremos.description')
            ->where('baremos.minimo', '<=', $nota)
            ->where('baremos.maxima', '>=', $nota);

        if ($lapso_id) {
            // Priorizar baremo específico del lapso
            $query->where(function ($q) use ($pestudio_id, $lapso_id) {
                $q->where('baremos.lapso_id', $lapso_id)
                    ->orWhere(function ($sub) use ($pestudio_id) {
                        $sub->whereNull('baremos.lapso_id')
                            ->where('baremos.pestudio_id', $pestudio_id);
                    });
            });
        } else {
            // Fallback a baremo general
            $query->where('baremos.pestudio_id', $pestudio_id)
                ->whereNull('baremos.lapso_id');
        }

        return $query->first();
    }

    /**
     * Obtener literal con misma lógica
     */
    public static function getLiteral($pestudio_id, $nota, $lapso_id = null)
    {
        $query = Baremo::select('baremos.literal')
            ->where('baremos.minimo', '<=', $nota)
            ->where('baremos.maxima', '>=', $nota);

        if ($lapso_id) {
            $query->where(function ($q) use ($pestudio_id, $lapso_id) {
                $q->where('baremos.lapso_id', $lapso_id)
                    ->orWhere(function ($sub) use ($pestudio_id) {
                        $sub->whereNull('baremos.lapso_id')
                            ->where('baremos.pestudio_id', $pestudio_id);
                    });
            });
        } else {
            $query->where('baremos.pestudio_id', $pestudio_id)
                ->orderBy('baremos.id', 'desc');
        }

        $result = $query->first();
        return $result ? $result->literal : null;
    }

    public static function baremo_apply_list()
    {
        return ['true' => 'SI', 'false' => 'NO'];
    }

}
