@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Perfil de Usuario</h4>
        </div>
    </div>
</div>

<div class="row">

    {{-- PERFIL IZQUIERDA --}}
    <div class="col-xxl-3">
        <div class="card">
            <div class="card-body p-4 text-center">

                <div class="profile-user position-relative d-inline-block mx-auto mb-4">

                    {{-- IMAGEN --}}
                    <img 
                        src="{{ Auth::user()->profile_photo 
                            ? asset('storage/' . Auth::user()->profile_photo) 
                            : asset('velzon/assets/images/users/avatar-1.jpg') }}" 
                        class="rounded-circle avatar-xl img-thumbnail user-profile-image"
                        alt="user-profile-image"
                    >

                    {{-- BOTÓN CAMBIAR FOTO --}}
                    <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                        <input 
                            id="profile-img-file-input" 
                            name="profile_photo"
                            type="file" 
                            class="profile-img-file-input"
                            form="profile-form"
                            accept="image/*"
                        >
                        <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                            <span class="avatar-title rounded-circle bg-light text-body">
                                <i class="ri-camera-fill"></i>
                            </span>
                        </label>
                    </div>

                </div>

                <h5 class="fs-16 mb-1">{{ Auth::user()->name }}</h5>
                <p class="text-muted mb-0">
                    Miembro desde: {{ Auth::user()->created_at->format('Y-m-d') }}
                </p>

            </div>
        </div>
    </div>

    {{-- FORMULARIOS --}}
    <div class="col-xxl-9">
        <div class="card">
            <div class="card-body pt-0">

                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails">
                            <i class="ri-user-2-fill me-1"></i> Detalles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#changePassword">
                            <i class="ri-lock-2-fill me-1"></i> Seguridad
                        </a>
                    </li>
                </ul>

                <div class="tab-content">

                    <div class="tab-pane active" id="personalDetails">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div class="tab-pane" id="changePassword">

                        <div class="card border mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Actualizar Contraseña</h5>
                            </div>
                            <div class="card-body">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>

                        <div class="card border mt-3">
                            <div class="card-header bg-danger-subtle">
                                <h5 class="card-title text-danger mb-0">Eliminar Cuenta</h5>
                            </div>
                            <div class="card-body">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

</div>

{{-- PREVIEW --}}
<script>
document.getElementById('profile-img-file-input').addEventListener('change', function(event) {
    const reader = new FileReader();

    reader.onload = function(){
        document.querySelector('.user-profile-image').src = reader.result;
    };

    if(event.target.files[0]){
        reader.readAsDataURL(event.target.files[0]);
    }
});
</script>

@endsection