<!DOCTYPE html>
<html lang="es">
<head>
  @include('layouts.head-page-meta', ['title' => 'Registro'])
  @include('layouts.front-head-css')
</head>

<body class="landing-page">
  @include('layouts.loader')
  
    <div class="auth-main">
        <div class="auth-wrapper v3 d-flex justify-content-center align-items-center min-vh-100 py-5">
            <div class="auth-form col-12 col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <a href="{{ route('home') }}" class="d-block mb-4">
                                <img src="{{ asset('assets/images/Logo.webp') }}" alt="EliCrochet" class="img-fluid" style="height: 60px;">
                            </a>
                            <h3 class="mb-2"><b>Crear Cuenta</b></h3>
                            <p class="text-muted">¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="link-primary fw-bold">Inicia Sesión</a></p>
                        </div>
            
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
              
                            <div class="row">
                                <!-- Nombre -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label">Nombres</label>
                                        <input type="text" id="name" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="María" value="{{ old('name') }}" required autofocus autocomplete="given-name">
                                        @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Apellidos -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="lastname" class="form-label">Apellidos</label>
                                        <input type="text" id="lastname" name="lastname" class="form-control form-control-lg @error('lastname') is-invalid @enderror" placeholder="Pérez" value="{{ old('lastname') }}" required autocomplete="family-name">
                                        @error('lastname')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
              
                            <!-- Email -->
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" id="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="correo@ejemplo.com" value="{{ old('email') }}" required autocomplete="username">
                                @error('email')
                                  <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
              
                            <!-- Password -->
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="••••••••" required autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </div>
                                <!-- Password strength indicator placeholder -->
                                <div id="password-strength" class="mt-1 small"></div>
                                @error('password')
                                  <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
              
                            <!-- Confirm Password -->
                            <div class="form-group mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-lg" placeholder="••••••••" required autocomplete="new-password">
                            </div>
              
                            <!-- Términos -->
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" required>
                                <label class="form-check-label text-muted small" for="flexCheckChecked">
                                    Acepto los <a href="#" class="text-primary">Términos</a> y <a href="#" class="text-primary">Política de Privacidad</a>
                                </label>
                            </div>
              
                            <!-- Botón -->
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                    Crear Cuenta
                                </button>
                            </div>
              
                            <!-- Social Login -->
                            <div class="position-relative text-center my-4">
                                <hr class="border-secondary opacity-25">
                                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">O regístrate con</span>
                            </div>

                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn-outline-light border text-muted p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="ti ti-brand-google fs-4"></i>
                                </button>
                                <button type="button" class="btn btn-outline-light border text-muted p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="ti ti-brand-x fs-4"></i>
                                </button>
                                <button type="button" class="btn btn-outline-light border text-muted p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="ti ti-brand-facebook fs-4"></i>
                                </button>
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
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Toggle Password
      const toggle = document.getElementById('togglePassword');
      const input = document.getElementById('password');
      if(toggle && input){
          toggle.addEventListener('click', () => {
              const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
              input.setAttribute('type', type);
              toggle.querySelector('i').className = type === 'text' ? 'ti ti-eye-off' : 'ti ti-eye';
          });
      }

      // Password Match Validation
      const confirmInput = document.getElementById('password_confirmation');
      const validateMatch = () => {
          if(input.value !== confirmInput.value) {
              confirmInput.classList.add('is-invalid');
              confirmInput.classList.remove('is-valid');
          } else if(confirmInput.value !== '') {
              confirmInput.classList.remove('is-invalid');
              confirmInput.classList.add('is-valid');
          }
      };
      input.addEventListener('input', validateMatch);
      confirmInput.addEventListener('input', validateMatch);
      
      // Strength
      input.addEventListener('input', function() {
        const val = this.value;
        const container = document.getElementById('password-strength');
        let strength = 0;
        if(val.length >= 8) strength++;
        if(/[A-Z]/.test(val)) strength++;
        if(/[0-9]/.test(val)) strength++;
        if(/[^A-Za-z0-9]/.test(val)) strength++;
        
        let msg = ''; let cls = '';
        if(val.length === 0) { msg = ''; }
        else if(val.length < 8) { msg = 'Muy corta'; cls = 'text-danger'; }
        else if(strength < 2) { msg = 'Débil'; cls = 'text-danger'; }
        else if(strength === 2) { msg = 'Media'; cls = 'text-warning'; }
        else { msg = 'Fuerte'; cls = 'text-success'; }
        
        container.innerHTML = `<span class="${cls}">${msg}</span>`;
      });
    });
  </script>
</body>
</html>
