@extends("layouts.registration")
@section('content')
    <div id="registration" class="box wider shadow border-top-0 p-4">
        <div class="row">
            <div class="col-6 d-flex align-items-center justify-content-start">
                <i class="fa fa-arrow-right fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="tooltip" title="بازگشت" v-on:click="WindowRelocate('{{route("step_four")}}')"></i>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-end">
                <i class="fa fa-question-circle fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="modal" title="راهنما" data-bs-target="#help_modal"></i>
            </div>
        </div>
        <h4 class="iranyekan text-left pb-2 pt-2 green-color">
            گام پنجم - ثبت اطلاعات شغلی و بانکی
        </h4>
        <div class="col-12 align-self-center text-center">
            <img class="introduction-image" alt="hello" src="{{ asset("/images/registration/registration_image_5.svg") }}"/>
        </div>
        <div class="col-12 align-self-center">
        <form id="registration_form" method="POST" action="{{ route('store_job_information') }}" v-on:submit="login">
            @csrf
            <div class="row">
                <div class="form-group mb-3 col-12 col-lg-4">
                    <label class="form-label iransans text-muted">سازمان محل خدمت</label>
                    <input type="text" class="form-control b-form registration-input-text iranyekan text-muted @error('contract_subset_id') is-invalid @enderror" readonly name="contract_subset_id" value="{{ $organization }}">
                    @error('contract_id')
                    <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12 col-lg-4">
                    <label class="form-label iransans text-muted">
                        محل استقرار
                        <strong class="red-color">*</strong>
                    </label>
                    <input type="text" class="form-control b-form registration-input-text iranyekan @error('job_seating') is-invalid @enderror" name="job_seating" value="{{ old('job_seating',array_key_exists("job_seating",$olds) ? $olds["job_seating"] : null) }}">
                    @error('job_seating')
                    <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12 col-lg-4">
                    <label class="form-label iransans text-muted">
                        عنوان شغل
                        <strong class="red-color">*</strong>
                    </label>
                    <input type="text" class="form-control b-form registration-input-text iranyekan @error('job_title') is-invalid @enderror" name="job_title" value="{{ old('job_title',array_key_exists("job_title",$olds) ? $olds["job_title"] : null) }}">
                    @error('job_title')
                    <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12 col-lg-4">
                    <label class="form-label iransans text-muted">
                        نام بانک
                        <strong class="red-color">*</strong>
                    </label>
                    <select class="form-control b-form iranyekan iranyekan @error('bank_name') is-invalid @enderror selectpicker-select" title="انتخاب کنید" data-size="10" data-live-search="true" name="bank_name">
                        @forelse($banks as $bank)
                            <option @if($bank->name == old("bank_name",array_key_exists("bank_name",$olds) ? $olds["bank_name"] : null)) selected @endif value="{{ $bank->name }}">{{ $bank->name }}</option>
                        @empty
                        @endforelse
                    </select>
                    @error('bank_name')
                    <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12 col-lg-4">
                    <label class="form-label iransans text-muted">
                        شماره حساب
                        <strong class="red-color">*</strong>
                    </label>
                    <input type="text" class="form-control b-form registration-input-text iranyekan @error('bank_account') is-invalid @enderror number_masked" style="direction: ltr" autocomplete="off" data-inputmask="'mask':'9','repeat':20,'greedy':false" name="bank_account" value="{{ old('bank_account',array_key_exists("bank_account",$olds) ? $olds["bank_account"] : null) }}">
                    @error('bank_account')
                    <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12 col-lg-4">
                    <label class="form-label iransans text-muted">شماره کارت</label>
                    <input type="text" class="form-control b-form registration-input-text iranyekan @error('credit_card') is-invalid @enderror number_masked" style="direction: ltr" autocomplete="off" data-inputmask="'mask':'9999 9999 9999 9999','placeholder': ''" name="credit_card" value="{{ old('credit_card',array_key_exists("credit_card",$olds) ? $olds["credit_card"] : null) }}">
                    @error('credit_card')
                    <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12 col-lg-4">
                    <label class="form-label iransans text-muted">
                        شماره شبا
                        <strong class="red-color">*</strong>
                    </label>
                    <input type="text" class="form-control b-form registration-input-text iranyekan @error('sheba_number') is-invalid @enderror number_masked" style="direction: ltr" autocomplete="off" data-inputmask="'mask':'IR 99 999 9999999999999999999','placeholder': ''" name="sheba_number" value="{{ old('sheba_number',array_key_exists("sheba_number",$olds) ? $olds["sheba_number"] : null) }}">
                    @error('sheba_number')
                    <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12 col-lg-4">
                    <label class="form-label iransans text-muted">شماره بیمه</label>
                    <input type="text" class="form-control b-form registration-input-text iranyekan @error('insurance_number') is-invalid @enderror number_masked" autocomplete="off" data-inputmask="'mask':'9','repeat' : 15" name="insurance_number" value="{{ old('insurance_number',array_key_exists("insurance_number",$olds) ? $olds["insurance_number"] : null) }}">
                    @error('insurance_number')
                    <span class="invalid-feedback iranyekan" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12 col-lg-4">
                    <label class="form-label iransans text-muted">سابقه بیمه (روز)</label>
                    <input type="text" class="form-control b-form registration-input-text iranyekan @error('insurance_days') is-invalid @enderror number_masked" name="insurance_days" data-inputmask="'mask':'9','repeat' : 4" value="{{ old("insurance_days",array_key_exists("insurance_days",$olds) ? $olds["insurance_days"] : null) }}">
                    @error('insurance_days')
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
                <span id="login-button-text">ذخیره اطلاعات شغلی</span>
                <i id="login-button-icon" class="fa fa-save ms-2 fa-1-4x"></i>
            </button>
        </form>
    </div>
    </div>
@endsection
@section('modal')
    <help-modal :title="'راهنمای ثبت اطلاعات شغلی و حساب بانکی'" :modal_id="'help_modal'" v-cloak>
        <ul class="iranyekan free-ul">
            <li>
                <p class="free-p text-justify pe-3">
                    درج اطلاعات مربوط به عناوینی که ستاره دار می باشند، الزامی می باشد.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    قبل از وارد نمودن محل استقرار و عنوان شغل از صحت آن مطمئن شوید؛ در صورت درج اطلاعات اشتباه ثبت نام شما تایید نخواهد گردید.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    در صورت وارد نمودن سابقه بیمه، در مرحله بعد بارگذاری فایل تصویر سابقه بیمه دریافتی از سازمان تامین اجتماعی الزامی می باشد.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    شماره شبای حساب شما یک عدد 24 رقمی می باشد که می باید به صورت کامل وارد گردد.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    وارد کردن شماره کارت اختیاری می باشد.
                </p>
            </li>
        </ul>
    </help-modal>
@endsection
