@extends("staff.staff_dashboard")
@section('variables')
    <script>
        let allowed_organizations = @json($contracts);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                کاربران سامانه
                <span class="vertical-middle ms-1 text-muted">ویرایش</span>
            </h5>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-light">
                <i class="fa fa-circle-question fa-1-4x green-color"></i>
            </button>
            <a role="button" class="btn btn-sm btn-outline-light" href={{route("staff_idle")}}>
                <i class="fa fa-times fa-1-4x gray-color"></i>
            </a>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content edit w-100">
        <form id="update_form" class="p-3" action="{{ route("StaffUsers.update",$staff_user->id) }}" method="POST"
              enctype="multipart/form-data" v-on:submit="submit_form">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        نام و نام خانوادگی
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control iransans text-center @error('name') is-invalid @enderror" type="text"
                           name="name" value="{{ $staff_user->name }}">
                    @error('name')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        جنسیت
                        <strong class="red-color">*</strong>
                    </label>
                    <select class="form-control text-center iransans @error('gender') is-invalid @enderror" name="gender">
                        <option @if($staff_user->gender == 'm') selected @endif value="m">مرد</option>
                        <option @if($staff_user->gender == 'f') selected @endif value="f">زن</option>
                    </select>
                    @error('gender')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        نام کاربری
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control text-center @error('username') is-invalid @enderror" type="text"
                           autocomplete="off" name="username" value="{{ $staff_user->username }}">
                    @error('username')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
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
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
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
                    <select class="form-control text-center @error('role_id') is-invalid @enderror selectpicker-select iransans"
                            data-size="20" data-live-search="true" name="role_id">
                        @forelse($roles as $role)
                            <option @if($role->id == $staff_user->role_id) selected
                                    @endif value="{{ $role->id }}">{{ $role->name }}</option>
                        @empty
                        @endforelse
                    </select>
                    @error('role_id')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3 col-12">
                    <label class="form-label iransans">
                        قراردادهای مجاز
                        <strong class="red-color">*</strong>
                    </label>
                    <div>
                        <tree-select dir="rtl" :branch_node="false" :name="'contracts[]'" :is_multiple="true" :selected="@json($staff_user->contracts->pluck("id")->toArray())" :placeholder="'انتخاب کنید'" :database="organizations"></tree-select>
                    </div>
                    @error('contracts')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        پست الکترونیکی
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control text-center @error('email') is-invalid @enderror" type="text"
                           name="email" value="{{ $staff_user->email }}">
                    @error('email')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        موبایل
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control iransans text-center @error('mobile') is-invalid @enderror" type="text"
                           name="mobile" value="{{ $staff_user->mobile }}">
                    @error('mobile')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="col-form-label iransans black_color" for="upload_file">تصویر پرسنلی</label>
                    <s-file-browser @error('avatar') :error_class="'is-invalid'" :error_message="'{{ $message }}'"
                                    @enderror :accept='["png","jpg","jpeg"]' :size="325000"
                                    :filename_box_id="'avatar_filename'" :file_box_id="'avatar'"
                                    :file_box_name="'avatar'"></s-file-browser>
                </div>
                @if($avatar != "")
                    <div class="form-group mb-3 col-12">
                        <label class="col-form-label iransans black_color" for="upload_file">نمونه تصویر</label>
                        <div class="w-100 border p-3 sign-box-container">
                            <div class="sign-box m-auto">
                                <img class="sign-image m-auto d-block" src="{{ "data:image/png;base64,$avatar" }}"
                                     alt="avatar">
                            </div>
                        </div>
                    </div>
                @endif
                <div class="form-group mb-3 col-12">
                    <label class="col-form-label iransans black_color" for="upload_file">اسکن امضا</label>
                    <s-file-browser @error('sign') :class="'is-invalid'" :error_message="'{{ $message }}'"
                                    @enderror :accept='["png"]' :size="325000" :filename_box_id="'sign_filename'"
                                    :file_box_id="'sign'" :file_box_name="'sign'"></s-file-browser>
                </div>
                @if($sign != "")
                    <div class="form-group mb-3 col-12">
                        <label class="col-form-label iransans black_color" for="upload_file">نمونه امضا</label>
                        <div class="w-100 border p-3 sign-box-container">
                            <div class="sign-box m-auto">
                                <img class="sign-image m-auto d-block" src="{{ "data:image/png;base64,$sign" }}"
                                     alt="sign">
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </form>
    </div>
@endsection
@section("footer")
    <div class="content-footer-container d-flex align-items-center justify-content-center gap-3 flex-wrap">
        <button type="submit" form="update_form" class="btn btn-success submit_button">
            <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
            <span class="iransans">ارسال و ویرایش</span>
        </button>
        <a role="button" href="{{ route("StaffUsers.index") }}"
           class="btn btn-outline-secondary iransans">
            <i class="fa fa-arrow-turn-right fa-1-2x me-1"></i>
            <span class="iransans">بازگشت به لیست</span>
        </a>
    </div>
@endsection
