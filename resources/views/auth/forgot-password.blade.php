<!DOCTYPE html>
<html lang="es">
<head>
  @include('layouts.head-page-meta', ['title' => 'Recuperar Contraseña'])
  @include('layouts.front-head-css')
</head>
<body class="landing-page">
  @include('layouts.loader')
  
  <div class="auth-main">
    <div class="auth-wrapper v3 d-flex justify-content-center align-items-center min-vh-100 py-5">
      <div class="auth-form col-12 col-md-6 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-body p-5">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="d-block mb-4">
                    <img src="{{ asset('assets/images/Logo.webp') }}" alt="EliCrochet" class="img-fluid" style="height: 60px;">
                </a>
                <div class="avatar-lg bg-light-warning text-warning mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px;">
                   <i class="ti ti-lock-question fs-2"></i>
                </div>
                <h3 class="mb-2"><b>Recuperar Contraseña</b></h3>
                <p class="text-muted small">Ingresa tu correo para recibir las instrucciones.</p>
             </div>

            @if (session('status'))
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class="ti ti-circle-check fs-4 me-2"></i>
                     <div>{{ session('status') }}</div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('password.email') }}">
              @csrf
              
              <div class="form-group mb-3">
                <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus>
                @error('email')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">
                  Enviar Enlace de Recuperación
                </button>
              </div>

              <div class="text-center mt-3">
                  <a href="{{ route('login') }}" class="link-secondary text-decoration-none">
                      <i class="ti ti-arrow-left me-1"></i> Volver al inicio de sesión
                  </a>
              </div>
            </form>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-4 text-muted small">
            <p class="mb-0">© {{ date('Y') }} {{ config('app.name', 'EliCrochet') }}. Todos los derechos reservados.</p>
        </div>
      </div>
    </div>
  </div>
  
  @include('layouts.footer-js')
</body>
</html>
