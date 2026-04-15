@extends('layouts.app')

@section('title', 'Pedido #'.$order->order_number.' - CombosPlus+')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Pedido #{{ $order->order_number }}</h1>
        @if($order->status == 'pending')
            <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('¿Cancelar este pedido?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">Cancelar pedido</button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Productos</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden">
                                <img src="{{ $item->product->image ? Storage::disk('local')->temporaryUrl($item->product->image, now()->addYear()) : 'https://placehold.co/100x100?text='.urlencode($item->product->name) }}" class="w-full h-full object-contain">
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold">{{ $item->product->name }}</h3>
                                <p class="text-sm text-gray-500">Cantidad: {{ $item->quantity }} x ${{ number_format($item->price, 2, '.', '') }}</p>
                            </div>
                            <div class="font-semibold">${{ number_format($item->price * $item->quantity, 2, '.', '') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-20">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Detalles</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Estado:</span>
                        <span class="font-medium
                            @if($order->status == 'pending') text-yellow-600
                            @elseif($order->status == 'paid') text-blue-600
                            @elseif($order->status == 'shipped') text-purple-600
                            @elseif($order->status == 'delivered') text-green-600
                            @elseif($order->status == 'cancelled') text-red-600
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fecha:</span>
                        <span class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Método de pago:</span>
                        <span class="font-medium">{{ $order->paymentMethod->name ?? 'No especificado' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Entrega:</span>
                        <span class="font-medium">{{ $order->delivery_type == 'delivery' ? 'Domicilio' : 'Recoger en tienda' }}</span>
                    </div>

                    @if($order->address)
                        <div class="border-t pt-3">
                            <span class="text-gray-600 block mb-1">Dirección:</span>
                            <p class="font-medium text-sm">
                                {{ $order->address->name }}<br>
                                {{ $order->address->address_line1 }}<br>
                                @if($order->address->address_line2) {{ $order->address->address_line2 }}<br> @endif
                                {{ $order->address->city }}, {{ $order->address->state }}<br>
                                {{ $order->address->country }} - {{ $order->address->postal_code }}<br>
                                Tel: {{ $order->address->phone }}
                            </p>
                        </div>
                    @endif

                    <div class="border-t pt-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">${{ number_format($order->subtotal, 2, '.', '') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Costo de envío:</span>
                            <span class="font-medium">${{ number_format($order->shipping_cost, 2, '.', '') }}</span>
                        </div>
                        <div class="flex justify-between text-base font-bold mt-2">
                            <span>Total:</span>
                            <span class="text-green-600">${{ number_format($order->total, 2, '.', '') }}</span>
                        </div>
                    </div>

                    @if($order->notes)
                        <div class="border-t pt-3">
                            <span class="text-gray-600 block mb-1">Notas:</span>
                            <p class="text-sm">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>

                <a href="{{ route('orders.index') }}" class="block text-center mt-6 text-sm text-green-600 hover:text-green-700">
                    <i class="fas fa-arrow-left mr-1"></i> Volver a mis pedidos
                </a>
            </div>
        </div>
    </div>
</div>
@endsection