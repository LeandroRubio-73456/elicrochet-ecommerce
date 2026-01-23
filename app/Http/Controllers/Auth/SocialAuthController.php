<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            if (! $googleUser || ! $googleUser->id) {
                return redirect()->route('login')->with('error', 'No se pudo obtener la información de Google. Por favor intenta nuevamente.');
            }

            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // Si el usuario ya existe, iniciar sesión
                Auth::login($user);
            } else {
                // Verificar si existe un usuario con el mismo email
                $existingUser = User::where('email', $googleUser->email)->first();

                if ($existingUser) {
                    // Si el email existe, actualizar con google_id y avatar
                    $existingUser->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                    ]);
                    $user = $existingUser;
                } else {
                    // Crear nuevo usuario
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                        'password' => bcrypt(Str::random(16)), // Contraseña aleatoria, pero segura
                        'role' => 'customer', // Asignar rol por defecto
                    ]);
                }
                Auth::login($user);
            }

            // Redireccionar según el rol
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('account.index');
            }

        } catch (\Exception $e) {
            // Manejar errores (ej. usuario cancela el login)
            return redirect()->route('login')->with('error', 'Hubo un problema al iniciar sesión con Google: '.$e->getMessage());
        }
    }
}
