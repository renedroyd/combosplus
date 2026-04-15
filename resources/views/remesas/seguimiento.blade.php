@extends('layouts.app')

@section('title', 'Seguimiento de envío - CombosPlus+')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8 text-white">
            <h1 class="text-3xl font-bold flex items-center">
                <i class="fas fa-search mr-3"></i>
                Seguimiento de envío
            </h1>
            <p class="text-blue-100 mt-2">Código: <span class="font-mono">{{ $remesa->codigo }}</span></p>
        </div>

        <div class="p-6">
            <!-- Estado actual -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Estado actual</h2>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($remesa->estado == 'pendiente') bg-yellow-100 text-yellow-800
                        @elseif($remesa->estado == 'pagado') bg-blue-100 text-blue-800
                        @elseif($remesa->estado == 'procesando') bg-purple-100 text-purple-800
                        @elseif($remesa->estado == 'enviado') bg-indigo-100 text-indigo-800
                        @elseif($remesa->estado == 'entregado') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($remesa->estado) }}
                    </span>
                </div>

                <!-- Barra de progreso -->
                @php
                    $estados = ['pendiente', 'pagado', 'procesando', 'enviado', 'entregado'];
                    $currentIndex = array_search($remesa->estado, $estados);
                    $progress = $currentIndex !== false ? (($currentIndex + 1) / count($estados)) * 100 : 0;
                @endphp
                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6">
                    <div class="bg-green-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                </div>

                <!-- Timeline -->
                <div class="relative">
                    @foreach(['pendiente', 'pagado', 'procesando', 'enviado', 'entregado'] as $index => $estado)
                        <div class="flex items-start mb-6 relative">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                {{ $index <= $currentIndex ? 'bg-green-600 text-white' : 'bg-gray-300 text-gray-500' }}">
                                @if($index < $currentIndex)
                                    <i class="fas fa-check"></i>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="font-semibold {{ $index <= $currentIndex ? 'text-gray-800' : 'text-gray-400' }}">
                                    {{ ucfirst($estado) }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    @switch($estado)
                                        @case('pendiente')
                                            Esperando confirmación de pago
                                            @break
                                        @case('pagado')
                                            Pago recibido, procesando tu envío
                                            @break
                                        @case('procesando')
                                            Preparando para enviar a Cuba
                                            @break
                                        @case('enviado')
                                            En camino a destino final
                                            @break
                                        @case('entregado')
                                            Entregado al destinatario
                                            @break
                                    @endswitch
                                </p>
                                @if($estado == 'entregado' && $remesa->entregado_en)
                                    <p class="text-xs text-gray-400 mt-1">Entregado el {{ $remesa->entregado_en->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                        @if(!$loop->last)
                            <div class="absolute left-4 top-8 bottom-0 w-0.5 bg-gray-300" style="height: calc(100% - 2rem);"></div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Detalles del envío -->
            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Detalles del envío</h2>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Destinatario:</p>
                        <p class="font-medium">{{ $remesa->destinatario_nombre }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Carnet de identidad:</p>
                        <p class="font-medium">{{ $remesa->destinatario_ci }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Teléfono en Cuba:</p>
                        <p class="font-medium">{{ $remesa->destinatario_telefono }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Dirección:</p>
                        <p class="font-medium">{{ $remesa->destinatario_direccion }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Municipio:</p>
                        <p class="font-medium">{{ $remesa->municipio->nombre }} ({{ $remesa->municipio->provincia->nombre }})</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Monto enviado:</p>
                        <p class="font-medium">${{ number_format($remesa->monto, 2) }} {{ $remesa->moneda_origen }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Recibirán en CUP:</p>
                        <p class="font-medium text-green-600">${{ number_format($remesa->monto_recibir, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Método de envío:</p>
                        <p class="font-medium">{{ $remesa->metodoEnvio->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Fecha de creación:</p>
                        <p class="font-medium">{{ $remesa->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($remesa->pagado_en)
                    <div>
                        <p class="text-gray-500">Pagado el:</p>
                        <p class="font-medium">{{ $remesa->pagado_en->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="border-t border-gray-200 mt-6 pt-6 flex justify-between">
                <a href="{{ route('remesas.index') }}" class="text-green-600 hover:text-green-700">
                    <i class="fas fa-arrow-left mr-1"></i> Nuevo envío
                </a>
                @if($remesa->user_id == auth()->id())
                    <a href="#" onclick="window.print()" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-print mr-1"></i> Imprimir comprobante
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection