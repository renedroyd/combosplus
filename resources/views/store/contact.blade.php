@extends('layouts.app')

@section('title', 'Contacto - CombosPlus+')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Contáctanos</h1>
            <p class="text-lg text-gray-600">Estamos aquí para ayudarte. Escríbenos y te responderemos a la brevedad.</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Formulario de contacto -->
            <div class="lg:w-2/3">
                <form action="{{ route('contact.send') }}" method="POST" class="bg-white rounded-xl shadow-md p-8">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre completo</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Correo electrónico</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                   required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Asunto</label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('subject') border-red-500 @enderror"
                               required>
                        @error('subject')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Mensaje</label>
                        <textarea name="message" id="message" rows="5" 
                                  class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('message') border-red-500 @enderror"
                                  required>{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                        Enviar mensaje
                    </button>
                </form>
            </div>

            <!-- Información de contacto -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-xl shadow-md p-8 space-y-6">
                    <h3 class="text-xl font-bold text-gray-800">Información de contacto</h3>
                    
                    <div class="flex items-start space-x-4">
                        <i class="fas fa-map-marker-alt text-green-600 text-xl mt-1"></i>
                        <div>
                            <p class="font-medium text-gray-800">Dirección</p>
                            <p class="text-gray-600">Av. Principal 123, Santiago, Chile</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <i class="fas fa-phone-alt text-green-600 text-xl mt-1"></i>
                        <div>
                            <p class="font-medium text-gray-800">Teléfono</p>
                            <p class="text-gray-600">+56 9 1234 5678</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <i class="fas fa-envelope text-green-600 text-xl mt-1"></i>
                        <div>
                            <p class="font-medium text-gray-800">Email</p>
                            <p class="text-gray-600">contacto@combosplus.com</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <i class="fas fa-clock text-green-600 text-xl mt-1"></i>
                        <div>
                            <p class="font-medium text-gray-800">Horario de atención</p>
                            <p class="text-gray-600">Lunes a Viernes: 9:00 - 18:00</p>
                            <p class="text-gray-600">Sábados: 10:00 - 14:00</p>
                        </div>
                    </div>

                    <hr class="border-gray-200">

                    <div>
                        <h4 class="font-medium text-gray-800 mb-3">Síguenos</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-green-600 transition"><i class="fab fa-facebook-f text-2xl"></i></a>
                            <a href="#" class="text-gray-400 hover:text-green-600 transition"><i class="fab fa-instagram text-2xl"></i></a>
                            <a href="#" class="text-gray-400 hover:text-green-600 transition"><i class="fab fa-whatsapp text-2xl"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mapa (opcional) -->
        <div class="mt-12">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3329.472959012142!2d-70.648514684796!3d-33.437490680778!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9662c5a7b1b1b1b1%3A0x1b1b1b1b1b1b1b1b!2sSantiago%2C%20Chile!5e0!3m2!1ses!2s!4v1620000000000!5m2!1ses!2s" 
                    width="100%" height="300" style="border:0; border-radius: 0.75rem;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
@endsection