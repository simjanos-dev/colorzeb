@extends('layouts.app')

@section('head')
    <link href="{{ asset('css/admin_sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin_search_products.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('admin.side_panel')
    <div class="row col-lg-8 offset-lg-1">
        @php
            $links = str_replace('?page=', '/', $products->links());
        @endphp
        <div id="pagination-box" class="col-sm-12">
            <div id="admin-products-nav">{!! $links !!}</div>
            <a href="/admin/create-product"><button id="create-product-button" class="button blue">Új termék</button></a>
        </div>

        <table id="products" class="table table-bordered table-sm col-sm-12 box-shadow">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Kép</th>
                    <th>Termék név</th>
                    <th>Kategória</th>
                    <th>Aktív</th>
                    <th>Módosítva</th>
                    <th>Eszközök</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            <a target="_blank" href="/product-image/{{ json_decode($product->main_image)->name }}/{{ json_decode($product->main_image)->color }}/{{ json_decode($product->main_image)->extraImage }}">
                                <img src="/product-image/{{ json_decode($product->main_image)->name }}/{{ json_decode($product->main_image)->color }}/{{ json_decode($product->main_image)->extraImage }}">
                            </a>
                        </td>
                        <td>{{ $product->name }}</td>
                        @if ($product->category_name == '')
                            <td></td>
                        @else
                            <td>{{ '(' . $product->category_id . ') ' . $product->category_name }}</td>
                        @endif
                        <td>{{ $product->active }}</td>
                        <td title="{{ date('H:i:s', strtotime($product->updated_at)) }}">{{ date('Y-m-d', strtotime($product->updated_at)) }}</td>
                        <td>
                            <a target="_blank" href="/product/{{ $product->id }}">
                                <button class="product-button button blue" title="Megtekintés">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </a>
                            <a href="/admin/edit-product/{{ $product->id }}">
                                <button class="product-button button blue" title="Szerkesztés">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </a>
                            <a href="/admin/copy-product/{{ $product->id }}">
                                <button class="product-button button blue" title="Másolás">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection