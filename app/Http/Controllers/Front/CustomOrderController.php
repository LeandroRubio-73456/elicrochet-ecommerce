<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomOrderController extends Controller
{
    public function create()
    {
        return view('front.custom-order');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|min:20',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'suggested_date' => 'nullable|date|after:today',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
        ], [
            'description.required' => 'La descripción es obligatoria.',
            'description.min' => 'La descripción debe tener al menos 20 caracteres para poder entender mejor tu idea.',
            'images.*.image' => 'El archivo debe ser una imagen.',
            'images.*.max' => 'La imagen no debe pesar más de 2MB.',
            'customer_name.required' => 'El nombre es obligatorio.',
            'customer_email.required' => 'El email es obligatorio.',
            'customer_email.email' => 'Ingresa un email válido.',
            'customer_phone.required' => 'El teléfono es obligatorio.',
        ]);

        \Illuminate\Support\Facades\Log::info('Custom Order Store Request', $request->all());

        try {
            DB::beginTransaction();

            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('custom_orders', 'public');
                    $imagePaths[] = $path;
                }
            }

            // Create Order
            $order = Order::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'status' => Order::STATUS_QUOTATION,
                'type' => 'custom',
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'total_amount' => 0, // Will be set by owner later
            ]);

            // Create Order Item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => null,
                'quantity' => 1,
                'price' => 0,
                'custom_description' => $request->description."\n\nFecha sugerida: ".($request->suggested_date ?? 'No especificada'),
                'images' => $imagePaths,
            ]);

            DB::commit();

            return redirect()->route('home')->with('success', '¡Tu solicitud de cotización ha sido enviada! Nos pondremos en contacto pronto.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Custom Order Error: '.$e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());

            return back()->with('error', 'Ocurrió un error al enviar tu solicitud: '.$e->getMessage())->withInput();
        }
    }
}
