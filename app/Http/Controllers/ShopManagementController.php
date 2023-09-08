<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Product;
use App\Shop;
use App\Statistic;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ShopManagementController extends Controller
{
    public function index(){
        $auth_user = Auth::user();
        $shops = Shop::where('owner_id',$auth_user['id'])->pluck('name', 'id');
        return view('shop-management.index', [
            'shops' => $shops,
        ]);
    }

    public function upsert($shop_id = null){
        $employees = [];
        $active_products = null;
        if($shop_id){
            $shop = Shop::find($shop_id);
            $employees = json_decode($shop->json_custom, true)['is_employee'];
            $active_products = $shop->products->pluck('id', 'id');
        }
        $product_ids = DB::table('user_product_rel')
                         ->where('user_id', Auth::user()->id)
                         ->select('product_id')
                         ->pluck('product_id');

        if($product_ids){
            $products = Product::whereIn('id', $product_ids)->get()->keyBy('id')->map(function($item) use ($active_products){
                if(isset($active_products[$item->id])){
                    $item->active = true;
                }
                return $item;
            })->sortByDesc('active');
        }
        $users_table = User::get()->map(function($user) use ($employees){
            $data_to_return = [
                'id'             => $user->id,
                'name'           => $user->name,
                'is_employee'    => in_array($user->id, $employees) ?? false,
            ];
            return $data_to_return;
        })
                           ->keyBy('id');

        $statistics = Statistic::where('shop_id', $shop_id)->get()->groupBy('product_id')->map(function($item_group, $item_id){
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

        return view('shop-management.upsert', [
           'title' => $shop_id ? 'Update Shop' : 'New Shop',
           'button_action' => $shop_id ? 'Update' : 'Create',
           'users_table' => $users_table,
           'shop' => $shop_id ? $shop : null,
           'products' => $products,
           'chart_data' => $shop_id != null && $statistics->count() ? [
               'product_group' => [
                   'labels' => $product_group->keys(),
                   'data' => $product_group,
               ],
               'item_group' => [
                   'labels' => $item_group->keys(),
                   'data' => $item_group
               ],
           ] : null,
        ]);
    }

    public function postdata(Request $request, $shop_id = null){
        // get shop if exists
        if($shop_id){
            $shop = Shop::find($shop_id);
        } else {
            $shop = new Shop();
            $shop->owner_id = Auth::user()->id;
        }
        // validate
        $request->validate([
           "name" => 'required|unique:shops,name,' . $shop->id,
           "store_image" => "image|mimes:jpg,jpeg,png|max:2048,"
        ]);
        $input = $request->input();
        $shop->name = $input['name'];
        // save shop image
        if($request->file('store_image')){
            if($shop_id == null){
                $shop_id_tmp = Shop::max('id') + 1 ?? 1;
            } else {
                $shop_id_tmp = $shop_id;
            }
            $path = 'stores/' . $shop_id_tmp;
            File::ensureDirectoryExists($path);
            File::cleanDirectory($path);
            $image = $request->file('store_image');
            $image_name = $shop_id_tmp . "_store_image" . "." .  $image->getClientOriginalExtension();
            $image->move($path, $image_name);
            $shop->store_image = $path . '/' . $image_name;
        }
        // setup json custom
        $custom_data = [
            'is_employee' => [],
        ];
        $employee_ids = [];
        foreach($input['users_table'] as $user_id => $is_employee){
            if($is_employee == 1){
                array_push($employee_ids, $user_id);
            }
        }
        $custom_data['is_employee'] = $employee_ids;
        $json_custom = json_encode($custom_data);
        // save shop
        $shop->json_custom = $json_custom;
        $shop->save();

        if(isset($input['products_table'])){
            $products = [];
            $shops_products = collect($input['products_table'])->map(function($item, $product_id) use (&$products){
                if($item == 1){
                    array_push($products, $product_id);
                }
            });
            $shop->products()->sync($products);
        }


        if($shop_id){
            $message = "Shop $shop->name updated successfully!";
        } else {
            $message = "Shop $shop->name created successfully!";
        }
        return back()->with('success', $message);
    }

    public function deleteShop($shop_id){
        $user = Auth::user();
        $shop = Shop::find($shop_id);
        if($shop['owner_id'] == $user['id']){
            $shop->delete();
            $shop_products_rel = DB::table('shop_product_rel')->where('shop_id', $shop_id)->delete();
            return back()->with('success', "Shop $shop->name and all of its products were deleted");
        }
    }
}
