<?php

namespace App\Http\Controllers;

use App\Models\Materiales;
use Illuminate\Http\Request;

class MaterialesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $perPage = $request->input('per_page', 10);

        $query = Materiales::query()->orderBy('order', 'asc');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('name', 'LIKE', '%' . $searchTerm . '%');
        }

        $materiales = $query->paginate($perPage);



        return inertia('admin/materialesAdmin', [
            'materiales' => $materiales,
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'order' => 'sometimes|string',
            'name' => 'required|string|max:255',

        ]);



        // Create the material
        Materiales::create($data);

        return redirect()->back()->with('success', 'Material created successfully.');
    }


    public function update(Request $request)
    {
        $material = Materiales::findOrFail($request->id);

        $data = $request->validate([
            'order' => 'sometimes|string',
            'name' => 'sometimes|string|max:255',
        ]);

        // Update the material
        $material->update($data);

        return redirect()->back()->with('success', 'Material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $material = Materiales::findOrFail($request->id);

        // Delete the material
        $material->delete();

        return redirect()->back()->with('success', 'Material deleted successfully.');
    }
}
