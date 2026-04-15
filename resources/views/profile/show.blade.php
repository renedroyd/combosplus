@extends('layouts.app')

@section('title', 'Mi Perfil - CombosPlus+')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Mi Perfil</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabs con Alpine.js -->
    <div x-data="{ tab: 'profile' }" class="mb-8">
        <!-- Cabecera de pestañas -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px space-x-8">
                <button @click="tab = 'profile'"
                        :class="{ 'border-green-500 text-green-600': tab === 'profile', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'profile' }"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition">
                    Datos personales
                </button>
                <button @click="tab = 'addresses'"
                        :class="{ 'border-green-500 text-green-600': tab === 'addresses', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'addresses' }"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition">
                    Direcciones
                </button>
                <button @click="tab = 'orders'"
                        :class="{ 'border-green-500 text-green-600': tab === 'orders', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'orders' }"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition">
                    Mis pedidos
                </button>
            </nav>
        </div>

        <!-- Contenido de las pestañas -->
        <div class="mt-8">
            <!-- Pestaña: Datos personales -->
            <div x-show="tab === 'profile'" x-transition>
                <div class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Datos personales</h2>
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 font-medium mb-2">Nombre</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-medium mb-2">Correo electrónico</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <hr class="my-6">

                        <h3 class="font-semibold text-gray-800 mb-4">Cambiar contraseña</h3>
                        <p class="text-sm text-gray-600 mb-4">Deja en blanco si no deseas cambiarla.</p>

                        <div class="mb-4">
                            <label for="current_password" class="block text-gray-700 font-medium mb-2">Contraseña actual</label>
                            <input type="password" id="current_password" name="current_password"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('current_password') border-red-500 @enderror">
                            @error('current_password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password" class="block text-gray-700 font-medium mb-2">Nueva contraseña</label>
                            <input type="password" id="new_password" name="new_password"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('new_password') border-red-500 @enderror">
                            @error('new_password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="new_password_confirmation" class="block text-gray-700 font-medium mb-2">Confirmar nueva contraseña</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition">
                            Actualizar perfil
                        </button>
                    </form>
                </div>
            </div>

            <!-- Pestaña: Direcciones -->
            <div x-show="tab === 'addresses'" x-transition>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-bold text-gray-800">Mis direcciones</h2>
                        <a href="{{ route('addresses.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-plus mr-1"></i> Nueva dirección
                        </a>
                    </div>

                    @if($addresses->isEmpty())
                        <p class="text-gray-500 text-center py-8">No tienes direcciones guardadas.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($addresses as $address)
                                <div class="border rounded-lg p-4 {{ $address->is_default ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold">{{ $address->alias ?? 'Sin alias' }}</h3>
                                            @if($address->is_default)
                                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mt-1">Principal</span>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('addresses.edit', $address) }}" class="text-blue-600 hover:text-blue-800" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('¿Eliminar esta dirección?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <p class="text-sm text-gray-700 mt-2">
                                        {{ $address->recipient_name }}<br>
                                        {{ $address->address_line1 }}<br>
                                        @if($address->address_line2) {{ $address->address_line2 }}<br> @endif
                                        {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                        {{ $address->country }}<br>
                                        @if($address->phone) Tel: {{ $address->phone }} @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pestaña: Pedidos -->
            <div x-show="tab === 'orders'" x-transition>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-6">Mis pedidos</h2>

                    @php
                        $orders = auth()->user()->orders()->latest()->get(); // O puedes pasar $orders desde el controlador
                    @endphp

                    @if($orders->isEmpty())
                        <p class="text-gray-500 text-center py-8">No has realizado ningún pedido aún.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($orders as $order)
                                <div class="border rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex flex-wrap justify-between items-center">
                                        <div>
                                            <span class="text-sm text-gray-500">Pedido #{{ $order->order_number }}</span>
                                            <span class="ml-4 px-2 py-1 text-xs rounded-full
                                                @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                                @elseif($order->status == 'completed') bg-green-100 text-green-800
                                                @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>

                                    <div class="mt-4 flex justify-between items-center">
                                        <div>
                                            <p class="font-semibold">Total: ${{ number_format($order->total, 2, '.', '') }}</p>
                                            <p class="text-sm text-gray-500">{{ $order->items->count() }} producto(s)</p>
                                        </div>
                                        <a href="{{ route('orders.show', $order) }}" class="text-green-600 hover:text-green-700 font-medium text-sm">
                                            Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection