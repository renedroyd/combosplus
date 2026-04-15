<div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300 flex flex-col h-full">
    <!-- Imagen -->
    <div class="h-48 w-full bg-white overflow-hidden">
        <img src="{{ $product->image ? Storage::disk('local')->temporaryUrl($product->image, now()->addYear()) : 'https://placehold.co/600x400?text='.urlencode($product->name) }}" 
             alt="{{ $product->name }}" 
             class="w-full h-full object-contain transition-transform duration-300 hover:scale-105"
             loading="lazy">
    </div>
    
    <!-- Contenido principal -->
    <div class="p-4 flex-1 flex flex-col">
        <h3 class="font-semibold text-lg text-gray-800">{{ $product->name }}</h3>
        <p class="text-sm text-gray-500 mt-1">{{ $product->description ?? 'Sin descripción' }}</p>
        
        <!-- Precio y valoración -->
        <div class="mt-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-green-600 font-bold text-xl">
                    ${{ number_format($product->price, 2) }}
                </span>
                @if(isset($product->compare_price) && $product->compare_price > 0)
                    <span class="text-sm text-gray-400 line-through">
                        ${{ number_format($product->compare_price, 2) }}
                    </span>
                @endif
            </div>
            
            <!-- Estrellas si existen -->
            @if(isset($product->rating))
                <div class="flex items-center">
                    @for ($i = 1; $i <= 5; $i++)
                        @if($i <= floor($product->rating))
                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                        @elseif($i - $product->rating < 0.5 && $i - $product->rating > 0)
                            <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                        @else
                            <i class="far fa-star text-yellow-400 text-sm"></i>
                        @endif
                    @endfor
                    <span class="text-xs text-gray-500 ml-1">({{ number_format($product->rating, 1) }})</span>
                </div>
            @endif
        </div>

        <!-- Botón añadir al carrito (con AJAX) -->
        <button type="button"
                class="add-to-cart-btn mt-4 w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition flex items-center justify-center"
                data-url="{{ route('cart.add', $product) }}"
                data-product-name="{{ $product->name }}">
            <i class="fas fa-cart-plus mr-2"></i> Añadir al carrito
        </button>
    </div>
</div>