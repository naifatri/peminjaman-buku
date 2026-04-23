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
        Schema::table('borrowings', function (Blueprint $table) {
            $table->enum('book_condition', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])->nullable()->after('return_notes');
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->change(); // Change to string for flexibility
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn('book_condition');
        });
    }
};
