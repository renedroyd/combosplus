<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ZellePayment;
use Illuminate\Http\Request;

class ZellePaymentController extends Controller
{
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        if ($order->payment_status !== 'pending') return redirect()->route('orders.show', $order)->with('error', 'Este pedido no está pendiente de pago.');
        return view('payment.zelle.show', ['order'=>$order,'zelleEmail'=>config('zelle.account_email'),'zellePhone'=>config('zelle.account_phone'),'zelleName'=>config('zelle.account_name')]);
    }

    public function confirm(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        if ($order->payment_status !== 'pending') return back()->with('error', 'Este pedido no está pendiente de pago.');
        $request->validate(['reference_number'=>'nullable|string|max:255','proof'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048']);
        if ($order->zellePayment) return back()->with('error', 'Ya se ha registrado un pago para este pedido.');
        $proofPath = $request->hasFile('proof') ? $request->file('proof')->store('zelle-proofs', 'public') : null;
        ZellePayment::create(['order_id'=>$order->id,'reference_number'=>$request->reference_number,'proof_path'=>$proofPath,'status'=>'pending']);
        $order->update(['payment_status'=>'pending']);
        return redirect()->route('orders.show', $order)->with('success', 'Gracias por confirmar el pago. Lo revisaremos a la brevedad.');
    }
}
