<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->text('description')->nullable()->after('name');
            $table->decimal('price', 10, 2)->default(0)->after('description');
            $table->string('image')->nullable()->after('price');
            $table->boolean('is_active')->default(true)->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'price', 'image', 'is_active']);
        });
    }
};
