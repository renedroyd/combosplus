<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Models\Admin; // Asegúrate de importar el modelo Admin
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver una orden.
     *
     * @param  mixed  $user  (puede ser User o Admin)
     * @param  \App\Models\Order  $order
     * @return bool
     */
    public function view($user, Order $order)
    {
        // Si es administrador, puede ver cualquier orden
        if ($user instanceof Admin) {
            return true; // O puedes agregar lógica de roles: $user->hasRole('super-admin')
        }

        // Si es cliente, solo puede ver sus propias órdenes
        return $user->id === $order->user_id;
    }

    /**
     * Determina si el usuario puede actualizar una orden.
     *
     * @param  mixed  $user
     * @param  \App\Models\Order  $order
     * @return bool
     */
    public function update($user, Order $order)
    {
        // Similar: administradores pueden, clientes solo las suyas (si aplica)
        if ($user instanceof Admin) {
            return true;
        }

        return $user->id === $order->user_id;
    }

    // Otros métodos (delete, etc.) si los tienes, con la misma lógica
}