@extends('layouts.app')

@section('title', 'Finalizar compra - CombosPlus+')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <i class="fas fa-lock text-6xl text-gray-300 mb-4"></i>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Para continuar, inicia sesión o regístrate</h1>
        <p class="text-gray-600 mb-6">Necesitas estar autenticado para completar tu compra. No te preocupes, tu carrito se guardará.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('login') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                Iniciar sesión
            </a>
            <a href="{{ route('register') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition">
                Crear cuenta
            </a>
        </div>
        <a href="{{ route('cart.index') }}" class="block mt-6 text-sm text-green-600 hover:text-green-700">
            <i class="fas fa-arrow-left mr-1"></i> Volver al carrito
        </a>
    </div>
</div>
@endsection