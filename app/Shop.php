<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = 'Shops';

    public function owner(){
        return User::find($this->owner_id);
    }

    public function products(){
        return $this->belongsToMany(Product::class, 'shop_product_rel');
    }
}
