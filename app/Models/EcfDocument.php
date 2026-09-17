<?php
// app/Models/EcfDocument.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcfDocument extends Model
{
    protected $fillable = [
        'documentable_type', 'documentable_id',
        'tipo_ecf', 'encf', 'track_id', 'estado',
        'qr_link', 'pdf_cloud_url',
        'payload_enviado', 'respuesta_completa',
        'error_code', 'error_message',
        'ecf_sequence_id', 'user_id',
        'intentos', 'enviado_at', 'respondido_at',
    ];

    protected $casts = [
        'payload_enviado' => 'array',
        'respuesta_completa' => 'array',
        'enviado_at' => 'datetime',
        'respondido_at' => 'datetime',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function ecfSequence()
    {
        return $this->belongsTo(EcfSequence::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}