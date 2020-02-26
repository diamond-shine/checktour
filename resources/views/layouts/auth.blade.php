<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>{{ config('app.name') }}</title>

        <meta http-equiv="cleartype" content="on" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="apple-mobile-web-app-title" content="." />

        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,700&amp;subset=cyrillic" rel="stylesheet">
        <link rel="stylesheet" href="{{ static_asset_rev('css/app.css') }}">
    </head>
    <body class="t-light">
        @yield('content')
    </body>
</html>
