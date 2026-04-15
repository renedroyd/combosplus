<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'remitente_nombre',
        'remitente_email',
        'remitente_telefono',
        'destinatario_nombre',
        'destinatario_ci',
        'destinatario_telefono',
        'destinatario_direccion',
        'municipio_id',
        'monto',
        'monto_recibir',
        'metodo_envio_id',
        'moneda_origen',
        'estado',
        'user_id',
        'comprobante_pago',
        'pagado_en',
        'entregado_en',
    ];

    protected $casts = [
        'pagado_en' => 'datetime',
        'entregado_en' => 'datetime',
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function metodoEnvio()
    {
        return $this->belongsTo(MetodoEnvio::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getEstadoColorAttribute()
    {
        return [
            'pendiente' => 'yellow',
            'pagado' => 'blue',
            'procesando' => 'purple',
            'enviado' => 'indigo',
            'entregado' => 'green',
            'cancelado' => 'red',
        ][$this->estado] ?? 'gray';
    }
}