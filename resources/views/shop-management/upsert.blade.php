@extends('layouts.app')
@section('header')
    <script src="{{asset('js/shop-upsert.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.1.1/chart.min.js"></script>
    <script> var chart_data = {!! json_encode($chart_data) !!} </script>
@endsection('header')
@section('content-header')
    <div class="row">
        <div class="col-2">
            <h1>{{$title}}</h1>
        </div>
    </div>
@endsection
@section('content')
    {{Form::open(['route' => ['shop-management.postdata', $shop->id ?? null ], 'files' => true])}}
    <div class="row">
        <div class="col-8">
            <div class="col-4">
                <div class="form-group">
                    {{Form::label('permission_name', 'Shop Name')}}
                    {{Form::text('name', $shop->name ?? '', ['class' => 'form-control'])}}
                    @error('name')
                    <p class="text-danger">
                        {{$message}}
                    </p>
                    @enderror
                </div>

                <div class="form-group">
                    {{Form::label('store_image', 'Store Image')}}
                    <input type="file" name="store_image" class="form-control" placeholder="image">
                    @error('store_image')
                    <p class="text-danger">
                        {{$message}}
                    </p>
                    @enderror
                </div>
            </div>
        </div>
        <div class="col-4">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th></th>
                        <th>Has Permission</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users_table as $user_id => $user_data)
                        <tr>
                            <td>{{$user_data['name']}}</td>
                            <td>{{Form::checkbox('users_table['.$user_id.']', false, true, ['style' => 'display:none'])}}</td>
                            <td>{{Form::checkbox('users_table['.$user_id.']', true, $user_data['is_employee'])}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($chart_data != null)
        <div class="col-6">
            <div class="card">
                <h5 class="card-header">
                    <canvas id="items_group" aria-label="chart" height="350" width="580"></canvas>
                </h5>
                <div class="card-body">
                    <h5 class="card-title">Items sold by name</h5>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product name</th>
                                <th class="text-right">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($chart_data['item_group']['data'] as $name => $qty)
                                <tr>
                                    <td>{{$name}}</td>
                                    <td class="text-right">{{$qty}}</td>
                                </tr>
                            @empty
                                No data found
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <h5 class="card-header">
                    <canvas id="product_group" aria-label="chart" height="350" width="580"></canvas>
                </h5>
                <div class="card-body">
                    <h5 class="card-title">Items sold by type</h5>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product type</th>
                                <th class="text-right">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($chart_data['product_group']['data'] as $name => $qty)
                                <tr>
                                    <td>{{$name}}</td>
                                    <td class="text-right">{{$qty}}</td>
                                </tr>
                            @empty
                                No data found
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if($products)
            <div class="col-12 pt-5">
                <h3>Products</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-left">Name</th>
                            <th class="text-center">Product Group</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Discount</th>
                            <th class="text-center">Discount Duration</th>
                            <th class="text-right" colspan="2">Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product_id => $product_data)
                            <tr>
                                @php
                                    // check if discount is active
                                    $discounted_price = null;
                                    $status_class = null;
                                    if($product_data['discount_duration']){
                                        $discount_daterange = explode(' - ', $product_data['discount_duration']);
                                        $carbon_discount_start = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $discount_daterange[0])->startOfDay();
                                        $carbon_discount_end = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $discount_daterange[1])->endOfDay();
                                        $carbon_now = \Illuminate\Support\Carbon::now();
                                        if($carbon_now->betweenIncluded($carbon_discount_start, $carbon_discount_end)){
                                            // display price after discount in brackets next to base price
                                            if($product_data['discount'] > 0){
                                                $status_class = 'text-success';
                                                $price = $product_data['price'];
                                                $discount = $product_data['discount'];
                                                $discounted_price = $price - ($discount * ($price / 100));
                                            }
                                        } else {
                                            $status_class = 'text-danger';
                                        }
                                    }
                                @endphp
                                <td class="text-left"><a href="{{route('product-management.upsert', $product_id)}}">{{$product_data['name']}}</a></td>
                                <td class="text-center">{{$product_data['product_group']}}</td>
                                <td class="text-center">${{$product_data['price']}}@if(isset($discounted_price)) {{"($" . $discounted_price . ")"}} @endif</td>
                                <td class="text-center">{{$product_data['discount'] ?? 0}}%</td>
                                <td class="text-center {{$status_class ?? ''}}">{{$product_data['discount_duration']}}</td>
                                <td class="text-center">{{Form::checkbox('products_table['.$product_id.']', false, true, ['style' => 'display:none'])}}</td>
                                <td class="text-right">{{Form::checkbox('products_table['.$product_id.']', true, $product_data->active)}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <div class="col-12 pt-5">
            <div class="form-group">
                {{Form::submit($button_action, ['class' => 'btn btn-primary'])}}
            </div>
        </div>
    </div>
    {{Form::close()}}
@endsection('content')
