<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with(['englishLang'])
            ->where('publish', 1)
            ->whereNull('deleted_at')
            ->get();

        return view('welcome', compact('products'));
    }
}
