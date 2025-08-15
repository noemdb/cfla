<?php

namespace App\Models\app\Learner;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Representant extends Model
{
    use HasFactory;

    // protected $connection = 's2526';
    // protected $table = 'representants';

    protected $fillable = ['user_id', 'ci_representant', 'name', 'phone', 'cellphone', 'pmovilphone', 'email', 'gsemail', 'status_active', 'status_blacklist', 'status_adviders'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function estudiants()
    {
        return $this->belongsTo(Estudiant::class, 'user_id');
    }

    const COLUMN_COMMENTS = ['user_id' => 'Nombre de usuario', 'name' => 'Nombres', 'ci_representant' => 'Cédula de Identidad', 'phone' => 'Número de teléfono fijo', 'pmovilphone' => 'Telef. Pago Móvil', 'cellphone' => 'Número de teléfono celular', 'email' => 'Correo electrónico', 'status_active' => 'Estado', 'status_blacklist' => 'Lista negra', 'status_sender' => 'Envío realizado', 'status_adviders' => 'Delegado'];

    public function getNameFullAttribute()
    {
        return $this->name;
    }

    public function getEstudiantsFormalyAttribute()
    {
        $estudiants = Estudiant::select('estudiants.*', 'grados.id as grado_id', 'seccions.id as seccion_id')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('representants.id', $this->id)
            ->where('estudiants.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('representants.status_active', 'true')
            ->get();
        return $estudiants;
    }

    public static function representantFormaly()
    {
        $representants = Representant::select('representants.*')
            ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->where('estudiants.status_active', 'true')
            ->where('representants.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('pestudios.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('representants.deleted_at')
            ->whereNull('seccions.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->whereNull('planpagos.deleted_at')
            ->groupBy('representants.id')
            ->get();

        return $representants;
    }
}
