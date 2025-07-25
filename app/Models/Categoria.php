<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $guarded = [];

    public function getImageAttribute($value)
    {
        return url("storage/" . $value);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class)->orderBy('order');
    }

    public function subCategorias()
    {
        return $this->hasMany(SubCategoria::class)->orderBy('order');
    }
}
