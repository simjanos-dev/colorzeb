@extends('layouts.app')

@section('head')
    <link href="{{ asset('css/custom_order.css') }}" rel="stylesheet">
@endsection

@section('content')
    <custom-order-component></custom-order-component>
@endsection
