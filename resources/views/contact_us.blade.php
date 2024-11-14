@extends('layouts.landing')
@section('content')
    <section class="container p-0 mt-3">

                <nav aria-label="breadcrumb ">
                    <ol class="breadcrumb mb-0 rad25">
                        <li class="breadcrumb-item"><a href="#">صفحه اصلی</a></li>
                        <li class="breadcrumb-item active" aria-current="page">ارتباط با ما</li>
                    </ol>
                </nav>

    </section>
    <section class="container-fluid mt-3 pb-4 pb-lg-0">

        <div class="container mb-5 p-5 box bg-page">
            <h5 class="mb-4 bt-color">ارتباط با ما</h5>
            <div class="row">
                <div class="col-md-8 pl-md-4">
                    <p><span class="IRANSansWeb_Medium  text-lightgreen"><i
                                class="fas fa-2x fa-map-marker-alt ml-2"></i>آدرس :</span>{{$company->address}}</p>
                    <p><span class="IRANSansWeb_Medium  text-lightgreen"><i class="fas fa-2x fa-phone ml-2"></i>تلفن :
                        </span>{{$company->phone}}</p>
                    <p><span class="IRANSansWeb_Medium  text-lightgreen"><i
                                class="fas fa-2x fa-paper-plane ml-2"></i>ایمیل : </span>{{$company->email}}</p>
                    <p class="mb-3"> جهت ارتباط با ما و ارسال نظـرات و پیشنهادات خود می توانید از فرم زیر استفاده نمایید
                        ..</p>

                    <div class="form-group">
                        <input class="form-control w-75 mb-2" type="email" placeholder="ایمیل معتـبر" />
                        <input class="form-control w-75 mb-2" type="tel" placeholder="شمـاره موبایل" />
                        <input class="form-control w-75 mb-2" type="text" placeholder="موضوع پیام" />
                        <textarea class="form-control area mb-2" cols="60" rows="9" placeholder="متن پیام"
                                  style="height: 150px!important"></textarea>
                    </div>

                    <button class="btn btn btn-teal mb-3 text-white" type="submit">ارسـال پیـام<i
                            class="fa fa-paper-plane fa-2x mr-2"></i></button>

                </div>


            </div>

        </div>

    </section>
@endsection
