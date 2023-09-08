<?php

namespace App\Http\Controllers;

use App\Product;
use App\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShoppingManagementController extends Controller
{
    public function index(Request $request){
        $input = $request->input();
        $now = Carbon::now()->startOfDay();
        if(isset($input['type']) || isset($input['search'])){
            $filter_type = 'products';
            if(isset($input['search'])){
                // search input
                $param = $input['search'];
                $products = Product::where('name', 'like',  '%' . $param . '%')->get()->groupBy('product_group')->map(function($product_group){
                    return $product_group->keyBy('id')->map(function($product){
                        if(DB::table('shop_product_rel')->where('product_id', $product->id)->exists()){
                            $product->sold_by = $product->soldBy->keyBy('id');
                            return $product;
                        }
                    });
                });
            } else {
                switch($input['type']){
                    case 'discounted':
                        $products = Product::where('discount', '>', 0)->get()->groupBy('product_group')->map(function($product_group) use($now){
                            return $product_group->keyBy('id')->map(function($product) use ($now){
                                if(isset($product->discount_duration)){
                                    $discount_duration_exploded = explode(' - ', $product->discount_duration);
                                    $start_carbon = Carbon::createFromFormat('Y-m-d', $discount_duration_exploded[0]);
                                    $end_carbon = Carbon::createFromFormat('Y-m-d', $discount_duration_exploded[1]);
                                    if($now->betweenIncluded($start_carbon, $end_carbon)){
                                        if(DB::table('shop_product_rel')->where('product_id', $product->id)->exists()){
                                            $product->sold_by = $product->soldBy->keyBy('id');
                                            return $product;
                                        }
                                    }
                                }
                            });
                        });
                        break;
                    case 'Food':
                    case 'Soft Drink':
                    case 'Alcoholic Drink':
                    case 'Sanitary':
                    case 'Pets':
                        $products = Product::where('product_group', $input['type'])->get()->groupBy('product_group')->map(function($product_group){
                            return $product_group->keyBy('id')->map(function($product){
                                if(DB::table('shop_product_rel')->where('product_id', $product->id)->exists()){
                                    $product->sold_by = $product->soldBy->keyBy('id');
                                    return $product;
                                }
                            });
                        });
                        break;
                }
            }

            // filter empty products
            foreach($products as $group => $group_data){
                foreach($group_data as $product_id => $product_data){
                    if($product_data == null){
                        unset($products[$group][$product_id]);
                    }
                }
            }
            // filter empty product groups
            foreach($products as $group => $group_data){
               if(!$group_data->count()){
                   unset($products[$group]);
               }
            }
            return view('shopping.index', [
                'filter_type' => $filter_type,
                'products' => $products,
            ]);

        } else {
            $shops = Shop::get();
            return view('shopping.index', [
                'filter_type' => 'shops',
                'shops' => $shops,
            ]);
        }
    }

    public function shopProducts($shop_id){
        $shop = Shop::find($shop_id);
        $products = $shop->products->groupBy('product_group');
        return view('shopping.shop-products', [
            'shop' => $shop,
            'products' => $products,
        ]);
    }
    public function viewProduct($product_id, $store_id = null){
        $selected_product = Product::find($product_id);
        $selected_product->on_sale = false;
        $selected_product->discounted_price = null;
        $now              = Carbon::now();
        // check if selected product is on sale
        if($selected_product->discount > 0){
            $discount              = explode(' - ', $selected_product['discount_duration']);
            $discount_start_carbon = Carbon::createFromFormat('Y-m-d', $discount[0])->startOfDay();
            $discount_end_carbon   = Carbon::createFromFormat('Y-m-d', $discount[1])->endOfDay();
            if($now->betweenIncluded($discount_start_carbon, $discount_end_carbon)){
                $selected_product->on_sale = true;
                $selected_product->discounted_price = $selected_product['price'] - ($selected_product['discount'] * ($selected_product['price'] / 100));
            }
        }
        $products = Product::where('name', $selected_product->name)->with('soldBy')->get()->toArray();
        $sold_by  = [];
        // get same product from different stores
        foreach($products as $key => $data){
            $on_sale          = false;
            $discounted_price = null;
            // check if other stores products are on sale
            if($data['discount'] > 0){
                $discount              = explode(' - ', $data['discount_duration']);
                $discount_start_carbon = Carbon::createFromFormat('Y-m-d', $discount[0])->startOfDay();
                $discount_end_carbon   = Carbon::createFromFormat('Y-m-d', $discount[1])->endOfDay();
                if($now->betweenIncluded($discount_start_carbon, $discount_end_carbon)){
                    $on_sale          = true;
                    $discounted_price = $data['price'] - ($data['discount'] * ($data['price'] / 100));
                }
            }
            // setup data for all stores with same product
            $sold_by[$data['id']] = [
                'product_group'    => $data['product_group'],
                'name'             => $data['name'],
                'on_sale'          => $on_sale,
                'price'            => $data['price'],
                'discounted_price' => $discounted_price,
                'discount'         => $data['discount'],
                'sold_by'          => [],
            ];
            if(isset($data['sold_by'])){
                foreach($data['sold_by'] as $sold_by_key => $sold_by_data){
                    $sold_by[$data['id']]['sold_by'][$sold_by_data['id']] = [
                        'shop_id' => $sold_by_data['id'],
                        'shop_name' => $sold_by_data['name'],
                    ];
                }
            }
        }

        return view('shopping.product', [
            'selected_shop' => $store_id ? Shop::find($store_id) : null,
            'selected_product' => $selected_product,
            'sold_by' => $sold_by,

        ]);
    }
}
