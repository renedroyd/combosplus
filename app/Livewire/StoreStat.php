<?php

namespace App\Livewire;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StoreStat extends StatsOverviewWidget
{
    protected static bool $isLazy = false; 
    
    protected function getStats(): array
    {
        $orders = Order::get()->count();
        $pending_orders = Order::where('status','=', 'pending')->count();

                // Total de ingresos (pedidos pagados/completados)
        $totalRevenue = Order::where('status', 'completed')->sum('total');
        
        // Pedidos pendientes (por ejemplo, con estado 'pending')
        $pendingOrders = Order::where('status', 'pending')->count();
        
        // Productos más vendidos (opcional, se puede hacer con una relación)
        // Podrías mostrar el producto estrella o simplemente el total de productos vendidos
        $totalSold = DB::table('order_items')->sum('quantity');

        return [
            Stat::make('Total Productos', Product::count())
                ->icon('heroicon-o-shopping-bag')
                ->color('success'),

            Stat::make('Pedidos Totales', Order::count())
                ->icon('heroicon-o-shopping-cart')
                ->color('primary'),

            Stat::make('Ingresos Totales', '$' . number_format($totalRevenue, 2))
                ->icon('heroicon-o-currency-dollar')
                ->color('warning'),
            Stat::make('Usuarios Registrados', User::count())
                ->icon('heroicon-o-users')
                ->color('info'),
            Stat::make('Pedidos Pendientes', $pendingOrders)
                ->icon('heroicon-o-clock')
                ->color('danger'),
            // Puedes añadir más tarjetas, como productos vendidos
            Stat::make('Unidades Vendidas', $totalSold)
                ->icon('heroicon-o-arrow-trending-up')
                ->color('secondary'),
        ];
    }
}
