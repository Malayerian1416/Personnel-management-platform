<!DOCTYPE html>
<html>
<head>
    @routes
    <title>
        @if(Route::is("introduction")) {{ "توضیحات" }} @endif
        @if(Route::is("step_one")) {{ "گام اول - اعتبارسنجی کد ملی" }} @endif
        @if(Route::is("step_two")) {{ "گام دوم - تلفن همراه" }} @endif
        @if(Route::is("step_three")) {{ "گام سوم - اعتبارسنجی تلفن همراه" }} @endif
        @if(Route::is("step_four")) {{ "گام چهارم - ورود اطلاعات شخصی" }} @endif
        @if(Route::is("step_five")) {{ "گام پنجم - ورود اطلاعات شغلی و بانکی" }} @endif
        @if(Route::is("step_six")) {{ "گام آخر - بارگذاری تصویر مدارک" }} @endif
    </title>
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
<body class="bg_dblue d-flex align-items-center justify-content-center" style="min-height: 100vh">
<section id="app" class="container-fluid p-2">
    <r-loading v-if="show_loading"></r-loading>
    <section class="m-3">
        @yield('content')
    </section>
    @yield('modal')
</section>
<script src="{{ asset('js/app.js?v=').$company_information->app_version }}"></script>
@yield('scripts')
</body>
</html>
