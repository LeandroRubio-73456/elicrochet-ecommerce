<x-guest-layout>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="ti ti-mail fs-1 text-primary"></i>
                    </div>

                    <h2 class="h4 mb-3">Verifica tu correo electrónico</h2>
                    
                    <p class="text-muted mb-4">
                        {{ __('¡Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar? Si no recibiste el correo, con gusto te enviaremos otro.') }}
                    </p>
    
                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success mb-4 text-start" role="alert">
                            {{ __('Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionaste durante el registro.') }}
                        </div>
                    @endif
    
                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('Reenviar correo de verificación') }}
                            </button>
                        </form>
            
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                {{ __('Cerrar Sesión') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-guest-layout>
