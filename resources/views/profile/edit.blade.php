@extends('layouts.app') {{-- Usa el layout principal de Velzon --}}

@section('title', 'Mi Perfil')

@section('content')

    {{-- Estructura de Encabezado de Página (Velzon Style) --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Perfil de Usuario</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Páginas</a></li>
                        <li class="breadcrumb-item active">Perfil</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenido Principal del Perfil (Siguiendo la estructura de 'pages-profile.html') --}}
    <div class="row">

        {{-- Columna para Información de Perfil / Imagen --}}
        {{-- Aquí se suele poner la tarjeta con la foto de perfil y la información resumida --}}
        <div class="col-xxl-3">
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-center">
                        <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                            {{-- Aquí puedes poner la lógica para mostrar la imagen de perfil --}}
                            <img src="{{ asset('velzon/assets/images/users/avatar-1.jpg') }}" 
                                 class="rounded-circle avatar-xl img-thumbnail user-profile-image" 
                                 alt="user-profile-image">
                            
                            {{-- Botón para cambiar la foto (Opcional, requiere JS/Upload) --}}
                            <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                <input id="profile-img-file-input" type="file" class="profile-img-file-input">
                                <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                    <span class="avatar-title rounded-circle bg-light text-body">
                                        <i class="ri-camera-fill"></i>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <h5 class="fs-16 mb-1">{{ Auth::user()->name }}</h5>
                        <p class="text-muted mb-0">Miembro desde: {{ Auth::user()->created_at->format('Y-m-d') }}</p>
                    </div>
                </div>
            </div>
            
            {{-- Puedes añadir más tarjetas de información aquí si la plantilla lo requiere --}}
        </div> 
        {{-- Fin Columna xx1-3 --}}

        {{-- Columna Principal para Formularios --}}
        <div class="col-xxl-9">
            
            {{-- Pestañas de Navegación (Si quieres replicar el diseño de pestañas) --}}
            {{-- Si quieres una vista más simple, omite esta sección de nav-tabs y coloca los cards directamente --}}
            <div class="card">
                <div class="card-body pt-0">
                    <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                <i class="ri-user-2-fill align-bottom me-1"></i> Detalles Personales
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                <i class="ri-lock-2-fill align-bottom me-1"></i> Seguridad
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">

                        {{-- Pestaña de Detalles Personales (Información de Perfil) --}}
                        <div class="tab-pane active" id="personalDetails" role="tabpanel">
                            {{-- Forma 1: Usar solo el parcial de Breeze (Necesita estilo) --}}
                            @include('profile.partials.update-profile-information-form')

                            {{-- Forma 2: Si quieres el formulario en una tarjeta aparte dentro de esta pestaña --}}
                            {{-- <div class="card border border-2">
                                <div class="card-header"><h5 class="card-title mb-0">Actualizar Información</h5></div>
                                <div class="card-body">
                                    @include('profile.partials.update-profile-information-form')
                                </div>
                            </div> --}}
                        </div>

                        {{-- Pestaña de Seguridad (Contraseña y Eliminar Cuenta) --}}
                        <div class="tab-pane" id="changePassword" role="tabpanel">
                            
                            {{-- Formulario de Actualizar Contraseña --}}
                            <div class="card border border-2">
                                <div class="card-header"><h5 class="card-title mb-0">Actualizar Contraseña</h5></div>
                                <div class="card-body">
                                    @include('profile.partials.update-password-form')
                                </div>
                            </div>

                            {{-- Formulario de Eliminar Cuenta --}}
                            <div class="card border border-2 mt-4">
                                <div class="card-header bg-danger-subtle"><h5 class="card-title mb-0 text-danger">Eliminar Cuenta</h5></div>
                                <div class="card-body">
                                    @include('profile.partials.delete-user-form')
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    {{-- Fin tab-content --}}
                </div>
            </div>
            {{-- Fin Card Pestañas --}}

        </div> 
        {{-- Fin Columna xx1-9 --}}
    </div>
    {{-- Fin Row Principal --}}
@endsection