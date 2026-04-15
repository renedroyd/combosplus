@extends('layouts.app')

@section('title', 'Mi Carrito - CombosPlus+')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Mi Carrito de Compras</h1>

    @if(!$cart || $cart->items->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="mb-4">
                <i class="fas fa-shopping-cart text-6xl text-gray-300"></i>
            </div>
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">Tu carrito está vacío</h2>
            <p class="text-gray-500 mb-6">¡Explora nuestros productos y agrega tus favoritos!</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-8 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i> Seguir comprando
            </a>
        </div>
    @else
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Lista de productos -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h2 class="font-semibold text-gray-700">Productos ({{ $cart->items->sum('quantity') }})</h2>
                    </div>

                    @foreach($cart->items as $item)
                        <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row items-start sm:items-center gap-4 hover:bg-gray-50 transition cart-item" data-item-id="{{ $item->id }}">
                            <!-- Imagen -->
                            <div class="w-24 h-24 bg-white rounded-lg overflow-hidden flex-shrink-0">
                                <img src="{{ $item->product->image ? Storage::disk('local')->temporaryUrl($item->product->image, now()->addYear()) : 'https://placehold.co/200x200?text='.urlencode($item->product->name) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="w-full h-full object-contain">
                            </div>

                            <!-- Info -->
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">{{ $item->product->name }}</h3>
                                <p class="text-sm text-gray-500">{{ Str::limit($item->product->description, 60) }}</p>
                                <div class="mt-2">
                                    <span class="text-green-600 font-bold product-price">${{ number_format($item->price, 2) }}</span>
                                </div>
                            </div>

                            <!-- Control cantidad -->
                            <div class="flex items-center space-x-3">
                                <button class="quantity-decrease w-8 h-8 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                                    <i class="fas fa-minus text-sm"></i>
                                </button>
                                <span class="quantity-value w-8 text-center font-medium">{{ $item->quantity }}</span>
                                <button class="quantity-increase w-8 h-8 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                                    <i class="fas fa-plus text-sm"></i>
                                </button>
                            </div>

                            <!-- Total por producto -->
                            <div class="w-24 text-right font-semibold text-gray-800 item-total">
                                ${{ number_format($item->product->price * $item->quantity, 2) }}
                            </div>

                            <!-- Botón eliminar -->
                            <button class="remove-item text-red-500 hover:text-red-700 transition" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Resumen -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-20">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Resumen de compra</h2>

                    @php
                        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
                        $envio = 5.00;
                        $total = $subtotal + $envio;
                    @endphp

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium subtotal-value">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Envío</span>
                            <span class="font-medium">${{ number_format($envio, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between text-base font-bold">
                            <span>Total</span>
                            <span class="text-green-600 total-value">${{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="w-full mt-6 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition block text-center">
                        Proceder al pago
                    </a>

                    <a href="{{ route('products.index') }}" class="block text-center mt-4 text-sm text-green-600 hover:text-green-700">
                        <i class="fas fa-arrow-left mr-1"></i> Seguir comprando
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
@parent
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para actualizar resumen y contador global
    function updateSummary(data) {
        document.querySelector('.subtotal-value').textContent = '$' + data.subtotal;
        document.querySelector('.total-value').textContent = '$' + data.total;
        const cartCount = document.getElementById('cart-count');
        if (cartCount && data.totalItems !== undefined) {
            cartCount.textContent = data.totalItems;
        }
    }

    // Mostrar notificación flotante
    function showNotification(message, type) {
        const notif = document.createElement('div');
        notif.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transition transform duration-300 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
        notif.textContent = message;
        document.body.appendChild(notif);
        setTimeout(() => {
            notif.style.opacity = '0';
            setTimeout(() => notif.remove(), 300);
        }, 3000);
    }

    // Manejar incremento/decremento
    document.querySelectorAll('.quantity-increase, .quantity-decrease').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const itemDiv = this.closest('.cart-item');
            const qtySpan = itemDiv.querySelector('.quantity-value');
            let quantity = parseInt(qtySpan.textContent);
            const itemId = itemDiv.dataset.itemId;

            if (this.classList.contains('quantity-increase')) {
                quantity++;
            } else {
                if (quantity > 1) quantity--;
                else return; // mínimo 1
            }

            // Actualización optimista
            qtySpan.textContent = quantity;

            fetch(`/cart/item/${itemId}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ quantity })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    itemDiv.querySelector('.item-total').textContent = '$' + data.itemTotal;
                    updateSummary(data);
                } else {
                    qtySpan.textContent = quantity - (this.classList.contains('quantity-increase') ? 1 : -1);
                    showNotification('Error al actualizar', 'error');
                }
            })
            .catch(() => {
                qtySpan.textContent = quantity - (this.classList.contains('quantity-increase') ? 1 : -1);
                showNotification('Error de conexión', 'error');
            });
        });
    });

    // Eliminar item
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!confirm('¿Eliminar este producto?')) return;

            const itemDiv = this.closest('.cart-item');
            const itemId = itemDiv.dataset.itemId;

            fetch(`/cart/item/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    itemDiv.remove();
                    updateSummary(data);
                    if (data.totalItems == 0) {
                        location.reload(); // muestra carrito vacío
                    } else {
                        showNotification('Producto eliminado', 'success');
                    }
                } else {
                    showNotification('Error al eliminar', 'error');
                }
            })
            .catch(() => showNotification('Error de conexión', 'error'));
        });
    });
});
</script>
@endsection