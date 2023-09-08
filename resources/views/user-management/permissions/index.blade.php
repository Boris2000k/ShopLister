@extends('layouts.app')
@section('header')
@endsection('header')
@section('content-header')
    <div class="row">
        <div class="col-1">
            <h1>Permissions</h1>
        </div>
        <div class="col-11 text-right">
            <a href="{{route('user-management.permissions.upsert')}}" class="btn btn-primary">New Permission</a>
        </div>
    </div>
@endsection
@section('content')
 <div class="row">
     <div class="col-12">
         <table class="table table-hover">
             <thead>
                 <tr>
                     <th class="text-left">ID</th>
                     <th class="text-center">Name</th>
                     <th class="text-right">Actions</th>
                 </tr>
             </thead>
             <tbody>
                 @foreach($permissions as $key => $permission)
                     <tr>
                         <td class="text-left">{{$permission->id}}</td>
                         <td class="text-center">{{$permission->name}}</td>
                         <td class="text-right">
                             <a href="/user-management/permissions/upsert/{{$permission->id}}"><i class="edit fa-solid fa-pen"></i></a>
                             <a href="/user-management/permissions/delete/{{$permission->id}}"><i class="delete fa-solid fa-trash"></i></a>
                         </td>
                     </tr>
                 @endforeach
             </tbody>
         </table>
     </div>
 </div>
@endsection('content')
