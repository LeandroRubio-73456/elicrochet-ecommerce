<!DOCTYPE html>
<html lang="es">
<head>
  @include('layouts.head-page-meta', ['title' => 'Recuperar Contraseña'])
  @include('layouts.front-head-css')
</head>
<body class="landing-page">
  @include('layouts.loader')
  
  <div class="auth-main">
    <div class="auth-wrapper v3">
      <div class="auth-form">
        <div class="auth-header">
          <a href="{{ route('home') }}">
            <img src="{{ asset('assets/images/Logo.webp') }}" alt="EliCrochet" class="img-fluid" style="height: 50px;">
          </a>
        </div>
        
        <div class="card my-5">
          <div class="card-body">
            <div class="text-center mb-4">
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
                       placeholder="correo@ejemplo.com"
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
        
        <div class="auth-footer row">
            <div class="col my-1">
              <p class="m-0">© {{ date('Y') }} <a href="#">{{ config('app.name', 'Laravel') }}</a></p>
            </div>
            <div class="col-auto my-1">
              <ul class="list-inline footer-link mb-0">
                <li class="list-inline-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="list-inline-item"><a href="#">Privacidad</a></li>
                <li class="list-inline-item"><a href="{{ route('contact') }}">Contacto</a></li>
              </ul>
            </div>
          </div>
      </div>
    </div>
  </div>
  
  @include('layouts.footer-js')
</body>
</html>
