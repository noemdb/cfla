<?php
namespace App\Http\Controllers\Administracion\Tab\Polls;

use App\Http\Controllers\Controller;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Poll\PollMain;
use App\Models\app\Poll\PollToken;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PollMainController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'is_common']);
    }

    public function questions()
    {
        return view('administracion.polls.questions.index');
    }
    public function options()
    {
        return view('administracion.polls.options.index');
    }

    public function index()
    {
        return view('administracion.polls.index');
    }

    public function competitors(Request $request)
    {
        $poll_main_id = (! empty($request->poll_main_id)) ? $request->poll_main_id : null;
        $grado_id     = (! empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id   = (! empty($request->seccion_id)) ? $request->seccion_id : null;
        $formally     = (! empty($request->formally)) ? $request->formally : null;
        $attendees    = collect();
        $poll_tokens  = collect();

        $user     = User::findOrFail(Auth::id());
        $is_admin = $user->IsAdmin();

        $poll_main = PollMain::select('poll_mains.*')->where('id', $poll_main_id);

        $poll_main = ($is_admin) ? $poll_main : $poll_main->where('user_id', $user->id);

        $poll_main = $poll_main->first();

        if ($poll_main) {
            $attendees = $poll_main->attendees;
            // $poll_tokens = $poll_main->poll_tokens;

            $poll_tokens =
            PollToken::select('poll_tokens.*')
            // ->join('poll_answers', 'poll_tokens.token', '=', 'poll_answers.token')
                ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                ->join('users', 'users.id', '=', 'poll_tokens.user_id');

            if ($grado_id) {
                $poll_tokens = $poll_tokens->join('representants', 'users.id', '=', 'representants.user_id')
                    ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                    ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    ->join('grados', 'grados.id', '=', 'seccions.grado_id');

                $poll_tokens = (isset($grado_id)) ? $poll_tokens->where('grados.id', $grado_id) : $poll_tokens;
                $poll_tokens = (isset($seccion_id) && isset($seccion_id)) ? $poll_tokens->where('seccions.id', $seccion_id) : $poll_tokens;
            }

            $poll_tokens = $poll_tokens->where('poll_mains.id', $poll_main->id)->groupBy('poll_tokens.token')->get();
        }

        $list_poll_main = PollMain::list_poll_main_user($user->id);
        $list_grado     = Grado::list_pestudio_grado();
        $list_seccion   = Seccion::list_seccion_grado($grado_id);

        return view(
            'administracion.polls.competitors',
            compact('poll_main', 'attendees', 'poll_tokens', 'list_poll_main', 'list_grado', 'list_seccion', 'poll_main_id', 'grado_id', 'seccion_id')
        );
    }

    public function analyzers(Request $request)
    {
        $poll_main_id = (! empty($request->poll_main_id)) ? $request->poll_main_id : null;
        $pestudios    = Pestudio::where('status_active', 1)->get();

        // $poll_main = PollMain::find($poll_main_id);

        $user     = User::findOrFail(Auth::id());
        $is_admin = $user->IsAdmin();

        $poll_main = PollMain::select('poll_mains.*')
            ->where('id', $poll_main_id);

        $poll_main = ($is_admin) ? $poll_main : $poll_main->where('user_id', $user->id);

        $poll_main = $poll_main->first();

        $list_poll_main = PollMain::list_poll_main_user($user->id);

        return view(
            'administracion.polls.analyzers',
            compact('poll_main', 'poll_main_id', 'list_poll_main', 'pestudios')
        );
    }
}
