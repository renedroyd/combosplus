<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ZellePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ZellePaymentController extends Controller
{
    /**
     * Muestra la página con instrucciones de pago.
     */
    public function show(Order $order)
    {
        // Aquí puedes agregar validación para asegurarte de que el pedido pertenece al usuario autenticado
        if ($order->user_id != auth()->id()) abort(403);

        // Verifica que el pedido esté en estado pendiente de pago
        if ($order->payment_status !== 'pending') {
            return redirect()->route('orders.show', $order)->with('error', 'Este pedido no está pendiente de pago.');
        }

        return view('payment.zelle.show', [
            'order' => $order,
            'zelleEmail' => config('zelle.account_email'),
            'zellePhone' => config('zelle.account_phone'),
            'zelleName' => config('zelle.account_name'),
        ]);
    }

    /**
     * Procesa la confirmación del pago (subida de comprobante y referencia).
     */
    public function confirm(Request $request, Order $order)
    {
        // if ($order->user_id != auth()->id()) abort(403);

        $request->validate([
            'reference_number' => 'nullable|string|max:255',
            'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // máx 2MB
        ]);

        // Verificar que no exista ya un pago Zelle para este pedido
        if ($order->zellePayment) {
            return back()->with('error', 'Ya se ha registrado un pago para este pedido.');
        }

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('zelle-proofs', 'public');
        }

        $zellePayment = ZellePayment::create([
            'order_id' => $order->id,
            'reference_number' => $request->reference_number,
            'proof_path' => $proofPath,
            'status' => 'pending',
        ]);

        // Actualizar estado del pedido
        $order->update(['payment_status' => 'pending']); // Podría ser ya pending_payment, pero asegura

        // Opcional: enviar notificación a administradores
        // event(new PaymentConfirmed($zellePayment));

        return redirect()->route('orders.show', $order)->with('success', 'Gracias por confirmar el pago. Lo revisaremos a la brevedad.');
    }
}