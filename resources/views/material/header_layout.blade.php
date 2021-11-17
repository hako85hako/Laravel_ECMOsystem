	<head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title> {{ __('App Name') }}</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ asset('css/public.css') }}">
        <link rel="stylesheet" href="{{ asset('css/headerNavButton.css') }}">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
        <!-- Scripts -->
    	<script src="{{ asset('js/app.js') }}" defer></script>

	</head>
<body>
    <div class="container-fluid">
    	<div class="row" id="header">
    		<div class="col-md-3 col-sm-2 col-xs-2"></div>
			<div class="col-md-6 col-sm-8 col-xs-8 text-center">
            	<a href="{{ url('/') }}" id="appTitle">
    	            {{ __('App Name') }}
    	        </a>
	        </div>
			<div class="col-md-3 col-sm-2 col-xs-2">
    			<div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->

                    <ul class="navbar-nav mr-auto">


                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto" id="headerUl">
            	@guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link btn" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link btn" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
    	        @else
                        <li class="nav-item dropdown hidden-sm hidden-xs">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle btn btn-default" href="#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre style="color:#D9D9D9">
                                {{ Auth::user()->name }}
                            </a>

                            <div id="dropdownMenu" class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a id="dropdown-item" class="dropdown-item btn" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
               @endguest
                    </ul>
    	        </div>
			</div>
    	</div>
    </div>
       	 @yield('content')
</body>