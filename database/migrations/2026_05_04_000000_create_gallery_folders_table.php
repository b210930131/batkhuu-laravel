<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();

            $table->unique(['user_id', 'name']);
        });

        Schema::table('generated_images', function (Blueprint $table) {
            $table->foreignId('gallery_folder_id')->nullable()->after('user_id')->constrained('gallery_folders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('generated_images', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gallery_folder_id');
        });

        Schema::dropIfExists('gallery_folders');
    }
};
