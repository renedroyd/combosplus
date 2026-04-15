@extends('layouts.app')

@section('title', 'Inicio - CombosPlus+')

@section('content')
    <!-- HERO CARRUSEL (mejorado con indicadores dinámicos y transiciones) -->
    <section class="relative w-full h-[500px] md:h-[600px] overflow-hidden">
        @php
            $slides = [
                ['image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e', 'title' => '¡Frescos y saludables!', 'subtitle' => 'Hasta 30% de descuento en frutas y verduras', 'btn_text' => 'Comprar ahora', 'btn_link' => route('products.index')],
                ['image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1', 'title' => 'Carnes de calidad', 'subtitle' => 'Ofertas especiales en cortes seleccionados', 'btn_text' => 'Descubrir', 'btn_link' => route('offers')],
                ['image' => 'https://images.unsplash.com/photo-1604909052743-94e838986d24', 'title' => 'Lácteos frescos', 'subtitle' => '2x1 en yogures y quesos', 'btn_text' => 'Aprovechar', 'btn_link' => route('products.index', ['categoria' => 'lacteos'])],
            ];
        @endphp

        @foreach($slides as $index => $slide)
            <div class="slide absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                 style="background-image: url('{{ $slide['image'] }}?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80'); background-size: cover; background-position: center;">
                <div class="absolute inset-0 bg-black/40 z-10"></div>
                <div class="absolute inset-0 z-20 flex flex-col items-center justify-center text-white text-center px-4">
                    <h2 class="text-3xl md:text-5xl font-bold mb-4 animate__animated animate__fadeInDown">{{ $slide['title'] }}</h2>
                    <p class="text-lg md:text-xl mb-6 animate__animated animate__fadeInUp">{{ $slide['subtitle'] }}</p>
                    <a href="{{ $slide['btn_link'] }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition transform hover:scale-105">
                        {{ $slide['btn_text'] }}
                    </a>
                </div>
            </div>
        @endforeach

        <!-- Controles -->
        <button onclick="prevSlide()" class="absolute left-4 top-1/2 transform -translate-y-1/2 z-30 bg-white/30 hover:bg-white/50 text-white p-3 rounded-full transition backdrop-blur-sm">
            <i class="fas fa-chevron-left text-2xl"></i>
        </button>
        <button onclick="nextSlide()" class="absolute right-4 top-1/2 transform -translate-y-1/2 z-30 bg-white/30 hover:bg-white/50 text-white p-3 rounded-full transition backdrop-blur-sm">
            <i class="fas fa-chevron-right text-2xl"></i>
        </button>

        <!-- Indicadores -->
        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 z-30 flex space-x-3">
            @foreach($slides as $index => $slide)
                <button onclick="goToSlide({{ $index }})" class="indicator w-3 h-3 rounded-full bg-white/50 hover:bg-white transition {{ $index === 0 ? 'bg-white' : '' }}"></button>
            @endforeach
        </div>
    </section>

    <!-- CATEGORÍAS POPULARES (desde BD) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Categorías populares</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['categoria' => $category->slug]) }}" 
                class="group block text-center p-6 rounded-2xl shadow-lg hover:shadow-2xl transition bg-gradient-to-br from-green-50 to-white border border-green-100 hover:border-green-300">
                    <div class="w-20 h-20 mx-auto mb-3 rounded-full overflow-hidden bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                        @if($category->image)
                            <img src="{{ $category->image ? Storage::disk('local')->temporaryUrl($category->image, now()->addYear()) : 'https://placehold.co/600x400?text='.urlencode($category->name) }}" 
                                alt="{{ $category->name }}" 
                                class="w-full h-full object-cover"
                                loading="lazy">
                        @else
                            <i class="fas {{ $category->icon ?? 'fa-tag' }} text-3xl text-green-600"></i>
                        @endif
                    </div>
                    <span class="font-medium text-gray-800 group-hover:text-green-600">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- SECCIÓN PARALLAX 1: FRESCURA Y CALIDAD -->
    <section class="parallax relative bg-fixed bg-cover bg-center py-24" style="background-image: url('https://images.unsplash.com/photo-1610832958506-aa56368176cf?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');" loading="lazy">
        <div class="absolute inset-0 bg-gradient-to-t from-green-900/80 via-green-800/50 to-transparent"></div>
        <div class="relative z-10 max-w-4xl mx-auto text-center text-white px-4">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">Productos 100% frescos</h2>
            <p class="text-xl mb-8">Directo del campo a tu mesa. Seleccionamos los mejores productos para ti.</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-white text-green-700 hover:bg-green-100 font-semibold py-3 px-8 rounded-lg transition transform hover:scale-105 shadow-lg">
                Ver productos
            </a>
        </div>
    </section>

    <!-- PRODUCTOS DESTACADOS -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Productos destacados</h2>
            <a href="{{ route('products.index') }}" class="text-green-600 hover:text-green-700 font-medium flex items-center gap-1">
                Ver todos <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($featuredProducts as $product)
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300 flex flex-col h-full">
                    <!-- Imagen -->
                    <div class="h-48 w-full bg-white overflow-hidden">
                        <img src="{{ $product->image ? Storage::disk('local')->temporaryUrl($product->image, now()->addYear()) : 'https://placehold.co/600x400?text='.urlencode($product->name) }}" 
                            alt="{{ $product->name }}" 
                            class="w-full h-full object-contain transition-transform duration-300 hover:scale-105"
                            loading="lazy">
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
                <p class="col-span-full text-center text-gray-500 py-12">No hay productos destacados en este momento.</p>
            @endforelse
        </div>
    </section>

    <!-- SECCIÓN PARALLAX 2: OFERTAS DEL DÍA -->
    <section class="parallax relative bg-fixed bg-cover bg-center py-24" style="background-image: url('https://images.unsplash.com/photo-1610832958506-aa56368176cf?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');" loading="lazy">
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
        <div class="relative z-10 max-w-4xl mx-auto text-center text-white px-4">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">Ofertas del día</h2>
            <div class="flex justify-center gap-4 mb-8" id="countdown">
                <div class="bg-white/20 backdrop-blur-md rounded-lg p-4 min-w-[80px]">
                    <span class="text-3xl font-bold" id="days">00</span>
                    <span class="block text-sm">Días</span>
                </div>
                <div class="bg-white/20 backdrop-blur-md rounded-lg p-4 min-w-[80px]">
                    <span class="text-3xl font-bold" id="hours">00</span>
                    <span class="block text-sm">Horas</span>
                </div>
                <div class="bg-white/20 backdrop-blur-md rounded-lg p-4 min-w-[80px]">
                    <span class="text-3xl font-bold" id="minutes">00</span>
                    <span class="block text-sm">Minutos</span>
                </div>
                <div class="bg-white/20 backdrop-blur-md rounded-lg p-4 min-w-[80px]">
                    <span class="text-3xl font-bold" id="seconds">00</span>
                    <span class="block text-sm">Segundos</span>
                </div>
            </div>
            <a href="{{ route('offers') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition transform hover:scale-105 shadow-lg">
                Ver ofertas
            </a>
        </div>
    </section>

    <!-- OFERTAS ESPECIALES (si existen) -->
    @if(isset($offers) && $offers->count())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Ofertas especiales</h2>
            <a href="{{ route('offers') }}" class="text-green-600 hover:text-green-700 font-medium flex items-center gap-1">
                Ver todas <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($offers as $product)
                <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition overflow-hidden group">
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
            @endforeach
        </div>
    </section>
    @endif

    <!-- TESTIMONIOS DE CLIENTES -->
    <section class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Lo que dicen nuestros clientes</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @php
                    $testimonials = [
                        ['name' => 'María González', 'text' => 'Excelente calidad y servicio. Los productos siempre llegan frescos y a tiempo.', 'rating' => 5],
                        ['name' => 'Carlos Pérez', 'text' => 'Me encanta la variedad y los precios. Mi tienda de confianza.', 'rating' => 5],
                        ['name' => 'Ana Martínez', 'text' => 'El mejor lugar para comprar alimentos saludables. Totalmente recomendado.', 'rating' => 5],
                    ];
                @endphp
                @foreach($testimonials as $testimonial)
                    <div class="bg-white rounded-xl shadow-md p-6 relative">
                        <i class="fas fa-quote-left text-green-200 text-4xl absolute top-4 left-4"></i>
                        <p class="text-gray-600 mb-4 relative z-10">"{{ $testimonial['text'] }}"</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white font-bold mr-3">
                                {{ substr($testimonial['name'], 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-semibold">{{ $testimonial['name'] }}</h4>
                                <div class="flex text-yellow-400">
                                    @for($i = 0; $i < $testimonial['rating']; $i++)
                                        <i class="fas fa-star"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- BANNER SUSCRIPCIÓN (con cuenta regresiva) -->
    <section class="bg-green-700 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">¡10% de descuento en tu primera compra!</h2>
            <p class="text-xl mb-8">Suscríbete a nuestro boletín y recibe el cupón directamente en tu correo</p>
            <form class="flex flex-col sm:flex-row justify-center gap-3 max-w-lg mx-auto">
                <input type="email" 
                    placeholder="ejemplo@correo.com" 
                    class="px-4 py-3 rounded-lg text-white bg-white/20 backdrop-blur-sm 
                            flex-1 focus:outline-none focus:ring-2 focus:ring-white 
                            border border-white/30 placeholder:text-white/70">
                <button class="bg-white text-green-700 hover:bg-gray-100 font-semibold py-3 px-8 rounded-lg transition transform hover:scale-105">Suscribirme</button>
            </form>
        </div>
    </section>
@endsection

@section('scripts')
@parent
<script>
    // Carrusel mejorado
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const indicators = document.querySelectorAll('.indicator');

    function showSlide(index) {
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        slides.forEach((slide, i) => {
            slide.classList.toggle('opacity-100', i === index);
            slide.classList.toggle('opacity-0', i !== index);
        });
        indicators.forEach((indicator, i) => {
            indicator.classList.toggle('bg-white', i === index);
            indicator.classList.toggle('bg-white/50', i !== index);
        });
        currentSlide = index;
    }

    function nextSlide() { showSlide(currentSlide + 1); }
    function prevSlide() { showSlide(currentSlide - 1); }
    function goToSlide(index) { showSlide(index); }

    // Auto-play cada 6 segundos
    setInterval(nextSlide, 6000);

    // Cuenta regresiva para ofertas del día (ejemplo: 2 días desde ahora)
    function startCountdown(targetDate) {
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                document.getElementById('days').textContent = '00';
                document.getElementById('hours').textContent = '00';
                document.getElementById('minutes').textContent = '00';
                document.getElementById('seconds').textContent = '00';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('days').textContent = days.toString().padStart(2, '0');
            document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    // Establecer fecha objetivo: 2 días a partir de ahora
    const targetDate = new Date();
    targetDate.setDate(targetDate.getDate() + 2);
    startCountdown(targetDate);
</script>
@endsection