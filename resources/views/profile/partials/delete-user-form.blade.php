<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0 text-danger">
            {{ __('Eliminar cuenta') }}
        </h4>
        <p class="text-muted mt-1">
            {{ __('Una vez eliminada su cuenta, todos sus recursos y datos se eliminarán de forma permanente. Antes de eliminar su cuenta, descargue cualquier dato o información que desee conservar.') }}
        </p>
    </div><div class="card-body">
        <button
            type="button"
            class="btn btn-soft-danger waves-effect waves-light"
            data-bs-toggle="modal"
            data-bs-target="#confirm-user-deletion-modal"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >
            {{ __('Eliminar cuenta') }}
        </button>
    </div></div><div class="modal fade" id="confirm-user-deletion-modal" tabindex="-1" aria-labelledby="confirmUserDeletionLabel" aria-hidden="true"
    x-data="{
        show: $errors.userDeletion?.isNotEmpty(), // Mantiene el error visible si existe
        focusables() { return [...document.querySelectorAll('#confirm-user-deletion-modal .form-control')] }
    }"
    x-init="$watch('show', value => {
        if (value) { focusables()[0].focus() }
    })"
    style="{{ $errors->userDeletion->isNotEmpty() ? 'display: block; background: rgba(0,0,0,0.5);' : '' }}"
    >
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
            @csrf
            @method('delete')

            <div class="modal-header">
                <h5 class="modal-title" id="confirmUserDeletionLabel">
                    {{ __('Are you sure you want to delete your account?') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <p class="text-muted mb-4">
                    {{ __('Una vez que se elimine tu cuenta, todos sus recursos y datos se eliminarán de forma permanente. Introduce tu contraseña para confirmar que deseas eliminar tu cuenta de forma permanente.') }}
                </p>

                <div class="mb-3">
                    <label for="delete_account_password" class="form-label sr-only">{{ __('Password') }}</label>
                    <input
                        id="delete_account_password"
                        name="password"
                        type="password"
                        class="form-control"
                        placeholder="{{ __('Password') }}"
                    />
                    @if ($errors->userDeletion->has('password'))
                        <div class="invalid-feedback d-block">{{ $errors->userDeletion->first('password') }}</div>
                    @endif
                </div>
            </div>

            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-light waves-effect" data-bs-dismiss="modal" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="btn btn-danger waves-effect waves-light ms-2">
                    {{ __('Eliminar cuenta') }}
                </button>
            </div>
        </form>
    </div>
</div>
