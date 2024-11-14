@extends("layouts.registration")
@section('content')
    <div id="registration" class="box shadow border-top-0 p-4">
        <div class="row">
            <div class="col-6 d-flex align-items-center justify-content-start">
                <i class="fa fa-arrow-right fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="tooltip" title="بازگشت" v-on:click="WindowRelocate('{{route("login")}}')"></i>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-end">
                <i class="fa fa-question-circle fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="modal" title="راهنما" data-bs-target="#help_modal"></i>
            </div>
        </div>
        <h4 class="iranyekan text-left pb-2 pt-2 green-color">
            انتخاب شیوه بازیابی گذرواژه
        </h4>
        <div class="col-12 align-self-center text-center">
            <img class="introduction-image" alt="hello" src="{{ asset("/images/authentication/password-forget.svg") }}"/>
        </div>
        <div class="col-12 align-self-center">
            <form id="registration_form" class="mt-3" method="POST" action="{{ route('password.email') }}" v-on:submit="login">
                @csrf
                <div class="row">
                    <div class="mb-3 col-12">
                        <input checked class="form-check-input align-middle" type="radio" value="email" name="via" id="withEmail" v-on:click="withEmail=false;withSMS=true">
                        <label for="withEmail">بازیابی گذرواژه با ایمیل</label>
                        <label for="email"></label>
                        <input :disabled="withEmail" id="email" type="text" autofocus tabindex="1" autocomplete="off" class="form-control b-form registration-input-text text-center iranyekan @error('national_code') is-invalid @enderror number_masked" name="email" value="{{ old('email') }}" placeholder="ایمیل">
                        @error('email')
                        <span class="invalid-feedback iranyekan" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-12">
                        <input class="form-check-input align-middle" type="radio" name="via" value="sms" id="withSMS" v-on:click="withEmail=true;withSMS=false">
                        <label for="withSMS">بازیابی گذرواژه با پیامک</label>
                        <label for="mobile"></label>
                        <input :disabled="withSMS" id="mobile" type="text" autofocus tabindex="1" autocomplete="off" class="form-control b-form registration-input-text text-center iranyekan @error('national_code') is-invalid @enderror number_masked" name="mobile" data-inputmask="'mask': '9', 'repeat': 11, 'greedy' : false" value="{{ old('mobile') }}" placeholder="شماره تلفن همراه">
                        @error('mobile')
                        <span class="invalid-feedback iranyekan" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-1 col-12">
                        <div class="input-group mb-3">
                            <span class="input-group-text p-0 captcha-image">
                                {!! Captcha::img() !!}
                            </span>
                            <input type="text" tabindex="2" class="form-control text-center b-form registration-input-text captcha-input iranyekan font-size-xl number_masked @error('captcha') is-invalid @enderror" data-inputmask="'mask': '9', 'repeat': 6, 'greedy' : false" placeholder="کد امنیتی" name="captcha" value="{{old('captcha')}}">
                            <span class="input-group-text">
                                <a role="button" v-on:click="recaptcha"><i class="fa fa-refresh fa-1-6x vertical-middle"></i></a>
                            </span>
                        </div>
                        @error('captcha')
                        <span class="w-100 invalid-feedback d-block iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
            </form>
            @error("not_found")
            <div class="alert alert-danger iransans" role="alert">
                {{ $message }}
            </div>
            @enderror
            @error("via")
            <div class="alert alert-danger iransans" role="alert">
                {{ $message }}
            </div>
            @enderror
            <button class="btn btn-success iranyekan login-button w-100 mt-3" form="registration_form" type="submit">
                <span id="login-button-text">دریافت لینک</span>
                <i id="login-button-icon" class="fa fa-link ms-2 fa-1-4x"></i>
            </button>
        </div>
    </div>
@endsection
@section('modal')
    <help-modal :title="'راهنمای اعتبارسنجی کد ملی'" :modal_id="'help_modal'" v-cloak>
        <ul class="iranyekan free-ul">
            <li>
                <p class="free-p text-justify pe-3">
                    اعتبارسنجی کد ملی، به مظور جستجو و مطابقت آن در لیست پرسنل سازمان طرف قرارداد که از قبل در سیستم ثبت شده است، صورت می پذیرد.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    در صورت مطابقت کد ملی شما و لیست پرسنل ثبت شده سازمان طرف قرارداد، ادامه ثبت نام شما میسر خواهد بود؛ در غیراینصورت ادامه ثبت نام امکانپذیر نمی باشد.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    در صورت اطمینان از عضویت خود در لیست پرسنل سازمان مورد نظر و دریافت پیغام خطای «کد ملی وارد شده در سامانه ثبت نام موجود نمی باشد»، با فشردن دکمه ثبت کد ملی، نسبت به ارسال اطلاعات خود به قسمت پشتیبانی شرکت اقدام و از طریق پیامک منتظر دریافت تایید امکان ثبت نام خود بمانید. کارشناسان شرکت پس از بررسی و مطابقت مجدد، کد ملی شما را به لیست اضافه خواهند نمود.
                </p>
            </li>
        </ul>
    </help-modal>
    <div class="modal fade" id="register_employee" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ثبت کد ملی</h5>
                </div>
                <div class="modal-body">
                    <h6 class="iranyekan ps-3 pe-3 text-justify">لطفا کلیه اطلاعات زیر را به طور صحیح وارد نموده و طی روزهای آینده از طریق پیامک منتظر اعلام نظر کارشناس بمانید:</h6>
                    <div class="row ps-3 pe-3 employee_information">
                        <div class="col-12 mb-3">
                            <label class="iransans form-label">
                                نام و نام خانوادگی
                            </label>
                            <input class="form-control b-form iransans text-center" type="text" v-model="name">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="iransans form-label">
                                کد ملی
                            </label>
                            <input class="form-control b-form iransans text-center number_masked" type="text" v-model="national_code" data-inputmask="'mask': '9', 'repeat': 10, 'greedy' : false">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="iransans form-label">
                                نام سازمان مربوطه
                            </label>
                            <select class="form-control b-form iransans selectpicker-select" title="انتخاب کنید" data-size="10" data-live-search="true" v-model="organization">
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="iransans form-label">
                                تلفن همراه
                            </label>
                            <input class="form-control b-form iransans text-center number_masked" type="text" v-model="mobile" data-inputmask="'mask': '9', 'repeat': 11, 'greedy' : false">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="iransans form-label">
                                توضیحات (در صورت نیاز)
                            </label>
                            <textarea class="form-control b-form iransans" v-model="description"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click="RegisterEmployee">ارسال اطلاعات</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                </div>
            </div>
        </div>
    </div>
@endsection
