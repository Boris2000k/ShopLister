@extends('layouts.app')
@section('header')
@endsection('header')
@section('content-header')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card" style="width: 30rem; margin: 0 auto; float: none;">
                <div class="card-header text-center">
                    <h2>{{$selected_product->name}}</h2>
                </div>
                <div class="card-body card-body-product">
                    <p class="text-center text-muted">Sold by: {{$selected_shop->name}}</p>
                    <p class="text-center text-muted">Description: {{$selected_product->description}}</p>
                    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            @if($selected_product->json_custom)
                                @php
                                    $first_run = true;
                                @endphp
                                @foreach(json_decode($selected_product->json_custom, true) as $img_name => $img_path)
                                    @if($first_run)
                                        <div class="carousel-item active">
                                            <div class="container" style="height: 25vh;">
                                                <img class="d-block w-100" src="{{url($img_path . '/' . $img_name)}}" alt="First slide">
                                            </div>
                                        </div>
                                        @php
                                        $first_run = false;
                                        @endphp
                                    @else
                                        <div class="carousel-item">
                                            <div class="container" style="height: 25vh;">
                                                <img class="d-block w-100" src="{{url($img_path . '/' . $img_name)}}" alt="First slide">
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-6 text-left">
                            @if($selected_product->on_sale)
                                <ins class="text-danger">${{$selected_product->price}}</ins>
                                <b class="text-success">${{$selected_product->discounted_price}}</b>
                            @else
                                <b class="text-success">${{$selected_product->price}}</b>
                            @endif
                        </div>
                        <div class="col-6 text-right">
                            <btn class="btn btn-primary add-to-cart" data-store_id="{{$selected_shop->id}}" data-product_price="{{$selected_product->on_sale ? $selected_product->discounted_price : $selected_product->price}}" data-product_name="{{$selected_product->name}}" data-product_id="{{$selected_product->id}}">
                                Add to Cart
                            </btn>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 pt-5">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-left">Store</th>
                        <th class="text-center">Price</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sold_by as $product_id => $data)
                        @php
                            $name = $data['name'];
                            $price = $data['price'];
                            $sale_price = null;
                            $discount = null;
                            if($data['on_sale']){
                                $sale_price = $data['discounted_price'];
                                $discount = $data['discount'];
                            }
                        @endphp
                        @foreach($data['sold_by'] as $store_id => $store_data)
                                <tr>
                                    <td class="text-left">{{$store_data['shop_name']}}</td>
                                    <td class="text-center">
                                        @if($data['on_sale'] == true)
                                            <ins class="text-danger">${{$price}}</ins>
                                            <b class="text-success">${{$sale_price}}</b>
                                        @else
                                            <b class="text-success">${{$price}}</b>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <btn class="btn btn-primary add-to-cart" data-store_id="{{$store_id}}" data-product_price="{{$sale_price > 0 ? $sale_price : $price}}" data-product_name="{{$name}}" data-product_id="{{$product_id}}">
                                            Add to Cart
                                        </btn>
                                    </td>
                                </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection('content')
