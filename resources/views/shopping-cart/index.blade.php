@extends('layouts.app')
@section('header')
@endsection('header')
@section('content-header')
    <div class="row">
        <div class="col-3">
            <h1>Shopping Carts</h1>
        </div>
        <div class="col-9 text-right">
            <a href="{{route('shopping-carts.upsert')}}" class="btn btn-primary">New Shopping Cart</a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-5">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="text-left">Cart Name</th>
                        <th class="text-center">Progress</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($carts as $cart_id => $cart_data)
                        <tr>
                            <td class="text-left">{{$cart_data->name}}</td>
                            <td>
                                <div id="progressbar">
                                    <div style="width:{{$cart_data['completion_rate'] ?? 0}}%;">
                                        <p class="progress-label"><strong>{{$cart_data['completion_rate'] ?? 0}}%</strong></p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <a href="/shopping-carts/upsert/{{$cart_id}}"><i class="edit fa-solid fa-pen"></i></a>
                                <a href="/shopping-carts/copy/{{$cart_id}}"><i class="copy-cart fa-solid fa-repeat"></i></a>
                                <a href="/shopping-carts/delete/{{$cart_id}}"><i class="delete fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    @empty
                        No shopping carts created yet
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection('content')
