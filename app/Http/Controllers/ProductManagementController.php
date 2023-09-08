<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductManagementController extends Controller
{
    public function index(){
        $products = Auth::user()->products->keyBy('id');
        return view('product-management.index', [
            'products' => $products,
        ]);
    }

    public function upsert($product_id = null){
        if($product_id){
            $product = Product::find($product_id);
        }

        return view('product-management.upsert', [
            'product' => $product ?? null,
            'title' => $product_id ? 'Update Product' : 'New Product',
            'button_action' => $product_id ? 'Update' : 'Create',
            'product_types' => config('product_types')['product_types'],
        ]);
    }

    public function postData(Request $request, $product_id = null){
        $auth_user = Auth::user();
        $input = $request->input();
        $rules = array(
            "name" => 'required',
            "product_group" => 'required',
            "price" => 'required|numeric',
            "discount" => 'numeric|nullable|max:100',
            "discount_duration" => Rule::requiredIf(isset($request->discount)),
            "images.*"          => "image|mimes:jpg,jpeg,png|max:2048,"
        );

        if($request->file('images')){
            if(count($request->file('images')) > 5){
                return back()->withErrors(['images' => 'Max 5 images can be uploaded for a product!']);
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return back()->withErrors(['images' => 'All file types must be jpg, jpeg or png!']);
        }

        if($product_id){
            $product = Product::find($product_id);
            $msg = 'Product updated successfully';
        } else {
            $product = new Product();
            $msg = 'Product created successfully';
        }
        $status = 'success';

        $product->name = $input['name'];
        $product->product_group = $input['product_group'];
        $product->description = $input['description'] ?? null;
        $product->price = $input['price'];
        $product->discount = $input['discount'] ?? null;
        $product->discount_duration = $input['discount_duration'] ?? null;

        if($request->file('images')){
            if($product_id == null){
                $product_id = Product::max('id') + 1 ?? 1;
            }
            $json_custom = [];
            $image_number = 0;
            $images = $request->file('images');
            $path = 'product_images/' . $product_id;
            // create path if doesnt exist
            File::ensureDirectoryExists($path);
            File::cleanDirectory($path);
            foreach($images as $key => $image){
                $image_name = $product_id . "_image" . $key . "." . $image->getClientOriginalExtension();
                $image->move($path, $image_name);
                $json_custom[$image_name] = $path;
            }
            $product->json_custom = json_encode($json_custom);
        }

        if($product->isDirty()){
            $product->save();
            // create user product relation if doesn't already exists
            $user_prod_rel = DB::table('user_product_rel')->where('user_id', $auth_user->id)->where('product_id', $product_id);
            if(!$user_prod_rel->exists()){
                $user_prod_rel = DB::table('user_product_rel')->insert([
                    'user_id' => $auth_user->id,
                    'product_id' => $product->id,
                ]);
                $msg = 'Product created successfully';
            } else {
                $msg = 'Product updated successfully';
            }
        } else {
            $status = 'error';
            $msg = 'Nothing to update';
        }
        return back()->with('success', $msg);
    }

    public function deleteProduct($product_id){
        File::deleteDirectory('product_images/' . $product_id);
        DB::table('user_product_rel')->where('product_id', $product_id)->delete();
        Product::find($product_id)->delete();
        return back()->with('Product deleted');
    }

    public function deleteProductImage($image_name){
        $image_data = explode('_', $image_name);
        $product_id = $image_data[0];

        $product = Product::find($product_id);
        $json_custom = json_decode($product->json_custom, true);
        if(isset($json_custom[$image_name])){
            unset($json_custom[$image_name]);
            $product->json_custom = json_encode($json_custom);
            $product->save();
            $img_path = 'product_images/' . $product_id . '/' . $image_name;
            File::delete($img_path);
            return back()->with('success', 'Image deleted successfully.');
        } else {
            return back()->with('error', 'Image not found.');
        }
    }
}
