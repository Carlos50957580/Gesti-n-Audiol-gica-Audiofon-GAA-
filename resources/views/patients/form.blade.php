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
<label class="form-label">Aseguradora</label>

<select name="insurance_id" class="form-select">

<option value="">Sin seguro</option>

@foreach($insurances as $insurance)

<option value="{{ $insurance->id }}"
{{ old('insurance_id',$patient->insurance_id ?? '') == $insurance->id ? 'selected':'' }}>

{{ $insurance->name }}

</option>

@endforeach

</select>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Poliza</label>

<input type="text" name="insurance_number" class="form-control"
value="{{ old('insurance_number',$patient->insurance_number ?? '') }}">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Email</label>
<input type="email" name="email" class="form-control"
value="{{ old('email',$patient->email ?? '') }}">
</div>

@if(auth()->user()->role->name == 'admin')

<div class="col-md-6 mb-3">
<label class="form-label">Sucursal</label>

<select name="branch_id" class="form-select">

<option value="">Seleccione</option>

@foreach($branches as $branch)

<option value="{{ $branch->id }}"
{{ old('branch_id',$patient->branch_id ?? '') == $branch->id ? 'selected':'' }}>

{{ $branch->name }}

</option>

@endforeach

</select>

</div>

@endif

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