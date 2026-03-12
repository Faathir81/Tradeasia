# Tradeasia Technical Test

---

## Task 1 — Section "Our Top Products"

<img width="2159" height="1440" alt="Screenshot 2026-03-12 145557" src="https://github.com/user-attachments/assets/b6c87f3b-40cd-42bc-9f62-0f9bc1d1e96f" />


<img width="2159" height="1440" alt="Screenshot 2026-03-12 145629" src="https://github.com/user-attachments/assets/ec29a066-b425-42bd-b585-8a11d1dea284" />

---

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
