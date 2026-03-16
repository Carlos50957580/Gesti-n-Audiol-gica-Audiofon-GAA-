<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el rol admin creado en RoleSeeder
        $adminRole = Role::where('name', 'admin')->first();

        // Crear el usuario administrador
        User::create([
            'role_id' => $adminRole->id,
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => Hash::make('1234'), // Encriptar la contraseña
        ]);
    }
}
