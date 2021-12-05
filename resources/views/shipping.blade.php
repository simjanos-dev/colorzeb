@extends('layouts.app')

@section('head')
    <link href="{{ asset('css/countent_outside_vue_app.css') }}" rel="stylesheet">
@endsection

@section('content')
    <script id="barat_script">var st = document.createElement("script");st.src = "//admin.fogyasztobarat.hu/e-api.js";st.type = "text/javascript";st.setAttribute("data-id", "YQ5L09IZ");st.setAttribute("id", "fbarat-embed");st.setAttribute("data-type", "dm");var s = document.getElementById("barat_script");s.parentNode.insertBefore(st, s);</script>
@endsection
