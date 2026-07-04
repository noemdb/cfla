<?php

namespace App\Models\app\Cobranzas;

use App\Models\app\Estudiante\Representant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CollPolitical extends Model
{
    protected $fillable = [
        'pescolar_id','name','code','description','finicial','ffinal','canon','status','status_approved','status_debts','numbers_bills'
    ];
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'pescolar_id' => 'Período Escolar',
        'name' => 'Nombre',
        'code' => 'Código',
        'description' => 'Descripción',
        'finicial' => 'Fecha inicial',
        'ffinal' => 'Fecha Final',
        'canon' => 'Criterio de Agrupamiento',
        'status' => 'Estado',
        'status_debts' => 'Sólo a deudores?',
        'status_approved' => 'Estado de aprobación',
        'numbers_bills' => 'Número de Cuotas',
    ];

    public function pescolar()
    {
        return $this->belongsTo('App\Models\app\Pescolar');
    }
    public function coll_nivels()
    {
        return $this->hasMany('App\Models\app\Cobranzas\CollNivel');
    }

    public function coll_messeges()
    {
        return $this->hasManyThrough('App\Models\app\Cobranzas\CollMessege', 'App\Models\app\Cobranzas\CollNivel');
    }

    public function getFullNameAttribute()
    {
        return "{$this->code} {$this->name} - {$this->description}";
    }

    public static function list_coll_politicals($active=true) /* usada para llenar los objetos de formularios select*/
    {
        $list = CollPolitical::select('id', DB::raw("CONCAT(code,' - ',name, ' - ', description) as concat_name"))
            ->orderby('name','asc');

        $list = ($active) ? $list->where('status','true') : $list->where('status','false') ;
        
        $list = $list->pluck('concat_name', 'id');
        
        return $list;
    }

    public static function list_coll_politicals_canon()
    {
        return ['all'=>'Todos','debts'=>'Deudores','levels'=>'Grados/Niveles','imployeds'=>'Empleados','grantees'=>'Becados','adviders'=>'Asesores','collaborators'=>'Colaboradores'];
    }

    public static function collPoliticalActive()
    {
        $coll_political = CollPolitical::select('coll_politicals.*')
        ->Where('coll_politicals.finicial', '<=', Carbon::now())
        ->Where('coll_politicals.ffinal', '>=', Carbon::now())
        ->where('coll_politicals.status','true')
        ->where('coll_politicals.status_approved','true')
        ->orderBy('coll_politicals.created_at','desc')
        ->first();
        return $coll_political;
    }

    public static function collPoliticalNumberBills($numbers_bills=1)
    {
        $coll_political = CollPolitical::select('coll_politicals.*')
        ->Where('coll_politicals.finicial', '<=', Carbon::now())
        ->Where('coll_politicals.ffinal', '>=', Carbon::now())
        // ->where('coll_politicals.status','true')
        // ->where('coll_politicals.status_approved','true')
        ->where('coll_politicals.numbers_bills',$numbers_bills)
        ->orderBy('coll_politicals.created_at','desc')
        ->first();
        return $coll_political;
    }

    public static function getRepresentants($canon,$status_debts='true')
    {
        $items = collect();
        switch ($canon) {
            case 'all': $items = Representant::representantFormaly();break;
            case 'imployeds': $items = Representant::imployeds();break;
            case 'collaborators': $items = Representant::collaborators();break;
            case 'grantees': $items = Representant::grantees();break;
            case 'adviders': $items = Representant::adviders();break;
        }

        $representants = $items;
        if ($status_debts=='true') {
            $representants = collect();
            foreach ($items as $item) {
                $ammount = $item->exchange_ammount_expire_bill;
                if ($ammount>0.09) {
                    $representants->push($item);
                }
            }
        }

        return $representants;
    }

    public function getFullCanonAttribute()
    {
        $arr = CollPolitical::list_coll_politicals_canon();
        return $arr[$this->canon];
    }

    public function getStatusSendMailAttribute()
    {
        $status_messeges =  ($this->coll_messeges->isNotEmpty()) ? true : false ;
        return ($this->status == 'true' && $this->status_approved == 'true' && $status_messeges) ? true:false;
    }

    public function getRepresentantsAttribute()
    {
        $canon = $this->canon; //dd($canon);
        $status_debts = $this->status_debts; //dd($canon);

        $items = collect();
        switch ($canon) {
            case 'debts': $items = Representant::debts();break;
            // case 'debts': $items = Representant::representantFormaly();break;
            case 'all': $items = Representant::representantFormaly();break;
            case 'imployeds': $items = Representant::imployeds();break;
            case 'collaborators': $items = Representant::collaborators();break;
            case 'grantees': $items = Representant::grantees();break;
            case 'adviders': $items = Representant::adviders();break;
        }

        $representants = $items; //dd($items);
        if ($status_debts=='true') {
            $representants = collect();
            foreach ($items as $item) {
                $count_expired_bills = $item->count_expired_bills; //dd($count_expired_bills,$item->count_expired_bills);
                if ($count_expired_bills >= $this->numbers_bills ) {
                    $representants->push($item);
                }
            }
        }

        //dd($this,$representants);

        return $representants;
    }
}
