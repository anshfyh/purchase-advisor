# Smart Student Purchase Advisor

Sistem pendukung keputusan pembelian barang mahasiswa berbasis **Laravel 12**. Proyek ini merupakan konversi sederhana dari program Python Fuzzy Tsukamoto: tiga variabel input, 27 rule, perhitungan alpha dan nilai `z`, lalu weighted average untuk skor kelayakan.

## Fitur yang Memenuhi Ketentuan

| Ketentuan Advanced Web Programming | Implementasi |
| --- | --- |
| Framework bebas | Laravel 12 / PHP 8.2+ |
| Autentikasi | Register, login, logout berbasis session |
| Minimal dua role | `user` dan `admin` dengan middleware akses |
| Minimal satu resource API | `GET /api/fuzzy` dan `POST /api/fuzzy/calculate` |
| Minimal satu environment AJAX | Form analisis user dikirim menggunakan Fetch API tanpa reload |
| Tema bebas | Keputusan pembelian finansial mahasiswa |

Rekomendasi pada versi sederhana ini dihasilkan secara naratif dari skor fuzzy agar demo tidak memerlukan API key eksternal. Endpoint API JSON sudah memenuhi komponen integrasi API mata kuliah dan mudah dikembangkan untuk AI API eksternal.

## Menjalankan Aplikasi

```bash
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Buka `http://127.0.0.1:8000`.

Database bawaan menggunakan SQLite. Pada Laragon, aktifkan baris `extension=pdo_sqlite` dan `extension=sqlite3` di `php.ini`. Jika belum ingin mengubah konfigurasi PHP global, jalankan perintah artisan dengan ekstensi sementara:

```bash
php -d extension=pdo_sqlite -d extension=sqlite3 artisan migrate:fresh --seed
php -d extension=pdo_sqlite -d extension=sqlite3 artisan serve
```

## Akun Demo

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@advisor.test` | `password` |
| User | `student@advisor.test` | `password` |

Pendaftaran dari halaman register selalu menghasilkan akun `user`. Akun admin dibuat menggunakan seeder agar role tidak dapat dipilih oleh pengunjung.

## Alur Pengguna

1. User login atau register.
2. User memasukkan nama barang, nominal uang, harga, kebutuhan, dan sisa hari.
3. Form mengirim request AJAX `POST /analyses`.
4. Service `FuzzyTsukamotoService` menghitung skor dan rekomendasi.
5. Hasil muncul langsung dan tersimpan ke history pribadi.
6. Admin dapat memonitor jumlah user, skor rata-rata, kategori, dan analisis terbaru.

## API Resource

Lihat definisi variabel dan seluruh rule:

```http
GET /api/fuzzy
```

Hitung preview tanpa login dan tanpa menyimpan history:

```http
POST /api/fuzzy/calculate
Content-Type: application/json

{
  "item_name": "Charger Laptop",
  "monthly_allowance": 1500000,
  "current_money": 1200000,
  "item_price": 250000,
  "need_level": 9,
  "days_until_allowance": 23
}
```

## Struktur Penting

- `app/Services/FuzzyTsukamotoService.php`: seluruh fuzzifikasi, rule, inferensi, dan defuzzifikasi.
- `app/Http/Controllers/AnalysisController.php`: request AJAX user dan penyimpanan history.
- `app/Http/Controllers/Api/FuzzyResourceController.php`: resource API publik.
- `app/Http/Middleware/AdminMiddleware.php`: pemisahan akses role.
- `resources/views/dashboard.blade.php`: interaksi AJAX dan tampilan hasil.

## Verifikasi

```bash
php -d extension=pdo_sqlite -d extension=sqlite3 vendor/bin/phpunit
```
