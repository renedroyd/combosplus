<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StoreStat extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected int|array|null $columns = 3;

    protected function getStats(): array
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'completed')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalRevenue = (float) Order::where('status', 'completed')->sum('total');
        $totalCustomers = User::count();
        $totalSold = (int) DB::table('order_items')->sum('quantity');

        return [
            Stat::make('Ventas', '$' . number_format($totalRevenue, 2))
                ->description($completedOrders . ' pedidos completados')
                ->descriptionIcon('heroicon-m-check-circle')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Pedidos', $totalOrders)
                ->description($pendingOrders . ' pendientes')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-shopping-cart')
                ->color($pendingOrders > 0 ? 'warning' : 'success'),

            Stat::make('Clientes', $totalCustomers)
                ->description('Clientes registrados')
                ->descriptionIcon('heroicon-m-users')
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make('Productos', $totalProducts)
                ->description('Productos en catálogo')
                ->descriptionIcon('heroicon-m-archive-box')
                ->icon('heroicon-o-archive-box')
                ->color('primary'),

            Stat::make('Unidades vendidas', $totalSold)
                ->description('Unidades procesadas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->icon('heroicon-o-chart-bar')
                ->color('success'),

            Stat::make('Atención', $pendingOrders)
                ->description($pendingOrders === 1 ? 'pedido requiere atención' : 'pedidos requieren atención')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->icon('heroicon-o-bell-alert')
                ->color($pendingOrders > 0 ? 'danger' : 'success'),
        ];
    }
}
