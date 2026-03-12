<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLang extends Model
{
    protected $table = 'product_lang';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
