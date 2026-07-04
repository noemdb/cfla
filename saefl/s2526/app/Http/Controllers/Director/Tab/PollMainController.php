<?php

namespace App\Http\Controllers\Director\Tab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\app\Institucion\Autoridad;

use App\Models\app\Poll\PollMain;
use App\Models\app\Poll\PollToken;
use App\Models\app\Poll\PollAnswer;
use App\User;
use Illuminate\Support\Facades\Auth;

class PollMainController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_director']);
    }

    public function index()
    {
        $list_comment = Autoridad::COLUMN_COMMENTS;

        $poll_main_id = (!empty($request->poll_main_id)) ? $request->poll_main_id : null;

        // $poll_main = PollMain::find($poll_main_id);

        $user = User::findOrFail(Auth::id());
        $autoridad = $user->autoridad;
        $autoridad_id = ($autoridad) ? $autoridad->id : null;

        $poll_main = PollMain::select('poll_mains.*')->where('autoridad_id',$autoridad_id)->where('id',$poll_main_id)->first();

        $list_poll_main = PollMain::list_poll_main_autority($autoridad_id);

        return view(
            'directors.pollmains.index',
            compact('poll_main', 'poll_main_id', 'list_poll_main')
        );
    }
}
