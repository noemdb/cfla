<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\OrderStatusUpdated;
use App\Models\app\Educational\DebateCompetition;

class UpdateOrderStatus extends Controller
{
    public function updateOrderStatus(Request $request, $orderId)
    {
        $competition = DebateCompetition::find($orderId);
        $competition->status_active = $request->input('status');
        $competition->save();

        broadcast(new OrderStatusUpdated($competition));

        return response()->json(['message' => 'Order status updated']);
    }
}
