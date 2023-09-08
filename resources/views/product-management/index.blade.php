@extends('layouts.app')
@section('header')
@endsection('header')
@section('content-header')
    <div class="row">
        <div class="col-3">
            <h1>Product Management</h1>
        </div>
        <div class="col-9 text-right">
            <a href="{{route('product-management.upsert')}}" class="btn btn-primary">New Product</a>
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
                        <th class="text-center">Price</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">Discount</th>
                        <th class="text-center">Discount Daterange</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product_id => $product_data)
                        <tr>
                            <td class="text-left">{{$product_data->name}}</td>
                            <td class="text-center">${{$product_data->price}}</td>
                            <td class="text-center">{{$product_data->description}}</td>
                            <td class="text-center">{{$product_data->discount ?? 0}}%</td>
                            <td class="text-center">{{$product_data->discount_duration}}</td>
                            <td class="text-right">
                                <a href="/product-management/upsert/{{$product_id}}"><i class="edit fa-solid fa-pen"></i></a>
                                <a href="/product-management/delete/{{$product_id}}"><i class="delete fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection('content')
