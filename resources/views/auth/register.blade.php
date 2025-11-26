@extends('layouts.mainlayout')


@section('extra-css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

     <form class="login-form" method="POST" action="{{ route('register.post') }}">
        <h1>Register</h1>
        @csrf
        
        <div class="input-container">

            <div class="input-field">
                <label>Name:</label>
                <input type="text" name="username" required>
            </div>

            <div class="input-field">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="input-field">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="input-field">
                <label>Confirm Password:</label>
                <input type="password" name="password_confirmation" required>
            </div>
        </div>

        <button type="submit">Register</button>

        <div class="login">
                <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
        </div>
    </form>


@endsection