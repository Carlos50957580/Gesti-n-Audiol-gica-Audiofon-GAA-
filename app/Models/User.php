<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'branch_id',
        'is_doctor',
        'profile_photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_doctor' => 'boolean',
        ];
    }

    // Relaciones
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // ✅ NUEVO: Scope para filtrar médicos
    public function scopeDoctors($query)
    {
        return $query->where('is_doctor', 1);
    }

    // ✅ NUEVO: Scope para filtrar no médicos
    public function scopeNonDoctors($query)
    {
        return $query->where('is_doctor', 0);
    }

    // ✅ NUEVO: Verificar si el usuario es médico
    public function isDoctor(): bool
    {
        return (bool) $this->is_doctor;
    }

    // ✅ NUEVO: Verificar si es administrador
    public function isAdmin(): bool
    {
        return $this->role_id == 1; // Asumiendo que el rol admin tiene ID 1
    }
}