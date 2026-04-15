<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Admin::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('Versat*19'),
        ]);

        Category::factory()->create([
            'name' => 'Granos',
            'slug' => 'granos',
            'description' => 'Productos a base de granos como arroz, frijoles, lentejas, etc.',
        ]);

        Category::factory()->create([
            'name' => 'Carnicos',
            'slug' => 'carnicos',
            'description' => 'Productos de origen animal como carne, pollo, pescado, etc.',
        ]);

        Category::factory()->create([
            'name' => 'Lacteos',
            'slug' => 'lacteos',
            'description' => 'Productos derivados de la leche como leche, queso, yogur, etc.',
        ]);

        Category::factory()->create([
            'name' => 'Refrescos',
            'slug' => 'refrescos',
            'description' => 'Bebidas carbonatadas y jugos naturales.',
        ]);

        Category::factory()->create([
            'name' => 'Bebidas Alcoholicas',
            'slug' => 'bebidas-alcoholicas',
            'description' => 'Bebidas con contenido de alcohol como cerveza, vino, licores, etc.',
        ]);

        Category::factory()->create([
            'name' => 'Dulces',
            'slug' => 'dulces',
            'description' => 'Productos azucarados como chocolates, caramelos, galletas, etc.',
        ]);
    
        Category::factory()->create([
            'name' => 'Snacks',
            'slug' => 'snacks',
            'description' => 'Aperitivos salados como papas fritas, frutos secos, etc.',
        ]);

        PaymentMethod::factory()->create([
            'name' => 'Zelle',
            'code' => 'transfer',
            'description' => 'Método de pago a través de la plataforma Zelle.',
            'is_active' => true,
        ]);
    }
}
