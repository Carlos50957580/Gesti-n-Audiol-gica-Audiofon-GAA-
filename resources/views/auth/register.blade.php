<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Registrarse | Audiofon</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Plataforma de Gestión de Planes de Estudio UASD" name="description" />
    <meta content="UASD" name="author" />
    
    <link rel="shortcut icon" href="{{ asset('velzon/assets/images/favicon.ico') }}">

    <script src="{{ asset('velzon/assets/js/layout.js') }}"></script>
    <link href="{{ asset('velzon/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('velzon/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('velzon/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('velzon/assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden m-0">
                            <div class="row justify-content-center g-0">
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a href="{{ url('/') }}" class="d-block">
                                                    <img src="{{ asset('velzon/assets/images/logo-light.png') }}" alt="UASD Logo" height="18">
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
                                                    <div class="carousel-inner text-center text-white-50 pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">"Una plataforma excelente que simplifica la gestión de planes de estudio de manera eficiente."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">"La mejor herramienta para digitalizar procesos académicos. Altamente recomendada."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">"Interfaz intuitiva y soporte excepcional. Facilita enormemente nuestro trabajo diario."</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <div>
                                            <h5 class="text-primary">Crear Cuenta</h5>
                                            <p class="text-muted">Obtenga su cuenta gratuita de la plataforma UASD ahora.</p>
                                        </div>

                                        <div class="mt-4">
                                            <form method="POST" action="{{ route('register') }}">
                                                @csrf

                                                <div class="mb-3">
                                                    <label for="username" class="form-label">Nombre de Usuario <span class="text-danger">*</span></label>
                                                    <input type="text"
                                                           class="form-control @error('name') is-invalid @enderror"
                                                           id="username"
                                                           name="name"
                                                           value="{{ old('name') }}"
                                                           required
                                                           autofocus
                                                           autocomplete="name"
                                                           placeholder="Ingrese nombre de usuario">
                                                    @error('name')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="useremail" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                                    <input type="email"
                                                           class="form-control @error('email') is-invalid @enderror"
                                                           id="useremail"
                                                           name="email"
                                                           value="{{ old('email') }}"
                                                           required
                                                           autocomplete="username"
                                                           placeholder="Ingrese correo electrónico">
                                                    @error('email')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="password-input">Contraseña</label>
                                                    <div class="position-relative auth-pass-inputgroup">
                                                        <input type="password"
                                                               class="form-control pe-5 password-input @error('password') is-invalid @enderror"
                                                               placeholder="Ingrese contraseña"
                                                               id="password-input"
                                                               name="password"
                                                               required
                                                               autocomplete="new-password">
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                        @error('password')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label" for="password_confirmation">Confirmar Contraseña</label>
                                                    <div class="position-relative auth-pass-inputgroup">
                                                        <input type="password"
                                                               class="form-control pe-5 password-input"
                                                               placeholder="Confirme su contraseña"
                                                               id="password_confirmation"
                                                               name="password_confirmation"
                                                               required
                                                               autocomplete="new-password">
                                                    </div>
                                                </div>
                                                
                                                <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                                    <h5 class="fs-13">La contraseña debe contener:</h5>
                                                    <p id="pass-length" class="invalid fs-12 mb-2">Mínimo <b>8 caracteres</b></p>
                                                    <p id="pass-lower" class="invalid fs-12 mb-2">Al menos una letra <b>minúscula</b> (a-z)</p>
                                                    <p id="pass-upper" class="invalid fs-12 mb-2">Al menos una letra <b>mayúscula</b> (A-Z)</p>
                                                    <p id="pass-number" class="invalid fs-12 mb-0">Al menos un <b>número</b> (0-9)</p>
                                                </div>

                                                <div class="mb-4">
                                                    <p class="mb-0 fs-12 text-muted fst-italic">Al registrarse, acepta los <a href="#" class="text-primary text-decoration-underline fst-normal fw-medium">Términos de Uso</a> de la plataforma UASD</p>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Registrarse</button>
                                                </div>

                                                <div class="mt-4 text-center">
                                                    <div class="signin-other-title">
                                                        <h5 class="fs-13 mb-4 title text-muted">Crear cuenta con</h5>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-facebook-fill fs-16"></i></button>
                                                        <button type="button" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-google-fill fs-16"></i></button>
                                                        <button type="button" class="btn btn-dark btn-icon waves-effect waves-light"><i class="ri-github-fill fs-16"></i></button>
                                                        <button type="button" class="btn btn-info btn-icon waves-effect waves-light"><i class="ri-twitter-fill fs-16"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">¿Ya tiene una cuenta? <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline">Iniciar Sesión</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">&copy;
                                <script>document.write(new Date().getFullYear())</script> Audiofon.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <script src="{{ asset('velzon/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('velzon/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('velzon/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('velzon/assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('velzon/assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('velzon/assets/js/plugins.js') }}"></script>
    <script src="{{ asset('velzon/assets/js/pages/passowrd-create.init.js') }}"></script>
</body>

</html>