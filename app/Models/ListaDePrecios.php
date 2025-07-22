<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ListaDePrecios extends Model
{
    protected $guarded = [];

    // Si el archivo se guarda como una ruta
    public function getArchivoAttribute($value)
    {
        return asset("storage/" . $value);
    }

    public function getFormatoArchivo()
    {
        // Usar getOriginal() para obtener el valor sin procesar por el accessor
        $archivoOriginal = $this->getOriginal('archivo');

        if (empty($archivoOriginal)) {
            return null;
        }

        // Obtener la extensión del archivo
        $extension = pathinfo($archivoOriginal, PATHINFO_EXTENSION);
        return $extension;
    }

    public function getPesoArchivo()
    {
        $archivoOriginal = $this->getOriginal('archivo');

        if (empty($archivoOriginal)) {
            return null;
        }

        $bytes = null;

        // Intentar obtener el tamaño usando diferentes métodos
        try {
            // Método 1: Disco público
            if (Storage::disk('public')->exists($archivoOriginal)) {
                $bytes = Storage::disk('public')->size($archivoOriginal);
            }
            // Método 2: Disco local
            elseif (Storage::disk('local')->exists($archivoOriginal)) {
                $bytes = Storage::disk('local')->size($archivoOriginal);
            }
            // Método 3: Ruta directa del sistema de archivos
            else {
                $posiblesRutas = [
                    storage_path('app/public/' . $archivoOriginal),
                    storage_path('app/' . $archivoOriginal),
                    public_path('storage/' . $archivoOriginal)
                ];

                foreach ($posiblesRutas as $ruta) {
                    if (file_exists($ruta)) {
                        $bytes = filesize($ruta);
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            // Log del error si necesitas debuggear
            // \Log::error('Error obteniendo peso archivo: ' . $e->getMessage());
            return null;
        }

        if ($bytes === null) {
            return null;
        }

        return $this->formatearBytes($bytes);
    }

    private function formatearBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    // Método adicional para obtener solo la ruta del archivo sin la URL completa
    public function getRutaArchivoAttribute()
    {
        return $this->getOriginal('archivo');
    }

    // Método para debug - te ayuda a ver qué está pasando
    public function debugArchivo()
    {
        $archivoOriginal = $this->getOriginal('archivo');

        return [
            'archivo_original' => $archivoOriginal,
            'existe_en_public' => Storage::disk('public')->exists($archivoOriginal),
            'existe_en_local' => Storage::disk('local')->exists($archivoOriginal),
            'ruta_public' => storage_path('app/public/' . $archivoOriginal),
            'existe_ruta_public' => file_exists(storage_path('app/public/' . $archivoOriginal)),
            'ruta_local' => storage_path('app/' . $archivoOriginal),
            'existe_ruta_local' => file_exists(storage_path('app/' . $archivoOriginal)),
        ];
    }

    public function productos()
    {
        return $this->hasMany(ListaProductos::class, 'lista_de_precios_id');
    }

    public function clientes()
    {
        return $this->hasMany(User::class, 'lista_de_precios_id');
    }
}
