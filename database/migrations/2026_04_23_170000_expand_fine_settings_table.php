<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fine_settings', function (Blueprint $table) {
            $table->decimal('max_fine_amount', 10, 2)->nullable()->after('late_fee_per_day');
            $table->unsignedInteger('grace_period_days')->default(0)->after('max_fine_amount');
            $table->unsignedInteger('default_loan_duration_days')->default(7)->after('grace_period_days');
            $table->foreignId('updated_by')->nullable()->after('default_loan_duration_days')->constrained('users')->nullOnDelete();
        });

        DB::table('fine_settings')->update([
            'max_fine_amount' => 50000,
            'grace_period_days' => 0,
            'default_loan_duration_days' => 7,
        ]);
    }

    public function down(): void
    {
        Schema::table('fine_settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('updated_by');
            $table->dropColumn([
                'max_fine_amount',
                'grace_period_days',
                'default_loan_duration_days',
            ]);
        });
    }
};
