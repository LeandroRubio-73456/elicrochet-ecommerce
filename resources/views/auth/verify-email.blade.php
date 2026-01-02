<!DOCTYPE html>
<html lang="es">
<head>
  @include('layouts.head-page-meta', ['title' => 'Verificar Correo'])
  @include('layouts.front-head-css')
</head>
<body class="landing-page">
  @include('layouts.loader')
  
  <div class="auth-main">
    <div class="auth-wrapper v3 d-flex justify-content-center align-items-center min-vh-100 py-5">
      <div class="auth-form col-12 col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-body p-5">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="d-block mb-4">
                    <img src="{{ asset('assets/images/Logo.webp') }}" alt="EliCrochet" class="img-fluid" style="height: 60px;">
                </a>
               <div class="avatar-lg bg-light-primary text-primary mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px;">
                  <i class="ti ti-mail fs-2"></i>
               </div>
               <h3 class="mb-2"><b>Verifica tu Correo</b></h3>
               <p class="text-muted">Hemos enviado un enlace de verificación a tu correo electrónico.</p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class="ti ti-circle-check fs-4 me-2"></i>
                    <div>
                        Se ha enviado un nuevo enlace de verificación.
                    </div>
                </div>
            @endif

            <p class="text-muted text-center mb-4 small">
                ¿No recibiste el correo? Revisa tu carpeta de spam o solicita otro enlace a continuación.
            </p>
            
            <form method="POST" action="{{ route('verification.send') }}">
              @csrf
              <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                  <i class="ti ti-send me-2"></i> Reenviar Correo
                </button>
              </div>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <div class="d-grid">
                    <button type="submit" class="btn btn-link text-muted text-decoration-none">
                        <i class="ti ti-logout me-2"></i> Cerrar Sesión
                    </button>
                </div>
            </form>

          </div>
        </div>
        
        <div class="text-center mt-4 text-muted small">
            <p class="mb-0">© {{ date('Y') }} {{ config('app.name', 'EliCrochet') }}. Todos los derechos reservados.</p>
        </div>
      </div>
    </div>
  </div>
  
  @include('layouts.footer-js')
</body>
</html>
