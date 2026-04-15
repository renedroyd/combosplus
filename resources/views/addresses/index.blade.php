@extends('layouts.app')

@section('title', 'Mis Direcciones - CombosPlus+')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Mis Direcciones</h1>
        <a href="{{ route('addresses.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Nueva dirección
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($addresses->isEmpty())
        <p class="text-gray-500">No tienes direcciones guardadas.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($addresses as $address)
                <div class="bg-white rounded-lg shadow-md p-6 {{ $address->is_default ? 'border-2 border-green-500' : '' }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-lg">{{ $address->alias ?? 'Dirección' }}</h3>
                            @if($address->is_default)
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Principal</span>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('addresses.edit', $address) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('¿Eliminar esta dirección?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <p class="text-gray-700 mt-2">
                        {{ $address->name }}<br>
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
@endsection