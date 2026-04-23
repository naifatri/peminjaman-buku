<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->string('status')->default('diajukan')->change();
            $table->text('return_notes')->nullable();
            $table->text('admin_notes')->nullable();
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->enum('payment_method', ['tunai', 'ganti_buku'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->text('return_notes')->dropColumn('return_notes');
            $table->text('admin_notes')->dropColumn('admin_notes');
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
