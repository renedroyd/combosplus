@extends('layouts.app')

@section('title', 'Pago vía Zelle - CombosPlus+')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Tarjeta principal -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Cabecera -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-5">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-university text-white text-2xl"></i>
                    <h1 class="text-2xl font-bold text-white">Pago vía Zelle</h1>
                </div>
            </div>

            <!-- Cuerpo -->
            <div class="p-6 md:p-8">
                <!-- Resumen del pedido -->
                <div class="bg-gray-50 rounded-xl p-5 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Pedido #{{ $order->id }}</p>
                        <p class="text-lg font-semibold text-gray-800">Total a pagar</p>
                    </div>
                    <div class="text-3xl font-bold text-green-600">${{ number_format($order->total, 2) }}</div>
                </div>

                <!-- Instrucciones paso a paso -->
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-green-600 mr-2"></i>
                    Instrucciones
                </h2>

                <div class="space-y-5">
                    <!-- Paso 1 -->
                    <div class="flex">
                        <div class="flex-shrink-0 mr-4">
                            <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold">1</div>
                        </div>
                        <div>
                            <p class="text-gray-700">Inicia sesión en tu aplicación bancaria y accede a <span class="font-semibold">Zelle</span>.</p>
                        </div>
                    </div>

                    <!-- Paso 2 -->
                    <div class="flex">
                        <div class="flex-shrink-0 mr-4">
                            <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold">2</div>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-700 mb-2">Envía el monto exacto de <span class="font-semibold text-green-600">${{ number_format($order->total, 2) }}</span> a la siguiente cuenta:</p>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-2">
                                @if($zelleEmail)
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope text-gray-500 w-6"></i>
                                        <span class="text-gray-700"><span class="font-medium">Email:</span> {{ $zelleEmail }}</span>
                                    </div>
                                @endif
                                @if($zellePhone)
                                    <div class="flex items-center">
                                        <i class="fas fa-phone-alt text-gray-500 w-6"></i>
                                        <span class="text-gray-700"><span class="font-medium">Teléfono:</span> {{ $zellePhone }}</span>
                                    </div>
                                @endif
                                @if($zelleName)
                                    <div class="flex items-center">
                                        <i class="fas fa-user text-gray-500 w-6"></i>
                                        <span class="text-gray-700"><span class="font-medium">Beneficiario:</span> {{ $zelleName }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Paso 3 -->
                    <div class="flex">
                        <div class="flex-shrink-0 mr-4">
                            <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold">3</div>
                        </div>
                        <div>
                            <p class="text-gray-700">Después de realizar la transferencia, completa el formulario a continuación.</p>
                        </div>
                    </div>
                </div>

                <hr class="my-8 border-gray-200">

                <!-- Formulario de confirmación -->
                <form method="POST" action="{{ route('zelle.confirm', $order) }}" enctype="multipart/form-data" id="zelleForm">
                    @csrf

                    <!-- Número de referencia -->
                    <div class="mb-6">
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-hashtag text-gray-500 mr-1"></i>
                            Número de referencia <span class="text-gray-400 font-normal">(opcional)</span>
                        </label>
                        <input type="text" 
                               name="reference_number" 
                               id="reference_number" 
                               value="{{ old('reference_number') }}"
                               placeholder="Ej: 1234567890"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('reference_number') border-red-500 @enderror">
                        @error('reference_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">Si tu banco proporciona un número de referencia, ingrésalo aquí.</p>
                        @enderror
                    </div>

                    <!-- Comprobante (imagen) -->
                    <div class="mb-6">
                        <label for="proof" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-camera text-gray-500 mr-1"></i>
                            Captura de pantalla del comprobante <span class="text-gray-400 font-normal">(opcional)</span>
                        </label>
                        
                        <!-- Vista previa -->
                        <div id="proofPreviewContainer" class="mb-3 hidden">
                            <img id="proofPreview" src="#" alt="Vista previa" class="max-h-48 rounded-lg border border-gray-300 shadow-sm">
                        </div>

                        <input type="file" 
                               name="proof" 
                               id="proof" 
                               accept="image/jpeg,image/png,image/gif,image/jpg"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 @error('proof') border-red-500 @enderror">

                        @error('proof')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">Máximo 2 MB. Formatos: JPG, JPEG, PNG, GIF.</p>
                        @enderror
                    </div>

                    <!-- Email de contacto (opcional) -->
                    <div class="mb-6">
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-gray-500 mr-1"></i>
                            Email de contacto <span class="text-gray-400 font-normal">(opcional)</span>
                        </label>
                        <input type="email" 
                               name="contact_email" 
                               id="contact_email" 
                               value="{{ old('contact_email', auth()->user()->email ?? '') }}"
                               placeholder="tucorreo@ejemplo.com"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        <p class="mt-1 text-sm text-gray-500">Te notificaremos cualquier novedad sobre tu pago.</p>
                    </div>

                    <!-- Botones -->
                    <div class="flex flex-col sm:flex-row gap-3 mt-8">
                        <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            Confirmar pago realizado
                        </button>
                        <a href="{{ route('orders.show', $order) }}" class="sm:flex-none bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Vista previa de la imagen y validación de tamaño
    document.getElementById('proof').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('proofPreviewContainer');
        const preview = document.getElementById('proofPreview');

        if (file) {
            // Validar tamaño (2 MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('El archivo no debe exceder los 2 MB.');
                event.target.value = ''; // Limpiar input
                previewContainer.classList.add('hidden');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.classList.add('hidden');
        }
    });
</script>
@endsection