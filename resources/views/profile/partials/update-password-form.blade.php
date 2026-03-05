
<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">
            {{ __('Actualizar contraseña') }}
        </h4>
        <p class="text-muted mt-1">
            {{ __('Asegúrate de que tu cuenta utiliza una contraseña larga y aleatoria para mantenerla segura.') }}
        </p>
    </div><div class="card-body">
        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="mb-3">
                <label for="update_password_current_password" class="form-label">{{ __('Contraseña actual') }}</label>
                <input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    class="form-control"
                    autocomplete="current-password"
                />
                @if ($errors->updatePassword->has('current_password'))
                    <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('current_password') }}</div>
                @endif
            </div>

            <div class="mb-3">
                <label for="update_password_password" class="form-label">{{ __('Nueva contraseña') }}</label>
                <input
                    id="update_password_password"
                    name="password"
                    type="password"
                    class="form-control"
                    autocomplete="new-password"
                />
                @if ($errors->updatePassword->has('password'))
                    <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('password') }}</div>
                @endif
            </div>

            <div class="mb-3">
                <label for="update_password_password_confirmation" class="form-label">{{ __('Confirmar contraseña') }}</label>
                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="form-control"
                    autocomplete="new-password"
                />
                @if ($errors->updatePassword->has('password_confirmation'))
                    <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                @endif
            </div>

            <div class="d-flex align-items-center gap-3 mt-4">
                <button type="submit" class="btn btn-primary waves-effect waves-light">
                    {{ __('Guardar') }}
                </button>

                @if (session('status') === 'password-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-success mb-0"
                    >{{ __('Saved.') }}</p>
                @endif
            </div>
        </form>
    </div></div>