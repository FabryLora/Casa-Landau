<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArchivoCalidadController;
use App\Http\Controllers\BannerPortadaController;
use App\Http\Controllers\CalidadController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ImagenProductoController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InformacionDePagoController;
use App\Http\Controllers\ListaDePreciosController;
use App\Http\Controllers\LogosController;
use App\Http\Controllers\MailNewsletterController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MarcaProductoController;
use App\Http\Controllers\MaterialesController;
use App\Http\Controllers\MetadatosController;
use App\Http\Controllers\NosotrosController;
use App\Http\Controllers\NovedadesController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PrivadaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SubCategoriaController;
use App\Http\Controllers\SubProductoController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\TerminacionesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValoresController;
use App\Models\Banner;
use App\Models\InformacionImportante;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest:admin')->group(function () {});

Route::get('/adm', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('/adm', [AdminAuthController::class, 'authenticate'])->name('admin.authenticate');

Route::middleware('auth:admin')->group(function () {
    Route::post('admin-logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    Route::get('admin/administradores', [AdminController::class, 'index'])->name('admin.index');
    Route::post('admin/store', [AdminController::class, 'store'])->name('admin.store');
    Route::put('admin/update/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('admin/destroy/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
    Route::get('admin/bannerportada', [BannerPortadaController::class, 'index'])->name('admin.bannerportada');
    Route::post('admin/bannerportada', [BannerPortadaController::class, 'update'])->name('admin.bannerportada.update');

    Route::get('admin/nosotros', [NosotrosController::class, 'index'])->name('admin.nosotros');
    Route::post('admin/nosotros/update', [NosotrosController::class, 'update'])->name('admin.nosotros.update');
    Route::get('admin/valores', [ValoresController::class, 'index'])->name('admin.valores');
    Route::post('admin/valores', [ValoresController::class, 'update'])->name('admin.valores.update');
    Route::get('admin/nosotros-banner', [NosotrosController::class, 'nosotrosBanner'])->name('admin.nosotros.banner');
    Route::get('admin/productos-banner', [ProductoController::class, 'productosBanner'])->name('admin.productos.banner');
    Route::get('admin/novedades-banner', [NovedadesController::class, 'novedadesBanner'])->name('admin.novedades.banner');
    Route::get('admin/contacto-banner', [ContactoController::class, 'contactoBanner'])->name('admin.contacto.banner');

    # Categorias
    Route::get('admin/categorias', [CategoriaController::class, 'index'])->name('admin.categorias');
    Route::post('admin/categorias', [CategoriaController::class, 'store'])->name('admin.categorias.store');
    Route::post('admin/categorias/update', [CategoriaController::class, 'update'])->name('admin.categorias.update');
    Route::delete('admin/categorias/destroy', [CategoriaController::class, 'destroy'])->name('admin.categorias.destroy');

    # Subcategorias
    Route::get('admin/subcategorias', [SubCategoriaController::class, 'index'])->name('admin.subcategorias');
    Route::post('admin/subcategorias', [SubCategoriaController::class, 'store'])->name('admin.subcategorias.store');
    Route::post('admin/subcategorias/update', [SubCategoriaController::class, 'update'])->name('admin.subcategorias.update');
    Route::delete('admin/subcategorias/destroy', [SubCategoriaController::class, 'destroy'])->name('admin.subcategorias.destroy');

    Route::get('admin/marcas', [MarcaController::class, 'index'])->name('admin.marcas');
    Route::post('admin/marcas', [MarcaController::class, 'store'])->name('admin.marcas.store');
    Route::post('admin/marcas/update', [MarcaController::class, 'update'])->name('admin.marcas.update');
    Route::delete('admin/marcas/destroy', [MarcaController::class, 'destroy'])->name('admin.marcas.destroy');
    Route::get('admin/productos', [ProductoController::class, 'index'])->name('admin.productos');
    Route::post('admin/productos', [ProductoController::class, 'store'])->name('admin.productos.store');
    Route::post('admin/productos/update', [ProductoController::class, 'update'])->name('admin.productos.update');
    Route::post('admin/productos/destroy', [ProductoController::class, 'destroy'])->name('admin.productos.destroy');
    Route::post('admin/productos/imagenes/store', [ImagenProductoController::class, 'store'])->name('admin.imagenes.store');
    Route::post('admin/productos/imagenes/update', [ImagenProductoController::class, 'update'])->name('admin.imagenes.update');
    Route::delete('admin/productos/imagenes/destroy', [ImagenProductoController::class, 'destroy'])->name('admin.imagenes.destroy');

    Route::get('admin/calidad', [CalidadController::class, 'index'])->name('admin.calidad');
    Route::post('admin/calidad', [CalidadController::class, 'update'])->name('admin.calidad.update');

    Route::get('admin/archivos', [ArchivoCalidadController::class, 'index'])->name('admin.archivos');
    Route::post('admin/archivos', [ArchivoCalidadController::class, 'store'])->name('admin.archivos.store');
    Route::post('admin/archivos/update', [ArchivoCalidadController::class, 'update'])->name('admin.archivos.update');
    Route::delete('admin/archivos/destroy', [ArchivoCalidadController::class, 'destroy'])->name('admin.archivos.destroy');

    Route::get('admin/novedades', [NovedadesController::class, 'index'])->name('admin.novedades');
    Route::post('admin/novedades', [NovedadesController::class, 'store'])->name('admin.novedades.store');
    Route::post('admin/novedades/update', [NovedadesController::class, 'update'])->name('admin.novedades.update');
    Route::delete('admin/novedades/destroy', [NovedadesController::class, 'destroy'])->name('admin.novedades.destroy');
    Route::post('admin/novedades/featured', [NovedadesController::class, 'changeFeatured'])->name('admin.novedades.changeFeatured');

    Route::post('cambiarDestacado', [ProductoController::class, 'cambiarDestacado'])->name('cambiarDestacado');

    Route::post('cambiarOferta', [ProductoController::class, 'cambiarOferta'])->name('cambiarOferta');

    Route::post('dashboard/banners', function (Request $request) {
        $banner = Banner::where('name', $request->name)->first();

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'image' => 'required|file',
        ]);

        if ($banner) {
            // Delete the old image if it exists
            if ($banner->image) {
                $absolutePath = public_path('storage/' . $banner->image);
                if (file_exists($absolutePath)) {
                    unlink($absolutePath);
                }
            }
            // Store the new image
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $banner->update($data);
    })->name('admin.banners.update');


    Route::get('admin/contacto', [ContactoController::class, 'index'])->name('admin.contacto');
    Route::post('admin/contacto', [ContactoController::class, 'update'])->name('admin.contacto.update');

    Route::get('admin/subproductos', [SubProductoController::class, 'index'])->name('admin.subproductos');
    Route::post('admin/subproductos', [SubProductoController::class, 'store'])->name('admin.subproductos.store');
    Route::post('admin/subproductos/update', [SubProductoController::class, 'update'])->name('admin.subproductos.update');
    Route::delete('admin/subproductos/destroy', [SubProductoController::class, 'destroy'])->name('admin.subproductos.destroy');

    Route::get('admin/clientes', [UserController::class, 'index'])->name('admin.clientes');
    Route::post('admin/clientes', [UserController::class, 'store'])->name('admin.clientes.store');
    Route::post('admin/clientes/update', [UserController::class, 'update'])->name('admin.clientes.update');
    Route::delete('admin/clientes/destroy', [UserController::class, 'destroy'])->name('admin.clientes.destroy');
    Route::post('admin/clientes/autorizar', [UserController::class, 'changeStatus'])->name('admin.clientes.autorizar');

    Route::get('admin/vendedores', [UserController::class, 'vendedores'])->name('admin.vendedores');

    Route::get('admin/carrito', [PrivadaController::class, 'carritoAdmin'])->name('admin.carrito');

    Route::get('admin/metadatos', [MetadatosController::class, 'index'])->name('admin.metadatos');
    Route::post('admin/metadatos', [MetadatosController::class, 'update'])->name('admin.metadatos.update');

    Route::post('admin/informacion', function (Request $request) {
        $informacion = InformacionImportante::first();

        $data = $request->validate([
            'text' => 'required|string',
        ]);

        $informacion->update($data);
    })->name('admin.informacion.update');


    Route::get('admin/sucursales', [SucursalController::class, 'index'])->name('admin.sucursales');
    Route::post('admin/sucursales', [SucursalController::class, 'store'])->name('admin.sucursales.store');
    Route::post('admin/sucursales/update', [SucursalController::class, 'update'])->name('admin.sucursales.update');
    Route::delete('admin/sucursales/destroy', [SucursalController::class, 'destroy'])->name('admin.sucursales.destroy');

    Route::get('admin/listadeprecios', [ListaDePreciosController::class, 'indexAdmin'])->name('admin.listadeprecios');
    Route::post('admin/listadeprecios', [ListaDePreciosController::class, 'store'])->name('admin.listadeprecios.store');
    Route::post('admin/listadeprecios/update', [ListaDePreciosController::class, 'update'])->name('admin.listadeprecios.update');
    Route::delete('admin/listadeprecios/destroy', [ListaDePreciosController::class, 'destroy'])->name('admin.listadeprecios.destroy');

    Route::get('admin/logos', [LogosController::class, 'index'])->name('admin.logos');
    Route::post('admin/logos', [LogosController::class, 'update'])->name('admin.logos.update');

    Route::get('admin/pedidos', [PedidoController::class, 'misPedidosAdmin'])->name('admin.pedidos');
    Route::post('admin/pedidos/update', [PedidoController::class, 'update'])->name('admin.pedidos.update');
    Route::post('cambiarEstado', [PedidoController::class, 'cambiarEstado'])->name('cambiarEstado');

    Route::get('admin/marcasProducto', [MarcaProductoController::class, 'index'])->name('admin.marcasProducto');
    Route::post('admin/marcasProducto', [MarcaProductoController::class, 'store'])->name('admin.marcasProducto.store');
    Route::post('admin/marcasProducto/update', [MarcaProductoController::class, 'update'])->name('admin.marcasProducto.update');
    Route::delete('admin/marcasProducto/destroy', [MarcaProductoController::class, 'destroy'])->name('admin.marcasProducto.destroy');

    Route::post('/importar-excel', [ImportController::class, 'importar'])->name('importar.excel');
    Route::post('/importar-excel/productos', [ImportController::class, 'importarProductos'])->name('importarProductos');
    Route::post('/importar-excel/clientes', [ImportController::class, 'importarClientes'])->name('importarClientes');
    Route::post('/importar-excel/vendedores', [ImportController::class, 'importarVendedores'])->name('importarVendedores');

    Route::get('admin/slider', [SliderController::class, 'index'])->name('admin.slider');
    Route::post('admin/slider/update', [SliderController::class, 'update'])->name('admin.slider.update');
    Route::delete('admin/slider/destroy', [SliderController::class, 'destroy'])->name('admin.slider.destroy');
    Route::post('admin/slider/store', [SliderController::class, 'store'])->name('admin.slider.store');

    Route::post('cambiarPrecios', [ImportController::class, 'importar'])->name('cambiarPrecios');

    Route::get('admin/productos-zonaprivada', [ProductoController::class, 'productoszonaprivada'])->name('admin.productos.productoszonaprivada');


    Route::get('admin/informacion-de-pago', [InformacionDePagoController::class, 'index'])->name('admin.informacion-de-pago');
    Route::post('admin/informacion-de-pago', [InformacionDePagoController::class, 'update'])->name('admin.informacion-de-pago.update');

    Route::get('admin/materiales', [MaterialesController::class, 'index'])->name('admin.materiales');
    Route::post('admin/materiales', [MaterialesController::class, 'store'])->name('admin.materiales.store');
    Route::post('admin/materiales/update', [MaterialesController::class, 'update'])->name('admin.materiales.update');
    Route::delete('admin/materiales/destroy', [MaterialesController::class, 'destroy'])->name('admin.materiales.destroy');

    Route::get('admin/terminaciones', [TerminacionesController::class, 'index'])->name('admin.terminaciones');
    Route::post('admin/terminaciones', [TerminacionesController::class, 'store'])->name('admin.terminaciones.store');
    Route::post('admin/terminaciones/update', [TerminacionesController::class, 'update'])->name('admin.terminaciones.update');
    Route::delete('admin/terminaciones/destroy', [TerminacionesController::class, 'destroy'])->name('admin.terminaciones.destroy');

    Route::get('admin/newsletter', [MailNewsletterController::class, 'index'])->name('admin.newsletter.index');
    Route::post('admin/newsletter', [MailNewsletterController::class, 'store'])->name('admin.newsletter.store');
    Route::post('admin/newsletter/update', [MailNewsletterController::class, 'update'])->name('admin.newsletter.update');
    Route::delete('admin/newsletter/destroy', [MailNewsletterController::class, 'destroy'])->name('admin.newsletter.destroy');

    Route::get('/admin/dashboard', function () {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('/adm');
        }
        return inertia('admin/dashboard');
    })->name('admin.dashboard');
});
