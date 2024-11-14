@extends('staff.admin_dashboard')
@section('content')
    <div class="page">
        <div class="w-100 content-window bg-white rounded border">
            <div class="w-100 iransans p-3 border-bottom d-flex flex-row align-items-center justify-content-between">
                <div>
                    <i class="fa fa-list fa-1-4x ms-1"></i>
                    <h5 class="iransans d-inline-block m-0">عملیات منو</h5>
                    <span>(ویرایش)</span>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-light">
                        <i class="fa fa-circle-question fa-1-4x green-color"></i>
                    </button>
                    <a role="button" class="btn btn-sm btn-outline-light">
                        <i class="fa fa-times fa-1-4x gray-color"></i>
                    </a>
                </div>
            </div>
            <form id="update_form" class="p-3" action="{{ route("Users.update",$user->id) }}" method="POST"
                  enctype="multipart/form-data" v-on:submit="submit_form">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group mb-3 col-12">
                        <label class="form-label iransans">
                            نام و نام خانوادگی
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control iranyekan text-center @error('name') is-invalid @enderror"
                               type="text" name="name" value="{{ $user->name }}">
                        @error('name')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-3 col-12">
                        <label class="form-label iransans">
                            نام کاربری
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control text-center @error('username') is-invalid @enderror" type="text"
                               autocomplete="off" name="username" value="{{ $user->username }}">
                        @error('username')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-3 col-12">
                        <label class="form-label iransans">
                            کلمه عبور
                            <span class="text-muted">(در صورت عدم بارگذاری، اطلاعات قبلی معتبر می باشد)</span>
                        </label>
                        <input class="form-control text-center @error('password') is-invalid @enderror" type="password"
                               name="password" autocomplete="new-password">
                        @error('password')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-3 col-12">
                        <label class="form-label iransans">
                            تکرار کلمه عبور
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control text-center" type="password" name="password_confirmation"
                               autocomplete="new-password">
                    </div>
                    <div class="form-group mb-3 col-12">
                        <label class="form-label iransans">
                            عنوان شغلی
                            <strong class="red-color">*</strong>
                        </label>
                        <select class="form-control text-center @error('role_id') is-invalid @enderror selectpicker-select iranyekan"
                                data-size="20" data-live-search="true" name="role_id">
                            @forelse($roles as $role)
                                <option @if($role->id == $user->role_id) selected
                                        @endif value="{{ $role->id }}">{{ $role->name }}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('role_id')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-3 col-12">
                        <label class="form-label iransans">
                            پست الکترونیکی
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control text-center @error('email') is-invalid @enderror" type="text"
                               name="email" value="{{ $user->email }}">
                        @error('email')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-3 col-12">
                        <label class="form-label iransans">
                            موبایل
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control iranyekan text-center @error('mobile') is-invalid @enderror"
                               type="text" name="mobile" value="{{ $user->mobile }}">
                        @error('mobile')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-3 col-12">
                        <label class="col-form-label iranyekan black_color" for="upload_file">
                            اسکن امضا
                            <span class="text-muted">(در صورت عدم بارگذاری، اطلاعات قبلی معتبر خواهد بود)</span>
                        </label>
                        <s-file-browser :accept='["png"]' :size="325000"
                                        @if($user->sign) :already="true" @endif></s-file-browser>
                        @error('upload_file')
                        <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    @if($sign != "")
                        <div class="form-group mb-3 col-12">
                            <label class="col-form-label iranyekan black_color" for="upload_file">نمونه امضا</label>
                            <div class="sign-box m-auto border p-2">
                                <img class="sign-image m-auto d-block" src="{{ "data:image/png;base64,$sign" }}"
                                     alt="sign">
                            </div>
                        </div>
                    @endif
                    <div class="form-group mb-3 col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" form="update_form" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-check fa-1-2x ms-1"></i>
                            <span class="iranyekan">ارسال و ویرایش</span>
                        </button>
                        <a role="button" href="{{ route("Users.index") }}" class="btn btn-outline-secondary iranyekan"
                           data-bs-dismiss="modal">
                            <i class="fa fa-arrow-turn-right fa-1-2x ms-1"></i>
                            <span class="iranyekan">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
