@extends("layouts.registration")
@section('content')
    <div id="registration" class="box shadow border-top-0 p-4">
        <div class="row">
            <div class="col-6 d-flex align-items-center justify-content-start">
                <i class="fa fa-arrow-right fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="tooltip" title="بازگشت" v-on:click="WindowRelocate('{{route("login")}}')"></i>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-end">
                <i class="fa fa-question-circle fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="tooltip" title="راهنما"></i>
            </div>
        </div>
        @if(Session::has("follow_up_message"))
            <h4 class="iranyekan text-left pt-2 pb-2 green-color" v-cloak>
                وضعیت ثبت نام
            </h4>
        @else
            <h4 class="iranyekan text-left pt-2 pb-2 green-color" v-cloak>
                پیگیری وضعیت ثبت نام
            </h4>
        @endif
        <div class="col-12 align-self-center text-center">
            <img class="introduction-image" alt="hello" src="{{ asset("/images/registration/follow_up.svg?v=asda") }}" v-cloak/>
        </div>
        <div class="col-12 align-self-center">
            @if(Session::has("follow_up_message"))
                <h4 class="iranyekan text-left pb-2 green-color" v-cloak>
                    وضعیت ثبت نام
                </h4>
                <div class="w-100">
                    <div class="alert alert-success mb-4" role="alert">
                        <h5 class="iransans text-justify" style="line-height: 25px">
                            {{ Session::get("follow_up_message") }}
                        </h5>
                    </div>
                </div>
            @else
                <form id="follow_up_form" method="POST" action="{{ route('registration_result') }}" v-on:submit="login">
                    @csrf
                    <div class="row">
                        <div class="form-group mb-3 col-12">
                            <input type="text" autofocus tabindex="1" class="form-control b-form registration-input-text iranyekan @error('national_code') is-invalid @enderror number_masked" name="national_code" placeholder="کد ملی" data-inputmask="'mask': '9', 'repeat': 10, 'greedy' : false, 'showMaskOnHover' : false" value="{{ $national_code }}">
                            @error('national_code')
                            <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="form-group mb-3 col-12">
                            <input type="text" autofocus tabindex="1" class="form-control b-form registration-input-text iranyekan @error('tracking_code') is-invalid @enderror number_masked" name="tracking_code" data-inputmask="'mask': '9', 'repeat': 10, 'greedy' : false, 'showMaskOnHover' : false" value="{{ old('tracking_code') }}" placeholder="کد رهگیری">
                            @error('tracking_code')
                            <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="form-group mb-3 col-12">
                            <div class="input-group">
                                <span class="input-group-text p-0 captcha-image">
                                    {!! Captcha::img() !!}
                                </span>
                                <input type="text" tabindex="3" class="form-control b-form text-center registration-input-text captcha-input iranyekan font-size-xl number_masked @error('captcha') is-invalid @enderror" data-inputmask="'mask': '9', 'repeat': 6, 'greedy' : false, 'showMaskOnHover' : false" name="captcha" placeholder="کد امنیتی">
                                <span class="input-group-text">
                                    <a href="#" v-on:click="recaptcha"><i class="fa fa-refresh fa-1-6x"></i></a>
                                </span>
                                @error('captcha')
                                <span class="invalid-feedback iranyekan" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </form>
            @endif
            @if(Session::has("follow_up_message"))
                <a role="button" href="{{ route("follow_up_registration") }}" class="btn btn-success form-control b-form iranyekan">
                    <span id="login-button-text">بازگشت</span>
                    <i class="fa fa-arrow-turn-right ms-2 fa-1-4x"></i>
                </a>
            @else
                <button type="submit" form="follow_up_form" class="btn btn-success form-control b-form iranyekan login-button">
                    <span id="login-button-text">جستجو</span>
                    <i id="login-button-icon" class="fa fa-search ms-2 fa-1-4x"></i>
                </button>
            @endif
        </div>
    </div>
@endsection
