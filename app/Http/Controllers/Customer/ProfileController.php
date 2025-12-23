<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user()->load('addresses');
        $address = $user->addresses->first(); // Get the first address or null
        return view('customer.profile.edit', compact('user', 'address'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'shipping_address' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['nullable', 'string', 'max:100'],
            'shipping_province' => ['nullable', 'string', 'max:100'], // Added
            'shipping_reference' => ['nullable', 'string', 'max:255'], // Added
            'shipping_zip' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];

        // Actualizar o Crear Dirección
        if ($request->filled('shipping_address')) {
            $user->addresses()->updateOrCreate(
                ['user_id' => $user->id], 
                [
                    'street' => $validated['shipping_address'], 
                    'address' => $validated['shipping_address'], // Keep redundant if needed for legacy
                    'city' => $validated['shipping_city'],
                    'province' => $validated['shipping_province'], 
                    'reference' => $validated['shipping_reference'],
                    'postal_code' => $validated['shipping_zip'],
                    'phone' => $validated['phone'],
                    'customer_name' => $user->name,
                    'customer_email' => $user->email
                ]
            );
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('customer.profile.edit')->with('success', 'Perfil actualizado correctamente.');
    }
}
