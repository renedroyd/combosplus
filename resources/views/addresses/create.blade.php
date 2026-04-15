@extends('layouts.app')

@section('title', 'Nueva Dirección - CombosPlus+')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Nueva Dirección</h1>

    <form action="{{ route('addresses.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf

        <div class="mb-4">
            <label for="alias" class="block text-gray-700 font-medium mb-2">Alias (opcional)</label>
            <input type="text" name="alias" id="alias" value="{{ old('alias') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-medium mb-2">Nombre del destinatario *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('name') border-red-500 @enderror">
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="address_line1" class="block text-gray-700 font-medium mb-2">Dirección línea 1 *</label>
            <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div class="mb-4">
            <label for="address_line2" class="block text-gray-700 font-medium mb-2">Dirección línea 2 (opcional)</label>
            <input type="text" name="address_line2" id="address_line2" value="{{ old('address_line2') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="city" class="block text-gray-700 font-medium mb-2">Ciudad *</label>
                <input type="text" name="city" id="city" value="{{ old('city') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="mb-4">
                <label for="state" class="block text-gray-700 font-medium mb-2">Provincia/Estado *</label>
                <input type="text" name="state" id="state" value="{{ old('state') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="postal_code" class="block text-gray-700 font-medium mb-2">Código postal *</label>
                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="mb-4">
                <label for="country" class="block text-gray-700 font-medium mb-2">País</label>
                <input type="text" name="country" id="country" value="{{ old('country', 'Chile') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>

        <div class="mb-4">
            <label for="phone" class="block text-gray-700 font-medium mb-2">Teléfono de contacto</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div class="mb-6">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="rounded text-green-600 focus:ring-green-500">
                <span class="ml-2 text-gray-700">Establecer como dirección principal</span>
            </label>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('addresses.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Guardar</button>
        </div>
    </form>
</div>
@endsection