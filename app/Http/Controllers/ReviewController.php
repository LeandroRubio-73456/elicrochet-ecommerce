<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // 1. Check if user has already reviewed
        if ($user->hasReviewed($product)) {
            return back()->with('error', 'Ya has enviado una reseña para este producto.');
        }

        // 2. Check if user has verified purchase
        $isVerified = $user->hasPurchased($product);

        // Optional: Block non-purchasers? 
        // For now, let's allow it but only mark as verified if purchased, 
        // OR strictly block as per user request (User said "verify if user has purchased ... and enable button").
        // The user request implies capability is restricted.
        if (! $isVerified) {
             return redirect()->route('product.show', $product->slug)->with('error', 'Debes haber comprado, recibido y completado la orden de este producto para dejar una reseña.');
        }

        try {
            Review::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'rating' => $request->rating,
                'title' => $request->title,
                'comment' => $request->comment,
                'is_verified_purchase' => true,
            ]);

            return redirect()->route('product.show', $product->slug)->with('success', '¡Gracias por compartir tu opinión!');
        } catch(\Exception $e) {
             return redirect()->route('product.show', $product->slug)->with('error', 'Ocurrió un error al guardar tu reseña. Por favor intenta nuevamente.');
        }
    }
}
