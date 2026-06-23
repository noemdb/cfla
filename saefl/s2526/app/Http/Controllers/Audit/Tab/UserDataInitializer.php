<?php

namespace App\Http\Controllers\Audit\Tab;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Leader;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Pestudio;
use ReflectionClass;

trait UserDataInitializer
{
    private User $user;
    private ? Autoridad $autoridad;
    private array $listCommentAutoridad = [];
    private $pestudios;
    private $peducativos;

    /**
     * Initialize user-related data.
     *
     * @return void
     */
    public function initializeUserData(): void
    {
        $this->user = Auth::user(); //dd($this->user);
        $this->autoridad = Autoridad::where('user_id', $this->user->id)->first();

        $this->listCommentAutoridad = class_exists(Autoridad::class) && (new ReflectionClass(Autoridad::class))->hasConstant('COLUMN_COMMENTS')
        ? Autoridad::COLUMN_COMMENTS
        : [];

        $this->pestudios = Leader::getPestudioForLeader($this->user->id);
        $this->peducativos = Leader::getPeducativosForLeader($this->user->id);
    }
}
