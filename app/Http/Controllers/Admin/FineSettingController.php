<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FineSetting;
use Illuminate\Http\Request;

class FineSettingController extends Controller
{
    public function index()
    {
        $setting = FineSetting::first();
        return view('admin.settings.fine', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'late_fee_per_day' => 'required|numeric|min:0',
        ]);

        $setting = FineSetting::first();
        $setting->update([
            'late_fee_per_day' => $request->late_fee_per_day,
        ]);

        return back()->with('success', 'Pengaturan denda berhasil diperbarui.');
    }
}
