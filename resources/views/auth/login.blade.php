<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.head-page-meta', ['title' => 'Iniciar Sesión'])
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
                            <h3 class="kb-2"><b>Bienvenido de nuevo</b></h3>
                            <p class="text-muted">¿No tienes una cuenta? <a href="{{ route('register') }}" class="link-primary fw-bold">Regístrate</a></p>
                        </div>
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email -->
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" placeholder="correo@ejemplo.com" value="{{ old('email') }}" required autofocus autocomplete="username">
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Remember & Forgot -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted" for="remember">Recuérdame</label>
                                </div>
                                <a href="{{ route('password.request') }}" class="text-primary small fw-bold">¿Olvidaste tu contraseña?</a>
                            </div>

                            <!-- Submit -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                    Iniciar Sesión
                                </button>
                            </div>

                            <!-- Social Login -->
                            <div class="position-relative text-center my-4">
                                <hr class="border-secondary opacity-25">
                                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">O inicia con</span>
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
        });
    </script>
</body>
</html>
