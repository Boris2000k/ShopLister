<?php
namespace App;

use App\Permission;
use App\Role;

trait HasPermissionsTrait {

//    public function givePermissionsTo(... $permissions) {
//
//        $permissions = $this->getAllPermissions($permissions);
//        dd($permissions);
//        if($permissions === null) {
//            return $this;
//        }
//        $this->permissions()->saveMany($permissions);
//        return $this;
//    }
//
//    public function withdrawPermissionsTo( ... $permissions ) {
//
//        $permissions = $this->getAllPermissions($permissions);
//        $this->permissions()->detach($permissions);
//        return $this;
//
//    }
//
//    public function refreshPermissions( ... $permissions ) {
//
//        $this->permissions()->detach();
//        return $this->givePermissionsTo($permissions);
//    }
//
    public function hasPermissionTo($permission) {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission) || $this->hasPermission('superadmin');
    }

    public function hasPermissionThroughRole($permission) {
        if(gettype($permission) == 'string'){
            $permission = Permission::where('name', $permission)->first();
        }
        foreach ($permission->roles as $role){
            if($this->roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    public function hasRole( ... $roles ) {
        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }
        if($this->hasPermission('superadmin')){
            return true;
        }
        return false;
    }

//    public function roles() {
//
//        return $this->belongsToMany(Role::class,'users_roles');
//
//    }

    public function getPermissions() {
        // get all permissions of a user
        $permissions = collect([]);
//        foreach($this->roles as $one_role){
//            foreach($one_role->permissions as $one_permission_through_role){
//                if(!$permissions->contains($one_permission_through_role)){
//                    $permissions->push($one_permission_through_role);
//                }
//            }
//        }
        foreach($this->permissions as $one_permission){
            if(!$permissions->contains($one_permission)){
                $permissions->push($one_permission);
            }
        }
        return $permissions;
    }

    public function hasPermission($permission) {
        if(gettype($permission) == 'string'){
            $permission_to_check = Permission::where('name', $permission)->first();
        } else {
            $permission_to_check = $permission;
        }
        return (bool) $this->permissions->where('slug', $permission_to_check->slug)->count();
    }
//
//    protected function getAllPermissions(array $permissions) {
//
//        return Permission::whereIn('slug',$permissions)->get();
//
//    }

}
