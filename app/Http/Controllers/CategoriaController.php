<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $perPage = $request->input('per_page', 10);

        $query = Categoria::query()->orderBy('order', 'asc');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('name', 'LIKE', '%' . $searchTerm . '%');
        }

        $categorias = $query->paginate($perPage);



        return Inertia::render('admin/categoriasAdmin', [
            'categorias' => $categorias,
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'order' => 'nullable|sometimes|string',
            'name' => 'required|string|max:255',
            'image' => 'sometimes|file|nullable',
        ]);

        // Check if the image is provided and store it
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        } else {
            $data['image'] = null; // Set to null if no image is provided
        }


        // Create the category
        Categoria::create($data);

        return redirect()->back()->with('success', 'Category created successfully.');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $categoria = Categoria::findOrFail($request->id);

        // Check if the category entry exists
        if (!$categoria) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        $data = $request->validate([
            'order' => 'sometimes|string',
            'name' => 'sometimes|string|max:255',
            'image' => 'sometimes|file|nullable',
        ]);

        // Check if the image is provided and store it
        if ($request->hasFile('image')) {
            // If an image is provided, delete the old one if it exists
            if ($categoria->getRawOriginal('image')) {
                Storage::disk('public')->delete($categoria->getRawOriginal('image'));
            }
            $data['image'] = $request->file('image')->store('images', 'public');
        } else {
            $data['image'] = $categoria->getRawOriginal('image'); // Keep the old image if no new one is provided
        }



        // Update the category
        $categoria->update($data);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $categoria = Categoria::findOrFail($request->id);

        // Check if the category entry exists
        if (!$categoria) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        // If an image is associated with the category, delete it
        if ($categoria->getRawOriginal('image')) {
            Storage::disk('public')->delete($categoria->getRawOriginal('image'));
        }



        // Delete the category
        $categoria->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
