@extends('layouts.app')

@section('title', 'Resultados de búsqueda - CombosPlus+')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Resultados para: "{{ $query }}"</h1>

    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @else
        <p class="text-gray-500 text-center py-12">No se encontraron productos que coincidan con tu búsqueda.</p>
    @endif
</div>
@endsection