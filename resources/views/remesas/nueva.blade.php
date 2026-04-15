@extends('layouts.app')

@section('title', 'Nuevo envío a Cuba - CombosPlus+')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('remesas.index') }}" class="text-green-600 hover:text-green-700 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver a remesas
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-8 text-white">
            <h1 class="text-3xl font-bold flex items-center">
                <i class="fas fa-paper-plane mr-3"></i>
                Nuevo envío a Cuba
            </h1>
            <p class="text-green-100 mt-2">Completa los datos del remitente y destinatario</p>
        </div>

        <form action="{{ route('remesas.enviar') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Datos del remitente -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <span class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-3">1</span>
                    Datos del remitente
                </h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre completo *</label>
                        <input type="text" name="remitente_nombre" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                               value="{{ old('remitente_nombre', auth()->user()->name ?? '') }}">
                        @error('remitente_nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="remitente_email" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                               value="{{ old('remitente_email', auth()->user()->email ?? '') }}">
                        @error('remitente_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
                        <input type="text" name="remitente_telefono" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                               value="{{ old('remitente_telefono') }}">
                        @error('remitente_telefono') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Datos del destinatario -->
            <div class="pt-4 border-t border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <span class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-3">2</span>
                    Datos del destinatario en Cuba
                </h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre completo *</label>
                        <input type="text" name="destinatario_nombre" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                               value="{{ old('destinatario_nombre') }}">
                        @error('destinatario_nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Carnet de identidad *</label>
                        <input type="text" name="destinatario_ci" required maxlength="11"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                               value="{{ old('destinatario_ci') }}">
                        @error('destinatario_ci') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
                        <input type="text" name="destinatario_telefono" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                               value="{{ old('destinatario_telefono') }}">
                        @error('destinatario_telefono') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dirección completa *</label>
                        <input type="text" name="destinatario_direccion" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                               value="{{ old('destinatario_direccion') }}">
                        @error('destinatario_direccion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Provincia *</label>
                        <select id="provincia" name="provincia_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">Seleccione provincia</option>
                            @foreach($provincias ?? [] as $provincia)
                                <option value="{{ $provincia->id }}" {{ old('provincia_id') == $provincia->id ? 'selected' : '' }}>
                                    {{ $provincia->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('provincia_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Municipio *</label>
                        <select name="municipio_id" id="municipio" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">Primero seleccione provincia</option>
                        </select>
                        @error('municipio_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Detalles del envío -->
            <div class="pt-4 border-t border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <span class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-3">3</span>
                    Detalles del envío
                </h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monto a enviar (USD) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" name="monto" id="monto" step="0.01" min="10" max="1000" required
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                   value="{{ old('monto') }}">
                        </div>
                        @error('monto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Moneda de origen *</label>
                        <select name="moneda_origen" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="USD" {{ old('moneda_origen') == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('moneda_origen') == 'EUR' ? 'selected' : '' }}>EUR</option>
                        </select>
                        @error('moneda_origen') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Método de envío *</label>
                        <select name="metodo_envio_id" id="metodo_envio" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">Seleccione método</option>
                            @foreach($metodosEnvio ?? [] as $metodo)
                                <option value="{{ $metodo->id }}" data-costo="{{ $metodo->costo_base }}" {{ old('metodo_envio_id') == $metodo->id ? 'selected' : '' }}>
                                    {{ $metodo->nombre }} - ${{ number_format($metodo->costo_base, 2) }} ({{ $metodo->tiempo_estimado_horas }}h)
                                </option>
                            @endforeach
                        </select>
                        @error('metodo_envio_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Resumen calculado dinámicamente -->
                <div x-data="resumenEnvio()" x-init="init()" class="mt-6 bg-green-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-3">Resumen del envío</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Monto enviado:</span>
                            <span class="font-medium" x-text="'$' + monto"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Comisión:</span>
                            <span class="font-medium" x-text="'$' + comision"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tasa de cambio:</span>
                            <span class="font-medium" x-text="'1 USD = ' + tasaCambio + ' CUP'"></span>
                        </div>
                        <div class="border-t border-green-200 my-2 pt-2">
                            <div class="flex justify-between font-bold text-green-700">
                                <span>Recibirán aproximadamente:</span>
                                <span x-text="'$' + montoFinalCUP + ' CUP'"></span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">*El monto final puede variar ligeramente según la tasa del día.</p>
                </div>
            </div>

            <!-- Términos y condiciones -->
            <div class="pt-4">
                <label class="flex items-start">
                    <input type="checkbox" name="acepto_terminos" required class="mt-1 mr-3">
                    <span class="text-sm text-gray-600">
                        He leído y acepto los <a href="#" class="text-green-600 hover:underline">Términos y condiciones</a> del servicio de remesas, y confirmo que los datos ingresados son correctos.
                    </span>
                </label>
                @error('acepto_terminos') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Botón de envío -->
            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition flex items-center">
                    <i class="fas fa-lock mr-2"></i>
                    Continuar al pago
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script para carga dinámica de municipios y cálculo de resumen -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinciaSelect = document.getElementById('provincia');
    const municipioSelect = document.getElementById('municipio');

    provinciaSelect.addEventListener('change', function() {
        const provinciaId = this.value;
        municipioSelect.innerHTML = '<option value="">Cargando...</option>';
        
        if (provinciaId) {
            fetch(`/api/municipios/${provinciaId}`)
                .then(response => response.json())
                .then(data => {
                    municipioSelect.innerHTML = '<option value="">Seleccione municipio</option>';
                    data.forEach(municipio => {
                        municipioSelect.innerHTML += `<option value="${municipio.id}">${municipio.nombre}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    municipioSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        } else {
            municipioSelect.innerHTML = '<option value="">Primero seleccione provincia</option>';
        }
    });
});

// Alpine component para resumen dinámico
function resumenEnvio() {
    return {
        monto: {{ old('monto', 0) }},
        metodoEnvioId: {{ old('metodo_envio_id', 0) }},
        comision: 0,
        tasaCambio: 120,
        montoFinalCUP: 0,
        
        init() {
            this.calcular();
            // Escuchar cambios en los campos
            document.getElementById('monto').addEventListener('input', (e) => {
                this.monto = e.target.value;
                this.calcular();
            });
            document.getElementById('metodo_envio').addEventListener('change', (e) => {
                this.metodoEnvioId = e.target.value;
                this.calcular();
            });
        },
        
        calcular() {
            if (this.monto && this.metodoEnvioId) {
                fetch('{{ route("remesas.calcular-costo") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        monto: this.monto,
                        metodo_envio: this.metodoEnvioId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.comision = data.costo_envio;
                        this.montoFinalCUP = data.monto_en_cup;
                        this.tasaCambio = data.tasa_cambio;
                    }
                })
                .catch(console.error);
            }
        }
    }
}
</script>
@endsection