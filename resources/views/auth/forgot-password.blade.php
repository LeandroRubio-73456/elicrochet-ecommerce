<!DOCTYPE html>
<html lang="es">
<!-- [Head] start -->
<head>
  @include('layouts.head-page-meta', ['title' => 'Recuperar Contraseña'])
  @include('layouts.front-head-css')
</head>
<!-- [Head] end -->
<!-- [Body] Start -->
<body class="landing-page">
  @include('layouts.loader')
  
  <div class="auth-main">
    <div class="auth-wrapper v3">
      <div class="auth-form">
        <div class="auth-header">
          <a href="{{ route('home') }}">
            <img src="{{ asset('Logo.webp') }}" alt="EliCrochet" class="img-fluid" style="height: 50px;">
          </a>
        </div>
        
        <div class="card my-5">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-end mb-4">
              <h3 class="mb-0"><b>Recuperar Contraseña</b></h3>
              <a href="{{ route('login') }}" class="link-primary">Volver al Login</a>
            </div>

            <div class="mb-3 text-muted">
                {{ __('¿Olvidaste tu contraseña? No hay problema. Simplemente haznos saber tu dirección de correo electrónico y te enviaremos un enlace para restablecerla.') }}
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">
                    {{ session('status') }}
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
              
              <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary">
                  Enviar Enlace de Recuperación
                </button>
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
