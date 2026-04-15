@extends('layouts.app')

@section('title', 'Buscar envío - CombosPlus+')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8 text-white">
            <h1 class="text-3xl font-bold flex items-center">
                <i class="fas fa-search mr-3"></i>
                Buscar envío
            </h1>
            <p class="text-blue-100 mt-2">Ingresa el código de tu remesa para ver su estado</p>
        </div>

        <div class="p-6">
            <form action="{{ route('remesas.seguimiento.buscar') }}" method="GET" class="mb-6">
                <div class="flex">
                    <input type="text" name="codigo" value="{{ request('codigo') }}" 
                           placeholder="Ej: CUB-ABC123-456" 
                           class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-r-lg font-semibold transition">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                </div>
            </form>

            @if(isset($remesa))
                @include('remesas.seguimiento', ['remesa' => $remesa])
            @elseif(request()->has('codigo'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    No se encontró ninguna remesa con el código "{{ request('codigo') }}". Verifica el código e intenta nuevamente.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection