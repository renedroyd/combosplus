<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', config('app.name', 'CombosPlus+'))</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else 
        <style>
            /* ... aquí va todo el estilo inline de tu plantilla ... */
        </style>
    @endif

    <!-- Fuente Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Alpine.js para interactividad (dropdown, búsqueda) -->

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">

    <!-- BANNER SUPERIOR -->
    <div class="bg-green-700 text-white text-center py-2 text-sm font-medium">
        <p>🚚 Envío gratis en compras superiores a $50 | 10% de descuento en tu primera compra</p>
    </div>

    <!-- BARRA DE NAVEGACIÓN (STICKY) -->
    <nav id="navbar" class="sticky top-0 z-50 bg-white shadow-md py-3 px-4 md:px-8 flex items-center justify-between transition-all duration-300">
        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/logo.png') }}" alt="CombosPlus+" class="h-14 w-auto">
            <span class="font-bold text-xl text-gray-800">CombosPlus+</span>
        </div>

        <!-- Menú de navegación (visible en desktop) -->
        <div class="hidden md:flex space-x-8 text-gray-600 font-medium">
            <a href="{{ route('home') }}" class="hover:text-green-600 transition">Inicio</a>
            <a href="{{ route('products.index') }}" class="hover:text-green-600 transition">Productos</a>
            <a href="{{ route('offers') }}" class="hover:text-green-600 transition">Ofertas</a>
            {{-- <a href="{{ route('remesas.index') }}" class="hover:text-green-600 transition">Envíos de remesas</a> --}}
            <a href="{{ route('contact') }}" class="hover:text-green-600 transition">Contacto</a>
            <a href="{{ route('about') }}" class="hover:text-green-600 transition">Sobre Nosotros</a>
        </div>

        <!-- Iconos de usuario, carrito y búsqueda -->
        <div class="flex items-center space-x-4">
            <!-- Búsqueda con toggle (escritorio y móvil) -->
            <!-- Búsqueda con toggle mejorado -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())" 
                        class="text-gray-600 hover:text-green-600 transition p-2 rounded-full hover:bg-gray-100" 
                        :class="{ 'text-green-600': open }">
                    <i class="fas fa-search text-lg"></i>
                </button>
                <div x-show="open" @click.away="open = false" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" 
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150" 
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-72 sm:w-80 bg-white rounded-xl shadow-xl p-3 z-50 border border-gray-100">
                    <form action="{{ route('search') }}" method="GET" class="flex items-center">
                        <input type="search" name="q" placeholder="Buscar productos, marcas..." 
                            x-ref="searchInput"
                            class="flex-1 border border-gray-300 rounded-l-full px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-r-full transition">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Autenticación condicional -->
            @guest
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-green-600 transition text-sm font-medium hidden md:inline-block">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium hidden md:inline-block">Registrarse</a>
            @else
                <!-- Menú desplegable para usuario autenticado -->
                <div x-data="{ open: false }" class="relative hidden md:block">
                    <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-green-600 focus:outline-none">
                        <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition.duration.200ms
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50" style="display: none;">
                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mi Perfil</a>
                        <a href="{{ route('cart.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mi Carrito</a>
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            @endguest

            <!-- Carrito -->
            <div class="relative">
                <a href="{{ route('cart.index') }}" class="relative">
                    <i class="fas fa-shopping-cart text-gray-700"></i>
                    <span id="cart-count" class="absolute -top-2 -right-2 bg-green-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        {{ App\Http\Controllers\CartController::getCartItemsCount() }}
                    </span>
                </a>
            </div>

            <!-- Menú hamburguesa para móvil -->
            <div x-data="{ mobileMenuOpen: false }" class="md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" x-transition.duration.200ms
                     class="absolute left-0 right-0 top-full mt-2 bg-white shadow-lg rounded-lg p-4 z-50" style="display: none;">
                    <div class="flex flex-col space-y-3">
                        <a href="{{ route('home') }}" class="text-gray-600 hover:text-green-600">Inicio</a>
                        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-green-600">Productos</a>
                        <a href="{{ route('offers') }}" class="text-gray-600 hover:text-green-600">Ofertas</a>
                        {{-- <a href="{{ route('remesas.index') }}" class="text-gray-600 hover:text-green-600">Envio de remesas</a> --}}
                        <a href="{{ route('contact') }}" class="text-gray-600 hover:text-green-600">Contacto</a>
                        <a href="{{ route('about') }}" class="hover:text-green-600 transition">Sobre Nosotros</a>
                        <hr class="border-gray-200">
                        @guest
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-green-600">Iniciar sesión</a>
                            <a href="{{ route('register') }}" class="text-gray-600 hover:text-green-600">Registrarse</a>
                        @else
                            <span class="text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                            <a href="{{ route('profile.show') }}" class="text-gray-600 hover:text-green-600">Mi Perfil</a>
                            <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-green-600">Mi Carrito</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-green-600 w-full text-left">Cerrar sesión</button>
                            </form>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL (sección variable) -->
    <main>
        @yield('content')
    </main>

    <!-- BOTÓN VOLVER ARRIBA -->
    <button id="btnVolverArriba" class="fixed bottom-6 right-6 bg-green-600 hover:bg-green-700 text-white p-3 rounded-full shadow-lg transition-opacity duration-300 opacity-0 invisible z-50">
        <i class="fas fa-arrow-up text-xl"></i>
    </button>

    <footer class="bg-gray-800 text-gray-300 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Columna 1: Información de la tienda -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-carrot text-green-400 text-2xl"></i>
                        <span class="font-bold text-xl text-white">CombosPlus+</span>
                    </div>
                    <p class="text-sm leading-relaxed">
                        Tu tienda online de alimentos frescos y saludables. Ofrecemos productos de alta calidad directamente a tu hogar.
                    </p>
                    <div class="mt-4 flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-whatsapp text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Columna 2: Enlaces útiles -->
                <div>
                    <h3 class="text-white font-semibold text-lg mb-4">Enlaces rápidos</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Sobre nosotros</a></li>
                        <li><a href="#" class="hover:text-white transition">Contacto</a></li>
                        <li><a href="#" class="hover:text-white transition">Ofertas especiales</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog / Recetas</a></li>
                        <li><a href="#" class="hover:text-white transition">Preguntas frecuentes</a></li>
                    </ul>
                </div>

                <!-- Columna 3: Categorías populares -->
                <div>
                    <h3 class="text-white font-semibold text-lg mb-4">Categorías</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Frutas y Verduras</a></li>
                        <li><a href="#" class="hover:text-white transition">Carnes y Pescados</a></li>
                        <li><a href="#" class="hover:text-white transition">Lácteos y Huevos</a></li>
                        <li><a href="#" class="hover:text-white transition">Panadería</a></li>
                        <li><a href="#" class="hover:text-white transition">Bebidas</a></li>
                    </ul>
                </div>

                <!-- Columna 4: Boletín y contacto -->
                <div>
                    <h3 class="text-white font-semibold text-lg mb-4">Suscríbete</h3>
                    <p class="text-sm mb-3">Recibe ofertas exclusivas y novedades</p>
                    <form class="flex flex-col space-y-2">
                        <input type="email" placeholder="Tu correo electrónico" 
                               class="px-4 py-2 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                        <button class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition text-sm">
                            Suscribirme
                        </button>
                    </form>
                    <div class="mt-4 text-sm">
                        <p><i class="fas fa-phone-alt mr-2 text-green-400"></i> +56 9 1234 5678</p>
                        <p><i class="fas fa-envelope mr-2 text-green-400"></i> camajuani@combosplus.com</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Línea inferior con copyright y enlaces legales -->
        <div class="border-t border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row justify-between items-center text-xs text-gray-400">
                <p>&copy; {{ date('Y') }} CombosPlus+. Todos los derechos reservados.</p>
                <div class="flex space-x-4 mt-2 sm:mt-0">
                    <a href="#" class="hover:text-white transition">Política de privacidad</a>
                    <a href="#" class="hover:text-white transition">Términos y condiciones</a>
                    <a href="#" class="hover:text-white transition">Cambios y devoluciones</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts comunes (incluye el que ya tenías) -->
    <script>
        // Navbar transparente al hacer scroll
        const navbar = document.getElementById('navbar');
        const banner = document.querySelector('.bg-green-700');
        const bannerHeight = banner ? banner.offsetHeight : 40;

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
        updateNavbar();

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
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Script para el carrito (AJAX) - si ya lo tenías, mantenlo
        document.addEventListener('DOMContentLoaded', function() {
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.dataset.url;
                    const productName = this.dataset.productName || 'Producto';
                    const originalHtml = this.innerHTML;
                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Añadiendo...';
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const cartCount = document.getElementById('cart-count');
                            if (cartCount) {
                                cartCount.textContent = data.totalItems;
                            }
                            showNotification(`${productName} añadido al carrito`, 'success');
                        } else {
                            showNotification('Error al añadir el producto', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error al añadir el producto', 'error');
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    });
                });
            });

            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transition transform duration-300 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
                notification.textContent = message;
                document.body.appendChild(notification);
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }
        });
    </script>

    @yield('scripts')
</body>
</html>