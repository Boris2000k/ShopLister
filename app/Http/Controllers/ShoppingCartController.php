<?php

namespace App\Http\Controllers;

use App\Product;
use App\ShoppingCart;
use App\Statistic;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ShoppingCartController extends Controller
{
    public function index(){
        $auth_user = Auth::user();
        $carts = ShoppingCart::where('owner_id', $auth_user->id)->get()->keyBy('id')->map(function($cart){
            $cart_items = json_decode($cart->json_custom, true);
            if($cart_items){
                $completed_items = 0;
                $completion_rate = 0;
                foreach($cart_items as $one_item){
                    if($one_item['complete'] == true){
                        $completion_rate += 100 / count($cart_items);
                    }
                }
                $cart->completion_rate = round($completion_rate,0);
            }
            return $cart;
        });
        return view('shopping-cart.index', [
            'carts' => $carts->sortBy('completion_rate'),
        ]);
    }

    public function upsert($cart_id = null){
        if($cart_id){
            $now = Carbon::now();
            $cart = ShoppingCart::find($cart_id);
            $content = json_decode($cart->json_custom, true);
            $products = [];
            if($content){
                foreach($content as $id => $data){
                    $product = Product::find($id);
                    if($product->discount > 0){
                        $discount_duration = explode(' - ', $product->discount_duration);
                        $discount_start_carbon = Carbon::createFromFormat('Y-m-d', $discount_duration[0])->startOfDay();
                        $discount_end_carbon = Carbon::createFromFormat('Y-m-d', $discount_duration[1])->endOfDay();
                        if($now->betweenIncluded($discount_start_carbon, $discount_end_carbon)){
                            $product->final_price = $product->price - (($product->price / 100) * $product->discount);
                        }
                    } else {
                        $product->final_price = $product->price;
                    }
                    $content[$id]['product'] = $product;
                }
            }

        }

        return view('shopping-cart.upsert', [
            'cart' => $cart ?? null,
            'cart_content' => $cart_id ? (collect($content)->sortBy('complete') ?? null) : null,
            'title' => $cart_id ? 'Update Cart' : 'New Shopping Cart',
            'button_action' => $cart_id ? 'Update' : 'Create',
        ]);
    }

    public function copyCart($cart_id){
        $auth_user = Auth::user();
        $new_cart = new ShoppingCart();
        // set up cart data
        $cart_to_copy = ShoppingCart::find($cart_id);
        $products = json_decode($cart_to_copy->json_custom, true);
        if($products){
            foreach($products as $product_id => $one_product){
                $products[$product_id]['complete'] = false;
            }
        } else {
            $products = null;
        }
        // add details of the new cart
        $new_cart->name = $cart_to_copy->name . ' - Copy';
        $new_cart->owner_id = $auth_user->id;
        $new_cart->json_custom = $products ? json_encode($products) : null;
        $new_cart->save();

        return back()->with('success', 'Cart copied successfully!');
    }

    public function deleteCart($cart_id){
        $auth_user = Auth::user();
        $cart = ShoppingCart::find($cart_id);
        if($auth_user->id == $cart->owner_id){
            $cart->delete();
            return back()->with('success', 'Cart deleted successfully!');
        } else {
            return back()->with('error', 'You dont own this shopping cart!');
        }
    }

    public function postData(Request $request, $cart_id = null){
        $request->validate([
            "name" => 'required|max:45',
        ]);
        $input = $request->input();

        $auth_user = Auth::user();
        if($cart_id){
            $cart = ShoppingCart::find($cart_id);
        } else {
            $cart = new ShoppingCart();
        }

        $cart->name = $input['name'];
        $cart->owner_id = $auth_user->id;
        $cart->save();

        return back()->with('success', 'Shopping Cart created successfully!');
    }

    public function addToCart(Request $request){
        $cart_id = $request->cart_id;
        $product_id = $request->product_id;
        $amount = $request->amount;
        $store_id = $request->store_id;

        $cart = ShoppingCart::find($cart_id);
        if(!$cart){
            return response()->json([
                'error' => 'No cart is selected!',
            ]);
        }
        $json_custom = json_decode($cart->json_custom, true);
        if(isset($json_custom[$product_id])){
            return response()->json([
                'error' => 'Product already in cart!',
            ]);
        } else {
            $json_custom[$product_id] = [
                'from' => $store_id,
                'amount' => $amount,
                'complete' => false,
            ];
        }

        $cart->json_custom = json_encode($json_custom);
        $cart->save();
        return response()->json([
            'success' => 'Product added to Cart!',
        ]);
    }

    public function resolveAction(Request $request){
        $input = $request->input();
        $cart = ShoppingCart::find($input['cart_id']);
        $cart_content = json_decode($cart->json_custom, true);
        switch($input['action']){
            case 'buy':
                $cart_content[$input['product_id']]['complete'] = true;
                $statistics = new Statistic();
                if(!Statistic::where('cart_id', $cart->id)->where('product_id', $input['product_id'])->exists()){
                    $statistics->shop_id =  $cart_content[$input['product_id']]['from'];
                    $statistics->user_id = Auth::user()->id;
                    $statistics->product_id = $input['product_id'];
                    $statistics->cart_id = $cart->id;
                    $statistics->quantity = $cart_content[$input['product_id']]['amount'];
                    $statistics->save();
                }
                break;
            case 'reset':
                $cart_content[$input['product_id']]['complete'] = false;
                if(Statistic::where('cart_id', $cart->id)->where('product_id', $input['product_id'])->exists()){
                    $statistics = Statistic::where('cart_id', $cart->id)->where('product_id', $input['product_id']);
                    $statistics->delete();
                }
                break;
            case 'delete':
                unset($cart_content[$input['product_id']]);
                if(Statistic::where('cart_id', $cart->id)->where('product_id', $input['product_id'])->exists()){
                    $statistics = Statistic::where('cart_id', $cart->id)->where('product_id', $input['product_id']);
                    $statistics->delete();
                }
                break;
        }
        $cart->json_custom = json_encode($cart_content);
        $cart->save();
        return response()->json([
            'success' => 'Cart updated',
        ]);
    }

    public function statistics(){
        $auth_user = Auth::user();
        $statistics = Statistic::where('user_id', $auth_user->id)->get()->groupBy('product_id')->map(function($item_group, $item_id){
            $data_to_return = [];
            $product = Product::where('id', $item_id)->get();
            if($product){
                $product = $product->first();
                $data_to_return['product_group'] = $product['product_group'];
                $data_to_return['name'] = $product['name'];
                $data_to_return['quantity'] = $item_group->sum('quantity');
                return $data_to_return;
            }
        });

        $product_group = $statistics->groupBy('product_group')->map(function($item, $group_name){
            return [
                'group_name' => $group_name,
                'qty' => $item->sum('quantity'),
            ];
        })->pluck('qty', 'group_name');

        $item_group = $statistics->pluck('quantity', 'name');

        return view('shopping-cart.statistics', [
            'chart_data' => [
                'product_group' => [
                    'labels' => $product_group->keys(),
                    'data' => $product_group,
                ],
                'item_group' => [
                  'labels' => $item_group->keys(),
                  'data' => $item_group
                ],
            ]
        ]);
    }
}
