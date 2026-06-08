<?php

namespace App\Http\Controllers\Administracion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Services\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::all();
        $list_comment = Transaction::COLUMN_COMMENTS;
        return view('administracion.transactions.index',compact('transactions','list_comment'));
    }
}
