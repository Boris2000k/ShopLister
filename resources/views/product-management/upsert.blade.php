@extends('layouts.app')
@section('content-header')
    <div class="row">
        <div class="col-2">
            <h1>{{$title}}</h1>
        </div>
    </div>
@endsection
@section('content')
    {{Form::open(['route' => ['product-management.postdata', $product->id ?? null], 'files' => true ])}}
    <div class="row">
{{--        <div class="col-12">--}}
            <div class="col-4">

                <div class="form-group">
                    {{Form::label('product_group', 'Product Group')}}
                    {{Form::select('product_group', $product_types ?? [], $product->product_group ?? null, ['class' => 'form-control'])}}
                    @error('product_group')
                    <p class="text-danger">
                        {{$message}}
                    </p>
                    @enderror
                </div>

                <div class="form-group">
                    {{Form::label('name', 'Product Name')}}
                    {{Form::text('name', $product->name ?? '', ['class' => 'form-control'])}}
                    @error('name')
                    <p class="text-danger">
                        {{$message}}
                    </p>
                    @enderror
                </div>

                <div class="form-group">
                    {{Form::label('description', 'Description')}}
                    {{Form::text('description', $product->description ?? '', ['class' => 'form-control'])}}
                    @error('description')
                    <p class="text-danger">
                        {{$message}}
                    </p>
                    @enderror
                </div>

                <div class="form-group">
                    {{Form::label('price', 'Price')}}
                    {{Form::text('price', $product->price ?? '', ['class' => 'form-control'])}}
                    <p class="text-success new-price"></p>
                    @error('price')
                    <p class="text-danger">
                        {{$message}}
                    </p>
                    @enderror
                </div>

                <div class="form-group">
                    {{Form::label('discount', 'Discount (%)')}}
                    {{Form::text('discount', $product->discount ?? '', ['class' => 'form-control'])}}
                    @error('discount')
                    <p class="text-danger">
                        {{$message}}
                    </p>
                    @enderror
                </div>

                <div class="form-group">
                    {{Form::label('discount_duration', 'Discount Duration')}}
                    {{Form::text('discount_duration', $product->discount_duration ?? '', ['class' => 'form-control', 'id' => 'date-range-picker'])}}
                    @error('discount_duration')
                    <p class="text-danger">
                        {{$message}}
                    </p>
                    @enderror
                </div>

                <div class="form-group">
                    {{Form::label('images', 'Images')}}
                    <input type="file" name="images[]" class="form-control" placeholder="images" multiple>
                    @error('images')
                    <p class="text-danger">
                        {{$message}}
                    </p>
                    @enderror
                </div>

                <div class="form-group">
                    {{Form::submit($button_action, ['class' => 'btn btn-primary'])}}
                </div>
            </div>
            <div class="col-3">
                @if(($product->json_custom ?? null) != null)
                    @forelse(json_decode($product->json_custom, true) as $file_name => $file_path)
                        <div class="card" style="width: 18rem;">
                            <div class="card-header">
                                <a href="{{route('product-management.delete-product-image', $file_name)}}"><i class="delete fa-solid fa-trash float-right"></i></a>
                            </div>
                            <div class="card-body card-body-product">
                                <img src="{{ url($file_path . '/' . $file_name) }}" alt="{{$file_name}}" style="max-height: 100%; max-width: 100%;"/>
                            </div>
                        </div>
                    @empty
                        No images
                    @endforelse
                @endif
            </div>
{{--        </div>--}}
    </div>
    {{Form::close()}}
    <script src="{{asset('js/product-management.js')}}"></script>
@endsection('content')
