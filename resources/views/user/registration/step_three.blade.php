@extends("layouts.registration")
@section('content')
    <div id="registration" class="box shadow border-top-0 p-4">
        <div class="row">
            <div class="col-6 d-flex align-items-center justify-content-start">
                <i class="fa fa-arrow-right fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="tooltip" title="بازگشت" v-on:click="WindowRelocate('{{route("step_two")}}')"></i>
            </div>
        </div>
        <h4 class="iranyekan text-left pb-2 pt-2 green-color">
            گام سوم - فعال سازی تلفن همراه
        </h4>
        <div class="col-12 align-self-center text-center">
            <img class="introduction-image" alt="hello" src="{{ asset("/images/registration/registration_image_3.svg?v=asd") }}"/>
        </div>
        <div class="col-12 align-self-center text-center">
            <form id="registration_form" method="POST" action="{{ route('check_verification_code') }}" v-on:submit="login">
                @csrf
                <h6 class="iranyekan text-muted text-center mb-3 ms-2 me-2">
                    کد فعال سازی 5 رقمی دریافت شده را در کادرهای مشخص شده و از چپ به راست وارد نمایید:
                </h6>
                <div class="w-100 text-center">
                    <div class="w-lg-100 m-auto d-flex flex-row align-items-center justify-content-between activation-code-container">
                        <input type="text" class="form-control ms-0 ms-md-3 registration-input-text iranyekan @error('activation_code') border border-danger @enderror d-inline-block number_masked" tabindex="5" id="verify_num5" name="activation_code[]" data-inputmask="'mask': '9', 'repeat': 1, 'greedy' : false" v-on:input="jump_input($event,5,5,'verify_num')" autocomplete="off" @click.right.prevent @copy.prevent @paste.prevent onfocus="this.select();">
                        <input type="text" class="form-control ms-0 ms-md-3 registration-input-text iranyekan @error('activation_code') border border-danger @enderror d-inline-block number_masked" tabindex="4" id="verify_num4" name="activation_code[]" data-inputmask="'mask': '9', 'repeat': 1, 'greedy' : false" v-on:input="jump_input($event,4,5,'verify_num')" autocomplete="off" @click.right.prevent @copy.prevent @paste.prevent onfocus="this.select();">
                        <input type="text" class="form-control ms-0 ms-md-3 registration-input-text iranyekan @error('activation_code') border border-danger @enderror d-inline-block number_masked" tabindex="3" id="verify_num3" name="activation_code[]" data-inputmask="'mask': '9', 'repeat': 1, 'greedy' : false" v-on:input="jump_input($event,3,5,'verify_num')" autocomplete="off" @click.right.prevent @copy.prevent @paste.prevent onfocus="this.select();">
                        <input type="text" class="form-control ms-0 ms-md-3 registration-input-text iranyekan @error('activation_code') border border-danger @enderror d-inline-block number_masked" tabindex="2" id="verify_num2" name="activation_code[]" data-inputmask="'mask': '9', 'repeat': 1, 'greedy' : false" v-on:input="jump_input($event,2,5,'verify_num')" autocomplete="off" @click.right.prevent @copy.prevent @paste.prevent onfocus="this.select();">
                        <input type="text" autofocus class="form-control registration-input-text iranyekan @error('activation_code') border border-danger @enderror d-inline-block number_masked" tabindex="1" id="verify_num1" name="activation_code[]" data-inputmask="'mask': '9', 'repeat': 1, 'greedy' : false" v-on:input="jump_input($event,1,5,'verify_num')" autocomplete="off" @click.right.prevent @copy.prevent @paste.prevent onfocus="this.select();">
                    </div>
                </div>
            </form>
            <div class="w-100 text-center mt-4 d-flex align-items-center justify-content-center">
                <form id="resend_sms" action="{{ route("resend_verification_code") }}" method="post" class="w-lg-100">
                    @csrf
                    <time-counter-button :css="'btn @if($remain_time["limit"] == 300) btn-outline-danger @else btn-outline-secondary @endif iranyekan form-control font-size-xl'" :form="'resend_sms'" :limit="{{ $remain_time["limit"] ?: 0 }}" :seconds="{{ $remain_time["seconds"] ?: 0 }}"></time-counter-button>
                </form>
            </div>
            <input type="hidden" class="@error('sms_error') is-invalid @enderror">
            @error('activation_code')
            <div class="alert alert-danger iransans" role="alert">
                {{ $message }}
            </div>
            @enderror
            @error('logical_error')
            <div class="alert alert-danger iransans" role="alert">
                مشکلی در سامانه ثبت نام رخ داده است. لطفا چند لحظه بعد مجددا اقدام فرمایید
            </div>
            @enderror
            @error('sent')
            <div class="alert alert-danger iransans" role="alert">
                {{ $message }}
            </div>
            @enderror
            @if($remain_time["limit"] == 300)
                <div class="alert alert-danger iransans" role="alert">
                    به دلیل تعدد درخواست، زمان انتظار به 5 دقیقه افزایش یافت
                </div>
            @endif
            <div class="col-12 text-center pt-2 pb-2 d-flex align-items-center justify-content-center">
                <i class="@if(Route::is("step_one")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                <i class="@if(Route::is("step_two")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                <i class="@if(Route::is("step_three")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                <i class="@if(Route::is("step_four")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                <i class="@if(Route::is("step_five")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                <i class="@if(Route::is("step_six")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
            </div>
            <div class="w-100 text-center mt-3 pr-2 pl-2 pr-md-3 pl-md-3" v-cloak>
                <button tabindex="6" form="registration_form" type="submit" disabled class="btn btn-success form-control iranyekan login-button submit-button">
                    <span id="login-button-text">اعتبارسنجی کد فعال سازی</span>
                    <i id="login-button-icon" class="fa fa-check-double ms-2 fa-1-4x"></i>
                </button>
            </div>
        </div>
    </div>
@endsection
