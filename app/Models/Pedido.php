<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productos()
    {
        return $this->hasMany(PedidoProducto::class);
    }

    protected function serializeDate($date)
    {
        return $date->format('d/m/Y');
    }
}
