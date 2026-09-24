<?php

namespace App\Services;

use App\Models\Remesa;
use DomainException;

class RemittancePaymentService
{
    public function submit(Remesa $remesa): Remesa
    {
        if ($remesa->estado !== 'pendiente') {
            throw new DomainException('Esta remesa ya no está pendiente de pago.');
        }

        $remesa->forceFill([
            'estado' => 'procesando',
            'pagado_en' => null,
        ])->save();

        return $remesa->refresh();
    }
}
