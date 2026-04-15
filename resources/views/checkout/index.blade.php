@extends('layouts.app')

@section('title', 'Finalizar Compra - CombosPlus+')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Finalizar Compra</h1>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Formulario de checkout -->
        <div class="lg:w-2/3">
            <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                @csrf

                <!-- Método de entrega -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Método de entrega</h2>
                    <div class="space-y-3">
                        <label class="flex items-center space-x-3">
                            <input type="radio" name="delivery_type" value="delivery" class="text-green-600 focus:ring-green-500" checked>
                            <span>Envío a domicilio</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="radio" name="delivery_type" value="pickup" class="text-green-600 focus:ring-green-500">
                            <span>Recoger en tienda</span>
                        </label>
                    </div>
                </div>

                <!-- Selección de dirección (visible solo si entrega a domicilio) -->
                <div id="address-section" class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold text-gray-800">Dirección de envío</h2>
                        <a href="{{ route('addresses.create') }}" class="text-sm text-green-600 hover:text-green-700">+ Nueva dirección</a>
                    </div>

                    @if($addresses->isEmpty())
                        <p class="text-gray-500 mb-4">No tienes direcciones guardadas. <a href="{{ route('addresses.create') }}" class="text-green-600">Agrega una</a> para continuar.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($addresses as $address)
                                <label class="flex items-start space-x-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" class="mt-1 text-green-600 focus:ring-green-500" {{ $address->is_default ? 'checked' : '' }}>
                                    <div>
                                        <strong>{{ $address->alias ?? 'Dirección' }}</strong>
                                        @if($address->is_default) <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Principal</span> @endif
                                        <p class="text-sm text-gray-600">
                                            {{ $address->name }}<br>
                                            {{ $address->address_line1 }}<br>
                                            {{ $address->city }}, {{ $address->state }} - {{ $address->postal_code }}<br>
                                            Tel: {{ $address->phone }}
                                        </p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Método de pago -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Método de pago</h2>
                    <div class="space-y-3">
                        @foreach($paymentMethods as $method)
                            <label class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="payment_method_id" value="{{ $method->id }}" class="text-green-600 focus:ring-green-500" required>
                                <div>
                                    <strong>{{ $method->name }}</strong>
                                    @if($method->description)
                                        <p class="text-sm text-gray-500">{{ $method->description }}</p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Notas adicionales -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <label for="notes" class="block text-gray-700 font-medium mb-2">Notas para el pedido (opcional)</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition">
                    Confirmar pedido
                </button>
            </form>
        </div>

        <!-- Resumen del pedido -->
        <div class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-20">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Resumen del pedido</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">${{ number_format($subtotal, 2, '.', '') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Costo de envío</span>
                        <span class="font-medium shipping-cost">${{ number_format($shippingCost, 2, '.', '') }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between text-base font-bold">
                        <span>Total</span>
                        <span class="text-green-600 total-value">${{ number_format($total, 2, '.', '') }}</span>
                    </div>
                </div>

                <p class="text-xs text-gray-500 mt-4">Los productos y precios están sujetos a disponibilidad.</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Mostrar/ocultar sección de dirección según método de entrega
    const deliveryRadios = document.querySelectorAll('input[name="delivery_type"]');
    const addressSection = document.getElementById('address-section');

    function toggleAddressSection() {
        const selected = document.querySelector('input[name="delivery_type"]:checked').value;
        if (selected === 'delivery') {
            addressSection.style.display = 'block';
        } else {
            addressSection.style.display = 'none';
        }
    }

    deliveryRadios.forEach(radio => {
        radio.addEventListener('change', toggleAddressSection);
    });
    toggleAddressSection();
</script>
@endsection