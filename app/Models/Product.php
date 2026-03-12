<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';

    public function productLangs()
    {
        return $this->hasMany(ProductLang::class, 'product_id');
    }

    public function englishLang()
    {
        return $this->hasOne(ProductLang::class, 'product_id')->where('language_id', 1);
    }
}
