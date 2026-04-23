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
            $table->integer('quantity')->default(1)->after('book_id');
            $table->text('borrow_reason')->nullable()->after('quantity');
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->decimal('damage_amount', 10, 2)->default(0)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'borrow_reason']);
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->dropColumn('damage_amount');
        });
    }
};
