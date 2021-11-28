@extends('layouts.app')

@section('head')
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    <link href="{{ asset('css/image_slide.css') }}" rel="stylesheet">
    <link href="{{ asset('css/product_slide.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div id="welcome-text-box" class="row col-sm-12 col-xl-12 w-100">
        <div id="welcome-text" class="col-sm-12">
            <div id="welcome-text-left">
                <image-slide-component
                    :_images="{{ json_encode($sliderImages) }}"
                ></image-slide-component>
            </div>
            <div id="welcome-text-right">
                <div id="welcome-text-header">
                    <img src="/images/logo-black.png">
                </div>
                <h5>Matricák és sok más kreatív díszlet és ajándék!</h5>
                <div>
                    Nézz szét honlapunkon és válogass termékeink széles választékából, vagy kérj árajánlatot az egyedi ötleteid megvalósításához:
                    <ul>
                        <li>Egyedi céges logó matrica</li>
                        <li>Fali matricák</li>
                        <li>Autós matricák</li>
                        <li>Ajándéktárgyak</li>
                        <li>Papír termékek </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="categories" class="row col-sm-12">
        <div class="home-category-box col-sm-6 col-xl-3 text-center">
            <div class="home-category">
                <div class="home-category-image-box"><img src="/images/wall-stickers.jpg"></div>
                <div class="home-category-name">Matricák</div>
                <div class="home-category-description">
                    Válogass a rengeteg előre megtervezett matricáink közül.<br>
                    <ul>
                        <li><a href="#">Fali matricák</a></li>
                        <li><a href="#">Autós matricák</a></li>
                        <li><a href="#">Matrica csomagok</a></li>
                    </ul>
                </div>
                <a href="#"><button class="button blue"><i class="fa fa-search"></i> Összes matrica</button></a>
            </div>
        </div>
        <div class="home-category-box col-sm-6 col-xl-3 text-center">
            <div class="home-category">
                <div class="home-category-image-box"><img src="/images/truck.jpg"></div>
                <div class="home-category-name">Egyedi matrica árajánlat</div>
                <div class="home-category-description">Kérj ajánlatot egyedi matricára a már meglévő mintádból. Remek lehetőség céges logó matricák készítéséhez.</div>
                <a href="/custom-order"><button class="button blue"><i class="fa fa-comment"></i> Egyedi árajánlat</button></a>
            </div>
        </div>
        <div class="home-category-box col-sm-6 col-xl-3 text-center">
            <div class="home-category">
                <div class="home-category-image-box"><img src="https://www.managementcentre.co.uk/wp-content/uploads/presents-500x500.jpg"></div>
                <div class="home-category-name">Ajándéktárgyak</div>
                <div class="home-category-description"></div>
                <a href="#"><button class="button blue"><i class="fa fa-gift"></i> Ajándéktárgyak</button></a>
            </div>
        </div>
        <div class="home-category-box col-sm-6 col-xl-3 text-center">
            <div class="home-category">
                <div class="home-category-image-box"><img src="/images/custom-boxes.jpg"></div>
                <div class="home-category-name">Papír ajándékok és díszek</div>
                <div class="home-category-description">Válogass papírból készült egyedi termékeink közül: díszdobozok, díszborítékok, adventi naptárak. </div>
                <a href="#"><button class="button blue"><i class="fa fa-sticky-note"></i> Papír díszek</button></a>
            </div>
        </div>
    </div>
    <product-slide-component :_products="{{ $newProducts }}" _title="Új termékek"></product-slide-component>
    <product-slide-component :_products="{{ $discountedProducts }}" _title="Akciós termékek"></product-slide-component>
</div>
@endsection
