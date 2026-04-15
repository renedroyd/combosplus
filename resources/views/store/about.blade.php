@extends('layouts.app')

@section('title', 'Sobre Nosotros - CombosPlus+')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Encabezado con imagen de fondo y gradiente -->
    <div class="relative bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl overflow-hidden mb-8">
        <!-- Imagen de fondo representativa (local, equipo, familia) -->
        <div class="absolute inset-0 opacity-20 mix-blend-overlay">
            <img src="https://images.unsplash.com/photo-1604719311686-9c6a6d7a8b8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" 
                 alt="Tienda y clientes felices" 
                 class="w-full h-full object-cover">
        </div>
        <!-- Elementos decorativos -->
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-yellow-300/20 rounded-full blur-3xl"></div>
        
        <div class="relative px-6 py-8 md:px-12 md:py-12 text-white">
            <h1 class="text-3xl md:text-4xl font-bold mb-3 drop-shadow-lg">Sobre Nosotros</h1>
            <p class="text-green-100 text-lg max-w-2xl drop-shadow">
                Conoce la historia y los valores que hacen de CombosPlus+ tu tienda de confianza en Guantánamo.
            </p>
        </div>
    </div>

    <!-- Contenido principal (igual que antes) -->
    <div class="grid md:grid-cols-2 gap-8 mb-8">
        <!-- Nuestra historia -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 mr-4">
                    <i class="fas fa-store-alt text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Nuestra Historia</h2>
            </div>
            <p class="text-gray-600 leading-relaxed">
                <span class="font-semibold text-green-700">CombosPlus+</span> nació en 2020 con el propósito de ofrecer a la comunidad guantanamera una experiencia de compra moderna, confiable y cercana. Lo que comenzó como un pequeño emprendimiento familiar se ha convertido en un referente local gracias a la confianza de nuestros clientes y al esfuerzo de un equipo comprometido con la calidad y el servicio.
            </p>
            <p class="text-gray-600 leading-relaxed mt-4">
                Hoy, seguimos creciendo sin perder nuestra esencia: trato personalizado, productos de calidad y el deseo de hacer la vida más fácil a quienes confían en nosotros.
            </p>
        </div>

        <!-- Misión y valores -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 mr-4">
                    <i class="fas fa-bullseye text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Misión y Valores</h2>
            </div>
            <p class="text-gray-600 leading-relaxed">
                <span class="font-semibold">Misión:</span> Proveer productos y servicios que satisfagan las necesidades de nuestros clientes, ofreciendo calidad, variedad y un trato cercano que nos distinga como la tienda de preferencia en Guantánamo.
            </p>
            <div class="mt-4">
                <span class="font-semibold text-gray-800">Nuestros valores:</span>
                <ul class="list-disc list-inside text-gray-600 mt-2 space-y-1">
                    <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Honestidad y transparencia</li>
                    <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Compromiso con el cliente</li>
                    <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Calidad en cada producto</li>
                    <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Cercanía y trato familiar</li>
                    <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Innovación constante</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Ubicación y horarios -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 mr-4">
                <i class="fas fa-map-marker-alt text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Encuéntranos</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">📍 Dirección exacta</h3>
                <p class="text-gray-600">
                    Calle Narciso López, entre Pedro A. Pérez y Calixto García<br>
                    <span class="font-medium">Reparto: </span>Centro<br>
                    <span class="font-medium">Municipio: </span>Guantánamo<br>
                    <span class="font-medium">Provincia: </span>Guantánamo, Cuba
                </p>
                <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-3">
                    <p class="text-sm text-yellow-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Estamos ubicados en el corazón de la ciudad, fácilmente accesible desde cualquier punto.
                    </p>
                </div>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">🕒 Horario de atención</h3>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex justify-between">
                        <span>Lunes a viernes:</span>
                        <span class="font-medium">9:00 AM – 6:00 PM</span>
                    </li>
                    <li class="flex justify-between">
                        <span>Sábados:</span>
                        <span class="font-medium">9:00 AM – 2:00 PM</span>
                    </li>
                    <li class="flex justify-between">
                        <span>Domingos y festivos:</span>
                        <span class="font-medium">Cerrado</span>
                    </li>
                </ul>
                <p class="text-sm text-gray-500 mt-4">
                    <i class="fas fa-phone-alt mr-2 text-green-600"></i>Teléfono: +53 21 123456<br>
                    <i class="fas fa-envelope mr-2 text-green-600"></i>Email: contacto@combosplus.com
                </p>
            </div>
        </div>
    </div>

    <!-- Mapa (coordenadas aproximadas de Guantánamo) -->
    <div class="rounded-2xl overflow-hidden shadow-lg mb-8">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1880.123456789!2d-75.207778!3d20.144722!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8ec4b8b8b8b8b8b%3A0x1b1b1b1b1b1b1b1b!2sCalle%20Narciso%20L%C3%B3pez%2C%20Guant%C3%A1namo!5e0!3m2!1ses!2scu!4v1620000000000!5m2!1ses!2scu" 
            width="100%" 
            height="350" 
            style="border:0; border-radius: 0.75rem;" 
            allowfullscreen="" 
            loading="lazy">
        </iframe>
    </div>

    <!-- Equipo o llamado a la acción -->
    <div class="bg-green-50 rounded-2xl p-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-3">¿Hablamos?</h2>
        <p class="text-gray-600 max-w-2xl mx-auto mb-6">
            Estamos aquí para ayudarte. Visítanos, llámanos o escríbenos. Será un placer atenderte personalmente.
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('contact') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-full transition flex items-center">
                <i class="fas fa-envelope mr-2"></i>Contactar
            </a>
            <a href="{{ route('products.index') }}" class="bg-white border-2 border-green-600 text-green-700 hover:bg-green-50 font-semibold py-3 px-6 rounded-full transition flex items-center">
                <i class="fas fa-shopping-bag mr-2"></i>Ver productos
            </a>
        </div>
    </div>
</div>
@endsection