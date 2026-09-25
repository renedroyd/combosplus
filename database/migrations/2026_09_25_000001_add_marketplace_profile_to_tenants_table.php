<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->string('slug')->nullable()->unique()->after('name');
            $table->text('description')->nullable()->after('slug');
            $table->string('logo')->nullable()->after('description');
            $table->string('status', 32)->default('active')->index()->after('logo');
            $table->decimal('rating', 3, 2)->nullable()->after('status');
            $table->unsignedInteger('review_count')->default(0)->after('rating');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropIndex(['status']);
            $table->dropColumn(['name', 'slug', 'description', 'logo', 'status', 'rating', 'review_count']);
        });
    }
};
