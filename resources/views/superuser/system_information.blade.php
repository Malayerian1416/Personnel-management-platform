@extends('superuser.superuser_dashboard')
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i v-if="sidebar_toggle" class="sidebar-toggle fa fa-bars fa-1-6x me-4" v-cloak
               v-on:click="toggle_sidebar('open')"></i>
            <h5 class="iransans d-inline-block m-0 d-flex align-items-center">
                اطلاعات سیستم
                <span class="vertical-middle ms-2">(مشاهده ، ویرایش)</span>
            </h5>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content w-100">
        <form id="main_submit_form" class="p-3"
              action="{{ route("SystemInformation.update",$company_information->id) }}" method="POST"
              v-on:submit="submit_form">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        نام
                    </label>
                    <input class="form-control text-center iranyekan @error('name') is-invalid @enderror" type="text"
                           name="name" value="{{ $company_information->name }}">
                    @error('name')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        نام مختصر
                    </label>
                    <input class="form-control text-center iranyekan @error('short_name') is-invalid @enderror"
                           type="text" name="short_name" value="{{ $company_information->short_name }}">
                    @error('short_name')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        توضیحات
                    </label>
                    <input class="form-control text-center iranyekan @error('description') is-invalid @enderror"
                           type="text" name="description" value="{{ $company_information->description }}">
                    @error('description')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        شماره ثبت
                    </label>
                    <input class="form-control text-center iranyekan @error('registration_number') is-invalid @enderror"
                           type="text" name="registration_number"
                           value="{{ $company_information->registration_number }}">
                    @error('registration_number')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        شناسه ملی
                    </label>
                    <input class="form-control text-center iranyekan @error('national_id') is-invalid @enderror"
                           type="text" name="national_id" value="{{ $company_information->national_id }}">
                    @error('national_id')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        آدرس وبسایت
                    </label>
                    <input class="form-control text-center iranyekan @error('website') is-invalid @enderror" type="text"
                           name="website" value="{{ $company_information->website }}">
                    @error('website')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        مدیرعامل
                    </label>
                    <select class="form-control text-center selectpicker-select iranyekan @error('ceo_user_id') is-invalid @enderror"
                            data-live-search="true" name="ceo_user_id" title="انتخاب کنید" data-size="20">
                        @forelse($users as $user)
                            <option @if($user->id == $company_information->ceo_user_id) selected
                                    @endif value="{{ $user->id }}">{{ $user->name }}</option>
                        @empty
                        @endforelse
                    </select>
                    @error('ceo_user_id')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        عنوان مدیرعامل
                    </label>
                    <input class="form-control text-center iranyekan @error('ceo_title') is-invalid @enderror"
                           type="text" name="ceo_title" value="{{ $company_information->ceo_title }}">
                    @error('ceo_title')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        معاونت
                    </label>
                    <select class="form-control text-center selectpicker-select iranyekan @error('substitute_user_id') is-invalid @enderror"
                            data-live-search="true" name="substitute_user_id" title="انتخاب کنید" data-size="20">
                        @forelse($users as $user)
                            <option @if($user->id == $company_information->substitute_user_id) selected
                                    @endif value="{{ $user->id }}">{{ $user->name }}</option>
                        @empty
                        @endforelse
                    </select>
                    @error('substitute_user_id')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        عنوان معاونت
                    </label>
                    <input class="form-control text-center iranyekan @error('substitute_title') is-invalid @enderror"
                           type="text" name="substitute_title" value="{{ $company_information->substitute_title }}">
                    @error('substitute_title')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        آدرس
                    </label>
                    <input class="form-control text-center iranyekan @error('address') is-invalid @enderror" type="text"
                           name="address" value="{{ $company_information->address }}">
                    @error('address')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        تلفن
                    </label>
                    <input class="form-control text-center iranyekan @error('phone') is-invalid @enderror" type="text"
                           name="phone" value="{{ $company_information->phone }}">
                    @error('phone')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        فکس
                    </label>
                    <input class="form-control text-center iranyekan @error('fax') is-invalid @enderror" type="text"
                           name="fax" value="{{ $company_information->fax }}">
                    @error('fax')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        نسخه نرم افزار
                    </label>
                    <input class="form-control text-center iranyekan @error('app_version') is-invalid @enderror"
                           type="text" name="app_version" value="{{ $company_information->app_version }}">
                    @error('app_version')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12 form-button-row text-center pt-4 pb-2">
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
                        <span class="iranyekan">ارسال و ویرایش</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
