<?php

namespace App\Http\Controllers;

use App\Models\ProductImage; // Asegúrate de que este es el modelo correcto
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    // Cambiamos $image a $productImage para que coincida con la ruta y evitar confusión.
    public function destroy(ProductImage $productImage)
    {
        // 1. Eliminar archivo físico
        if (Storage::disk('public')->exists($productImage->image_path)) {
            Storage::disk('public')->delete($productImage->image_path);
        }

        // 2. Eliminar registro BD
        $productImage->delete();

        // Devuelve una respuesta JSON simple
        return response()->json(['success' => true, 'message' => 'Imagen eliminada correctamente.']);
    }
}
