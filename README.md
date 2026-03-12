# Tradeasia Technical Test

Technical test untuk posisi magang di Tradeasia. Project ini dibuat menggunakan Laravel 12.

---

## Cara Install

```bash
git clone https://github.com/Faathir81/Tradeasia.git
cd Tradeasia

composer install

cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi database di file `.env`:
```
DB_DATABASE=tradeasia_database
DB_USERNAME=root
DB_PASSWORD=
```

Lalu jalankan:
```bash
php artisan migrate
php artisan serve
```

Buka di browser: `http://127.0.0.1:8000`

---

## Task 1 — Section "Our Top Products"

Bikin section carousel produk di halaman utama yang datanya diambil dari tabel `product` dan `product_lang`.

**Yang ada di section ini:**
- Judul "Our Top Products"
- 4 card produk per baris di desktop (2 di tablet, 1 di mobile)
- Setiap card ada: foto produk, nama produk, CAS Number, HS Code, tombol "Inquire Now"
- Tombol panah kiri/kanan untuk geser carousel
- Dots indicator di bawah

**Pendekatan yang dipake:**

Data produk disimpan di 2 tabel yang direlasikan:
- `product` → nyimpan foto dan status produk
- `product_lang` → nyimpan nama, CAS Number, HS Code per bahasa. Kita ambil yang `language_id = 1` (Inggris)

Di controller (`HomeController.php`), data diambil pake eager loading biar tidak N+1 query:
```php
$products = Product::with(['englishLang'])
    ->where('publish', 1)
    ->whereNull('deleted_at')
    ->get();
```

Carousel-nya dibuat manual pake CSS `translateX` dan vanilla JS, bukan pake library. Foto produk prefix URLnya dari `https://cdn.chemtradeasia.com`.

**File yang dibuat/diubah:**
- `app/Models/Product.php`
- `app/Models/ProductLang.php`
- `app/Http/Controllers/HomeController.php`
- `routes/web.php`
- `resources/views/welcome.blade.php`
- `public/css/top-products.css`

---

## Task 3 — Modal Inquire Now + Passing product_id

Saat tombol "Inquire Now" di card produk diklik, muncul modal login yang isinya pilihan sign in (Google, Microsoft, Apple, LinkedIn, Facebook).

**Yang penting di task ini:** `product_id` dari produk yang diklik harus ikut "terkirim" ke dalam modal, tanpa bikin route baru dan tanpa page refresh.

**Cara kerjanya:**

Tiap tombol "Inquire Now" nyimpen `product_id` di atribut `data-product-id`:
```html
<button class="inquire-now-btn" data-product-id="{{ $product->id }}">
    Inquire Now
</button>
```

Pas diklik, JavaScript baca nilai itu terus set ke hidden input di dalam modal:
```js
const productId = this.getAttribute('data-product-id');
document.getElementById('inquireProductId').value = productId;
```

Modal muncul/hilang cukup pake toggle CSS class, jadi tidak ada redirect atau reload sama sekali.

Modal bisa ditutup dengan: klik tombol ×, klik area di luar modal, atau tekan Escape.

**File yang diubah:**
- `resources/views/welcome.blade.php` — tambahin modal HTML + JS
- `public/css/top-products.css` — tambahin style modal
