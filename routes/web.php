<?php

use App\Http\Controllers\ContactoController;
use App\Http\Controllers\DescargarArchivo;
use App\Http\Controllers\HomePages;
use App\Http\Controllers\MailNewsletterController;
use App\Http\Controllers\NovedadesController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SendContactInfoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



# ---------------------- Rutas de zona pública ---------------------- #

Route::get('/', [HomePages::class, 'home'])->name('home');
Route::get('/nosotros', [HomePages::class, 'nosotros'])->name('nosotros');
Route::get('/calidad', [HomePages::class, 'calidad'])->name('calidad');
Route::get('/novedades', [HomePages::class, 'novedades'])->name('novedades');
Route::get('/contacto', [HomePages::class, 'contacto'])->name('contacto');
Route::get('/novedades/{id}', [NovedadesController::class, 'novedadesShow'])->name('novedades.show');
Route::post('/contacto/sendemail', [ContactoController::class, 'sendContact'])->name('send.contact');

Route::get('/productos', [ProductoController::class, 'productosAnteSala'])->name('productos');
Route::get('/productos/categorias', [ProductoController::class, 'indexVistaPrevia'])->name('productos.categorias');

Route::get('/p/{codigo}', [ProductoController::class, 'show'])->name('producto');

Route::get('/busqueda', [ProductoController::class, 'SearchProducts'])->name('searchproducts');

Route::post('newsletterStore', [MailNewsletterController::class, 'store'])->name('newsletter.store');


# ------------------------------------------------------------------- #
// Ruta para la API de búsqueda (AJAX)
Route::post('/api/search', [SearchController::class, 'search'])
    ->name('api.search');

// Ruta para la página de resultados completos
Route::get('/buscar', [SearchController::class, 'searchPage'])
    ->name('search.results');

Route::get('/{code}', [ProductoController::class, 'handleQR'])->name(
    'handleQR'
);







Route::get('/fix-images', [ProductoController::class, 'fixImagePath'])->name('fix.images');

Route::get('/imagenes-prod', [ProductoController::class, 'imagenesProducto']);
Route::get('/agregar-marca', [ProductoController::class, 'agregarMarca']);



// routes/web.php
Route::get('/descargar/archivo/{id}', [DescargarArchivo::class, 'descargarArchivo'])
    ->name('descargar.archivo');



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render(component: 'dashboard');
    })->name('dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/admin_auth.php';
