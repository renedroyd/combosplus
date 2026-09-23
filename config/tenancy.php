<?php

use Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper;
use Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper;
use Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

return [
    'tenant_model' => App\Models\Tenant::class,
    'domain_model' => Stancl\Tenancy\Database\Models\Domain::class,

    'central_domains' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('TENANCY_CENTRAL_DOMAINS', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost'))
    ))),

    'bootstrappers' => [
        DatabaseTenancyBootstrapper::class,
        CacheTenancyBootstrapper::class,
        FilesystemTenancyBootstrapper::class,
    ],

    'database' => [
        'central_connection' => env('DB_CONNECTION', 'sqlite'),
        'template_tenant_connection' => env('TENANCY_TEMPLATE_CONNECTION'),
        'prefix' => env('TENANCY_DB_PREFIX', 'tenant_'),
        'suffix' => env('TENANCY_DB_SUFFIX', ''),
        'managers' => [
            'mysql' => Stancl\Tenancy\Database\Managers\MySQLDatabaseManager::class,
            'pgsql' => Stancl\Tenancy\Database\Managers\PostgreSQLDatabaseManager::class,
            'sqlite' => Stancl\Tenancy\Database\Managers\SQLiteDatabaseManager::class,
        ],
    ],

    'migration_parameters' => [
        '--force' => true,
        '--path' => [database_path('migrations/tenant')],
    ],

    'seeder_parameters' => [
        '--class' => 'Database\\Seeders\\DatabaseSeeder',
    ],

    'features' => [],

    'routes' => [
        'middleware' => [
            'web',
            InitializeTenancyByDomain::class,
        ],
    ],
];
