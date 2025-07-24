<?php

namespace App\Http\Controllers;

use App\Models\MailNewsletter;
use Illuminate\Http\Request;

class MailNewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = MailNewsletter::query()->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('email', 'LIKE', '%' . $searchTerm . '%');
        }

        $newsletters = $query->paginate($perPage);

        return inertia('admin/newsletterAdmin', [
            'newsletters' => $newsletters,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar que sea una petición AJAX
            if (!$request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Petición no válida'
                ], 400);
            }

            $request->validate([
                'email' => 'required|email|unique:mail_newsletters,email',
            ], [
                'email.required' => 'El campo email es obligatorio.',
                'email.email' => 'El formato del email no es válido.',
                'email.unique' => 'Este email ya está suscrito al newsletter.',
            ]);

            MailNewsletter::create($request->only('email'));

            return response()->json([
                'success' => true,
                'message' => '¡Te has suscrito correctamente al newsletter!'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log del error para debugging
            \Log::error('Newsletter subscription error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar tu solicitud'
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:mail_newsletters,email,' . $request->id,
        ]);

        $mailNewsletter = MailNewsletter::findOrFail($request->id);
        $mailNewsletter->update($request->only('email'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $mailNewsletter = MailNewsletter::findOrFail($request->id);
        $mailNewsletter->delete();
    }
}
