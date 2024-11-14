@extends("layouts.registration")
@section('content')
    <div id="registration" class="box wider shadow border-top-0 p-4">
        <div class="row">
            <div class="col-6 d-flex align-items-center justify-content-start">
                <i class="fa fa-arrow-right fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="tooltip" title="بازگشت" v-on:click="WindowRelocate('{{route("step_three")}}')"></i>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-end">
                <i class="fa fa-question-circle fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="modal" title="راهنما" data-bs-target="#help_modal"></i>
            </div>
        </div>
        <h4 class="iranyekan text-left pt-2 m-0 green-color">
            گام چهارم - ثبت اطلاعات شخصی
        </h4>
        <div class="col-12 align-self-center text-center">
            <img class="introduction-image" alt="hello" src="{{ asset("/images/registration/registration_image_4.svg") }}"/>
        </div>
        <div class="col-12 align-self-center">
            <form id="registration_form" method="POST" action="{{ route('store_personal_information') }}" v-on:submit="login">
                @csrf
                <div class="row">
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            نام
                            <strong class="red-color">*</strong>
                        </label>
                        <input type="text" class="form-control b-form registration-input-text iranyekan @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name',array_key_exists("first_name",$olds) ? $olds["first_name"] : null) }}">
                        @error('first_name')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            نام خانوادگی
                            <strong class="red-color">*</strong>
                        </label>
                        <input type="text" class="form-control b-form registration-input-text iranyekan @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name',array_key_exists("last_name",$olds) ? $olds["last_name"] : null) }}">
                        @error('last_name')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            نام پدر
                            <strong class="red-color">*</strong>
                        </label>
                        <input type="text" class="form-control b-form registration-input-text iranyekan @error('father_name') is-invalid @enderror" name="father_name" value="{{ old('father_name',array_key_exists("father_name",$olds) ? $olds["father_name"] : null) }}">
                        @error('father_name')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            تاریخ تولد
                            <strong class="red-color">*</strong>
                        </label>
                        <input type="text" class="form-control b-form registration-input-text iranyekan @error('birth_date') is-invalid @enderror date_masked" readonly name="birth_date" value="{{ old('birth_date',array_key_exists("birth_date",$olds) ? $olds["birth_date"] : null) }}">
                        @error('birth_date')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            محل تولد
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control b-form iranyekan @error('birth_city') is-invalid is-invalid-fake @enderror selectpicker-select" title="انتخاب کنید" data-size="10" data-live-search="true" name="birth_city">
                            @forelse($provinces as $province)
                                <optgroup style="font-size: 18px" label="{{ $province->name }}">
                                    @forelse($province->cities as $city)
                                        <option @if($city->name == old("birth_city",array_key_exists("birth_city",$olds) ? $olds["birth_city"] : null)) selected @endif value="{{ $city->name }}">{{ $city->name }}</option>
                                    @empty
                                    @endforelse
                                </optgroup>
                            @empty
                            @endforelse
                        </select>
                        @error('birth_city')
                        <span class="invalid-feedback iranyekan d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            محل صدور
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control b-form iranyekan @error('issue_city') is-invalid is-invalid-fake @enderror selectpicker-select" title="انتخاب کنید" data-size="10" data-live-search="true" name="issue_city">
                            @forelse($provinces as $province)
                                <optgroup style="font-size: 18px" label="{{ $province->name }}">
                                    @forelse($province->cities as $city)
                                        <option @if($city->name == old("issue_city",array_key_exists("issue_city",$olds) ? $olds["issue_city"] : null)) selected @endif value="{{ $city->name }}">{{ $city->name }}</option>
                                    @empty
                                    @endforelse
                                </optgroup>
                            @empty
                            @endforelse
                        </select>
                        @error('issue_city')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            شماره شناسنامه
                            <strong class="red-color">*</strong>
                        </label>
                        <input type="text" class="form-control b-form registration-input-text iranyekan @error('id_number') is-invalid @enderror number_masked" data-inputmask="'mask': '9', 'repeat': 10, 'greedy' : false" name="id_number" value="{{ old('id_number',array_key_exists("id_number",$olds) ? $olds["id_number"] : null) }}">
                        @error('id_number')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            کد ملی
                        </label>
                        <input type="text" class="form-control b-form registration-input-text iranyekan text-muted @error('national_code') is-invalid @enderror" readonly name="national_code" value="{{ session('register.national_code') }}">
                        @error('national_code')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            جنسیت
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control b-form selectpicker-select iranyekan iranyekan @error('gender') is-invalid @enderror" name="gender">
                            <option @if(old("gender",array_key_exists("gender",$olds) ? $olds["gender"] : null) == 'm') selected @endif value="m">مرد</option>
                            <option @if(old("gender",array_key_exists("gender",$olds) ? $olds["gender"] : null) == 'f') selected @endif value="f">زن</option>
                        </select>
                        @error('gender')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            وضعیت تاهل
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control b-form selectpicker-select iranyekan iranyekan @error('marital_status') is-invalid @enderror" name="marital_status">
                            <option @if(old("marital_status",array_key_exists("marital_status",$olds) ? $olds["marital_status"] : null) == 'm') selected @endif value="m">متاهل</option>
                            <option @if(old("marital_status",array_key_exists("marital_status",$olds) ? $olds["marital_status"] : null) == 's') selected @endif value="s">مجرد</option>
                        </select>
                        @error('marital_status')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            خدمت سربازی
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control b-form selectpicker-select iranyekan iranyekan @error('military_status') is-invalid @enderror" name="military_status">
                            <option @if(old("military_status",array_key_exists("military_status",$olds) ? $olds["military_status"] : null) == 'h') selected @endif value="h">کارت پایان خدمت</option>
                            <option @if(old("military_status",array_key_exists("military_status",$olds) ? $olds["military_status"] : null) == 'e') selected @endif value="e">کارت معافیت</option>
                            <option @if(old("military_status",array_key_exists("military_status",$olds) ? $olds["military_status"] : null) == 'n') selected @endif value="n">در حال تحصیل</option>
                        </select>
                        @error('military_status')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            تحصیلات
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control b-form selectpicker-select iranyekan iranyekan @error('education') is-invalid @enderror" name="education">
                            <option @if(old("education",array_key_exists("education",$olds) ? $olds["education"] : null) == 'در حال تحصیل') selected @endif value="در حال تحصیل">در حال تحصیل</option>
                            <option @if(old("education",array_key_exists("education",$olds) ? $olds["education"] : null) == 'زیر دیپلم و دیپلم') selected @endif value="زیر دیپلم و دیپلم">زیر دیپلم و دیپلم</option>
                            <option @if(old("education",array_key_exists("education",$olds) ? $olds["education"] : null) == 'کاردانی') selected @endif value="کاردانی">کاردانی</option>
                            <option @if(old("education",array_key_exists("education",$olds) ? $olds["education"] : null) == 'کارشناسی') selected @endif value="کارشناسی">کارشناسی</option>
                            <option @if(old("education",array_key_exists("education",$olds) ? $olds["education"] : null) == 'کارشناسی ارشد') selected @endif value="کارشناسی ارشد">کارشناسی ارشد</option>
                            <option @if(old("education",array_key_exists("education",$olds) ? $olds["education"] : null) == 'دکتری') selected @endif value="دکتری">دکتری</option>
                        </select>
                        @error('education')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">تعداد فرزندان</label>
                        <input type="number" class="form-control b-form registration-input-text iranyekan @error('children_count') is-invalid @enderror" name="children_count" value="{{ old('children_count',array_key_exists("children_count",$olds) ? $olds["children_count"] : null) }}">
                        @error('children_count')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            فرزندان مشمول حق اولاد
                            <strong class="red-color">*</strong>
                        </label>
                        <input type="number" class="form-control b-form registration-input-text iranyekan @error('included_children_count') is-invalid @enderror" name="included_children_count" value="{{ old('included_children_count',array_key_exists("included_children_count",$olds) ? $olds["included_children_count"] : null) }}">
                        @error('included_children_count')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">آدرس پست الکترونیکی</label>
                        <input type="email" class="form-control b-form registration-input-text iranyekan @error('email') is-invalid @enderror" name="email" value="{{ old('email',array_key_exists("email",$olds) ? $olds["email"] : null) }}">
                        @error('email')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">شماره موبایل</label>
                        <input type="text" class="form-control b-form registration-input-text iranyekan text-muted @error('mobile') is-invalid @enderror" readonly name="mobile" value="{{ session('register.mobile') }}">
                        @error('mobile')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">تلفن ثابت</label>
                        <input type="text" class="form-control b-form registration-input-text iranyekan @error('phone') is-invalid @enderror number_masked" data-inputmask="'mask': '9', 'repeat': 11, 'greedy' : false" name="phone" value="{{ old('phone',array_key_exists("phone",$olds) ? $olds["phone"] : null) }}">
                        @error('phone')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-1 col-12 col-lg-4">
                        <label class="form-label iransans text-muted">
                            آدرس منزل
                            <strong class="red-color">*</strong>
                        </label>
                        <input type="text" class="form-control b-form registration-input-text iranyekan @error('address') is-invalid @enderror" name="address" value="{{ old('address',array_key_exists("address",$olds) ? $olds["address"] : null) }}">
                        @error('address')
                        <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
                @error('logical_error')
                <div class="alert alert-danger iransans mt-3" role="alert">
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
                    <span id="login-button-text">ذخیره اطلاعات شخصی</span>
                    <i id="login-button-icon" class="fa fa-save ms-2 fa-1-4x"></i>
                </button>
            </form>
        </div>
    </div>
@endsection
@section('modal')
    <help-modal :title="'راهنمای درج اطلاعات شخصی'" :modal_id="'help_modal'" v-cloak>
        <ul class="iranyekan free-ul">
            <li>
                <p class="free-p text-justify pe-3">
                    درج اطلاعات مربوط به عناوینی که ستاره دار می باشند، الزامی می باشد.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    در وارد نمودن اطلاعات صحیح دقت فرمایید؛ در صورت وجود هرگونه مغایرت در اطلاعات ذخیره شده، ثبت نام شما تایید نخواهد گردید.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    در صورت انتخاب جنسیت زن، در قسمت بارگذاری تصویر مدارک، جعبه انتخاب تصویر مدرک دوره ضروری خدمت سربازی حذف خواهد شد.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    درج آدرس پست الکترونیکی الزامی نمی باشد؛ اما در صورت وارد نمودن باید معتبر باشد.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    تعداد فرزندان به معنی تعداد کل فرزندان شما بوده و وارد کردن آن اختیاری می باشد؛ اما تعداد فرزندان مشمول حق اولاد الزامی بوده و در صورت نداشتن عدد 0 وارد گردد.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    در صورت انتخاب عنوان در حال تحصیل در قسمت تحصیلات، می باید تصویر مدرک گواهی اشتغال به تحصیل را بارگذاری نمایید.
                </p>
            </li>
        </ul>
    </help-modal>
@endsection
