# Tradeasia Technical Test

---

## Task 1 — Section "Our Top Products"

<img width="2159" height="1440" alt="Screenshot 2026-03-12 145557" src="https://github.com/user-attachments/assets/b6c87f3b-40cd-42bc-9f62-0f9bc1d1e96f" />


<img width="2159" height="1440" alt="Screenshot 2026-03-12 145629" src="https://github.com/user-attachments/assets/ec29a066-b425-42bd-b585-8a11d1dea284" />

**File terkait:**
- File Blade / HTML section: [resources/views/welcome.blade.php](resources/views/welcome.blade.php)
- File CSS terkait: [public/css/top-products.css](public/css/top-products.css)



## Task 2 — Optimasi Query Listing Produk

### Penyebab bottleneck

Query original lambat karena beberapa hal:

1. **5 tabel di-JOIN sekaligus** — `product_domain_category`, `product`, `prodind`, `category`, `product_lang`, dan `product_seo` digabung dalam satu query. Semakin banyak JOIN, semakin besar data sementara yang harus diproses MySQL sebelum bisa difilter.

2. **DISTINCT di hasil JOIN yang besar** — `DISTINCT` dipaksa jalan setelah semua tabel sudah digabung, jadi MySQL harus deduplikasi dari ribuan baris hasil JOIN, bukan dari tabel yang kecil.

3. **`product_seo` di-JOIN padahal cuma dipakai untuk WHERE** — tabel ini tidak ada di `SELECT`, tapi tetap di-JOIN dan ikut memperbesar hasil sementara.

### Query yang sudah dioptimasi

Dipecah jadi 2 query. Query pertama ambil `product_id` yang memenuhi semua syarat, query kedua ambil dari `product_lang` pake `whereIn`. Dengan begitu `DISTINCT` dan `orderBy` cuma jalan di `product_lang` saja, bukan di hasil gabungan 5 tabel.

```php
// ambil product_id yang lolos semua filter
$productIds = DB::table('product_domain_category')
    ->join('product', 'product_domain_category.product_id', '=', 'product.id')
    ->join('prodind', 'product_domain_category.prodind_id', '=', 'prodind.id')
    ->join('category', 'product_domain_category.category_id', '=', 'category.id')
    ->join('product_seo', 'product_domain_category.product_id', '=', 'product_seo.product_id')
    ->where('product.publish', 1)
    ->where('category.publish', 1)
    ->where('prodind.publish', 1)
    ->where('product_domain_category.domain_id', $domain_id)
    ->where('product_seo.domain_id', $domain_id)
    ->where('product_seo.language_id', $language)
    ->pluck('product_domain_category.product_id');

//ambil nama produk dari product_lang berdasarkan product_id di atas
$products = DB::table('product_lang')
    ->whereIn('product_id', $productIds)
    ->where('language_id', $language)
    ->select('productname', 'product_id')
    ->distinct()
    ->orderBy('productname')
    ->get();
```

---

## Task 3 — Modal Inquire Now + Passing product_id

<img width="2159" height="1440" alt="task 3" src="https://github.com/user-attachments/assets/1ac657aa-c16d-4059-afc3-595ca0eb6960" />

---

# Task 4 — Rekomendasi Optimasi Website

Berdasarkan hasil analisis asset statis dan PageSpeed Insights, performa website masih dapat ditingkatkan terutama pada perangkat mobile. Hal ini kemungkinan disebabkan oleh jumlah gambar yang cukup banyak, ukuran asset yang cukup besar, serta adanya JavaScript yang mempengaruhi proses rendering halaman.

### Prioritas Tinggi
Melakukan optimasi gambar (kompresi atau format WebP) serta menerapkan lazy loading agar tidak semua gambar dimuat saat halaman pertama kali dibuka.

### Prioritas Menengah
Mengurangi render blocking JavaScript dengan menggunakan atribut `defer` atau `async`, serta melakukan minify pada file CSS dan JavaScript.

### Prioritas Rendah
Mengimplementasikan caching pada server atau menggunakan CDN untuk meningkatkan kecepatan distribusi asset statis.

# Task 5 — Halaman Tanpa Metadata

## Risiko SEO jika metadata tidak tersedia

Jika halaman tidak memiliki metadata seperti meta title atau meta description, mesin pencari seperti Google akan lebih sulit memahami isi halaman tersebut. Hal ini bisa membuat halaman kurang optimal di hasil pencarian dan berpotensi menurunkan ranking. Selain itu, tampilan hasil pencarian juga bisa menjadi kurang jelas karena Google harus membuat deskripsi otomatis dari isi halaman.

--- 

## Rekomendasi fallback metadata

Sejujurnya saya belum pernah menangani kasus metadata fallback secara langsung. Tapi dari yang saya pahami, metadata penting untuk SEO karena membantu search engine memahami isi halaman. Jika metadata tidak tersedia, biasanya bisa dibuat fallback seperti menggunakan title halaman atau deskripsi default dari template.