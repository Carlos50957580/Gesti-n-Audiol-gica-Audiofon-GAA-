<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Iniciar Sesión | {{ company('name', 'Audiofon') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ company('slogan', 'Plataforma de Gestión Clínica') }}" name="description" />
    <meta content="{{ company('name', 'Audiofon') }}" name="author" />
    
    <!-- Favicon -->
    @if(company('favicon'))
        <link rel="shortcut icon" href="{{ asset('storage/' . company('favicon')) }}">
    @else
        <link rel="shortcut icon" href="{{ asset('velzon/assets/images/favicon.ico') }}">
    @endif

    <script src="{{ asset('velzon/assets/js/layout.js') }}"></script>
    <link href="{{ asset('velzon/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('velzon/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('velzon/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('velzon/assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden">
                            <div class="row g-0">
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a href="/" class="d-block">
                                                    @if(company('logo'))
                                                        <img src="{{ asset('storage/' . company('logo')) }}" 
                                                             alt="{{ company('name') }} Logo" 
                                                             height="60" 
                                                             style="max-width:200px; object-fit:contain;">
                                                    @else
                                                        <img src="{{ asset('velzon/assets/images/logo-darkh.png') }}" 
                                                             alt="{{ company('name') }} Logo" 
                                                             height="60" 
                                                             width="120">
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-success"></i>
                                                </div>

                                                <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Diapositiva 1"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="1" aria-label="Diapositiva 2"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="2" aria-label="Diapositiva 3"></button>
                                                    </div>
                                                    <div class="carousel-inner text-center text-white-50 pb-5" style="background-color: rgba(0, 0, 0, 0.5); border-radius: 5px; padding: 10px;">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">"{{ company('slogan', 'Tu salud es nuestra prioridad') }}"</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">"{{ company('footer_text', 'Gracias por confiar en nosotros') }}"</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">"Bienvenido a {{ company('name', 'Audiofon') }}"</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end carousel -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <div>
                                            <h5 class="text-primary">¡Bienvenido de Nuevo!</h5>
                                            <p class="text-muted">Inicia sesión para continuar a {{ company('name', 'la plataforma') }}.</p>
                                            
                                            @if(company('phone'))
                                                <p class="text-muted" style="font-size:13px;">
                                                    @if(company('email'))
                                                        | <i class="ri-mail-line"></i> {{ company('email') }}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>

                                        {{-- ✅ MOSTRAR MENSAJES FLASH --}}
                                        @if(session('success'))
                                            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                                <i class="ri-check-line me-1"></i> {{ session('success') }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        @endif

                                        @if(session('error'))
                                            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                                <i class="ri-error-warning-line me-1"></i> {{ session('error') }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        @endif

                                        {{-- ✅ MOSTRAR ERRORES DE VALIDACIÓN GENERALES --}}
                                        @if($errors->any())
                                            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                                <i class="ri-error-warning-line me-1"></i>
                                                <ul class="mb-0">
                                                    @foreach($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        @endif

                                        <div class="mt-4">
                                            <form method="POST" action="{{ route('login') }}">
                                                @csrf 

                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Correo Electrónico</label> 
                                                    
                                                    <input type="email" 
                                                           class="form-control @error('email') is-invalid @enderror" 
                                                           id="email" 
                                                           name="email" 
                                                           value="{{ old('email') }}" 
                                                           required 
                                                           autofocus 
                                                           autocomplete="username"
                                                           placeholder="Ingrese su correo electrónico">
                                                    
                                                    @error('email')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <div class="float-end">
                                                        @if (Route::has('password.request'))
                                                            <a href="{{ route('password.request') }}" class="text-muted">¿Olvidó su contraseña?</a>
                                                        @endif
                                                    </div>
                                                    
                                                    <label class="form-label" for="password">Contraseña</label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" 
                                                               class="form-control pe-5 @error('password') is-invalid @enderror" 
                                                               placeholder="Ingrese su contraseña" 
                                                               id="password"
                                                               name="password"
                                                               required 
                                                               autocomplete="current-password">
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>

                                                        @error('password')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remember" id="auth-remember-check">
                                                    <label class="form-check-label" for="auth-remember-check">Recordarme</label>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Iniciar Sesión</button>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">
                                                <span class="fw-semibold text-primary">¿No tiene una cuenta?</span>
                                                <br>
                                                <span class="text-muted" style="font-size:13px;">
                                                    Contacta con el administrador
                                                    @if(company('phone'))
                                                        <br>
                                                        <i class="ri-phone-line"></i> {{ company('phone') }}
                                                    @endif
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">
                                &copy; <script>document.write(new Date().getFullYear())</script> 
                                {{ company('name', 'Audiofon') }}
                                @if(company('rnc'))
                                    <span class="text-muted" style="font-size:12px;margin-left:8px;">
                                        <i class="ri-identifier-line"></i> RNC: {{ company('rnc') }}
                                    </span>
                                @endif
                                <br>
                                <small class="text-muted">
                                    {{ company('footer_text', 'Todos los derechos reservados.') }}
                                    @if(company('address'))
                                        <br>{{ company('address') }}
                                    @endif
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="{{ asset('velzon/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('velzon/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('velzon/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('velzon/assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('velzon/assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('velzon/assets/js/plugins.js') }}"></script>
    <script src="{{ asset('velzon/assets/js/pages/password-addon.init.js') }}"></script>

    <script>
        const passwordBtn = document.getElementById('password-addon');
        const passwordInput = document.getElementById('password');

        passwordBtn.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('ri-eye-fill');
            this.querySelector('i').classList.toggle('ri-eye-off-fill');
        });
    </script>
</body>

</html>