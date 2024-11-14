@extends('layouts.landing')
@section('content')
    <section class="container mt-3">
        <div class="row">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb ">
                    <ol class="breadcrumb mb-0 rad25">
                        <li class="breadcrumb-item"><a href="#">صفحه اصلی</a></li>
                        <li class="breadcrumb-item active" aria-current="page">درباره ما</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <section class="container-fluid mt-3  pb-5">
        <div class="container p-md-5 box p-4 bg-page">
            <div class="row" style="min-height: 500px">
                <div class="col-lg-12">
                    <h5 class="mb-4 bt-color IRANSansWeb_Medium">درباره ما</h5>
                    <p class="text-justify">
                        {{$company->about_us}}
                    </p>

                </div>

            </div>
        </div>
    </section>
@endsection
