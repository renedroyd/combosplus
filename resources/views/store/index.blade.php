@extends('layouts.app')

@section('title', 'Productos - CombosPlus+')

@section('content')
    <!-- HERO SECTION - CARRUSEL PROMOCIONAL -->
    <section class="relative w-full h-[400px] md:h-[500px] overflow-hidden">
        <!-- Slides -->
        <div id="slide1" class="absolute inset-0 transition-opacity duration-700 opacity-100">
            <div class="absolute inset-0 bg-black/40 z-10"></div>
            <img src="{{ asset('images/carrusel1.png') }}" 
                 alt="Promoción frutas y verduras" 
                 class="w-full h-full  object-contain">
            <div class="absolute inset-0 z-20 flex flex-col items-center justify-center text-white text-center px-4">
                <h2 class="text-3xl md:text-5xl font-bold mb-4">¡Frescos y saludables!</h2>
                <p class="text-lg md:text-xl mb-6">Hasta 30% de descuento en frutas y verduras</p>
                <a href="#" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition">Comprar ahora</a>
            </div>
        </div>
        <div id="slide2" class="absolute inset-0 transition-opacity duration-700 opacity-0">
            <div class="absolute inset-0 bg-black/40 z-10"></div>
            <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" 
                 alt="Promoción carnes" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 z-20 flex flex-col items-center justify-center text-white text-center px-4">
                <h2 class="text-3xl md:text-5xl font-bold mb-4">Carnes de calidad</h2>
                <p class="text-lg md:text-xl mb-6">Ofertas especiales en cortes seleccionados</p>
                <a href="#" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition">Descubrir</a>
            </div>
        </div>
        <div id="slide3" class="absolute inset-0 transition-opacity duration-700 opacity-0">
            <div class="absolute inset-0 bg-black/40 z-10"></div>
            <img src="https://images.unsplash.com/photo-1604909052743-94e838986d24?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2073&q=80" 
                 alt="Promoción lácteos" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 z-20 flex flex-col items-center justify-center text-white text-center px-4">
                <h2 class="text-3xl md:text-5xl font-bold mb-4">Lácteos frescos</h2>
                <p class="text-lg md:text-xl mb-6">2x1 en yogures y quesos</p>
                <a href="#" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition">Aprovechar</a>
            </div>
        </div>

        <!-- Controles (anterior/siguiente) -->
        <button onclick="prevSlide()" class="absolute left-4 top-1/2 transform -translate-y-1/2 z-30 bg-white/30 hover:bg-white/50 text-white p-2 rounded-full transition">
            <i class="fas fa-chevron-left text-2xl"></i>
        </button>
        <button onclick="nextSlide()" class="absolute right-4 top-1/2 transform -translate-y-1/2 z-30 bg-white/30 hover:bg-white/50 text-white p-2 rounded-full transition">
            <i class="fas fa-chevron-right text-2xl"></i>
        </button>

        <!-- Indicadores -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-30 flex space-x-2">
            <button onclick="goToSlide(0)" class="w-3 h-3 rounded-full bg-white/50 hover:bg-white transition" id="indicator0"></button>
            <button onclick="goToSlide(1)" class="w-3 h-3 rounded-full bg-white/50 hover:bg-white transition" id="indicator1"></button>
            <button onclick="goToSlide(2)" class="w-3 h-3 rounded-full bg-white/50 hover:bg-white transition" id="indicator2"></button>
        </div>
    </section>

    <!-- CONTENIDO PRINCIPAL (FILTROS IZQUIERDA / PRODUCTOS DERECHA) -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- SECCIÓN DE FILTROS (IZQUIERDA) -->
            <aside class="lg:w-80 order-1">
                <form method="GET" action="{{ route('products.index') }}" id="filtros-form" class="bg-white rounded-xl shadow-md p-6 sticky top-20">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-filter mr-2 text-green-600"></i> Filtros
                    </h3>

                    <!-- Categorías -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-700 mb-2">Categorías</h4>
                        <ul class="space-y-2">
                            @forelse ($categories as $category)
                            <li>
                                <label class="flex items-center justify-between text-gray-600">
                                    <span class="flex items-center">
                                        <input type="checkbox" 
                                               name="categorias[]" 
                                               value="{{ $category->id }}"
                                               {{ in_array($category->id, request()->input('categorias', [])) ? 'checked' : '' }}
                                               class="rounded text-green-600 focus:ring-green-500 mr-2">
                                        {{ $category->name }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-gray-800">
                                        {{ $category->products_count }}
                                    </span>
                                </label>
                            </li>
                            @empty
                            <li class="text-gray-500">No hay categorías disponibles</li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Rango de precio -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-700 mb-2">Rango de precio</h4>
                        <div class="flex items-center space-x-2">
                            <input type="number" 
                                   name="min_price" 
                                   placeholder="Min" 
                                   value="{{ request('min_price') }}"
                                   class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <span>-</span>
                            <input type="number" 
                                   name="max_price" 
                                   placeholder="Max" 
                                   value="{{ request('max_price') }}"
                                   class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>

                    <!-- Etiquetas populares (ahora como checkboxes) -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-700 mb-2">Etiquetas</h4>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $etiquetas = ['Orgánico', 'Sin gluten', 'Vegano', 'Sin azúcar', 'Keto', 'Natural'];
                                $selectedTags = request()->input('tags', []);
                            @endphp
                            @foreach ($etiquetas as $tag)
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       name="tags[]" 
                                       value="{{ $tag }}"
                                       {{ in_array($tag, $selectedTags) ? 'checked' : '' }}
                                       class="hidden peer">
                                <span class="px-3 py-1 rounded-full text-sm cursor-pointer transition
                                             bg-gray-200 text-gray-700 
                                             peer-checked:bg-green-600 peer-checked:text-white">
                                    {{ $tag }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Valoración -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-700 mb-2">Valoración</h4>
                        <select name="rating" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Cualquiera</option>
                            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 estrellas</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 estrellas o más</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 estrellas o más</option>
                        </select>
                    </div>

                    <button type="submit" class="mt-6 w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        Aplicar filtros
                    </button>

                    <!-- Enlace para limpiar filtros (opcional) -->
                    <a href="{{ route('products.index') }}" class="block text-center mt-2 text-sm text-gray-500 hover:text-gray-700">
                        Limpiar filtros
                    </a>
                </form>
            </aside>

            <!-- SECCIÓN DE PRODUCTOS (DERECHA) -->
            <section class="flex-1 order-2">
                <!-- Título y selector de ordenamiento alineados -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Nuestros productos</h2>
                    <div class="mt-2 sm:mt-0">
                        <label for="ordenar" class="sr-only">Ordenar por</label>
                        <select id="ordenar" 
                                name="orden" 
                                form="filtros-form" 
                                onchange="document.getElementById('filtros-form').submit()"
                                class="block w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 bg-white">
                            <option value="relevancia" {{ request('orden') == 'relevancia' ? 'selected' : '' }}>Más relevantes</option>
                            <option value="popular" {{ request('orden') == 'popular' ? 'selected' : '' }}>Más populares</option>
                            <option value="price_asc" {{ request('orden') == 'price_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                            <option value="price_desc" {{ request('orden') == 'price_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                            <option value="rating" {{ request('orden') == 'rating' ? 'selected' : '' }}>Mejor valorados</option>
                            <option value="newest" {{ request('orden') == 'newest' ? 'selected' : '' }}>Novedades</option>
                        </select>
                    </div>
                </div>

                <!-- Grid de productos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($products as $product)
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
                        <div class="col-span-full text-center py-12 text-gray-500">
                            No hay productos disponibles en este momento.
                        </div>
                    @endforelse
                </div>

                <!-- Paginación con filtros -->
                @if(method_exists($products, 'links'))
                <div class="mt-8">
                    {{ $products->appends(request()->query())->links() }}
                </div>
                @endif
            </section>
        </div>
    </main>


    <!-- BOTÓN FLOTANTE VOLVER ARRIBA -->
    <button id="btnVolverArriba" class="fixed bottom-6 right-6 bg-green-600 hover:bg-green-700 text-white p-3 rounded-full shadow-lg transition-opacity duration-300 opacity-0 invisible z-50">
        <i class="fas fa-arrow-up text-xl"></i>
    </button>

    <
@endsection
@section('scripts')
    <!-- JavaScript para el carrusel, botón volver arriba y navbar -->
    <script>
        // Carrusel
        let currentSlide = 0;
        const slides = document.querySelectorAll('[id^="slide"]');
        const indicators = document.querySelectorAll('[id^="indicator"]');
        const totalSlides = slides.length;

        function showSlide(index) {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            slides.forEach((slide, i) => {
                slide.style.opacity = i === index ? '1' : '0';
            });
            indicators.forEach((indicator, i) => {
                indicator.style.backgroundColor = i === index ? 'white' : 'rgba(255,255,255,0.5)';
            });
            currentSlide = index;
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function prevSlide() {
            showSlide(currentSlide - 1);
        }

        function goToSlide(index) {
            showSlide(index);
        }

        // Auto-play
        setInterval(nextSlide, 5000);

        // Botón volver arriba
        const btnVolverArriba = document.getElementById('btnVolverArriba');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                btnVolverArriba.classList.remove('opacity-0', 'invisible');
                btnVolverArriba.classList.add('opacity-100', 'visible');
            } else {
                btnVolverArriba.classList.add('opacity-0', 'invisible');
                btnVolverArriba.classList.remove('opacity-100', 'visible');
            }
        });

        btnVolverArriba.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Navbar transparente al hacer scroll (usando la altura del banner)
        const navbar = document.getElementById('navbar');
        const banner = document.querySelector('.bg-green-700');
        const bannerHeight = banner ? banner.offsetHeight : 40; // altura aproximada

        function updateNavbar() {
            if (window.scrollY > bannerHeight + 10) {
                navbar.classList.remove('bg-white', 'shadow-md');
                navbar.classList.add('bg-white/70', 'backdrop-blur-md', 'shadow-md');
            } else {
                navbar.classList.remove('bg-white/70', 'backdrop-blur-md');
                navbar.classList.add('bg-white', 'shadow-md');
            }
        }

        window.addEventListener('scroll', updateNavbar);
        updateNavbar(); // estado inicial
    </script>
@endsection