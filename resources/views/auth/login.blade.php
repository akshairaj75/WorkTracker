@extends('layouts.mainlayout')

@section('extra-css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')

    <form class="login-form" method="POST" action="{{ route('login.post') }}">
        <h1>Login</h1>
        @csrf
        
        <div class="input-container">
            <div class="input-field">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="input-field">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
        </div>

        @if($errors->any())
            <div style="color:red;">
                {{ $errors->first() }}
            </div>
        @endif   

        <button type="submit">Login</button>
        

        <div class="login">
            <p>Don't have an account ?</p>
            <a href="{{ route('register') }}">Register here</a>

        </div>
    </form>
    @if(session('success'))
        <div style="color:green;">
            {{ session('success') }}
        </div>
    @endif

    
    
@endsection