<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type'
    ];

    protected $casts = [
        'value' => 'string'
    ];

    // Obtener un valor
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    // Establecer un valor
    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    // Obtener todos los settings como array clave-valor
    public static function getAll()
    {
        return self::pluck('value', 'key')->toArray();
    }
}