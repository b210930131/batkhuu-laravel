<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generated_images', function (Blueprint $table) {
            $table->longText('original_prompt')->nullable()->after('prompt_id');
            $table->longText('canonical_prompt')->nullable()->after('original_prompt');
        });
    }

    public function down(): void
    {
        Schema::table('generated_images', function (Blueprint $table) {
            $table->dropColumn(['original_prompt', 'canonical_prompt']);
        });
    }
};