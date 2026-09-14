<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NcfType;

class NcfTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            // ── COMPROBANTES DE INGRESOS ─────────────────────────
            ['code' => 'B01', 'name' => 'Factura de Crédito Fiscal', 'description' => 'Comprobante para operaciones con personas jurídicas o negocios.', 'category' => 'ingresos', 'is_electronic' => false],
            ['code' => 'B02', 'name' => 'Factura de Consumo', 'description' => 'Comprobante para operaciones con consumidores finales.', 'category' => 'ingresos', 'is_electronic' => false],
            ['code' => 'B03', 'name' => 'Nota de Débito', 'description' => 'Aumenta el monto de una factura.', 'category' => 'ingresos', 'is_electronic' => false],
            ['code' => 'B04', 'name' => 'Nota de Crédito', 'description' => 'Disminuye el monto de una factura.', 'category' => 'ingresos', 'is_electronic' => false],

            // ── COMPROBANTES DE GASTOS ───────────────────────────
            ['code' => 'B11', 'name' => 'Comprobante de Compras', 'description' => 'Compras de bienes y servicios.', 'category' => 'gastos', 'is_electronic' => false],
            ['code' => 'B13', 'name' => 'Comprobante para Gastos Menores', 'description' => 'Gastos menores sin comprobante.', 'category' => 'gastos', 'is_electronic' => false],
            ['code' => 'B15', 'name' => 'Comprobante Gubernamental', 'description' => 'Comprobante de pago al Estado.', 'category' => 'gubernamental', 'is_electronic' => false],
            ['code' => 'B17', 'name' => 'Comprobante para Pagos al Exterior', 'description' => 'Pagos a proveedores del exterior.', 'category' => 'gastos', 'is_electronic' => false],

            // ── RÉGIMEN ESPECIAL ─────────────────────────────────
            ['code' => 'B14', 'name' => 'Comprobante Régimen Especial', 'description' => 'Facturación a contribuyentes de regímenes especiales.', 'category' => 'regimen', 'is_electronic' => false],
            ['code' => 'B16', 'name' => 'Comprobante Gubernamental (Rég. Especial)', 'description' => 'Pago al Estado por régimen especial.', 'category' => 'gubernamental', 'is_electronic' => false],

            // ── OTROS ────────────────────────────────────────────
            ['code' => 'B12', 'name' => 'Registro de Proveedores', 'description' => 'Registro de proveedores informales.', 'category' => 'ingresos', 'is_electronic' => false],
            ['code' => 'B19', 'name' => 'Comprobante de Importación', 'description' => 'Importaciones.', 'category' => 'especial', 'is_electronic' => false],
            ['code' => 'B20', 'name' => 'Comprobante de Exportación', 'description' => 'Exportaciones.', 'category' => 'especial', 'is_electronic' => false],
        ];

        foreach ($types as $type) {
            NcfType::updateOrCreate(['code' => $type['code']], $type);
        }
    }
}