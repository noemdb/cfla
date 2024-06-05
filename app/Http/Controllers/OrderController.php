<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\app\Educational\DebateCompetition;
use App\Providers\OrderShipped;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function updateOrderStatus($orderId,$status)
    {
        //dd($orderId,$request);
        // Validar la solicitud
        // $request->validate([
        //     'status' => 'required|string',
        // ]);

        // dd($orderId);

        // Encontrar el pedido por ID
        $competition = DebateCompetition::find($orderId); //dd($competition);

        // Verificar si el pedido existe
        if (!$competition) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Actualizar el estado del pedido
        $competition->status_active = $status;
        $competition->save();

        // Emitir el evento
        // broadcast(new OrderStatusUpdated($competition))->toOthers();
        // OrderStatusUpdated::dispatch($competition);
        OrderShipped::dispatch($competition);

        return response()->json(['message' => 'Order status updated successfully: '.$status]);
    }
}
