<?php

namespace App\Http\Controllers;

use App\Models\Terminaciones;
use Illuminate\Http\Request;

class TerminacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = Terminaciones::query()->orderBy('order', 'asc');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('name', 'LIKE', '%' . $searchTerm . '%');
        }

        $terminaciones = $query->paginate($perPage);

        return inertia('admin/terminacionesAdmin', [
            'terminaciones' => $terminaciones,
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'order' => 'sometimes|nullable|string|max:255',
            'name' => 'required|string|max:255',
        ]);

        // Create the terminacion
        Terminaciones::create($data);

        return redirect()->back()->with('success', 'Terminación created successfully.');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'order' => 'sometimes|nullable|string|max:255',
            'name' => 'required|string|max:255',
        ]);

        // Update the terminacion
        Terminaciones::where('id', $request->id)->update($data);

        return redirect()->back()->with('success', 'Terminación updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $terminacion = Terminaciones::findOrFail($request->id);
        $terminacion->delete();

        return redirect()->back()->with('success', 'Terminación deleted successfully.');
    }
}
