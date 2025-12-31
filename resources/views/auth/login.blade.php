<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    @include('layouts.head-page-meta', ['title' => 'Iniciar Sesión'])
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
                        <img src="{{ asset('Logo.png') }}" alt="EliCrochet" class="img-fluid" style="height: 50px;">
                    </a>
                </div>
                
                <div class="card my-5">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-end mb-4">
                            <h3 class="mb-0"><b>Iniciar Sesión</b></h3>
                            <a href="{{ route('register') }}" class="link-primary">
                                ¿No tienes una cuenta?
                            </a>
                        </div>
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email -->
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">
                                    Correo Electrónico <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       placeholder="correo@ejemplo.com"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">
                                    Contraseña <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           placeholder="••••••••" 
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Remember me & Forgot password -->
                            <div class="d-flex mt-1 justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input input-primary"
                                           type="checkbox"
                                           id="remember"
                                           name="remember"
                                           {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted" for="remember">
                                        Mantener sesión iniciada
                                    </label>
                                </div>
                                <a href="{{ route('password.request') }}" class="text-primary">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>

                            <!-- Submit button -->
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-login me-2"></i> Iniciar Sesión
                                </button>
                            </div>

                            <!-- Separator -->
                            <div class="saprator mt-3">
                                <span>O inicia sesión con</span>
                            </div>

                            <!-- Social login -->
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
                        <p class="m-0">
                            Copyright © {{ date('Y') }} 
                            <a href="#">{{ config('app.name', 'Laravel') }}</a>
                        </p>
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
            // Mostrar/ocultar contraseña
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Cambiar ícono
                    const icon = this.querySelector('i');
                    if (type === 'text') {
                        icon.classList.remove('ti-eye');
                        icon.classList.add('ti-eye-off');
                        this.setAttribute('aria-label', 'Ocultar contraseña');
                    } else {
                        icon.classList.remove('ti-eye-off');
                        icon.classList.add('ti-eye');
                        this.setAttribute('aria-label', 'Mostrar contraseña');
                    }
                });
            }
            
            // Validación de formulario en tiempo real
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            
            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            if (emailInput) {
                emailInput.addEventListener('blur', function() {
                    if (this.value && !validateEmail(this.value)) {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    } else if (this.value) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            }
            
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    if (this.value.length >= 8) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else if (this.value.length > 0) {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    }
                });
            }
            
            // Mostrar mensajes de error de Laravel como toast
            @if ($errors->any())
                setTimeout(function() {
                    @foreach ($errors->all() as $error)
                        const toast = document.createElement('div');
                        toast.className = 'toast align-items-center text-bg-danger border-0 position-fixed top-0 end-0 m-3';
                        toast.setAttribute('role', 'alert');
                        toast.setAttribute('aria-live', 'assertive');
                        toast.setAttribute('aria-atomic', 'true');
                        
                        toast.innerHTML = `
                            <div class="d-flex">
                                <div class="toast-body">
                                    <i class="ti ti-alert-circle me-2"></i>
                                    {{ $error }}
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                            </div>
                        `;
                        
                        document.body.appendChild(toast);
                        const bsToast = new bootstrap.Toast(toast);
                        bsToast.show();
                        
                        // Eliminar toast después de 5 segundos
                        setTimeout(() => {
                            toast.remove();
                        }, 5000);
                    @endforeach
                }, 500);
            @endif
        });
    </script>
</body>
<!-- [Body] end -->
</html>