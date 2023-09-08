@extends('layouts.app')
@section('header')
@endsection('header')
@section('content-header')
    <div class="row">
        <div class="col-2 text-center"><a href="/shops/?type=discounted">Discounted</a></div>
        @php
            $product_types = config('product_types')['product_types'];
        @endphp
        @foreach($product_types as $product_type)
            <div class="col-2 text-center"><a href="shops/?type={{$product_type}}">{{$product_type}}</a></div>
        @endforeach
    </div>
    <div class="row pt-5">
        <div class="col-sm-5 col-md-3 col-lg-3 col-xl-3 text-center form-inline">
            {{Form::open(['route' => ['shops.index'], 'method' => 'GET'] )}}
            {{Form::text('search', null, ['class' => 'form-control', 'placeholder' => 'Search by Product Name'] )}}
            <button class="btn btn-primary" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            {{Form::close()}}
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        @switch($filter_type)
            @case('shops')
                @forelse($shops as $shop_data)
                    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
                        <div class="card" style="width: 18rem;">
                            <div class="card-header text-center">
                                <a href="{{route('shops.products', $shop_data->id)}}">{{$shop_data->name}}</i></a>
                            </div>
                            <div class="card-body card-body-product" style="height:20vh !important;">
                                <img src="{{url($shop_data->store_image)}}" alt="store_image" style="max-height: 100%; max-width: 100%;"/>
                            </div>
                        </div>
                    </div>
                @empty
                    No shops
                @endforelse
                @break;
            @case('products')
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="row">
                        @forelse($products as $product_type => $product_data)
                            <div class="col-12">
                                <div class="card" data-product_type="{{$product_type}}">
                                    <div class="card-header text-center">
                                        {{$product_type}}
                                    </div>
                                    <div class="card-body card-body-product">
                                        <div class="row">
                                            @foreach($product_data as $product)
                                                @php
                                                    $on_sale = false;
                                                    $now = \Carbon\Carbon::now();
                                                    if(isset($product->discount_duration)){
                                                        $discount_duration = explode(' - ', $product->discount_duration);
                                                        $discount_start_carbon = \Carbon\Carbon::createFromFormat('Y-m-d', $discount_duration[0])->startOfDay();
                                                        $discount_end_carbon = \Carbon\Carbon::createFromFormat('Y-m-d', $discount_duration[1])->endOfDay();
                                                    }

                                                    if($product->discount && $now->betweenIncluded($discount_start_carbon, $discount_end_carbon)){
                                                        $on_sale = true;
                                                    }
                                                @endphp
                                                <div class="col-4 pt-3">
                                                    <div class="card">
                                                        <div class="card-header text-center">
                                                            @php
                                                                $store_id = $product['sold_by']->first()->id;
                                                            @endphp
                                                            <a class="product-name" href="{{url("product/$product->id/store/$store_id")}}">{{$product->name}}</a>
                                                            {{-- <a href="{{route('view-product', $product->id, 13)}}">{{$product->name}}</a>--}}
                                                            @if($on_sale)
                                                                ({{$product->discount}}% off untill {{$discount_end_carbon->format('M d')}})
                                                            @endif
                                                        </div>
                                                        <div class="card-body card-body-product">
                                                            @php
                                                                $image = null;
                                                                if(isset($product->json_custom)){
                                                                    $image = collect(json_decode($product->json_custom, true))->take(1);
                                                                    $img_path = $image->values()->first() . '/' . $image->keys()->first();
                                                                }
                                                            @endphp
                                                            @if($image)
                                                                <img src="{{ url($img_path)}}" alt="product_image" style="max-height: 100%; max-width: 100%; background-size: contain"/>
                                                            @endif

                                                        </div>
                                                        <div class="card-footer text-muted">
                                                            <div class="row">
                                                                <div class="col-6 text-left">
                                                                    @if($on_sale)
                                                                        <s class="text-danger">${{$product->price}}</s>
                                                                        <b class="text-success">${{$product->price - ($product->discount * ($product->price / 100))}}</b>
                                                                    @else
                                                                        <b class="text-success">${{$product->price}}</b>
                                                                    @endif
                                                                </div>
                                                                <div class="col-6 text-right">
                                                                    <a class="btn btn-primary" href="{{url("product/$product->id/store/$store_id")}}">
                                                                        View All Offers
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            No Products to Filter
                        @endforelse
                    </div>
                </div>
                @break;
        @endswitch
    </div>

@endsection('content')
