<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">
            {{ __('Información del perfil') }}
        </h4>
        <p class="text-muted mt-1">
            {{ __("Actualiza la información del perfil y la dirección de correo electrónico de tu cuenta.") }}
        </p>
    </div><div class="card-body">
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('Nombre') }}</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    class="form-control"
                    value="{{ old('name', $user->name) }}"
                    required
                    autofocus
                    autocomplete="name"
                />
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    class="form-control"
                    value="{{ old('email', $user->email) }}"
                    required
                    autocomplete="username"
                />
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-sm text-muted">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" class="btn btn-link p-0 align-baseline text-decoration-underline">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-success">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="d-flex align-items-center gap-3 mt-4">
                <button type="submit" class="btn btn-primary waves-effect waves-light">
                    {{ __('Guardar') }}
                </button>

                @if (session('status') === 'profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-success mb-0"
                    >{{ __('Guardado.') }}</p>
                @endif
            </div>
        </form>
    </div></div>