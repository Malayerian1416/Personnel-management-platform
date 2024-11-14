@extends('layouts.landing')
@section('content')
    <section class="container mt-3">
        <div class="row">
            <div class="col-lg-3 card pt-2 order-lg-1 order-2 text-center position-relative px-0">
                <div class="container">
                    <div class="row Qlink IRANSansWeb_Medium">
                        <div class="col-12 pt-lg-2">
                            <h5 class="IRANSansWeb_Medium bt-color">{{$company->ceo_title}}</h5>
                        </div>
                        <div class="col-12 mb-lg-1 mt-lg-0 my-md-2 my-1">
                            <h5>{{$company->ceo->name}}</h5>
                        </div>
                        <div class="col-12 mb-lg-2 mt-lg-0 my-md-2 my-1 px-2">
                            <h6 class="text-muted">جهت ثبت هرگونه انتقادات و یا پیشنهادات و مشاوره در هر یک از عناوین مربوطه با من در ارتباط باشید</h6>
                        </div>
                        <button class="btn btn-outline-info IRANSansWeb_Medium mx-auto">ارتباط مستقیم</button>
                    </div>
                </div>
                <div class="" style="bottom: 0;overflow: hidden">
                    <img alt="همیاران شمال شرق" src="{{asset("images/home/ceo.png?v=101")}}" class="img-fluid">
                </div>
            </div>
            <div class="col-lg-9 mb-lg-0 mb-3 order-lg-2  order-1">
                <div id="owl-slider" class="owl-carousel">
                    @forelse($news as $article)
                        <div class="item">
                            <a href="{{route("NewsDetails",$article->id)}}">
                                <img alt="اخبار همیاران شمال شرق" src="{{asset("storage/news/$article->id/$article->image")}}" class="img-fluid" />
                                <div class="caption">
                                <span class="SlideCategori position-absolute bg-danger px-3">
                                    {{$article->title}}
                                </span>
                                    <p>{{$article->brief}}</p>
                                </div>
                            </a>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>

    </section>

    <section id="occasions" class="container my-4 text-center">
        <h5 class="IRANSansWeb_Medium  bt-color mb-5">مناسبت ها</h5>
        <div class="mx-auto text-center">
            <img src="{{asset("images/home/head.png")}}" alt="همیاران شمال شرق" />
        </div>
        <div class="grad-bg px-2 py-2 px-lg-4 py-lg-4 rad25">
            <div id="owl-slider2" class="owl-carousel owl-theme">
                @forelse($occasions as $occasion)
                    <div class="item row">
                        @if($occasion->title)
                            <div class="col-12 col-lg-3 align-self-stretch">
                                <div class="text-right">
                                    <img class="mt-5 mr-3" src="{{asset("images/home/double_quotation.svg")}}" alt="همیاران شمال شرق" style="width: 30px;height: auto">
                                    <h4 class="mr-3 mt-3 ml-3 text-justify" style="color: white">
                                        {{$occasion->title}}
                                    </h4>
                                    <h6 class="mt-4 mr-3 ml-3 text-justify" style="color: ghostwhite">{{$occasion->description}}</h6>
                                </div>
                            </div>
                            <div class="col-12 col-lg-9">
                                <img src="{{asset("storage/occasions/$occasion->id/$occasion->image")}}" alt="slide1" class="img-fluid rad25 mb-3 mb-lg-0" style="object-fit: contain" />
                            </div>
                        @else
                            <div class="col-12">
                                <img src="{{asset("storage/occasions/$occasion->id/$occasion->image")}}" alt="slide1" class="img-fluid rad25" style="object-fit: contain" />
                            </div>
                        @endif
                    </div>
                @empty
                @endforelse
            </div>
        </div>
        <div class="mx-auto text-center">
            <img src="{{asset("images/home/foot.png")}}" alt="همیاران شمال شرق" />
        </div>
    </section>

    <section id="services" class="container my-4 text-center">
        <h5 class="IRANSansWeb_Medium bt-color">خدمات ما</h5>
        <p>کلیه خدمات الکترونیکی قابل ارائه در شرکت همیاران شمال شرق </p>
        <div id="owl-Service" class="owl-carousel">
            <div class="card mx-2 my-3">
                <div class="card-body">
                    <a href="#">
                        <img class="rounded-circle" src="{{asset("/images/home/worker.png")}}" alt="">
                    </a>
                    <h6>داشبورد پرسنلی</h6>
                    <p>مدیریت درخواست ها و فیش حقوقی</p>
                    <a href="{{route("login")}}" class="btn btn-grad px-5 text-white mt-2 rad25">مشاهده</a>
                </div>
            </div>
            <div class="card mx-2 my-3">
                <div class="card-body">
                    <a href="#">
                        <img class="rounded-circle" src="{{asset("/images/home/customer-service.png")}}" alt="">
                    </a>
                    <h6>داشبورد مدیریتی</h6>
                    <p>پاسخگویی به درخواست ها توسط کارشناس</p>
                    <a href="{{route("login")}}" class="btn btn-grad px-5 text-white mt-2 rad25">مشاهده</a>
                </div>
            </div>
            <div class="card mx-2 my-3">
                <div class="card-body">
                    <a href="#">
                        <img class="rounded-circle" src="{{asset("/images/home/browser.png")}}" alt="">
                    </a>
                    <h6>ثبت نام</h6>
                    <p>سامانه ثبت نام پرسنل</p>
                    <a href="{{route("introduction")}}" class="btn btn-grad px-5 text-white mt-2 rad25">مشاهده</a>
                </div>
            </div>
            <div class="card mx-2 my-3">
                <div class="card-body">
                    <a href="#">
                        <img class="rounded-circle" src="{{asset("/images/home/search.png")}}" alt="">
                    </a>
                    <h6> پیگیری ثبت نام</h6>
                    <p>پیگیری وضعیت ثبت نام پرسنل</p>
                    <a href="{{route("follow_up_registration")}}" class="btn btn-grad px-5 text-white mt-2 rad25">مشاهده</a>
                </div>
            </div>
        </div>

    </section>

    <section id="news" class="container mt-5">
        <h5 class="IRANSansWeb_Medium text-center bt-color">آخرین اخبارها و رویدادها</h5>
        <div id="owl-topnews" class="mt-3 text-center row">
            @forelse($news as $article)
                <div class="col-12 col-lg-3 align-self-stretch pr-2 pl-2">
                    <div class="card news position-relative h-100">
                        <a target="_blank" href="{{route("NewsDetails",["id" => $article->id])}}" class="relative">
                            <img alt="اخبار همیاران شمال شرق" src="{{asset("storage/news/$article->id/$article->image")}}" class="mb-3 img-fluid rad12" />
                            <div class="covernews d-flex flex-row justify-content-between px-3 text-white IRANSansWeb_Medium ">
                                <span class="bottom_p"><i class="fas fa-clock ml-1"></i>{{verta($article->created_at)->format("Y/m/d")}}</span>
                                <span><i class="fas fa-eye ml-1"></i>{{$article->views." بار"}}</span>
                            </div>
                        </a>

                        <a target="_blank" href="{{route("NewsDetails",["id" => $article->id])}}">
                            <h2 class="text-justify">{{$article->title}}</h2>
                        </a>
                        <p class="text-justify mb-5">{{$article->brief}}</p>
                        <a target="_blank" href="{{route("NewsDetails",["id" => $article->id])}}" class="text-danger IRANSansWeb_Medium mb-3 details">شرح خبر »</a>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
        <div class="w-100 d-flex justify-content-center iranYekanFont p-2 pt-4 p-lg-5 flex-wrap">
            {!! $news->render() !!}
        </div>
    </section>

    <section class="container-fluid py-5">
        <div class="container">
            <div id="foottop" class="row p-3 mx-2 mt-2 grad-bg text-center text-white rad25">
                <div class="col-12 col-lg-7 align-self-stretch d-flex align-items-center justify-content-center justify-content-lg-start">
                    <span class="free-h2 mr-0 mr-lg-5 mb-4 mb-lg-0 support-text">ارتباط مستقیم با واحد حراست</span>
                </div>
                <div class="col-12 col-lg-5">
                    <h6 class="mb-2 IRANSansWeb_Medium">شماره تماس :</h6>
                    <img src="{{asset("images/home/chevron.png")}}" alt="همیاران شمال شرق" />
                    <a href="#" class="rounded-btn text-white text-center support-phone IRANSansWeb_Bold">051-91001666</a>
                    <img src="{{asset("images/home/chevron2.png")}}" alt="همیاران شمال شرق" />
                </div>
            </div>

        </div>
    </section>

    <section id="related_links" class="container text-center pb-5">
        <h5 class="IRANSansWeb_Bold bt-color">لینک های مرتبط</h5>
        <p>جهت مشاهده بر روی هر لینک کلیک کرده و وارد سایت مربوطه شوید</p>
        <div id="owl-province" class="owl-carousel mt-4">
            <div class="card m-2">
                <a href="#">
                    <img src="{{asset("images/home/ostandary_icon.png")}}" class="rad25" alt="همیاران شمال شرق" />
                    <h6>استانداری خراسان رضوی</h6>
                </a>
            </div>
            <div class="card m-2">
                <a href="#">
                    <img src="{{asset("images/home/shahrdary_icon.png")}}" class="rad25" alt="همیاران شمال شرق" />
                    <h6>شهرداری مشهد</h6>
                </a>
            </div>
            <div class="card m-2">
                <a href="#">
                    <img src="{{asset("images/home/hamyari_icon.png")}}" class="rad25" alt="همیاران شمال شرق" />
                    <h6>سازمان همیاری خراسان رضوی</h6>
                </a>
            </div>
            <div class="card m-2">
                <a href="#">
                    <img src="{{asset("images/home/portal_icon.png")}}" class="rad25" alt="همیاران شمال شرق" />
                    <h6>پورتال جامع شهر مشهد</h6>
                </a>
            </div>
            <div class="card m-2">
                <a href="#">
                    <img src="{{asset("images/home/fash_icon.png")}}" class="rad25" alt="همیاران شمال شرق" />
                    <h6>سامانه فاش شهرداری مشهد</h6>
                </a>
            </div>
            <div class="card m-2">
                <a href="#">
                    <img src="{{asset("images/home/jahan_icon.png")}}" class="rad25" alt="همیاران شمال شرق" />
                    <h6>پایگاه اینترنتی جهان شهر</h6>
                </a>
            </div>
        </div>

    </section>
@endsection
