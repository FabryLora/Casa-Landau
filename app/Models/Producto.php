<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $guarded = [];
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function subcategoria()
    {
        return $this->belongsTo(SubCategoria::class, 'sub_categoria_id');
    }
    public function terminacion()
    {
        return $this->belongsTo(Terminaciones::class, 'terminacion_id');
    }

    public function material()
    {
        return $this->belongsTo(Materiales::class, 'material_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenProducto::class, 'producto_id');
    }



    public function getImageAttribute($value)
    {
        return url("storage/" . $value);
    }

    public function precio()
    {
        return $this->hasOne(ListaProductos::class, 'producto_id')
            ->where('lista_de_precios_id',  session('cliente_seleccionado') ? session('cliente_seleccionado')->lista_de_precios_id : auth()->user()->lista_de_precios_id ?? null);
    }

    public function pedidos()
    {
        return $this->hasMany(PedidoProducto::class, 'producto_id');
    }
}
