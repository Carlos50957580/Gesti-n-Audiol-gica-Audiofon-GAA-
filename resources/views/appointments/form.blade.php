<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Paciente</label>

<select name="patient_id" class="form-control">

@foreach($patients as $patient)

<option value="{{ $patient->id }}"
{{ old('patient_id',$appointment->patient_id ?? '')==$patient->id?'selected':'' }}>

{{ $patient->first_name }} {{ $patient->last_name }}

</option>

@endforeach

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Audiólogo</label>

<select name="audiologist_id" class="form-control">

@foreach($audiologists as $user)

<option value="{{ $user->id }}"
{{ old('audiologist_id',$appointment->audiologist_id ?? '')==$user->id?'selected':'' }}>

{{ $user->name }}

</option>

@endforeach

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Fecha</label>

<input type="date"
name="appointment_date"
class="form-control"
value="{{ old('appointment_date',$appointment->appointment_date ?? '') }}">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Hora</label>

<input type="time"
name="appointment_time"
class="form-control"
value="{{ old('appointment_time',$appointment->appointment_time ?? '') }}">

</div>

<div class="col-md-12 mb-3">

<label class="form-label">Estado</label>

<select name="status" class="form-control">

<option value="programada">Programada</option>
<option value="completada">Completada</option>
<option value="cancelada">Cancelada</option>

</select>

</div>

<div class="col-md-12 mb-3">

<label class="form-label">Notas</label>

<textarea name="notes" class="form-control">

{{ old('notes',$appointment->notes ?? '') }}

</textarea>

</div>

</div>