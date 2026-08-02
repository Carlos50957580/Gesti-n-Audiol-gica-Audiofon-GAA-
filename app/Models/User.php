<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes; // ✅ Importar SoftDeletes

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes; // ✅ Agregar SoftDeletes

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'branch_id',
        'is_doctor',
        'is_active', // ✅ Agregar
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
        'is_active' => 'boolean', // ✅ Agregar
    ];

    // ✅ Fechas para soft delete
    protected $dates = ['deleted_at'];

    // Relaciones
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // ✅ Scopes
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

    // ✅ Métodos
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
}