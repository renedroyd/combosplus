@extends('layouts.app')

@section('title', 'Enviar Remesas a Cuba - CombosPlus+')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header con imagen de fondo y familia feliz -->
    <div class="relative bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl overflow-hidden mb-8">
        <!-- Imagen de fondo (familia feliz) con opacidad y mezcla -->
        <div class="absolute inset-0 opacity-20 mix-blend-overlay">
            <img src="https://images.unsplash.com/photo-1609220136736-443140cffec6?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" 
                alt="Familia feliz recibiendo dinero" 
                class="w-full h-full object-cover">
        </div>
        <!-- Elementos decorativos flotantes (difuminados) -->
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-yellow-300/20 rounded-full blur-3xl"></div>
        
        <div class="relative px-6 py-8 md:px-12 md:py-12 text-white">
            <div class="flex items-center justify-between flex-wrap gap-6">
                <div class="max-w-2xl">
                    <h1 class="text-3xl md:text-4xl font-bold mb-3 drop-shadow-lg">
                        Envía dinero a Cuba 🇨🇺
                    </h1>
                    <p class="text-green-100 text-lg drop-shadow">
                        Rápido, seguro y confiable. Tus seres queridos en Cuba recibirán el dinero en minutos.
                    </p>
                    <!-- Botón de acción principal -->
                    <div class="mt-6">
                        <a href="{{ route('remesas.nueva') }}" class="inline-flex items-center bg-white text-green-700 hover:bg-green-50 font-semibold py-3 px-6 rounded-full shadow-lg transition transform hover:scale-105">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Enviar ahora
                        </a>
                    </div>
                </div>
                <div class="flex space-x-4">
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-3 text-center shadow-lg">
                        <span class="block text-2xl font-bold">24h</span>
                        <span class="text-xs opacity-90">Entrega</span>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-3 text-center shadow-lg">
                        <span class="block text-2xl font-bold">$0</span>
                        <span class="text-xs opacity-90">Comisión*</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calculadora de Remesas en Tiempo Real -->
    <div x-data="remesaCalculator()" x-init="init()" class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-calculator text-green-600 mr-3"></i>
            Calcula cuánto recibirán en Cuba
        </h2>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Formulario de cálculo -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Monto a enviar (USD)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" x-model="monto" @input="calcular" 
                               class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="100.00" min="10" max="1000">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Mínimo $10, máximo $1000</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Método de entrega
                    </label>
                    <select x-model="metodoEnvio" @change="calcular" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="banco">Transferencia bancaria (24h)</option>
                        <option value="efectivo">Efectivo a domicilio (48h)</option>
                        <option value="tarjeta">Tarjeta prepago (inmediato)</option>
                    </select>
                </div>

                <!-- Resumen en tiempo real -->
                <div class="bg-green-50 p-4 rounded-lg space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Monto enviado:</span>
                        <span class="font-medium" x-text="'$' + monto"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Comisión:</span>
                        <span class="font-medium" x-text="'$' + comision"></span>
                    </div>
                    <div class="border-t border-green-200 my-2 pt-2">
                        <div class="flex justify-between font-bold text-green-700">
                            <span>Recibirán en CUP:</span>
                            <span x-text="'$' + montoFinalCUP"></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1" x-text="'Tasa: 1 USD = ' + tasaCambio + ' CUP'"></p>
                    </div>
                </div>
            </div>

            <!-- Beneficios y características -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="font-semibold text-gray-800 mb-4">¿Por qué enviar con nosotros?</h3>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <i class="fas fa-shield-alt text-green-600 mt-1 mr-3"></i>
                        <span class="text-gray-600">Seguridad garantizada en cada transacción</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-bolt text-green-600 mt-1 mr-3"></i>
                        <span class="text-gray-600">Transferencias inmediatas a tarjetas</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt text-green-600 mt-1 mr-3"></i>
                        <span class="text-gray-600">Entrega a domicilio en toda Cuba</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-headset text-green-600 mt-1 mr-3"></i>
                        <span class="text-gray-600">Soporte en español 24/7</span>
                    </li>
                </ul>
                
                <div class="mt-6">
                    <a href="{{ route('remesas.nueva') }}" 
                       class="block w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition">
                        <i class="fas fa-paper-plane mr-2"></i>Comenzar envío
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cómo funciona -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-green-600 font-bold text-xl">1</span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2">Calcula</h3>
            <p class="text-gray-600 text-sm">Ingresa el monto y elige cómo quieres enviar</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-green-600 font-bold text-xl">2</span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2">Paga online</h3>
            <p class="text-gray-600 text-sm">Usa tu tarjeta de crédito/débito o transferencia</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-green-600 font-bold text-xl">3</span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2">Reciben en Cuba</h3>
            <p class="text-gray-600 text-sm">En minutos o horas, según el método elegido</p>
        </div>
    </div>

    <!-- Provincias y municipios -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Cobertura en toda Cuba</h2>
        <div class="grid md:grid-cols-4 gap-4">
            @php
                $provincias = ['La Habana', 'Santiago de Cuba', 'Holguín', 'Villa Clara', 'Camagüey', 'Granma', 'Guantánamo', 'Pinar del Río'];
            @endphp
            @foreach($provincias as $provincia)
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-green-700 mb-2">{{ $provincia }}</h3>
                <p class="text-sm text-gray-600">Cobertura total</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Sección de seguimiento -->
    <div class="mt-8 bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-xl overflow-hidden">
        <div class="px-6 py-8 md:px-12 flex items-center justify-between flex-wrap gap-6">
            <div class="text-white">
                <h3 class="text-xl font-bold mb-2">¿Ya enviaste? Da seguimiento</h3>
                <p class="text-blue-100">Ingresa el código de tu envío para ver el estado</p>
            </div>
            <form action="{{ route('remesas.seguimiento.buscar') }}" method="GET" class="flex-1 max-w-md">
                <div class="flex">
                    <input type="text" name="codigo" placeholder="Ej: CUB-ABC123-456" 
                           class="flex-1 px-4 py-3 rounded-l-lg focus:outline-none">
                    <button type="submit" class="bg-white text-blue-700 px-6 py-3 rounded-r-lg font-semibold hover:bg-gray-100 transition">
                        <i class="fas fa-search mr-2"></i>Seguir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script de Alpine.js para la calculadora -->
<script>
function remesaCalculator() {
    return {
        monto: 100,
        metodoEnvio: 'banco',
        comision: 0,
        montoFinalCUP: 0,
        tasaCambio: 120,
        
        init() {
            this.calcular();
            // Actualizar tasa cada 5 minutos
            setInterval(() => {
                this.actualizarTasa();
            }, 300000);
        },
        
        calcular() {
            // Calcular comisión según método
            if (this.metodoEnvio === 'banco') {
                this.comision = this.monto > 500 ? 5 : 8;
            } else if (this.metodoEnvio === 'efectivo') {
                this.comision = this.monto > 500 ? 7 : 10;
            } else {
                this.comision = 3; // Tarjeta prepago
            }
            
            // Calcular monto final en CUP
            const montoNeto = this.monto - this.comision;
            this.montoFinalCUP = (montoNeto * this.tasaCambio).toFixed(2);
            
            // Enviar a backend para cálculos precisos (opcional)
            fetch('{{ route("remesas.calcular-costo") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    'monto': this.monto,
                    'metodo_envio': this.metodoEnvio
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
        },
        
        actualizarTasa() {
            // Aquí iría la lógica para actualizar tasa desde API
            console.log('Actualizando tasa de cambio...');
        }
    }
}
</script>
@endsection