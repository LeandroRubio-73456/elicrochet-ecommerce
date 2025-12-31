<!DOCTYPE html>
<html lang="es">
<head>
  @include('layouts.head-page-meta', ['title' => 'Restablecer Contraseña'])
  @include('layouts.front-head-css')
</head>
<body class="landing-page">
  @include('layouts.loader')
  
  <div class="auth-main">
    <div class="auth-wrapper v3">
      <div class="auth-form">
        <div class="auth-header">
          <a href="{{ route('home') }}">
            <img src="{{ asset('Logo.png') }}" alt="EliCrochet" class="img-fluid" style="height: 50px;">
          </a>
        </div>
        
        <div class="card my-5">
          <div class="card-body">
            <div class="mb-4">
              <h3 class="mb-0"><b>Restablecer Contraseña</b></h3>
              <p class="text-muted small mt-2">Crea una nueva contraseña para tu cuenta.</p>
            </div>
            
            <form method="POST" action="{{ route('password.store') }}">
              @csrf
              <input type="hidden" name="token" value="{{ $request->route('token') }}">
              
              <!-- Email -->
              <div class="form-group mb-3">
                <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email', $request->email) }}"
                       required
                       autofocus>
                @error('email')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <!-- Password -->
              <div class="form-group mb-3">
                <label for="password" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                <input type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       id="password"
                       name="password"
                       required
                       autocomplete="new-password">
                @error('password')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <!-- Confirm Password -->
              <div class="form-group mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                <input type="password"
                       class="form-control @error('password_confirmation') is-invalid @enderror"
                       id="password_confirmation"
                       name="password_confirmation"
                       required
                       autocomplete="new-password">
                @error('password_confirmation')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">
                  Restablecer Contraseña
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
