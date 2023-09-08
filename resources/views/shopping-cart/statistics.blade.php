@extends('layouts.app')
@section('header')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.1.1/chart.min.js"></script>
    <script src="{{asset('js/statistics-users.js')}}"></script>
    <script> var chart_data = {!! json_encode($chart_data) !!} </script>
@endsection('header')
@section('content-header')
    <div class="row">
        <div class="col-3">
            <h1>Statistics</h1>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-6">
            <div class="card">
                <h5 class="card-header">
                    <canvas id="items_group" aria-label="chart" height="350" width="580"></canvas>
                </h5>
                <div class="card-body">
                    <h5 class="card-title">Items bought by name</h5>
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
                    <h5 class="card-title">Items bought by type</h5>
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
    </div>
@endsection('content')
