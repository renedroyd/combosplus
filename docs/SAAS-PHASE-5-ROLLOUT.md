# Fase 5: despliegue y validación

Antes de habilitar la política en producción:

1. Ejecutar las migraciones centrales con \`php artisan migrate --force\`.
2. Ejecutar las migraciones de los tenants con \`php artisan tenants:migrate --force\` para crear la tabla \`branches\` en cada base de datos de tienda.
3. Confirmar que los slugs de los planes existentes identifican Inicial, Profesional y Empresarial/Enterprise para que la migración alinee los tres límites acordados.
4. Configurar \`SAAS_GRACE_DAYS=7\` si se desea explicitar el valor predeterminado.
5. Validar en un entorno de prueba los casos de suscripción vigente, día 7 de gracia, primer instante tras la gracia, renovación y límites de cada plan.

La política se evalúa en cada solicitud; no requiere una tarea programada de expiración. La migración de sucursales es tenant-local y debe ejecutarse para todas las tiendas existentes antes de usar la gestión de sucursales.

Después de la gracia se bloquean las escrituras comerciales, pero se mantienen la consulta del historial, la renovación, el acceso y mantenimiento del perfil personal, la gestión de direcciones y las funciones de autenticación y contacto. Las pruebas cubren los estados activo, gracia y restringido, los límites predeterminados de los tres planes y el límite de usuarios con el propietario incluido.

Las comprobaciones de cupos de productos y sucursales se serializan usando la fila central del tenant; las altas de usuarios del equipo usan una transacción central y el mismo bloqueo para evitar que solicitudes concurrentes consuman la misma plaza.
