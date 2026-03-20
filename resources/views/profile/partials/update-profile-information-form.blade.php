<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">
            {{ __('Información del perfil') }}
        </h4>
        <p class="text-muted mt-1">
            {{ __("Actualiza tu nombre y correo electrónico.") }}
        </p>
    </div>

    <div class="card-body">

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form id="profile-form" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('patch')

            {{-- NOMBRE --}}
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input
                    name="name"
                    type="text"
                    class="form-control"
                    value="{{ old('name', $user->name) }}"
                    required
                >
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- EMAIL --}}
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input
                    name="email"
                    type="email"
                    class="form-control"
                    value="{{ old('email', $user->email) }}"
                    required
                >
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- BOTÓN --}}
            <div class="mt-4">
                <button class="btn btn-primary">
                    Guardar
                </button>

                @if (session('status') === 'profile-updated')
                    <span class="text-success ms-2">Guardado ✔</span>
                @endif
            </div>

        </form>
    </div>
</div>