<?php

namespace App\Http\Controllers;

use App\Product;
use App\Shop;
use Illuminate\Http\Request;

class apiController extends Controller
{
    public function getProducts(){
        return Product::with('soldBy')->get()->groupBy('name');
    }

    public function getShops(){
        return Shop::with('products')->get();
    }
}
