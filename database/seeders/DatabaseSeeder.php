<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. SEED KATEGORI (Nama Indonesia)
        $categories = [
            ['name' => 'Fiksi', 'slug' => 'fiksi', 'description' => 'Koleksi novel dan cerita fiksi'],
            ['name' => 'Sejarah', 'slug' => 'sejarah', 'description' => 'Buku sejarah Indonesia dan Dunia'],
            ['name' => 'Teknologi', 'slug' => 'teknologi', 'description' => 'Pemrograman, AI, dan Gadget'],
            ['name' => 'Agama', 'slug' => 'agama', 'description' => 'Buku spiritual dan tuntunan agama'],
            ['name' => 'Sains', 'slug' => 'sains', 'description' => 'Ilmu pengetahuan alam dan biologi'],
            ['name' => 'Biografi', 'slug' => 'biografi', 'description' => 'Kisah hidup tokoh inspiratif'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // 2. SEED USERS (Nama Indonesia)
        // Admin
        User::create([
            'name' => 'Admin SIPBUK',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nisn' => '0000000001',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 1, Jakarta',
        ]);

        // Peminjam
        $peminjams = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
                'nisn' => '1000000001',
                'phone' => '082133445566',
                'address' => 'Jl. Mawar No. 12, Bandung',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
                'nisn' => '1000000002',
                'phone' => '085711223344',
                'address' => 'Griya Asri Blok C, Surabaya',
            ],
            [
                'name' => 'Rizky Pratama',
                'email' => 'rizky@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
                'nisn' => '1000000003',
                'phone' => '081988776655',
                'address' => 'Kos Hijau, Yogyakarta',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
                'nisn' => '1000000004',
                'phone' => '081299001122',
                'address' => 'Perum Permai, Semarang',
            ],
            [
                'name' => 'Agus Setiawan',
                'email' => 'agus@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
                'nisn' => '1000000005',
                'phone' => '087755443322',
                'address' => 'Jl. Kenanga No. 5, Malang',
            ],
        ];

        foreach ($peminjams as $user) {
            User::create($user);
        }

        // 3. SEED BUKU (Judul & Penulis Indonesia)
        $books = [
            // Fiksi (ID 1)
            [
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'isbn' => '9789793062791',
                'category_id' => 1,
                'stock' => 5,
                'published_year' => 2005,
                'publisher' => 'Bentang Pustaka',
                'page_count' => 529,
                'rating' => 4.8,
                'description' => 'Kisah perjuangan anak-anak di Belitung.',
                'genre_tags' => 'Inspiratif, Persahabatan, Pendidikan',
                'status' => 'tersedia',
            ],
            [
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'isbn' => '9789799731234',
                'category_id' => 1,
                'stock' => 3,
                'published_year' => 1980,
                'publisher' => 'Lentera Dipantara',
                'page_count' => 535,
                'rating' => 4.9,
                'description' => 'Karya masterpiece sastra Indonesia era kolonial.',
                'genre_tags' => 'Klasik, Sejarah, Drama',
                'status' => 'tersedia',
            ],
            [
                'title' => 'Negeri 5 Menara',
                'author' => 'A. Fuadi',
                'isbn' => '9789792257458',
                'category_id' => 1,
                'stock' => 7,
                'published_year' => 2009,
                'publisher' => 'Gramedia Pustaka Utama',
                'page_count' => 423,
                'rating' => 4.7,
                'description' => 'Man Jadda Wajada, siapa bersungguh-sungguh akan berhasil.',
                'genre_tags' => 'Motivasi, Pendidikan, Petualangan',
                'status' => 'tersedia',
            ],
            // Sejarah (ID 2)
            [
                'title' => 'Sejarah Nasional Indonesia',
                'author' => 'Sartono Kartodirdjo',
                'isbn' => '9789794615456',
                'category_id' => 2,
                'stock' => 2,
                'published_year' => 2010,
                'publisher' => 'Balai Pustaka',
                'page_count' => 388,
                'rating' => 4.4,
                'description' => 'Buku standar sejarah resmi Indonesia.',
                'genre_tags' => 'Nasional, Referensi, Akademik',
                'status' => 'tersedia',
            ],
            // Teknologi (ID 3)
            [
                'title' => 'Belajar Laravel 11 untuk Pemula',
                'author' => 'Eko Kurniawan Khannedy',
                'isbn' => '9786020444555',
                'category_id' => 3,
                'stock' => 10,
                'published_year' => 2024,
                'publisher' => 'Informatika Nusantara',
                'page_count' => 312,
                'rating' => 4.6,
                'description' => 'Panduan lengkap membangun web dengan Laravel terbaru.',
                'genre_tags' => 'Laravel, Web Dev, Pemrograman',
                'status' => 'tersedia',
            ],
            // Agama (ID 4)
            [
                'title' => 'Fiqih Sunnah',
                'author' => 'Sayyid Sabiq',
                'isbn' => '9786022501234',
                'category_id' => 4,
                'stock' => 4,
                'published_year' => 2015,
                'publisher' => 'Pustaka Al-Kautsar',
                'page_count' => 448,
                'rating' => 4.5,
                'description' => 'Tuntunan ibadah sesuai sunnah Rasulullah.',
                'genre_tags' => 'Ibadah, Referensi, Keislaman',
                'status' => 'tersedia',
            ],
            // Biografi (ID 6)
            [
                'title' => 'Habibie & Ainun',
                'author' => 'B.J. Habibie',
                'isbn' => '9789791227000',
                'category_id' => 6,
                'stock' => 6,
                'published_year' => 2010,
                'publisher' => 'THC Mandiri',
                'page_count' => 323,
                'rating' => 4.7,
                'description' => 'Kisah cinta abadi Presiden ke-3 RI.',
                'genre_tags' => 'Biografi, Inspiratif, Romansa',
                'status' => 'tersedia',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
