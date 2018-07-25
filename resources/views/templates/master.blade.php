<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Vault Innovation">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">

    @yield('title', '<title>Vault App</title>')

    <!-- Custom styles for this template -->
    <link href="{{ elixir('app.css') }}" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="//oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    @if(env('APP_ENV') === 'production')
        <script data-cfasync="false">
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', '<INSERT_UA>', 'auto');
            ga('send', 'pageview');
        </script>
    @endif

</head>

<body>

<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button class="hamburger hamburger--spring navbar-toggle collapsed hide" type="button" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="hamburger-box">
                  <span class="hamburger-inner"></span>
                </span>
            </button>

            <a class="navbar-brand" href="{{ \Auth::check() ? '/admin/dashboard':'/' }}"><img src="/img/wastemasterlogo.png" class="logo"/></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            @if(\Auth::check())
                <ul class="nav navbar-nav">
                    <li class="{{ request()->is('admin/lead*') ? 'active':'' }}"><a href="{{ route('leads::home') }}">Leads</a></li>
                    <li class="{{ request()->is('admin/client*') ? 'active':'' }}"><a href="{{ route('clients::home') }}">Clients</a></li>
                    <li class="{{ request()->is('admin/bid*') ? 'active':'' }}">
                        <a href="{{ route('bids::home') }}">
                            Bids
                            @if ($newBidCount)
                                <span class="badge success">{{ $newBidCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="{{ request()->is('admin/hauler*') ? 'active':'' }}"><a href="/admin/haulers">Haulers</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="{{ request()->is('admin/areas*') ? 'active':'' }}"><a href="{{ route('areas::home') }}">Areas</a></li>
                    <li class="{{ request()->is('admin/user*') ? 'active':'' }}"><a href="/admin/users">Users</a></li>
                    <li><a href="/logout">Logout</a></li>
                </ul>
            @endif
        </div><!--/.nav-collapse -->
    </div>
</nav>

@include('templates._error_block')

<div class="container">
    @yield('content')
</div>

@yield('modals')
@yield('handlebars')

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->

<script src="{{ elixir('components.js') }}"></script>
<script src="{{ elixir('app.js') }}"></script>

@yield('scripts')

</body>
</html>
