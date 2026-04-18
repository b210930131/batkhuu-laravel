<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('generated_images', function (Blueprint $table) {
        // This allows the column to be empty during the initial insert
        $table->string('file_name')->nullable()->change();
        
        // Also add prompt_id if you haven't, so we can find this record later
        if (!Schema::hasColumn('generated_images', 'prompt_id')) {
            $table->string('prompt_id')->nullable()->after('id');
        }
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
