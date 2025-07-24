<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Categoria;

use App\Models\ImagenProducto;
use App\Models\Materiales;
use App\Models\Metadatos;
use App\Models\Producto;
use App\Models\SubCategoria;
use App\Models\SubProducto;
use App\Models\Terminaciones;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $categorias = Categoria::select('id', 'name')->get();
        $subcategorias = SubCategoria::orderBy('order', 'asc')->get();
        $terminaciones = Terminaciones::orderBy('order', 'asc')->get();
        $materiales = Materiales::orderBy('order', 'asc')->get();

        $perPage = $request->input('per_page', default: 10);

        $query = Producto::query()->orderBy('order', 'asc')->with(['terminacion', 'material', 'categoria', 'imagenes', 'subcategoria']);

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('name', 'LIKE', '%' . $searchTerm . '%')->orWhere('code', 'LIKE', '%' . $searchTerm . '%');
        }

        $productos = $query->paginate($perPage);





        return Inertia::render('admin/productosAdmin', [
            'productos' => $productos,
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
            'terminaciones' => $terminaciones,
            'materiales' => $materiales

        ]);
    }

    public function productosAnteSala(Request $request)
    {
        $categorias = Categoria::orderBy('order', 'asc')->get();
        $subcategorias = SubCategoria::orderBy('order', 'asc')->get();
        /* $materiales = Materiales::orderBy('order', 'asc')->get(); */
        /* $medidas = Medidas::orderBy('order', 'asc')->get(); */
        $banner = Banner::where('name', 'productos')->first();

        $terminaciones = Terminaciones::orderBy('order', 'asc')->get();
        $materiales = Materiales::orderBy('order', 'asc')->get();

        return view('productos-ante', [
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
            'banner' => $banner,
            'terminaciones' => $terminaciones,
            'materiales' => $materiales,
        ]);
    }

    public function indexVistaPrevia(Request $request)
    {
        // Construir query base para productos
        $query = Producto::query();

        if ($request->filled('id')) {
            $query->whereHas('categoria', function ($q) use ($request) {
                $q->where('categoria_id', $request->id);
            });
        }

        // Filtro por modelo/subcategoría
        if ($request->filled('rubro')) {
            $query->whereHas('subcategoria', function ($q) use ($request) {
                $q->where('sub_categoria_id', $request->rubro);
            });
        }

        // Filtro por código
        if ($request->filled('terminacion')) {
            $query->where('terminacion_id', 'LIKE', '%' . $request->terminacion . '%');
        }

        if ($request->filled('material')) {
            $query->where('material_id', 'LIKE', '%' . $request->material . '%');
        }


        if ($request->filled('medida')) {
            $query->where('medida', 'LIKE', '%' . $request->medida . '%');
        }

        /* buscar por todo */

        if ($request->filled('avanzada')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->avanzada . '%')
                    ->orWhere('code', 'LIKE', '%' . $request->avanzada . '%')
                    ->orWhere('medida', 'LIKE', '%' . $request->avanzada . '%');
            });
        }



        // Aplicar ordenamiento por defecto
        $query->orderBy('order', 'asc');

        // Ejecutar query con paginación
        $productos = $query->with(['categoria', 'subcategoria'])->where('categoria_id', $request->id)
            ->paginate(15)
            ->appends($request->query());

        // Si solo hay un producto en total (no en la página actual), redirigir
        /*  if ($productos->total() === 1) {
            return redirect('/p/' . $productos->first()->code);
        } */

        // Cargar datos adicionales para la vista
        $categorias = Categoria::with('subCategorias')->orderBy('order', 'asc')->get();
        $subcategorias = SubCategoria::orderBy('order', 'asc')->get();
        $categoria = $request->filled('id') ? Categoria::findOrFail($request->id) : null;
        $materiales = Materiales::orderBy('order', 'asc')->get();
        $terminaciones = Terminaciones::orderBy('order', 'asc')->get();

        return view('productos', [
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
            'productos' => $productos,
            'categoria' => $categoria,
            'id' => $request->id,
            'code' => $request->code,
            'rubro_id' => $request->rubro,
            'medida' => $request->medida,
            'categoria_id' => $request->id,
            'terminaciones' => $terminaciones,
            'materiales' => $materiales,
            'terminacion_id' => $request->terminacion,
            'material_id' => $request->material,
            'avanzada' => $request->avanzada,

        ]);
    }

    public function show($codigo, Request $request)
    {
        $producto = Producto::with(['categoria:id,name', 'imagenes', 'categoria', 'subcategoria'])->where('code', $codigo)->first();

        $subcategorias = SubCategoria::orderBy('order', 'asc')->get();

        $categorias = Categoria::select('id', 'name', 'order')->orderBy('order', 'asc')->get();

        $productosRelacionados = Producto::where('id', '!=', $producto->id)->orderBy('order', 'asc')->take(3)->get();

        return view('producto', [
            'producto' => $producto,
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
            'categoria' => $producto->categoria,
            'productosRelacionados' => $productosRelacionados,
            'modelo_id' => $request->modelo_id ?? null
        ]);
    }
    public function indexPrivada(Request $request)
    {


        $perPage = $request->input('per_page', 10);

        $qty = $request->input('qty', 1); // Valor por defecto para qty
        $carrito = Cart::content();

        $query = Producto::with(['imagenes', 'categoria', 'subcategoria', 'precio', 'material', 'terminacion'])->orderBy('order', 'asc');

        // Filtrar por código del subproducto

        if ($request->filled('id')) {
            $query->whereHas('categoria', function ($q) use ($request) {
                $q->where('categoria_id', $request->id);
            });
        }

        // Filtro por modelo/subcategoría
        if ($request->filled('modelo_id')) {
            $query->whereHas('modelos', function ($q) use ($request) {
                $q->where('sub_categoria_id', $request->modelo_id);
            });
        }

        // Filtro por código
        if ($request->filled('code')) {
            $query->where('code', 'LIKE', '%' . $request->code . '%');
        }

        // Filtro por código OEM
        if ($request->filled('code_oem')) {
            $query->where('code_oem', 'LIKE', '%' . $request->code_oem . '%');
        }

        if ($request->filled('medida')) {
            $query->where('medida', 'LIKE', '%' . $request->medida . '%');
        }

        // Filtro por descripción visible
        if ($request->filled('desc_visible')) {
            $query->where('desc_visible', 'LIKE', '%' . $request->desc . '%')->orWhere('desc_invisible', 'LIKE', '%' . $request->desc . '%');
        }



        $productos = $query->paginate(perPage: $perPage);

        // Modificar los productos para agregar rowId y qty del carrito
        $productos->getCollection()->transform(function ($producto) use ($carrito, $qty) {
            // Buscar el item del carrito que corresponde a este producto
            $itemCarrito = $carrito->where('id', $producto->id)->first();

            if ($itemCarrito) {
                $producto->rowId = $itemCarrito ? $itemCarrito->rowId : null;
                $producto->qty = $itemCarrito ? $itemCarrito->qty : null;
                $producto->subtotal =  $itemCarrito ? $itemCarrito->price * ($itemCarrito->qty ?? 1) : $producto->precio->precio;
            } else {
                $producto->rowId = null;
                $producto->qty = $qty; // Asignar qty por defecto si no está en el carrito
                $producto->subtotal = $producto->precio ? $producto->precio->precio * ($producto->qty ?? 1) : 0; // Asignar precio base si no está en el carrito
            }

            // Aquí puedes agregar más lógica si es necesario, como calcular el subtotal
            // Agregar el rowId y qty al producto
            // Calcular subtotal

            return $producto;
        });
        # si el usuario es vendedor

        $categorias = Categoria::orderBy('order', 'asc')->get();
        $subcategorias = SubCategoria::orderBy('order', 'asc')->get();



        return inertia('privada/productosPrivada', [
            'productos' => $productos,
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
            'id' => $request->id ?? null,
            'modelo_id' => $request->modelo_id ?? null,
            'code' => $request->code ?? null,
            'code_oem' => $request->code_oem ?? null,
            'desc_visible' => $request->desc_visible ?? null,

        ]);
    }

    /* public function indexInicio(Request $request, $id)
    {
        $marcas = Marca::select('id', 'name', 'order')->orderBy('order', 'asc')->get();

        $categorias = Categoria::select('id', 'name', 'order')
            ->orderBy('order', 'asc')
            ->get();
        $metadatos = Metadatos::where('title', 'Productos')->first();
        if ($request->has('marca') && !empty($request->marca)) {
            $productos = Producto::where('categoria_id', $id)->whereHas('subproductos')->whereHas('imagenes')->where('marca_id', $request->marca)->with('marca', 'imagenes')->orderBy('order', 'asc')->get();
        } else {
            $productos = Producto::where('categoria_id', $id)->whereHas('subproductos')->whereHas('imagenes')->with('marca', 'imagenes')->orderBy('order', 'asc')->get();
        }
        $subproductos = SubProducto::orderBy('order', 'asc')->get();

        return Inertia::render('productos', [
            'productos' => $productos,
            'categorias' => $categorias,
            'marcas' => $marcas,
            'metadatos' => $metadatos,
            'id' => $id,
            'marca_id' => $request->marca,
            'subproductos' => $subproductos,

        ]);
    } */

    public function indexInicio(Request $request, $id)
    {


        $categorias = Categoria::select('id', 'name', 'order')
            ->orderBy('order', 'asc')
            ->get();

        $metadatos = Metadatos::where('title', 'Productos')->first();

        $query = Producto::where('categoria_id', $id)

            ->orderBy('order', 'asc');



        $productos = $query->paginate(12)->withQueryString(); // 12 por página, mantiene filtros

        // Opcional: solo subproductos de productos actuales (más eficiente)
        $productoIds = $productos->pluck('id');
        $subproductos = SubProducto::whereIn('producto_id', $productoIds)
            ->orderBy('order', 'asc')
            ->get();

        return Inertia::render('productos', [
            'productos' => $productos,
            'categorias' => $categorias,

            'metadatos' => $metadatos,
            'id' => $id,

            'subproductos' => $subproductos,
        ]);
    }

    public function imagenesProducto()
    {
        $fotos = Storage::disk('public')->files('repuestos');

        foreach ($fotos as $foto) {
            $path = pathinfo(basename($foto), PATHINFO_FILENAME);

            $producto = Producto::where('code', $path)->first();
            if (!$producto) {
                continue; // Skip if the product is not found
            }
            $url = Storage::url($foto);
            ImagenProducto::create([
                'producto_id' => $producto->id,
                'image' => $url,
            ]);
        }
    }







    public function SearchProducts(Request $request)
    {
        $query = Producto::query();

        // Aplicar filtros solo si existen
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }



        if ($request->filled('codigo')) {
            $query->where('code', 'LIKE', '%' . $request->codigo . '%');
        }

        $productos = $query->with(['categoria:id,name', 'imagenes'])
            ->get();

        $categorias = Categoria::select('id', 'name', 'order')->orderBy('order', 'asc')->get();


        return Inertia::render('productos/productoSearch', [
            'productos' => $productos, // Cambié 'producto' a 'productos' (plural)
            'categorias' => $categorias,

        ]);
    }

    public function fixImagePath()
    {
        # Quitar /storage/ de las rutas de las imágenes
        $imagenes = ImagenProducto::all();
        foreach ($imagenes as $imagen) {
            if (strpos($imagen->image, '/storage/') === 0) {
                $imagen->image = str_replace('/storage/', '', $imagen->image);
                $imagen->save();
            }
        }

        return response()->json(['message' => 'Rutas de imágenes actualizadas correctamente.']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            // Validaciones del producto
            'order' => 'nullable|sometimes|max:255',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'medida' => 'nullable|string|max:255',
            'unidad_minima' => 'nullable|integer',
            'descuento' => 'nullable|integer',
            'destacado' => 'nullable|sometimes|boolean',
            'categoria_id' => 'required|exists:categorias,id',
            'sub_categoria_id' => 'required|exists:sub_categorias,id',
            'terminacion_id' => 'required|exists:terminaciones,id',
            'material_id' => 'required|exists:materiales,id',
            'images' => 'nullable|array|min:1',
            'images.*' => 'required|file|image',
        ]);

        try {
            return DB::transaction(function () use ($request, $data) {
                // Crear el producto primero
                $producto = Producto::create([
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'destacado' => $data['destacado'] ?? false,
                    'descuento' => $data['descuento'] ?? 0,
                    'unidad_minima' => $data['unidad_minima'] ?? 1,
                    'order' => $data['order'] ?? 'zzz',
                    'medida' => $data['medida'],
                    'categoria_id' => $data['categoria_id'],
                    'sub_categoria_id' => $data['sub_categoria_id'],
                    'terminacion_id' => $data['terminacion_id'],
                    'material_id' => $data['material_id'],
                ]);

                $createdImages = [];

                // Procesar imágenes si existen
                if ($request->hasFile(key: 'images')) {
                    foreach ($request->file('images') as $image) {
                        // Subir cada imagen
                        $imagePath = $image->store('images', 'public');

                        // Crear registro para cada imagen usando el ID del producto recién creado
                        $imageRecord = ImagenProducto::create([
                            'producto_id' => $producto->id,
                            'order' => $data['order'] ?? null,
                            'image' => $imagePath,
                        ]);

                        $createdImages[] = $imageRecord;
                    }
                }
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function update(Request $request)
    {
        $data = $request->validate([
            // Validaciones del producto
            'order' => 'nullable|sometimes|max:255',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'medida' => 'nullable|string|max:255',
            'unidad_minima' => 'nullable|integer',
            'descuento' => 'nullable|integer',
            'destacado' => 'nullable|sometimes|boolean',
            'categoria_id' => 'required|exists:categorias,id',
            'sub_categoria_id' => 'required|exists:sub_categorias,id',
            'terminacion_id' => 'required|exists:terminaciones,id',
            'material_id' => 'required|exists:materiales,id',
            'images' => 'nullable|array|min:1',
            'images.*' => 'required|file|image',
            // Para eliminar imágenes existentes
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:imagen_productos,id',
        ]);

        try {
            return DB::transaction(function () use ($request, $data) {
                // Buscar el producto
                $producto = Producto::findOrFail($request->id);

                // Actualizar los datos del producto
                $producto->update([
                    'order' => $data['order'],
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'destacado' => $data['destacado'] ?? false,
                    'descuento' => $data['descuento'] ?? 0,
                    'unidad_minima' => $data['unidad_minima'],
                    'medida' => $data['medida'],
                    'categoria_id' => $data['categoria_id'],
                    'sub_categoria_id' => $data['sub_categoria_id'],
                    'terminacion_id' => $data['terminacion_id'],
                    'material_id' => $data['material_id'],
                ]);

                if ($request->has('images_to_delete')) {
                    foreach ($request->images_to_delete as $imageId) {
                        $image = ImagenProducto::find($imageId);
                        if ($image) {
                            // Eliminar archivo del storage
                            Storage::delete($image->image);
                            // Eliminar registro de la base de datos
                            $image->delete();
                        }
                    }
                }

                // Agregar nuevas imágenes
                if ($request->hasFile('new_images')) {
                    foreach ($request->file('new_images') as $image) {
                        $path = $image->store('images', 'public');

                        ImagenProducto::create([
                            'producto_id' => $producto->id,
                            'image' => $path,
                        ]);
                    }
                }

                // Actualizar otros campos del producto


                // Eliminar imágenes seleccionadas si se especificaron
                if ($request->has('delete_images')) {
                    $imagesToDelete = ImagenProducto::where('producto_id', $producto->id)
                        ->whereIn('id', $data['delete_images'])
                        ->get();

                    foreach ($imagesToDelete as $imageRecord) {
                        // Eliminar archivo físico
                        if (Storage::disk('public')->exists($imageRecord->image)) {
                            Storage::disk('public')->delete($imageRecord->image);
                        }
                        // Eliminar registro de la base de datos
                        $imageRecord->delete();
                    }
                }

                // Procesar nuevas imágenes si existen
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        // Subir cada imagen
                        $imagePath = $image->store('images', 'public');

                        // Crear registro para cada imagen
                        ImagenProducto::create([
                            'producto_id' => $producto->id,
                            'order' => $data['order'] ?? null,
                            'image' => $imagePath,
                        ]);
                    }
                }

                // Actualizar relaciones con modelos

            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {

        $id = $request->id;
        try {
            return DB::transaction(function () use ($id) {
                // Buscar el producto
                $producto = Producto::findOrFail($id);

                // Eliminar todas las imágenes asociadas
                $imagenes = ImagenProducto::where('producto_id', $producto->id)->get();
                foreach ($imagenes as $imagen) {
                    // Eliminar archivo físico del storage
                    if (Storage::disk('public')->exists($imagen->image)) {
                        Storage::disk('public')->delete($imagen->image);
                    }
                    // Eliminar registro de la base de datos
                    $imagen->delete();
                }



                // Eliminar el producto
                $producto->delete();
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function cambiarDestacado(Request $request)
    {
        $producto = Producto::findOrFail($request->id);
        $producto->destacado = !$producto->destacado; // Cambiar el estado de destacado
        $producto->save();
    }

    public function productoszonaprivada(Request $request)
    {
        return inertia('admin/zonaprivadaProductosAdmin');
    }

    public function cambiarOferta(Request $request)
    {
        $producto = Producto::findOrFail($request->id);
        $producto->oferta = !$producto->oferta;
        $producto->save();
    }

    public function handleQR($code)
    {

        $producto = Producto::where('code', $code)->first();

        if (!$producto && $code != "adm") {
            return redirect('/productos');
        } else if ($code == "adm") {
            if (Auth::guard('admin')->check()) {
                return redirect('/admin/dashboard');
            }
            return Inertia::render('admin/login');
        }



        if (Auth::check()) {
            Cart::add(
                $producto->id,
                $producto->name,
                $producto->unidad_pack ?? 1,
                $producto->oferta ? $producto->precio->precio * (1 - $producto->descuento_oferta / 100) : $producto->precio->precio, // Asegurarse de que el precio sea correcto
                0
            );

            // Guardar en base de datos si hay usuario logueado
            if (Auth::check() && !session('cliente_seleccionado')) {
                if (Cart::count() < 0) {
                    Cart::store(Auth::id());
                }
            } else {
                Cart::store(session('cliente_seleccionado')->id);
            }

            return redirect('/privada/carrito');
        } else {
            return redirect('/p/' . $producto->code);
        }
    }

    public function productosBanner()
    {
        $productos = Banner::where('name', 'productos')->first();

        return Inertia::render('admin/productosBanner', ['productos' => $productos]);
    }
}
