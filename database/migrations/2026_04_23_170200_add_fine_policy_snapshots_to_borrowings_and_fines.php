<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->decimal('late_fee_per_day', 10, 2)->default(5000)->after('fine_amount');
            $table->decimal('max_fine_amount', 10, 2)->nullable()->after('late_fee_per_day');
            $table->unsignedInteger('grace_period_days')->default(0)->after('max_fine_amount');
            $table->unsignedInteger('loan_duration_days')->default(7)->after('grace_period_days');
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->decimal('late_fee_per_day', 10, 2)->default(5000)->after('days_late');
            $table->decimal('max_fine_amount', 10, 2)->nullable()->after('late_fee_per_day');
            $table->unsignedInteger('grace_period_days')->default(0)->after('max_fine_amount');
            $table->unsignedInteger('raw_late_days')->default(0)->after('grace_period_days');
            $table->unsignedInteger('charged_late_days')->default(0)->after('raw_late_days');
            $table->decimal('late_fee_subtotal', 10, 2)->default(0)->after('charged_late_days');
        });

        $setting = DB::table('fine_settings')->first();

        $lateFeePerDay = $setting->late_fee_per_day ?? 5000;
        $maxFineAmount = $setting->max_fine_amount ?? 50000;
        $gracePeriodDays = $setting->grace_period_days ?? 0;
        $loanDurationDays = $setting->default_loan_duration_days ?? 7;

        DB::table('borrowings')->update([
            'late_fee_per_day' => $lateFeePerDay,
            'max_fine_amount' => $maxFineAmount,
            'grace_period_days' => $gracePeriodDays,
            'loan_duration_days' => $loanDurationDays,
        ]);

        $fines = DB::table('fines')->get();

        foreach ($fines as $fine) {
            $damageAmount = property_exists($fine, 'damage_amount') ? (float) $fine->damage_amount : 0.0;
            $lateFeeAmount = max(0, (float) $fine->amount - $damageAmount);
            $rawLateDays = (int) $fine->days_late;
            $chargedLateDays = max(0, $rawLateDays - $gracePeriodDays);
            $effectiveLateFeePerDay = $chargedLateDays > 0
                ? $lateFeeAmount / $chargedLateDays
                : $lateFeePerDay;

            DB::table('fines')
                ->where('id', $fine->id)
                ->update([
                    'late_fee_per_day' => $effectiveLateFeePerDay,
                    'max_fine_amount' => $maxFineAmount,
                    'grace_period_days' => $gracePeriodDays,
                    'raw_late_days' => $rawLateDays,
                    'charged_late_days' => $chargedLateDays,
                    'late_fee_subtotal' => $lateFeeAmount,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            $table->dropColumn([
                'late_fee_per_day',
                'max_fine_amount',
                'grace_period_days',
                'raw_late_days',
                'charged_late_days',
                'late_fee_subtotal',
            ]);
        });

        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn([
                'late_fee_per_day',
                'max_fine_amount',
                'grace_period_days',
                'loan_duration_days',
            ]);
        });
    }
};
