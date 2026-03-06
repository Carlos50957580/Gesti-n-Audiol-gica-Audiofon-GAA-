<div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" name="name" class="form-control"
           value="{{ old('name', $branch->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Dirección</label>
    <input type="text" name="address" class="form-control"
           value="{{ old('address', $branch->address ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Teléfono</label>
    <input type="text" name="phone" class="form-control"
           value="{{ old('phone', $branch->phone ?? '') }}">
</div>
