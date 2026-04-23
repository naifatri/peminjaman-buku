<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fine_setting_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fine_setting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('old_late_fee_per_day', 10, 2)->nullable();
            $table->decimal('new_late_fee_per_day', 10, 2);
            $table->decimal('old_max_fine_amount', 10, 2)->nullable();
            $table->decimal('new_max_fine_amount', 10, 2)->nullable();
            $table->unsignedInteger('old_grace_period_days')->nullable();
            $table->unsignedInteger('new_grace_period_days');
            $table->unsignedInteger('old_default_loan_duration_days')->nullable();
            $table->unsignedInteger('new_default_loan_duration_days');
            $table->timestamp('changed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fine_setting_histories');
    }
};
