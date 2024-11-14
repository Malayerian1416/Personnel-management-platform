<!DOCTYPE html>
<html>
<head>
    <title>همیاران شمال شرق</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="content-language" content="fa" />
    <link rel="icon" type="image/x-icon" href="{{ asset("/images/logo.ico?v=2.01") }}">
    <meta name="document-type" content="Public" />
    <meta name="document-rating" content="General" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="همیاران شمال شرق" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('css/app.css?v=').$company_information->app_version }}" rel="stylesheet">
    <link href="{{asset("/css/home/Style.css")}}" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
</head>
<body class="bg_dblue pt-3 pt-lg-5" style="min-height: 100vh">

<section id="app" class="container-fluid p-0 p-lg-5 p-2 pb-lg-0">
    <section class="m-3">
        @yield('content')
    </section>
</section>
<script src="{{ asset('js/app.js?v=').$company_information->app_version }}"></script>
</body>
</html>
