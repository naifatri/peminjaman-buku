<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'nisn')) {
            return;
        }

        DB::table('users')
            ->select('id')
            ->whereNull('nisn')
            ->orWhere('nisn', '')
            ->orderBy('id')
            ->lazy()
            ->each(function ($user) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'nisn' => '9' . str_pad((string) $user->id, 9, '0', STR_PAD_LEFT),
                    ]);
            });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'nisn')) {
            return;
        }

        DB::table('users')
            ->where('nisn', 'like', '9%')
            ->whereRaw('CHAR_LENGTH(nisn) = 10')
            ->update([
                'nisn' => null,
            ]);
    }
};
