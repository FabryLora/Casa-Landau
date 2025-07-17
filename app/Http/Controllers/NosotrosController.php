<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Nosotros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class NosotrosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nosotros = Nosotros::first();


        return Inertia::render('admin/nosotrosAdmin', ['nosotros' => $nosotros]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $nosotros = Nosotros::first();



        // Check if the Nosotros entry exists

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'text' => 'sometimes',
            'video' => 'sometimes|nullable|file',
            'image' => 'sometimes|file',
        ]);

        if ($request->hasFile('image') && $nosotros) {
            // Guardar la ruta del archivo antiguo para eliminarlo después
            $oldImagePath = $nosotros->getRawOriginal('image');

            // Guardar el nuevo archivo
            $data['image'] = $request->file('image')->store('slider', 'public');

            // Eliminar el archivo antiguo si existe
            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        } else if ($request->hasFile('image') && !$nosotros) {
            $data['image'] = $request->file('image')->store('slider', 'public');
        }

        if ($request->hasFile('video') && $nosotros) {
            // Guardar la ruta del archivo antiguo para eliminarlo después
            $oldVideoPath = $nosotros->getRawOriginal('video');

            // Guardar el nuevo archivo
            $data['video'] = $request->file('video')->store('slider', 'public');

            // Eliminar el archivo antiguo si existe
            if ($oldVideoPath && Storage::disk('public')->exists($oldVideoPath)) {
                Storage::disk('public')->delete($oldVideoPath);
            }
        } else if ($request->hasFile('video') && !$nosotros) {
            $data['video'] = $request->file('video')->store('slider', 'public');
        }

        if (!$nosotros) {
            // Si no existe, crear una nueva entrada
            $nosotros = Nosotros::create($data);
            return redirect()->back()->with('success', 'Nosotros created successfully.');
        }

        $nosotros->update($data);

        return redirect()->back()->with('success', 'Nosotros updated successfully.');
    }

    public function nosotrosBanner()
    {
        $nosotros = Banner::where('name', 'nosotros')->first();

        return Inertia::render('admin/nosotrosBanner', ['nosotros' => $nosotros]);
    }
}
