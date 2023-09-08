<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Product;
use App\Role;
use App\Shop;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('home.index');
    }

    public function logout(){
        Auth::logout();
        return redirect('/');
    }
}
