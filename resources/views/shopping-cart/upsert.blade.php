@extends('layouts.app')
@section('header')
    <script src="{{asset('js/cart.js')}}"></script>
    <script>
        let cart_id = {{$cart->id ?? null}}
    </script>
@endsection
@section('content-header')
    <div class="row">
        <div class="col-3">
            <h1>{{$title}}</h1>
        </div>
    </div>
@endsection
@section('content')
    {{Form::open(['route' => ['shopping-carts.postdata', $cart->id ?? null]])}}
    <div class="row">
        <div class="col-3">
            <div class="form-group">
                {{Form::label('name', 'Shopping Cart Name')}}
                {{Form::text('name', $cart->name ?? '', ['class' => 'form-control'])}}
                @error('name')
                <p class="text-danger">
                    {{$message}}
                </p>
                @enderror
            </div>
            <div class="form-group">
                {{Form::submit($button_action, ['class' => 'btn btn-primary'])}}
            </div>
        </div>
        @if($cart_content)
        <div class="col-9">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-left">Product Name</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Single Price</th>
                        <th class="text-center">Current Total Price</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cart_content as $product_id => $content)
                        <tr data-row_product_id="{{$product_id}}" class="{{$content['complete'] == true ? 'item-complete' : 'item-pending'}}">
                            <td class="text-left">{{$content['product']['name']}}</td>
                            <td class="text-center">{{$content['amount']}}</td>
                            <td class="text-center">$ {{$content['product']['final_price']}}</td>
                            <td class="text-center">$ {{$content['product']['final_price'] * $content['amount']}}</td>
                            <td class="text-right">
                                <a><i data-product_id="{{$product_id}}" class="confirm-buy edit fa-regular fa-circle-check"></i></a>
                                <a><i data-product_id="{{$product_id}}" class="confirm-forget edit fa-solid fa-xmark"></i></a>
                                <a><i data-product_id="{{$product_id}}" class="confirm-delete delete-custom fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        @empty
                        Empty cart
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
    {{Form::close()}}
@endsection('content')
