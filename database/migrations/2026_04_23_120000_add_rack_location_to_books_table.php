<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('books', 'rack_location')) {
            Schema::table('books', function (Blueprint $table) {
                $table->string('rack_location', 20)->nullable()->after('isbn');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('books', 'rack_location')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('rack_location');
            });
        }
    }
};
