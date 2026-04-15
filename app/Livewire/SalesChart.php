<?php

namespace App\Livewire;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesChart extends ChartWidget
{
    protected ?string $heading = 'Ventas diarias (últimos 7 días)';

    protected function getData(): array
    {
        // Obtener ventas por día de los últimos 7 días
        $data = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Rellenar días sin ventas con 0
        $dates = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->toDateString());
        $values = $dates->map(fn($date) => $data[$date] ?? 0)->toArray();
        
        return [
            'datasets' => [
                [
                    'label' => 'Ventas ($)',
                    'data' => $values,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $dates->map(fn($date) => \Carbon\Carbon::parse($date)->format('d/m'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
