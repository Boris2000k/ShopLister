<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index(){
//        $products = Product::where('')
        $permissions = Permission::get();
        return view('user-management.permissions.index', [
            'permissions' => $permissions,
        ]);
    }

    public function upsert($id = null){
        $permission = Permission::find($id) ?? null;

        $users_table = User::get()->map(function($user) use ($permission){
            $data_to_return = [
                'id'             => $user->id,
                'name'           => $user->name,
                'can_permission' => $permission ? $user->can($permission->slug) : false,
            ];
            return $data_to_return;
        })
       ->keyBy('id');

        $title = isset($id) ? 'Update Permission' : 'New Permission';

        return view('user-management.permissions.form', [
            'users_table'   => $users_table,
            'title'         => $title,
            'permission'    => Permission::find($id),
            'button_action' => $id ? 'Update' : 'Create',
        ]);
    }

    public function postData(Request $request, $permission_id = null){
        $permission_exists = null;
        if($permission_id){
            $permission_exists = true;
            $permission = Permission::find($permission_id);
        } else {
            $permission_exists = false;
            $permission = new Permission();
        }
        $request->validate([
            "name" => 'required|unique:permissions,name,' . $permission->id,
            "slug" => 'required|unique:permissions,slug,' . $permission->id,
        ]);
        $input = $request->input();
        $permission->menu_name = $input['menu_name'];
        $permission->name = $input['name'];
        $permission->slug = $input['slug'];

        $permission->save();
        foreach($input['users_table'] as $user_id => $can_permission){
            $permission_exists = DB::table('users_permissions')->where('user_id', $user_id)->where('permission_id', $permission_id)->exists();
            if($can_permission){
                if(!$permission_exists){
                    DB::table('users_permissions')->insert([
                        'user_id' => $user_id,
                        'permission_id' => $permission->id,
                    ]);
                }
            } else {
                if($permission_exists){
                    DB::table('users_permissions')->where('user_id', $user_id)->where('permission_id', $permission_id)->delete();
                }
            }
        }
        return back()->with('success', $permission_id ? 'Permission Updated Successfully' : 'Permission Created Successfully');
    }
    public function deletePermission($permission_id){
        Permission::find($permission_id)->delete();
        return back()->with('success', 'Permission Deleted Successfully');
    }
}
