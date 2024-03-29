<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Scripts -->
  <script src="{{ asset('js/app.js') }}" defer></script>

  <!-- Fonts -->
  <link rel="dns-prefetch" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

  <!-- Styles -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
  <div id="app">
    <header>
      <a href="{{ url('/') }}">
        {{ config('app.name', 'Laravel') }}
      </a>

      @auth
        <div>
          <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
          >
            {{ __('Log Out') }}
          </a>

          <form id="logout-form"
            action="{{ route('logout') }}"
            method="POST"
            style="display: none;"
          >
            @csrf
          </form>
        </div>
      @endauth
    </header>

    <main>
      @yield('content')
    </main>
  </div>
</body>
</html>
