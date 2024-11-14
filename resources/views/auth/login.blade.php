@extends("layouts.authentication")
@section('content')
    <div id="login" class="box shadow text-center border-top-0 p-4">
        <img alt="همیاران شمال شرق" src="{{asset("/images/home/Svg/login.svg")}}" style="width:55px" />
        <form id="login_form" method="POST" action="{{ route('login') }}" v-on:submit="login">
            @csrf
            <h4 class="IRANSansWeb_Bold text-grad pt-3">ورود به داشبورد </h4>
            <p class="mb-3">جهت ورود ، نام کاربری و گذروازه خود را وراد نمایید</p>
            <input class="form-control mb-3 @error('username') is-invalid @enderror"  tabindex="1" name="username" type="text" placeholder="نام کاربری" />
            <input class="form-control mb-3 @error('password') is-invalid @enderror" tabindex="2" name="password" type="password" placeholder="گذرواژه" />
            <div class="input-group mb-3">
                <div class="input-group-text p-0">
                    <span class="input-group-text p-0 captcha-image">
                        {!! Captcha::img() !!}
                    </span>
                </div>
                <input type="text" tabindex="3" class="form-control text-center captcha-input no-radius number_masked @error('captcha') is-invalid @enderror" data-inputmask="'mask': '9', 'repeat': 6, 'greedy' : false, 'showMaskOnHover' : false" name="captcha" placeholder="کد امنیتی">
                <div class="input-group-text">
                    <a class="d-flex align-items-center justify-content-center" role="button" v-on:click="recaptcha"><i class="fa fa-refresh fa-1-6x" data-toggle="tooltip" data- title="بازنشانی کد امنیتی"></i></a>
                </div>
                <div class="w-100 text-start">
                    <a href="{{ route("password.request") }}">گذرواژه خود را فراموش کرده اید؟</a>
                </div>
                @error('captcha')
                <div class="alert alert-danger invalid-feedback" role="alert">
                    <h6 class="iransans text-justify mb-0" style="line-height: 25px">
                        {{ $message }}
                    </h6>
                </div>
                @enderror
            </div>
            <div class="mb-3">
                <h6 class="iransans border p-3 d-flex justify-content-between align-items-center flex-wrap" style="border-radius: 6px">
                    <a href="{{ route("introduction") }}">
                        ثبت نام در سامانه
                    </a>
                    <a href="{{ route("follow_up_registration") }}">
                        پیگیری وضعیت ثبت نام
                    </a>
                </h6>
            </div>
            @error('baned')
            <div class="alert alert-danger mb-4" role="alert">
                <h6 class="iransans text-justify mb-0" style="line-height: 25px">
                    {{ $message }}
                </h6>
            </div>
            @enderror
            @error('login_failed')
            <div class="alert alert-danger mb-4" role="alert">
                <h6 class="iransans text-justify mb-0" style="line-height: 25px">
                    {{ $message }}
                </h6>
            </div>
            @enderror
            <button class="btn btn-grad btn-block text-white mb-3 py-3 w-100 fs-4 login-button over" tabindex="4" type="submit" style="height: 40px">
                <span id="login-button-text">ورود به داشبورد</span>
                <i id="login-button-icon" class="fa fa-chevron-left ms-2"></i>
            </button>
        </form>
    </div>
@endsection
