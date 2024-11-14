@extends('layouts.landing')
@section('content')
    <section class="container mt-3">
        <div class="row">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb ">
                    <ol class="breadcrumb mb-0 rad25">
                        <li class="breadcrumb-item"><a href="{{route("Home")}}">صفحه اصلی</a></li>
                        <li class="breadcrumb-item active" aria-current="page">اعتبارسنجی نامه های اداری</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    <section class="container-fluid mt-3 pb-5">
        <div class="container px-5 py-3 box  bg-page">
            <div class="row">
                <div id="blog_details">
                    <h1 class="pt-3 IRANSansWeb_Medium">
                        <i class="fa fa-info-circle fa-2x" style="vertical-align: middle"></i>
                        اعتبارسنجی نامه های اداری
                    </h1>
                </div>
                @if(isset($application))
                    {{$application}}
                @else
                    <form class="w-100" action="{{route("Validation.check")}}" method="post">
                        @csrf
                        <div class="col-lg-12">
                            <div class="col-12 my-5">
                                <label class="IRANSansWeb_Medium">شناسه یکتای مندرج برروی نامه را به طور کامل در جعبه متن ذیل وارد نموده و سپس برروی گزینه اعتبارسنجی کلیک فرمایید:</label>
                                <input class="form-control text-center IRANSansWeb_Medium b-form @error('i_number') is-invalid @enderror" type="text" placeholder="شناسه یکتا" name="i_number">
                                @error('i_number')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 text-center pb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search" style="vertical-align: middle"></i>
                                    <span class="IRANSansWeb_Medium">اعتبارسنجی</span>
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
