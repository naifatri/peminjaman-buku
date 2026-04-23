<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFineSettingRequest;
use App\Models\ActivityLog;
use App\Models\FineSetting;
use App\Models\FineSettingHistory;
use App\Services\FinePolicyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FineSettingController extends Controller
{
    public function __construct(
        private readonly FinePolicyService $finePolicyService
    ) {
    }

    public function index()
    {
        $setting = $this->finePolicyService->currentSettings();
        $schemaStatus = $this->finePolicyService->schemaStatus();

        if ($schemaStatus['has_history_table']) {
            $setting->load('histories.admin');
        } else {
            $setting->setRelation('histories', collect());
        }

        if ($schemaStatus['has_updated_by_column']) {
            $setting->load('updatedBy');
        }

        $previewDays = [1, 2, 3, 7, 14, 30];
        $previewRows = collect($previewDays)->map(function (int $day) use ($setting) {
            $preview = $this->finePolicyService->calculateLateFee($day, [
                'late_fee_per_day' => (float) $setting->late_fee_per_day,
                'max_fine_amount' => $setting->max_fine_amount !== null ? (float) $setting->max_fine_amount : null,
                'grace_period_days' => (int) $setting->grace_period_days,
            ]);

            return [
                'late_days' => $day,
                'charged_days' => $preview['charged_late_days'],
                'total' => $preview['late_fee_subtotal'],
            ];
        });

        $needsMigration = in_array(false, $schemaStatus, true);

        return view('admin.settings.fine', compact('setting', 'previewRows', 'needsMigration'));
    }

    public function update(UpdateFineSettingRequest $request)
    {
        if (! Schema::hasTable('fine_setting_histories')) {
            return back()->with('error', 'Database belum memakai schema terbaru. Jalankan migrate terlebih dahulu.');
        }

        $data = $request->validated();
        $setting = $this->finePolicyService->currentSettings();
        $before = $setting->only([
            'late_fee_per_day',
            'max_fine_amount',
            'grace_period_days',
            'default_loan_duration_days',
        ]);

        DB::transaction(function () use ($setting, $data, $before) {
            $setting->update([
                'late_fee_per_day' => $data['late_fee_per_day'],
                'max_fine_amount' => $data['max_fine_amount'] ?? null,
                'grace_period_days' => $data['grace_period_days'],
                'default_loan_duration_days' => $data['default_loan_duration_days'],
                'updated_by' => auth()->id(),
            ]);

            FineSettingHistory::create([
                'fine_setting_id' => $setting->id,
                'changed_by' => auth()->id(),
                'old_late_fee_per_day' => $before['late_fee_per_day'],
                'new_late_fee_per_day' => $setting->late_fee_per_day,
                'old_max_fine_amount' => $before['max_fine_amount'],
                'new_max_fine_amount' => $setting->max_fine_amount,
                'old_grace_period_days' => $before['grace_period_days'],
                'new_grace_period_days' => $setting->grace_period_days,
                'old_default_loan_duration_days' => $before['default_loan_duration_days'],
                'new_default_loan_duration_days' => $setting->default_loan_duration_days,
                'changed_at' => now(),
            ]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Update Pengaturan Denda',
                'description' => "Admin memperbarui tarif denda dari Rp " . number_format((float) $before['late_fee_per_day'], 0, ',', '.')
                    . " menjadi Rp " . number_format((float) $setting->late_fee_per_day, 0, ',', '.'),
            ]);
        });

        return back()->with('success', 'Pengaturan denda berhasil diperbarui.');
    }
}
