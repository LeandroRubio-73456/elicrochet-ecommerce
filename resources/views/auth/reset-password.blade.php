<!DOCTYPE html>
<html lang="es">
<head>
  @include('layouts.head-page-meta', ['title' => 'Restablecer Contraseña'])
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
                <div class="avatar-lg bg-light-info text-info mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px;">
                   <i class="ti ti-key fs-2"></i>
                </div>
                <h3 class="mb-2"><b>Nueva Contraseña</b></h3>
                <p class="text-muted small">Por favor ingresa tu nueva contraseña.</p>
             </div>
            
            <form method="POST" action="{{ route('password.store') }}">
              @csrf
              <input type="hidden" name="token" value="{{ $request->route('token') }}">
              
              <!-- Email -->
              <div class="form-group mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email"
                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email', $request->email) }}"
                       required
                       readonly>
                @error('email')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <!-- Password -->
              <div class="form-group mb-3">
                <label for="password" class="form-label">Nueva Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password" placeholder="••••••••">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="ti ti-eye"></i>
                    </button>
                </div>
                <small class="text-muted mt-1 d-block">Mínimo 8 caracteres.</small>
                @error('password')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <!-- Confirm Password -->
              <div class="form-group mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirm">
                        <i class="ti ti-eye"></i>
                    </button>
                </div>
              </div>
              
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                  Restablecer Contraseña
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

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Password Visibility
        const setupToggle = (btnId, inputId) => {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            if(btn && input) {
                btn.addEventListener('click', () => {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    btn.querySelector('i').className = type === 'text' ? 'ti ti-eye-off' : 'ti ti-eye';
                });
            }
        };

        setupToggle('togglePassword', 'password');
        setupToggle('toggleConfirm', 'password_confirmation');
    });
  </script>
</body>
</html>
