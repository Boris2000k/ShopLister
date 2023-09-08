@extends('layouts.app')
@section('header')
@endsection('header')
@section('content-header')
    <div class="row">
        <div class="col-2">
            <h1>Shop Management</h1>
        </div>
        <div class="col-10 text-right">
            <a href="{{route('shop-management.upsert')}}" class="btn btn-primary">New Shop</a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="text-left">Name</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shops as $shop_id => $shop_name)
                        <tr>
                            <td class="text-left">{{$shop_name}}</td>
                            <td class="text-right">
                                <a href="/shop-management/upsert/{{$shop_id}}"><i class="edit fa-solid fa-pen"></i></a>
                                <a href="/shop-management/delete/{{$shop_id}}"><i class="delete fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection('content')
