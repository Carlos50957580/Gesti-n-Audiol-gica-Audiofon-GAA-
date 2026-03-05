<div class="row">

<div class="col-md-6 mb-3">
<label class="form-label">Nombre</label>
<input type="text" name="first_name" class="form-control"
value="{{ old('first_name',$patient->first_name ?? '') }}" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Apellido</label>
<input type="text" name="last_name" class="form-control"
value="{{ old('last_name',$patient->last_name ?? '') }}" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Cédula</label>
<input type="text" name="cedula" class="form-control"
value="{{ old('cedula',$patient->cedula ?? '') }}">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Teléfono</label>
<input type="text" name="phone" class="form-control"
value="{{ old('phone',$patient->phone ?? '') }}">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Email</label>
<input type="email" name="email" class="form-control"
value="{{ old('email',$patient->email ?? '') }}">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Fecha de nacimiento</label>
<input type="date" name="birth_date" class="form-control"
value="{{ old('birth_date',$patient->birth_date ?? '') }}">
</div>

<div class="col-md-12 mb-3">
<label class="form-label">Dirección</label>
<textarea name="address" class="form-control">{{ old('address',$patient->address ?? '') }}</textarea>
</div>

</div>