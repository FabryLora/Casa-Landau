<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create()
    {
        return redirect('/');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => "required|string|max:255",
            'email' => "sometimes|nullable|string|email|max:255|unique:users,email",

            "password" => "required|confirmed|string|min:8",
            'cuit' => 'required|string|max:20',
            'razon_social' => 'nullable|sometimes|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'localidad' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'descuento_uno' => 'nullable|sometimes|integer|min:0|max:100',
            'descuento_dos' => 'nullable|sometimes|integer|min:0|max:100',
            'descuento_tres' => 'nullable|sometimes|integer|min:0|max:100',
            'rol' => 'sometimes|nullable|string|max:255', // Optional role, default is 'cliente'
            'lista_de_precios_id' => 'nullable|sometimes|exists:lista_de_precios,id',
            'vendedor_id' => 'nullable|sometimes|exists:users,id',
            'autorizado' => 'nullable|boolean',
            'sucursales' => 'nullable|array',
            'sucursales.*' => 'exists:sucursals,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,

            'razon_social' => $request->razon_social,
            'cuit' => $request->cuit,
            'direccion' => $request->direccion,
            'provincia' => $request->provincia,
            'localidad' => $request->localidad,
            'telefono' => $request->telefono,
            'descuento_uno' => $request->descuento_uno || 0,
            'descuento_dos' => $request->descuento_dos || 0,
            'descuento_tres' => $request->descuento_tres || 0,
            'rol' => $request->rol ?? 'cliente',
            'vendedor_id' => $request->vendedor_id,
            'autorizado' => $request->autorizado || false,
            'lista_de_precios_id' => $request->lista_de_precios_id ?? null,
            'password' => Hash::make($request->password),
        ]);
    }
}
