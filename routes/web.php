<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ZellePaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/* Route::get('/', function () {
    return view('store.index');
}); */
// Route::get('/', [ProductController::class, 'index'])->name('productos.index');



// Login
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Registro
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Restablecer contraseña
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
Route::get('/productos/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/ofertas', [OfferController::class, 'index'])->name('offers');

Route::get('/contacto', [ContactController::class, 'showForm'])->name('contact');
Route::post('/contacto', [ContactController::class, 'send'])->name('contact.send');

Route::get('/sobre-nosotros', [HomeController::class, 'about'])->name('about');

Route::get('/buscar', [SearchController::class, 'index'])->name('search');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/item/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/item/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

// Rutas para remesas
Route::prefix('remesas')->name('remesas.')->group(function () {
    Route::get('/', [App\Http\Controllers\RemesaController::class, 'index'])->name('index');
    Route::get('/nueva', [App\Http\Controllers\RemesaController::class, 'create'])->name('nueva'); // o create
    Route::post('/enviar', [App\Http\Controllers\RemesaController::class, 'enviar'])->name('enviar');
    Route::get('/pago/{codigo}', [App\Http\Controllers\RemesaController::class, 'pago'])->name('pago');
    Route::post('/pago/{codigo}', [App\Http\Controllers\RemesaController::class, 'procesarPago'])->name('procesar-pago');
    Route::get('/seguimiento', [App\Http\Controllers\RemesaController::class, 'buscarSeguimiento'])->name('seguimiento.buscar');
    Route::get('/seguimiento/{codigo}', [App\Http\Controllers\RemesaController::class, 'seguimiento'])->name('seguimiento');
    Route::post('/calcular-costo', [App\Http\Controllers\RemesaController::class, 'calcularCosto'])->name('calcular-costo');
});

Route::middleware(['auth'])->group(function () {
        // Perfil de usuario
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('profile.update');

    // Direcciones
    Route::resource('addresses', AddressController::class)->except(['show']);
    
    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    // Pedidos
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/orders/{order}/pay-zelle', [ZellePaymentController::class, 'show'])->name('zelle.pay');
    Route::post('/orders/{order}/pay-zelle/confirm', [ZellePaymentController::class, 'confirm'])->name('zelle.confirm');
});
