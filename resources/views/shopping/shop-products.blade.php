@extends('layouts.app')
@section('header')
    <script src="{{asset('js/shop-products.js')}}"></script>
@endsection('header')
@section('content-header')
    <div class="row">
        <div class="col-2">
            <h1>{{$shop->name}}</h1>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-4 col-md-3 col-lg-3 col-xl-3">
            <div class="card">
                <div class="card-header text-center">
                    Filters
                </div>
                <div class="card-body card-body-product mb-3">
                    @forelse($products->keys() as $product_type)
                        <div style="font-size:1.2rem" class="form-check pl-3 pt-3">
                            {{Form::label('filter_label', $product_type, [ 'class' => 'form-check-label'])}}
                            {{Form::checkbox($product_type, null, true, [ 'class' => 'product-filter', 'data-filter_type' => $product_type ] )}}
                        </div>
                    @empty
                        No Products to Filter
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-sm-8 col-md-9 col-lg-9 col-xl-9">
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
                                                    <a class="product-name" href="{{url("product/$product->id/store/$shop->id")}}">{{$product->name}}</a>
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
                                                            <btn class="btn btn-primary add-to-cart" data-store_id="{{$shop->id}}" data-product_price="{{$product->price}}" data-product_name="{{$product->name}}" data-product_id="{{$product->id}}">
                                                                Add to Cart
                                                            </btn>
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
    </div>
@endsection('content')
