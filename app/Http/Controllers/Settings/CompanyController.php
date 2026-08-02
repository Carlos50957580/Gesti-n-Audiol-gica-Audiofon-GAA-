<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * Muestra el formulario de configuración de la empresa
     */
    public function index()
    {
        // Obtener toda la configuración de la empresa usando tu modelo
        $company = [
            'name' => Setting::get('company_name', 'Mi Clínica'),
            'business_name' => Setting::get('company_business_name', 'Mi Clínica SRL'),
            'rnc' => Setting::get('company_rnc', ''),
            'email' => Setting::get('company_email', ''),
            'phone' => Setting::get('company_phone', ''),
            'mobile' => Setting::get('company_mobile', ''),
            'address' => Setting::get('company_address', ''),
            'slogan' => Setting::get('company_slogan', ''),
            'website' => Setting::get('company_website', ''),
            'logo' => Setting::get('company_logo', null),
            'favicon' => Setting::get('company_favicon', null),
            'footer_text' => Setting::get('company_footer_text', ''),
            'currency' => Setting::get('company_currency', 'DOP'),
            'tax_rate' => Setting::get('company_tax_rate', 18),
            'invoice_prefix' => Setting::get('company_invoice_prefix', 'FAC-'),
            'receipt_prefix' => Setting::get('company_receipt_prefix', 'REC-'),
            'ncf_type' => Setting::get('company_ncf_type', 'consumidor_final'),
            'ncf_sequence' => Setting::get('company_ncf_sequence', 1),
        ];

        return view('settings.company', compact('company'));
    }

    /**
     * Actualiza la configuración de la empresa
     */
    public function update(Request $request)
    {
        // Validar los datos
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_business_name' => 'required|string|max:255',
            'company_rnc' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:20',
            'company_mobile' => 'nullable|string|max:20',
            'company_address' => 'nullable|string|max:500',
            'company_slogan' => 'nullable|string|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'company_favicon' => 'nullable|image|mimes:png,ico,svg|max:1024',
            'company_footer_text' => 'nullable|string|max:255',
            'company_currency' => 'required|string|max:10',
            'company_tax_rate' => 'nullable|numeric|min:0|max:100',
            'company_invoice_prefix' => 'nullable|string|max:20',
            'company_receipt_prefix' => 'nullable|string|max:20',
            'company_ncf_type' => 'nullable|in:consumidor_final,credito_fiscal,gubernamental,regimen_especial',
            'company_ncf_sequence' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Guardar campos de texto usando tu modelo
        $textFields = [
            'company_name',
            'company_business_name',
            'company_rnc',
            'company_email',
            'company_phone',
            'company_mobile',
            'company_address',
            'company_slogan',
            'company_website',
            'company_footer_text',
            'company_currency',
            'company_invoice_prefix',
            'company_receipt_prefix',
            'company_ncf_type',
        ];

        foreach ($textFields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->$field);
            }
        }

        // Guardar campos numéricos
        if ($request->has('company_tax_rate')) {
            Setting::set('company_tax_rate', $request->company_tax_rate);
        }

        if ($request->has('company_ncf_sequence')) {
            Setting::set('company_ncf_sequence', $request->company_ncf_sequence);
        }

        // Procesar el logo
        if ($request->hasFile('company_logo')) {
            // Eliminar logo anterior si existe
            $oldLogo = Setting::get('company_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            
            $path = $request->file('company_logo')->store('company/logos', 'public');
            Setting::set('company_logo', $path);
        }

        // Procesar el favicon
        if ($request->hasFile('company_favicon')) {
            // Eliminar favicon anterior si existe
            $oldFavicon = Setting::get('company_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            
            $path = $request->file('company_favicon')->store('company/favicons', 'public');
            Setting::set('company_favicon', $path);
        }

        // Limpiar cache de settings si existe
        if (function_exists('cache')) {
            cache()->forget('settings');
        }

        return redirect()->route('settings.company')
            ->with('success', 'Información de la empresa actualizada correctamente.');
    }
}