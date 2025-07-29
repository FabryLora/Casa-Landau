<?php

namespace App\Http\Controllers;

use App\Models\PedidoProducto;
use Illuminate\Http\Request;

class PedidoProductoController extends Controller
{


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([

            'cantidad_entregada' => 'nullable|numeric',
        ]);

        PedidoProducto::create($data);
    }


    public function update(Request $request)
    {

        $data = $request->validate([

            'cantidad_entregada' => 'nullable|numeric',
        ]);

        $pedidoProducto = PedidoProducto::where('pedido_id', $request->pedido_id)
            ->where('producto_id', $request->id)
            ->firstOrFail();

        $pedidoProducto->update($data);
    }
}
