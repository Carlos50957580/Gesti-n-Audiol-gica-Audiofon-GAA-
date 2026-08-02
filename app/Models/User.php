<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'branch_id',
        'is_doctor',
        'is_active',
        'profile_photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_doctor' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // ── Relaciones ───────────────────────────────────────────────────────────

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // ✅ Relación con citas (como médico/doctor)
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    // ✅ Relación con citas (como paciente - si aplica)
    public function patientAppointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    // ✅ Relación con facturas (como usuario que creó)
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'user_id');
    }

    // ✅ Relación con facturas (como médico/doctor)
    public function doctorInvoices()
    {
        return $this->hasMany(Invoice::class, 'doctor_id');
    }

    // ✅ Relación con recibos
    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'user_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeDoctors($query)
    {
        return $query->where('is_doctor', 1);
    }

    public function scopeNonDoctors($query)
    {
        return $query->where('is_doctor', 0);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', 0);
    }

    // ── Métodos ──────────────────────────────────────────────────────────────

    public function isDoctor(): bool
    {
        return (bool) $this->is_doctor;
    }

    public function isAdmin(): bool
    {
        return $this->role_id == 1;
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    // app/Models/User.php - Agregar relaciones

// ── Relaciones con honorarios ──────────────────────────────

// Relación con DoctorFee (honorarios)
public function doctorFees()
{
    return $this->hasMany(DoctorFee::class, 'doctor_id');
}

// Relación con DoctorFeePayment (pagos de honorarios)
public function doctorFeePayments()
{
    return $this->hasMany(DoctorFeePayment::class, 'doctor_id');
}

// Relación con DoctorFeeSetting (configuraciones)
public function doctorFeeSettings()
{
    return $this->hasMany(DoctorFeeSetting::class, 'doctor_id');
}
}