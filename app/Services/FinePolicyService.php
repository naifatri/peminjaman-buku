<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\FineSetting;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Schema;

class FinePolicyService
{
    public function schemaStatus(): array
    {
        $fineSettingsColumns = Schema::hasTable('fine_settings')
            ? Schema::getColumnListing('fine_settings')
            : [];

        return [
            'has_history_table' => Schema::hasTable('fine_setting_histories'),
            'has_updated_by_column' => in_array('updated_by', $fineSettingsColumns, true),
            'has_max_fine_amount_column' => in_array('max_fine_amount', $fineSettingsColumns, true),
            'has_grace_period_days_column' => in_array('grace_period_days', $fineSettingsColumns, true),
            'has_default_loan_duration_days_column' => in_array('default_loan_duration_days', $fineSettingsColumns, true),
        ];
    }

    public function currentSettings(): FineSetting
    {
        $setting = FineSetting::query()->first();

        if ($setting) {
            return $setting;
        }

        return FineSetting::create([
            'late_fee_per_day' => 5000,
            'max_fine_amount' => 50000,
            'grace_period_days' => 0,
            'default_loan_duration_days' => 7,
        ]);
    }

    public function currentPolicy(): array
    {
        $setting = $this->currentSettings();
        $schema = $this->schemaStatus();

        return [
            'late_fee_per_day' => (float) $setting->late_fee_per_day,
            'max_fine_amount' => $schema['has_max_fine_amount_column'] && $setting->max_fine_amount !== null ? (float) $setting->max_fine_amount : null,
            'grace_period_days' => $schema['has_grace_period_days_column'] ? (int) $setting->grace_period_days : 0,
            'default_loan_duration_days' => $schema['has_default_loan_duration_days_column'] ? (int) $setting->default_loan_duration_days : 7,
        ];
    }

    public function borrowingPolicy(Borrowing $borrowing): array
    {
        $current = $this->currentPolicy();

        return [
            'late_fee_per_day' => (float) ($borrowing->late_fee_per_day ?? $current['late_fee_per_day']),
            'max_fine_amount' => $borrowing->max_fine_amount !== null
                ? (float) $borrowing->max_fine_amount
                : $current['max_fine_amount'],
            'grace_period_days' => (int) ($borrowing->grace_period_days ?? $current['grace_period_days']),
            'default_loan_duration_days' => (int) ($borrowing->loan_duration_days ?? $current['default_loan_duration_days']),
        ];
    }

    public function calculateLateFee(int $rawLateDays, array $policy): array
    {
        $gracePeriodDays = max(0, (int) ($policy['grace_period_days'] ?? 0));
        $lateFeePerDay = max(0, (float) ($policy['late_fee_per_day'] ?? 0));
        $maxFineAmount = isset($policy['max_fine_amount']) && $policy['max_fine_amount'] !== null
            ? max(0, (float) $policy['max_fine_amount'])
            : null;

        $chargedLateDays = max(0, $rawLateDays - $gracePeriodDays);
        $lateFeeSubtotal = $chargedLateDays * $lateFeePerDay;
        $lateFeeTotal = $maxFineAmount !== null ? min($lateFeeSubtotal, $maxFineAmount) : $lateFeeSubtotal;
        $isCapped = $maxFineAmount !== null && $lateFeeSubtotal > $lateFeeTotal;

        return [
            'raw_late_days' => $rawLateDays,
            'grace_period_days' => $gracePeriodDays,
            'charged_late_days' => $chargedLateDays,
            'late_fee_per_day' => $lateFeePerDay,
            'late_fee_subtotal' => $lateFeeTotal,
            'late_fee_before_cap' => $lateFeeSubtotal,
            'max_fine_amount' => $maxFineAmount,
            'is_capped' => $isCapped,
        ];
    }

    public function dueDateFromBorrowDate(CarbonInterface $borrowDate, ?array $policy = null): CarbonInterface
    {
        $policy ??= $this->currentPolicy();

        return $borrowDate->copy()->addDays((int) ($policy['default_loan_duration_days'] ?? 7));
    }
}
