@extends("layouts.registration")
@section('content')
    <div id="registration" class="box shadow border-top-0 p-4">
        <div class="row">
            <div class="col-6 d-flex align-items-center justify-content-start">
                <i class="fa fa-arrow-right fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="tooltip" title="بازگشت" v-on:click="WindowRelocate('{{route("step_one")}}')"></i>
            </div>
        </div>
        <h4 class="iranyekan text-left pb-2 pt-2 green-color">
            گام دوم - شماره تلفن همراه
        </h4>
        <div class="col-12 align-self-center text-center">
            <img class="introduction-image" alt="hello" src="{{ asset("/images/registration/registration_image_2.svg?v=jkj") }}"/>
        </div>
        <div class="col-12 align-self-center">
            <form id="registration_form" class="mt-3" method="POST" action="{{ route('send_verification_code') }}" v-on:submit="login">
                @csrf
                <h6 class="iranyekan text-muted text-justify mb-3">
                    <span style="color: #0b5ed7">{{$employee_name}}</span>
                    عزیز؛ بنا به دلایل امنیتی لطفا فقط شماره تلفن همراه متعلق به خود را وارد نمایید و از درج شماره تلفن همراه متعلق به دیگران حتی آشنایان نزدیک پرهیز نمایید:
                </h6>
                <div class="form-row">
                    <div class="form-group mb-3 col-12">
                        <input type="text" autocomplete="off" class="form-control b-form registration-input-text iranyekan @error('mobile') is-invalid @enderror number_masked" name="mobile" data-inputmask="'mask': '9', 'repeat': 11, 'greedy' : false" value="{{ old('mobile') }}" placeholder="تلفن همراه">
                        @error('mobile')
                        <span class="invalid-feedback iranyekan d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
                <input type="hidden" class="@error('not_found') is-invalid @enderror">
                <input type="hidden" class="@error('sms_error') is-invalid @enderror">
                @error('sms_fail')
                <div class="alert alert-danger iransans" role="alert">
                    {{ $message }}
                </div>
                @enderror
                @error('duplicated')
                <div class="alert alert-danger iransans" role="alert">
                    {{ $message }}
                </div>
                @enderror
                @error("logical_error")
                <div class="alert alert-danger iransans" role="alert">
                    مشکلی در سامانه ثبت نام رخ داده است. لطفا چند لحظه بعد مجددا اقدام فرمایید
                </div>
                @enderror
                <div class="col-12 text-center pt-2 pb-2 d-flex align-items-center justify-content-center">
                    <i class="@if(Route::is("step_one")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                    <i class="@if(Route::is("step_two")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                    <i class="@if(Route::is("step_three")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                    <i class="@if(Route::is("step_four")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                    <i class="@if(Route::is("step_five")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                    <i class="@if(Route::is("step_six")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                </div>
                <button type="submit" form="registration_form" class="btn btn-success form-control iranyekan login-button">
                    <span id="login-button-text">ارسال کد فعال سازی</span>
                    <i id="login-button-icon" class="fa fa-sms ms-2 fa-1-4x"></i>
                </button>
            </form>
        </div>
@endsection
@section('buttons')
@endsection
