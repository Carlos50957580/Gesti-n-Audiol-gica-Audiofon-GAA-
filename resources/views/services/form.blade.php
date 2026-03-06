<div class="row">

<div class="col-md-6 mb-3">
<label class="form-label">Nombre del servicio</label>
<input type="text" name="name" class="form-control"
value="{{ old('name',$service->name ?? '') }}" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Precio</label>
<input type="number" step="0.01" name="price" class="form-control"
value="{{ old('price',$service->price ?? '') }}" required>
</div>

<div class="col-md-12 mb-3">
<label class="form-label">Descripción</label>
<textarea name="description" class="form-control">{{ old('description',$service->description ?? '') }}</textarea>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Estado</label>

<select name="active" class="form-select">

<option value="1" {{ old('active',$service->active ?? 1) == 1 ? 'selected':'' }}>
Activo
</option>

<option value="0" {{ old('active',$service->active ?? 1) == 0 ? 'selected':'' }}>
Inactivo
</option>

</select>

</div>

</div>