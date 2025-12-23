<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
  @include('layouts.head-page-meta', ['title' => 'Registro'])
  @include('layouts.front-head-css')
</head>
<!-- [Body] Start -->

<body class="landing-page">
  @include('layouts.loader')
  
  <div class="auth-main">
    <div class="auth-wrapper v3">
      <div class="auth-form">
        <div class="auth-header">
          <a href="{{ route('home') }}"><img src="{{ asset('Logo.png') }}" alt="EliCrochet" class="img-fluid" style="height: 50px;"></a>
        </div>
        
        <div class="card my-5">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-end mb-4">
              <h3 class="mb-0"><b>Crear Cuenta</b></h3>
              <a href="{{ route('login') }}" class="link-primary">¿Ya tienes una cuenta?</a>
            </div>
            
            <form method="POST" action="{{ route('register') }}">
              @csrf
              
              <!-- Nombre -->
              <div class="form-group mb-3">
                <label for="name" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control @error('name') is-invalid @enderror" 
                       placeholder="Tu nombre completo" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus 
                       autocomplete="name">
                @error('name')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
              
              <!-- Email -->
              <div class="form-group mb-3">
                <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       placeholder="correo@ejemplo.com" 
                       value="{{ old('email') }}" 
                       required 
                       autocomplete="username">
                @error('email')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
              
              <!-- Password -->
              <div class="form-group mb-3">
                <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       placeholder="••••••••" 
                       required 
                       autocomplete="new-password">
                <small class="text-muted">Mínimo 8 caracteres</small>
                @error('password')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
              
              <!-- Confirm Password -->
              <div class="form-group mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       class="form-control" 
                       placeholder="••••••••" 
                       required 
                       autocomplete="new-password">
              </div>
              
              <!-- Términos y condiciones -->
              <p class="mt-4 text-sm text-muted">
                Al registrarte, aceptas nuestros 
                <a href="#" class="text-primary">Términos de Servicio</a> 
                y 
                <a href="#" class="text-primary">Política de Privacidad</a>
              </p>
              
              <!-- Botón de registro -->
              <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary">
                  Crear Cuenta
                </button>
              </div>
              
              <!-- Separador -->
              <div class="saprator mt-3">
                <span>O regístrate con</span>
              </div>
              
              <!-- Login social -->
              <div class="row">
                <div class="col-4">
                  <div class="d-grid">
                    <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                      <img src="{{ asset('assets/images/authentication/google.svg') }}" alt="Google">
                      <span class="d-none d-sm-inline-block"> Google</span>
                    </button>
                  </div>
                </div>
                <div class="col-4">
                  <div class="d-grid">
                    <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                      <img src="{{ asset('assets/images/authentication/twitter.svg') }}" alt="Twitter">
                      <span class="d-none d-sm-inline-block"> Twitter</span>
                    </button>
                  </div>
                </div>
                <div class="col-4">
                  <div class="d-grid">
                    <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                      <img src="{{ asset('assets/images/authentication/facebook.svg') }}" alt="Facebook">
                      <span class="d-none d-sm-inline-block"> Facebook</span>
                    </button>
                  </div>
                </div>
              </div>
              
            </form>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="auth-footer row">
          <div class="col my-1">
            <p class="m-0">© {{ date('Y') }} <a href="#">{{ config('app.name', 'Laravel') }}</a></p>
          </div>
          <div class="col-auto my-1">
            <ul class="list-inline footer-link mb-0">
              <li class="list-inline-item"><a href="{{ route('home') }}">Inicio</a></li>
              <li class="list-inline-item"><a href="#">Política de Privacidad</a></li>
              <li class="list-inline-item"><a href="{{ route('contact') }}">Contacto</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- [ Main Content ] end -->
  @include('layouts.footer-js')
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Validación de contraseña en tiempo real
      const passwordInput = document.getElementById('password');
      const confirmInput = document.getElementById('password_confirmation');
      
      function validatePassword() {
        if (passwordInput.value !== confirmInput.value) {
          confirmInput.classList.add('is-invalid');
          confirmInput.classList.remove('is-valid');
        } else {
          confirmInput.classList.remove('is-invalid');
          confirmInput.classList.add('is-valid');
        }
      }
      
      if (passwordInput && confirmInput) {
        passwordInput.addEventListener('input', validatePassword);
        confirmInput.addEventListener('input', validatePassword);
      }
      
      // Validación de fortaleza de contraseña (opcional)
      passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strengthText = document.getElementById('password-strength');
        
        if (!strengthText) {
          const strengthDiv = document.createElement('div');
          strengthDiv.id = 'password-strength';
          strengthDiv.className = 'text-sm mt-1';
          this.parentNode.appendChild(strengthDiv);
        }
        
        let strength = 0;
        let message = '';
        let color = 'text-danger';
        
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        switch(strength) {
          case 0:
          case 1:
            message = 'Contraseña débil';
            color = 'text-danger';
            break;
          case 2:
            message = 'Contraseña media';
            color = 'text-warning';
            break;
          case 3:
            message = 'Contraseña fuerte';
            color = 'text-success';
            break;
          case 4:
            message = 'Contraseña muy fuerte';
            color = 'text-success';
            break;
        }
        
        document.getElementById('password-strength').innerHTML = 
          `<span class="${color}">${message}</span>`;
      });
    });
  </script>
</body>
<!-- [Body] end -->
</html>