<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('saas_plans')
            || ! DB::getSchemaBuilder()->hasTable('saas_plan_entitlements')) {
            return;
        }

        foreach (DB::table('saas_plans')->get(['id', 'slug', 'name']) as $plan) {
            $slug = Str::slug((string) ($plan->slug ?: $plan->name));
            $type = match (true) {
                in_array($slug, ['inicial', 'initial', 'starter', 'basic', 'basico'], true)
                    || str_contains($slug, 'inicial')
                    || str_contains($slug, 'initial')
                    || str_contains($slug, 'starter')
                    || str_contains($slug, 'basic') => 'initial',
                in_array($slug, ['profesional', 'professional', 'pro'], true)
                    || str_contains($slug, 'profesional')
                    || str_contains($slug, 'professional') => 'professional',
                in_array($slug, ['empresarial', 'enterprise', 'business', 'empresa'], true)
                    || str_contains($slug, 'empresarial')
                    || str_contains($slug, 'enterprise')
                    || str_contains($slug, 'business')
                    || str_contains($slug, 'empresa') => 'enterprise',
                default => null,
            };

            if ($type === null) {
                continue;
            }

            $limits = [
                'initial' => ['max_products' => '100', 'max_users' => '1', 'max_branches' => '1'],
                'professional' => ['max_products' => '1000', 'max_users' => '5', 'max_branches' => '2'],
                'enterprise' => ['max_products' => 'unlimited', 'max_users' => '15', 'max_branches' => '5'],
            ][$type];

            foreach ($limits as $key => $value) {
                $exists = DB::table('saas_plan_entitlements')
                    ->where('plan_id', $plan->id)
                    ->where('key', $key)
                    ->exists();

                if (! $exists) {
                    DB::table('saas_plan_entitlements')->insert([
                        'plan_id' => $plan->id,
                        'key' => $key,
                        'value' => $value,
                        'description' => match ($key) {
                            'max_products' => 'Máximo de productos guardados; unlimited significa sin límite.',
                            'max_users' => 'Usuarios del equipo activos, incluido el propietario; no cuenta clientes.',
                            'max_branches' => 'Máximo de sucursales activas.',
                        },
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Do not delete entitlement rows: platform administrators may
        // have edited them after this migration ran.
    }
};
