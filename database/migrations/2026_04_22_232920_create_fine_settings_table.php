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
        Schema::create('fine_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('late_fee_per_day', 10, 2)->default(5000);
            $table->timestamps();
        });

        // Insert default setting
        DB::table('fine_settings')->insert([
            'late_fee_per_day' => 5000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fine_settings');
    }
};
