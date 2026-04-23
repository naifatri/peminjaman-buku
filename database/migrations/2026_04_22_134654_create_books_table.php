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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->unique();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->integer('published_year');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('status', ['tersedia', 'dipinjam', 'habis'])->default('tersedia');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
