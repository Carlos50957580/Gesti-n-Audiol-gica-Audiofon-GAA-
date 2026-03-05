<?php

namespace App\Exports;

use App\Models\PlanEstudio;
use Maatwebsite\Excel\Concerns\FromCollection;

class PlanesExport implements FromCollection
{
    protected $planes;

    public function __construct($planes)
    {
        $this->planes = $planes;
    }

    public function collection()
    {
        return $this->planes;
    }
}
