<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function owners(){
        return $this->belongsToMany(User::class, 'user_product_rel');
    }

    public function soldBy(){
        return $this->belongsToMany(Shop::class, 'shop_product_rel');
    }
}
