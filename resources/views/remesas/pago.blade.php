@extends('layouts.app')

@section('title', 'Pagar envío - CombosPlus+')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-8 text-white">
            <h1 class="text-3xl font-bold flex items-center">
                <i class="fas fa-credit-card mr-3"></i>
                Completar pago
            </h1>
            <p class="text-green-100 mt-2">Remesa #{{ $remesa->codigo }}</p>
        </div>

        <div class="p-6">
            <!-- Resumen de la remesa -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h2 class="font-semibold text-gray-800 mb-3">Resumen de tu envío</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Destinatario:</p>
                        <p class="font-medium">{{ $remesa->destinatario_nombre }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Monto enviado:</p>
                        <p class="font-medium">${{ number_format($remesa->monto, 2) }} USD</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Método de envío:</p>
                        <p class="font-medium">{{ $remesa->metodoEnvio->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Recibirán en CUP:</p>
                        <p class="font-medium text-green-600">${{ number_format($remesa->monto_recibir, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Opciones de pago -->
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Selecciona método de pago</h2>
            
            <div x-data="{ metodo: 'tarjeta' }" class="space-y-4">
                <!-- Tarjeta de crédito/débito -->
                <div class="border rounded-lg p-4" :class="{ 'border-green-500 bg-green-50': metodo === 'tarjeta' }">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="metodo_pago" value="tarjeta" x-model="metodo" class="mr-3">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Tarjeta de crédito/débito</span>
                                <div class="flex space-x-2">
                                    <i class="fab fa-cc-visa text-2xl text-blue-600"></i>
                                    <i class="fab fa-cc-mastercard text-2xl text-orange-600"></i>
                                    <i class="fab fa-cc-amex text-2xl text-blue-400"></i>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500">Pago seguro con Stripe</p>
                        </div>
                    </label>
                    <div x-show="metodo === 'tarjeta'" x-transition class="mt-4">
                        <!-- Aquí iría el formulario de Stripe o similar -->
                        <div class="bg-yellow-50 p-3 rounded-lg text-sm text-yellow-700 mb-3">
                            <i class="fas fa-info-circle mr-2"></i>
                            Modo prueba: usa 4242 4242 4242 4242, fecha futura, CVC 123
                        </div>
                        <form action="{{ route('remesas.procesar-pago', $remesa->codigo) }}" method="POST" id="payment-form">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Número de tarjeta</label>
                                <div id="card-number" class="border rounded-lg p-3 bg-white"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de expiración</label>
                                    <div id="card-expiry" class="border rounded-lg p-3 bg-white"></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">CVC</label>
                                    <div id="card-cvc" class="border rounded-lg p-3 bg-white"></div>
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition">
                                Pagar ${{ number_format($remesa->monto, 2) }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- PayPal -->
                <div class="border rounded-lg p-4" :class="{ 'border-green-500 bg-green-50': metodo === 'paypal' }">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="metodo_pago" value="paypal" x-model="metodo" class="mr-3">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">PayPal</span>
                                <i class="fab fa-paypal text-2xl text-blue-800"></i>
                            </div>
                            <p class="text-sm text-gray-500">Paga con tu cuenta de PayPal</p>
                        </div>
                    </label>
                    <div x-show="metodo === 'paypal'" x-transition class="mt-4">
                        <a href="{{ route('remesas.paypal', $remesa->codigo) }}" 
                           class="block w-full bg-yellow-400 hover:bg-yellow-500 text-center text-gray-800 font-semibold py-3 rounded-lg transition">
                            <i class="fab fa-paypal mr-2"></i>
                            Continuar con PayPal
                        </a>
                    </div>
                </div>

                <!-- Transferencia bancaria -->
                <div class="border rounded-lg p-4" :class="{ 'border-green-500 bg-green-50': metodo === 'transferencia' }">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="metodo_pago" value="transferencia" x-model="metodo" class="mr-3">
                        <div class="flex-1">
                            <span class="font-medium">Transferencia bancaria</span>
                            <p class="text-sm text-gray-500">Paga desde tu banco (puede demorar 1-2 días)</p>
                        </div>
                    </label>
                    <div x-show="metodo === 'transferencia'" x-transition class="mt-4 bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-700 mb-2">Datos para la transferencia:</p>
                        <ul class="text-sm space-y-1">
                            <li><span class="font-medium">Banco:</span> Banco de Ejemplo</li>
                            <li><span class="font-medium">Titular:</span> CombosPlus+ S.A.</li>
                            <li><span class="font-medium">Cuenta:</span> 1234-5678-9012-3456</li>
                            <li><span class="font-medium">Monto:</span> ${{ number_format($remesa->monto, 2) }} USD</li>
                            <li><span class="font-medium">Referencia:</span> {{ $remesa->codigo }}</li>
                        </ul>
                        <p class="text-xs text-gray-500 mt-2">Una vez realizada la transferencia, envía el comprobante a pagos@combosplus.com</p>
                    </div>
                </div>
            </div>

            <!-- Botón de cancelar -->
            <div class="mt-6 text-center">
                <a href="{{ route('remesas.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">
                    <i class="fas fa-times mr-1"></i> Cancelar y volver
                </a>
            </div>
        </div>
    </div>
</div>

@if(config('services.stripe.key'))
<!-- Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements();
    
    const cardNumber = elements.create('cardNumber');
    const cardExpiry = elements.create('cardExpiry');
    const cardCvc = elements.create('cardCvc');
    
    cardNumber.mount('#card-number');
    cardExpiry.mount('#card-expiry');
    cardCvc.mount('#card-cvc');
    
    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        
        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: 'card',
            card: cardNumber,
        });
        
        if (error) {
            alert(error.message);
        } else {
            // Enviar paymentMethod.id al servidor
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'payment_method_id');
            hiddenInput.setAttribute('value', paymentMethod.id);
            form.appendChild(hiddenInput);
            
            form.submit();
        }
    });
</script>
@endif
@endsection