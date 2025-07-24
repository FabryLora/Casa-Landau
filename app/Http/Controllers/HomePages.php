<?php

namespace App\Http\Controllers;

use App\Models\ArchivoCalidad;
use App\Models\Banner;
use App\Models\BannerPortada;
use App\Models\Calidad;
use App\Models\Categoria;
use App\Models\Contacto;
use App\Models\Materiales;
use App\Models\Metadatos;
use App\Models\Nosotros;
use App\Models\Novedades;
use App\Models\Producto;
use App\Models\Slider;
use App\Models\SubCategoria;
use App\Models\Terminaciones;
use App\Models\Valores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomePages extends Controller
{
    public function home()
    {
        if (Auth::check()) {
            return redirect('/privada/productos');
        }

        $metadatos = Metadatos::where('title', 'home')->first();

        $categorias = Categoria::orderBy('order', 'asc')->get();
        $subcategorias = SubCategoria::orderBy('order', 'asc')->get();
        $sliders = Slider::orderBy('order', 'asc')->get();
        $bannerPortada = BannerPortada::first();
        $novedades = Novedades::where('featured', true)->orderBy('order', 'asc')->get();
        $productos = Producto::where('destacado', true)
            ->orderBy('order', 'asc')
            ->with(['imagenes', 'precio'])
            ->get();
        $terminaciones = Terminaciones::orderBy('order', 'asc')->get();
        $materiales = Materiales::orderBy('order', 'asc')->get();
        return view('home', [
            'sliders' => $sliders,
            'bannerPortada' => $bannerPortada,
            'novedades' => $novedades,
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
            'productos' => $productos,
            'metadatos' => $metadatos,
            'terminaciones' => $terminaciones,
            'materiales' => $materiales,
        ]);
    }

    public function nosotros()
    {
        $banner = Banner::where('name', 'nosotros')->first();
        $nosotros = Nosotros::first();

        return view('empresa', [
            'nosotros' => $nosotros,
            'banner' => $banner,
        ]);
    }

    public function calidad()
    {
        $calidad = Calidad::first();
        $archivos = ArchivoCalidad::orderBy('order', 'asc')->get();

        return view('calidad', [
            'calidad' => $calidad,
            'archivos' => $archivos,
        ]);
    }

    public function novedades()
    {
        $novedades = Novedades::orderBy('order', 'asc')
            ->get();
        $banner = Banner::where('name', 'novedades')->first();
        return view('lanzamientos', [
            'novedades' => $novedades,
            'banner' => $banner,
        ]);
    }

    public function contacto(Request $request)
    {
        $contacto = Contacto::first();
        $banner = Banner::where('name', 'contacto')->first();
        return view('contacto', [
            'contacto' => $contacto,
            'banner' => $banner,
            'mensaje' => $request->mensaje ?? null,
        ]);
    }
}
