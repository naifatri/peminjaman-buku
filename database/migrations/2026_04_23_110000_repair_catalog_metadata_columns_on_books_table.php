<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (! Schema::hasColumn('books', 'publisher')) {
                $table->string('publisher')->nullable()->after('published_year');
            }

            if (! Schema::hasColumn('books', 'page_count')) {
                $table->unsignedSmallInteger('page_count')->nullable()->after('publisher');
            }

            if (! Schema::hasColumn('books', 'rating')) {
                $table->decimal('rating', 2, 1)->default(4.0)->after('page_count');
            }

            if (! Schema::hasColumn('books', 'genre_tags')) {
                $table->string('genre_tags')->nullable()->after('description');
            }
        });

        $catalogMetadata = [
            'Laskar Pelangi' => ['publisher' => 'Bentang Pustaka', 'page_count' => 529, 'rating' => 4.8, 'genre_tags' => 'Inspiratif, Persahabatan, Pendidikan'],
            'Bumi Manusia' => ['publisher' => 'Lentera Dipantara', 'page_count' => 535, 'rating' => 4.9, 'genre_tags' => 'Klasik, Sejarah, Drama'],
            'Negeri 5 Menara' => ['publisher' => 'Gramedia Pustaka Utama', 'page_count' => 423, 'rating' => 4.7, 'genre_tags' => 'Motivasi, Pendidikan, Petualangan'],
            'Sejarah Nasional Indonesia' => ['publisher' => 'Balai Pustaka', 'page_count' => 388, 'rating' => 4.4, 'genre_tags' => 'Nasional, Referensi, Akademik'],
            'Belajar Laravel 11 untuk Pemula' => ['publisher' => 'Informatika Nusantara', 'page_count' => 312, 'rating' => 4.6, 'genre_tags' => 'Laravel, Web Dev, Pemrograman'],
            'Fiqih Sunnah' => ['publisher' => 'Pustaka Al-Kautsar', 'page_count' => 448, 'rating' => 4.5, 'genre_tags' => 'Ibadah, Referensi, Keislaman'],
            'Habibie & Ainun' => ['publisher' => 'THC Mandiri', 'page_count' => 323, 'rating' => 4.7, 'genre_tags' => 'Biografi, Inspiratif, Romansa'],
        ];

        foreach ($catalogMetadata as $title => $metadata) {
            DB::table('books')->where('title', $title)->update($metadata);
        }
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $columnsToDrop = collect(['publisher', 'page_count', 'rating', 'genre_tags'])
                ->filter(fn ($column) => Schema::hasColumn('books', $column))
                ->all();

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
