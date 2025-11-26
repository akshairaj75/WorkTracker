<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>@yield('title', 'My Laravel Site')</title>   
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @yield('extra-css')

</head>

<body>
    <header>
        <div class="header-title">
            <h2><a href="/">Work Tracker</a></h2>
        </div>
        <nav>
            <ul class="navlinks">
                <li><a href="/">Home</a></li>
                @guest
                    <div class="log">
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li>  | </li>
                        <li><a href="{{ route('register') }}"> Register</a></li>
                    </div>
                @endguest

                @auth
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" style="background:none;border:none;color:rgb(255, 255, 255);cursor:pointer;">
                                Logout
                            </button>
                        </form>
                    </li>
                @endauth
                
            </ul>                        
        </nav>
    </header>



    <main>
        @yield('content')
    </main>




    <footer>
        <p>© 2025 My Company</p>
    </footer>
</body>
</html>
