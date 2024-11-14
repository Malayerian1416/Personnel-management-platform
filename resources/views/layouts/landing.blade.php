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

    <link href="{{asset("/css/home/bootstrap.min.css")}}" rel="stylesheet" />
    <link href="{{asset("/css/home/bootstrap-rtl.min.css")}}" rel="stylesheet" />
    <link href="{{asset("/css/home/animate.css")}}" rel="stylesheet" />
    <link href="{{asset("/css/home/Style.css")}}" rel="stylesheet" />
    <link href="{{asset("/css/home/owl-carousel/owl.carousel.min.css")}}" rel="stylesheet" />

    <link href="{{asset("/css/home/sina-nav.min.css")}}" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
</head>
<body>
<div class="loading active">
    <img alt="همیاران شمال شرق" style="width: 200px;height: auto" src="{{asset("images/home/loading.gif")}}">
</div>
<section id="tophead" class="container-fluid p-2 grad-bg">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 col-sm-5 d-lg-block d-none">
                <a href="{{route("Home")}}"><img alt="همیاران شمال شرق" src="{{asset("/images/home-logo.png?v=".time())}}" class="img-fluid" /></a>
            </div>

            <div class="col-lg-3 col-6 mr-auto">
                <div class="d-flex flex-column">
                        <span class="text-white">
                            {{"امروز ".verta()->formatWord("l d F")." ".verta()->format("Y")}}
                        </span>
                    <form class="d-sm-block d-none">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="جستجو .."
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'جستجو ..'">
                            <div class="input-group-prepend">
                                <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

            <div class="col-lg-1 col-6  text-left">
                <img alt="همیاران شمال شرق" src="{{asset("/images/home/flag.png")}}" class="pt-sm-4 flag img-fluid" />
            </div>
        </div>
    </div>
</section>

<header class="nav-container">
    <nav class="sina-nav mobile-sidebar navbar-fixed" data-top="60">
        <div class="container-fluid">

            <div class="extension-nav">
                <ul class="pt-2">
                    <li>
                        <a id="loginbtn" href="{{route("login")}}" class="btn btn-link p-1 ml-2">
                            <img alt="همیاران شمال شرق" src="{{asset("/images/home/Svg/login.svg")}}" /><span class="mr-1">ورود</span>
                        </a>
                    </li>
                    <li>
                        <a id="loginbtn" href="{{ route("introduction") }}" class="btn btn-link p-1 ml-2">
                            <img alt="همیاران شمال شرق" src="{{asset("/images/home/Svg/user.svg")}}" /><span class="mr-1">ثبت نام</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="sina-nav-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="sina-brand pl-4 d-lg-none d-block" href="Index.html">
                    <img alt="همیاران شمال شرق" src="{{asset("/images/logo.png")}}" class="img-fluid" />
                </a>
            </div>

            <div class="collapse navbar-collapse" id="navbar-menu">
                <ul class="sina-menu sina-menu-center">
                    <li><a href="{{route("Home")}}">صفحه اصلی</a></li>
                    <li><a href="#services">خدمات</a></li>
                    <li><a href="{{route("Validation.index")}}">اعتبارسنجی</a></li>
                    <li><a href="#news">اخبار</a></li>
                    <li><a href="#occasions">مناسبت ها</a></li>
                    <li><a href="#related_links">لینک های مرتبط</a></li>
{{--                    <li class="dropdown">--}}
{{--                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">برگه ها<i--}}
{{--                                class="fa  fa-chevron-down pr-2"></i></a>--}}
{{--                        <ul class="dropdown-menu">--}}
{{--                            <li><a href="News_Details.html">جزئیات اخبار</a></li>--}}
{{--                            <li><a href="Component.html">المان ها</a></li>--}}
{{--                            <li><a href="Features.html">ویژگی ها</a></li>--}}
{{--                            <li><a href="Guide.html">راهنمای استفاده</a></li>--}}
{{--                            <li><a href="Price.html">جدول تعرفه</a></li>--}}
{{--                            <li><a href="404.html">صفحه 404</a></li>--}}

{{--                        </ul>--}}
{{--                    </li>--}}

                    <li><a href="{{route("AboutUs")}}">درباره ما</a></li>
                    <li><a href="{{route("ContactUs")}}">ارتباط با ما</a></li>
                </ul>

            </div>
        </div>
    </nav>
</header>
@yield('content')
<footer class="rel-footer">
    <div id="footer-content">
        <div class="container-fluid">
            <div class="row pt-4 mx-2">
                <div class="col-lg-4 col-sm-6 py-3 order-lg-1 order-1 pl-lg-2">
                    <h5 class="IRANSansWeb_Medium">آخرین اخبار : </h5>
                    <ul>
                        @forelse($news as $article)
                            <li>
                                <a href="{{route("NewsDetails",["id" => $article->id])}}" title="">{{$article->title}}</a>
                            </li>
                        @empty
                        @endforelse
                    </ul>
                </div>
                <div class="col-lg-2 col-sm-6 py-3 order-lg-1 order-1">
                    <h5 class="IRANSansWeb_Medium"> لینک های سریع : </h5>
                    <ul>
                        <li>
                            <a href="Ourteam.html" title="">مدیران و معاونان</a>
                        </li>
                        <li>
                            <a href="News.html" title="">اخبار و رویدادها</a>
                        </li>
                        <li>
                            <a href="News_Details.html" title="">جزئیات اخبار </a>
                        </li>
                        <li>
                            <a href="Component.html" title="">المان ها</a>
                        </li>
                        <li>
                            <a href="Features.html" title="">ویژگی ها </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-2 col-sm-6 py-3 order-lg-2  order-3">
                    <h5 class="IRANSansWeb_Medium">پیوندها : </h5>
                    <ul>
                        <li>
                            <a href="Faq.html" title="">سوالات متداول</a>
                        </li>

                        <li>
                            <a href="{{route("login")}}" title="">ورود</a>
                        </li>
                        <li>
                            <a href="{{route("introduction")}}" title="">عضویت</a>
                        </li>
                        <li>
                            <a href="AboutUs.html" title="">درباره ما</a>
                        </li>
                        <li>
                            <a href="ContactUs.html" title="">تماس با ما </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4 col-sm-6 pr-lg-4 py-3 IRANSansWeb_FaNum  order-lg-3  order-2">
                    <h5 class="IRANSansWeb_Medium"> ارتباط با ما : </h5>
                    <p><i class="fas fa-2x fa-map-marker-alt ml-2"></i>{{"آدرس : " . $company->address}}</p>
                    <p><i class="fas fa-2x fa-phone ml-2"></i>{{"تلفن : " . $company->phone}}</p>
                    <p><i class="fas fa-2x fa-fax ml-2"></i>{{"نمابر : " . $company->fax}}</p>
                    <p><i class="fas fa-2x fa-paper-plane ml-2"></i>{{"ایمیل : " . $company->email}}</p>
                </div>


            </div>
        </div>
    </div>
    <div id="footer-copyright">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-12">
                    <a href="https://www.rtl-theme.com/author/shamim831/" class="text-white ">
                        {{"کلیه حقوق این سامانه برای شرکت همیاران شمال شرق محفوظ می باشد." . "(" . verta()->format("F Y") . ")"}}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset("/js/home/jquery-2.0.0.min.js")}}"></script>
    <script src="{{asset("/js/home/bootstrap.min.js")}}"></script>
    <script src="{{asset("/js/home/owl.carousel.js")}}"></script>
    <script src="{{asset("/js/home/custom.js")}}"></script>
    <script src="{{asset("/js/home//sina-nav.min.js")}}"></script>
</footer>
</body>
</html>
