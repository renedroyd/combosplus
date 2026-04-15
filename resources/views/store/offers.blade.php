@extends('layouts.app')

@section('title', 'Ofertas - CombosPlus+')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Cabecera -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">🔥 Ofertas especiales</h1>
            <p class="text-lg text-gray-600">Aprovecha descuentos exclusivos en productos seleccionados</p>
        </div>

        <!-- Grid de productos en oferta -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($offers as $product)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300 flex flex-col h-full">
                    <!-- Imagen -->
                    <div class="relative h-48 overflow-hidden">
                        <img src="{{ $product->image ? Storage::disk('local')->temporaryUrl($product->image, now()->addYear()) : 'https://placehold.co/600x400?text='.urlencode($product->name) }}" 
                            alt="{{ $product->name }}" 
                            class="w-full h-full object-contain transition-transform duration-300 hover:scale-105"
                            loading="lazy">
                        <span class="absolute top-2 left-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">Oferta</span>
                    </div>
                    
                    <!-- Contenido principal (flex-1 para empujar el botón hacia abajo) -->
                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="font-semibold text-lg text-gray-800">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $product->description ?? 'Sin descripción' }}</p>
                        
                        <!-- Precio y valoración en la misma línea -->
                        <div class="mt-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                @php
                                    $price_parts = explode('.', number_format($product->price, 2, '.', ''));
                                    $integer_part = $price_parts[0];
                                    $decimal_part = $price_parts[1];
                                @endphp
                                <span class="text-green-600 font-bold text-xl">
                                    ${{ $integer_part }}<span class="text-sm">.{{ $decimal_part }}</span>
                                </span>
                                @if(isset($product->compare_price) && $product->compare_price > 0)
                                    @php
                                        $compare_parts = explode('.', number_format($product->compare_price, 2, '.', ''));
                                    @endphp
                                    <span class="text-sm text-gray-400 line-through">
                                        ${{ $compare_parts[0] }}<span class="text-xs">.{{ $compare_parts[1] }}</span>
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Estrellas de valoración -->
                            @if(isset($product->rating))
                                <div class="flex items-center">
                                    @php
                                        $rating = $product->rating;
                                        $fullStars = floor($rating);
                                        $halfStar = $rating - $fullStars >= 0.5 ? 1 : 0;
                                        $emptyStars = 5 - $fullStars - $halfStar;
                                    @endphp
                                    @for ($i = 0; $i < $fullStars; $i++)
                                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                                    @endfor
                                    @if ($halfStar)
                                        <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                                    @endif
                                    @for ($i = 0; $i < $emptyStars; $i++)
                                        <i class="far fa-star text-yellow-400 text-sm"></i>
                                    @endfor
                                    <span class="text-xs text-gray-500 ml-1">({{ number_format($rating, 1) }})</span>
                                </div>
                            @endif
                        </div>

                        <!-- Botón añadir al carrito (ocupa todo el ancho) -->
                        <button type="button"
                                class="add-to-cart-btn mt-4 w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition flex items-center justify-center"
                                data-url="{{ route('cart.add', $product) }}"
                                data-product-name="{{ $product->name }}">
                            <i class="fas fa-cart-plus mr-2"></i> Añadir al carrito
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-500">
                    No hay ofertas disponibles en este momento. ¡Vuelve pronto!
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        @if(method_exists($offers, 'links'))
            <div class="mt-8">
                {{ $offers->links() }}
            </div>
        @endif
    </div>
@endsection