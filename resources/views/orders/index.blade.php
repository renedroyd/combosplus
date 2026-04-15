@extends('layouts.app')

@section('title', 'Mis Pedidos - CombosPlus+')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Mis Pedidos</h1>

    @if($orders->isEmpty())
        <p class="text-gray-500">No has realizado ningún pedido aún.</p>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex flex-wrap justify-between items-center">
                        <div>
                            <span class="text-sm text-gray-500">Pedido #{{ $order->order_number }}</span>
                            <span class="ml-4 px-2 py-1 text-xs rounded-full
                                @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'paid') bg-blue-100 text-blue-800
                                @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                                @elseif($order->status == 'delivered') bg-green-100 text-green-800
                                @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="font-semibold">Total: ${{ number_format($order->total, 2, '.', '') }}</p>
                        <p class="text-sm text-gray-600">{{ $order->items->count() }} producto(s)</p>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('orders.show', $order) }}" class="text-green-600 hover:text-green-700 font-medium">
                            Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection