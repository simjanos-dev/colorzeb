@extends('layouts.app')

@section('head')
    <link href="{{ asset('css/admin_sidebar.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('admin.side_panel')
    <modify-categories-component :_category-list="{{ $categories }}">
    </modify-categories-component>
@endsection