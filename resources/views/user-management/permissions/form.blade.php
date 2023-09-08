@extends('layouts.app')
@section('header')
@endsection('header')
@section('content-header')
    <div class="row">
        <div class="col-2">
            <h1>{{$title}}</h1>
        </div>
    </div>
@endsection
@section('content')
    {{Form::open(['route' => ['user-management.permissions.postdata', $permission->id ?? null ]])}}
        <div class="row">
            <div class="col-8">
                <div class="col-4">
                    <div class="form-group">
                        {{Form::label('menu_name', 'Menu Tab Name')}}
                        {{Form::text('menu_name', $permission->menu_name ?? '', ['class' => 'form-control'])}}
                        @error('menu_name')
                        <p class="text-danger">
                            {{$message}}
                        </p>
                        @enderror
                    </div>
                    <div class="form-group">
                        {{Form::label('permission_name', 'Name')}}
                        {{Form::text('name', $permission->name ?? '', ['class' => 'form-control'])}}
                        @error('name')
                            <p class="text-danger">
                                {{$message}}
                            </p>
                        @enderror
                    </div>
                    <div class="form-group">
                        {{Form::label('permission_slug', 'Slug')}}
                        {{Form::text('slug', $permission->slug ?? '', ['class' => 'form-control'])}}
                        @error('slug')
                        <p class="text-danger">
                            {{$message}}
                        </p>
                        @enderror
                    </div>
                    <div class="form-group">
                        {{Form::submit($button_action, ['class' => 'btn btn-primary'])}}
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
                                <td>{{Form::checkbox('users_table['.$user_id.']', true, $user_data['can_permission'])}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    {{Form::close()}}
@endsection('content')
