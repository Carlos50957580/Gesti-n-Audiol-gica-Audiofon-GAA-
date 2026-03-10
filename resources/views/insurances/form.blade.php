<div class="row">

<div class="col-md-6 mb-3">
<label class="form-label">Nombre</label>
<input type="text" name="name" class="form-control"
value="{{ old('name',$insurance->name ?? '') }}" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Teléfono</label>
<input type="text" name="phone" class="form-control"
value="{{ old('phone',$insurance->phone ?? '') }}">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Teléfono de autorización</label>
<input type="text" name="authorization_phone" class="form-control"
value="{{ old('authorization_phone',$insurance->authorization_phone ?? '') }}">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Correo</label>
<input type="email" name="email" class="form-control"
value="{{ old('email',$insurance->email ?? '') }}">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Cobertura base (%)</label>
<input type="number" step="0.01" name="coverage_percentage" class="form-control"
value="{{ old('coverage_percentage',$insurance->coverage_percentage ?? '') }}">
</div>

<div class="col-md-12 mb-3">
<label class="form-label">Dirección</label>
<textarea name="address" class="form-control">{{ old('address',$insurance->address ?? '') }}</textarea>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Activa</label>

<select name="active" class="form-select">

<option value="1"
{{ old('active',$insurance->active ?? 1) == 1 ? 'selected':'' }}>
Sí
</option>

<option value="0"
{{ old('active',$insurance->active ?? 1) == 0 ? 'selected':'' }}>
No
</option>

</select>

</div>

</div>