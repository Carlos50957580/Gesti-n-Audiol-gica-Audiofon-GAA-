<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Obtiene un valor de configuración
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        static $settings = null;
        
        if ($settings === null) {
            $settings = Setting::getAll();
        }
        
        return $settings[$key] ?? $default;
    }
}

if (!function_exists('company')) {
    /**
     * Obtiene un dato de la empresa
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function company($key, $default = null)
    {
        return setting('company_' . $key, $default);
    }
}