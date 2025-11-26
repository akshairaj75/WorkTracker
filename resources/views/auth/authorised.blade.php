@extends('layouts.mainlayout')

@section('extra-css')
<link rel="stylesheet" href="{{asset('css/authorised.css')}}">

@endsection


@section('content')

    <div class="no-access-box">
        <h1>Access Denied !</h1>
        <p>Sorry, this page is restricted to <strong>administrators only</strong>.</p>
        <a href="{{ url('/') }}" class="back-home-btn">Back Home</a>
    </div>
@endsection