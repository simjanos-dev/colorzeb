@extends('layouts.app')

@section('head')
<link href="{{ asset('css/user_data.css') }}" rel="stylesheet">
@endsection

@section('content')
    <user-data-component
        _payment-method="{{ $user->payment_method }}"

        _shipping-name="{{ $user->shipping_name }}"
        _shipping-phone="{{ $user->shipping_phone }}"
        _shipping-zip="{{ $user->shipping_zip_code }}"
        _shipping-city="{{ $user->shipping_city }}"
        _shipping-address="{{ $user->shipping_address }}"
        _shipping-method="{{ $user->shipping_method }}"

        _billing-name="{{ $user->billing_name }}"
        _billing-tax-number="{{ $user->billing_tax_number }}"
        _billing-zip="{{ $user->billing_zip_code }}"
        _billing-city="{{ $user->billing_city }}"
        _billing-address="{{ $user->billing_address }}"
    >
    </user-data-component>
@endsection