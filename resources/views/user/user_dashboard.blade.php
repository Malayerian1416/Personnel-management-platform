<!doctype html>
@routes
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset("/images/logo.ico") }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>داشبورد</title>
    <link href="{{ asset('css/app.css?v=').$company_information->app_version }}" rel="stylesheet">
    @yield('variables')
</head>
<body style="background-color: #f1f1f1">
<div id="app">
    <loading v-show="show_loading" v-cloak></loading>
    <div class="accordion position-sticky top-0 w-100" id="accordionExample" style="box-shadow: 0 5px 5px -1px #d8d8d8">
        <div class="accordion-item">
            <div class="accordion-header" id="headingOne">
                <div class="accordion-button menu-header pe-4 ps-4" type="button">
                    <div class="w-100 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <a href="{{ route("user_idle") }}" class="text-decoration-none">
                                <img class="dashboard-logo" alt="همیاران شمال شرق" src="{{ asset("/images/logo-original.svg") }}"/>
                                <span class="company_name iranyekan ms-2" style="color: #0a53be;font-weight: 700;text-shadow: 2px 2px rgba(26,83,255,0.37)">همیاران شمال شرق</span>
                            </a>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fad fa-user-alt fa-1-7x me-2 vertical-middle"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item iransans" role="button" data-bs-target="#change_username_modal" data-bs-toggle="modal">
                                        <i class="fa fa-user-edit vertical-middle"></i>
                                        تغییر نام کاربری
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item iransans">
                                        <a class="dropdown-item iransans" role="button" data-bs-target="#change_password_modal" data-bs-toggle="modal">
                                            <i class="fa fa-key vertical-middle"></i>
                                            تغییر گذرواژه
                                        </a>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item iransans" href="{{ route("logout") }}">
                                        <i class="fa fa-sign-out-alt vertical-middle"></i>
                                        خروج از حساب
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                <div class="user-menu-container accordion-body">
                    <a href="{{route("UserTickets.create")}}" class="user-dashboard-button btn btn-outline-primary d-flex flex-column align-items-center justify-content-center gap-3">
                        <i class="fad fa-message-dots fa-3x"></i>
                        <span class="iranyekan">تیکت جدید</span>
                    </a>
                    <a href="{{route("UserTickets.index")}}" class="position-relative user-dashboard-button btn btn-outline-primary d-flex flex-column align-items-center justify-content-center gap-3">
                        <i class="fad fa-messages fa-3x"></i>
                        <span class="iranyekan">سوابق تیکت ها</span>
                        @if($tickets)
                            <span class="badge position-absolute white-color bg-danger iransans" style="top:5px;left: 5px;font-size: 10px">{{$tickets}}</span>
                        @endif
                    </a>
                    <a href="{{route("ApplicationForms.create")}}" class="user-dashboard-button btn btn-outline-primary d-flex flex-column align-items-center justify-content-center gap-3">
                        <i class="fad fa-file-plus fa-3x"></i>
                        <span class="iranyekan">درخواست جدید</span>
                    </a>
                    <a href="{{route("ApplicationForms.index")}}" class="position-relative user-dashboard-button btn btn-outline-primary d-flex flex-column align-items-center justify-content-center gap-3">
                        <i class="fad fa-file-magnifying-glass fa-3x"></i>
                        <span class="iranyekan">سوابق درخواست ها</span>
                        @if($requests)
                            <span class="badge position-absolute white-color bg-danger iransans" style="top:5px;left: 5px;font-size: 10px">{{$requests}}</span>
                        @endif
                    </a>
                    <a href="{{route("UserPaySlips.index")}}" class="user-dashboard-button btn btn-outline-primary d-flex flex-column align-items-center justify-content-center gap-3">
                        <i class="fad fa-receipt fa-3x"></i>
                        <span class="iranyekan">فیش حقوقی</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="position-fixed announcement-container">
        <div class="accordion" id="announcement">
            <div class="accordion-item" style="box-shadow: 0 5px 10px -1px #cccdce">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <i class="fa fa-bullhorn fa-1-4x me-2"></i>
                        <span class="iransans">اعلانات سیستم</span>
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#announcement">
                    <div class="accordion-body">
                        <strong class="iranyekan">اعلانی وجود ندارد</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @yield('contents')
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed top-0 end-0 p-3">
            @if($errors->any())
                <div role="alert" id="fail_toast" aria-live="assertive" aria-atomic="true" class="toast text-bg-danger" data-bs-autohide="false">
                    <div class="toast-header">
                        <i class="far fa-times-circle fa-1-4x red-color me-2"></i>
                        <span class="me-auto iransans fw-bolder" style="line-height: 20px;font-size: 12px">خطا در عملیات!</span>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li class="iransans mb-1">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            @if(session()->has("result"))
                @switch(session("result"))
                    @case("success")
                        <div role="alert" id="success_toast" aria-live="assertive" aria-atomic="true" class="toast text-bg-success" data-bs-autohide="false">
                            <div class="toast-header">
                                <i class="far fa-check-circle fa-1-4x green-color me-2"></i>
                                <span class="me-auto iransans fw-bolder" style="line-height: 20px;font-size: 12px">عملیات موفق</span>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            @break
                            @case("warning")
                                <div role="alert" id="success_toast" aria-live="assertive" aria-atomic="true" class="toast text-bg-warning" data-bs-autohide="false">
                                    <div class="toast-header">
                                        <i class="far fa-triangle-exclamation fa-1-4x yellow-color me-2"></i>
                                        <span class="me-auto iransans fw-bolder" style="line-height: 20px;font-size: 12px">توجه!</span>
                                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                    @break
                                    @endswitch
                                    <div class="toast-body">
                                                <span class="iransans">
                                                @switch(session("message"))
                                                        @case("unknown")
                                                            نتیجه عملیات نامشخص می باشد
                                                            @break
                                                        @case("saved")
                                                            عملیات ذخیره سازی با موفقیت انجام شد
                                                            @break
                                                        @case("updated")
                                                            عملیات ویرایش با موفقیت انجام شد
                                                            @break
                                                        @case("deleted")
                                                            عملیات حذف با موفقیت انجام شد
                                                            @break
                                                        @case("relation_exists")
                                                            به دلیل وجود رابطه با موجودیت های دیگر، امکان حذف این آیتم وجود ندارد
                                                            @break
                                                        @case("inactive")
                                                            عملیات غیرفعال سازی با موفقیت انجام شد
                                                            @break
                                                        @case("active")
                                                            عملیات فعال سازی با موفقیت انجام شد
                                                            @break
                                                        @case("registered")
                                                            عملیات ثبت نام با موفقیت انجام شد
                                                            @if(Session::has("result"))
                                                                <button class="btn btn-sm btn-outline-dark ms-2 report-modal" data-bs-toggle="modal" data-bs-target="#registration_result">
                                                                <i class="fa fa-magnifying-glass me-1 vertical-middle"></i>
                                                                <span class="iransans">گزارش</span>
                                                            </button>
                                                            @endif
                                                            @break
                                                        @case("employee_data_reload")
                                                            عملیات درخواست بازنشانی اطلاعات با موفقیت انجام شد
                                                            @if(Session::has("result"))
                                                                <button class="btn btn-sm btn-outline-dark ms-2 report-modal" data-bs-toggle="modal" data-bs-target="#registration_result">
                                                                <i class="fa fa-magnifying-glass me-1 vertical-middle"></i>
                                                                <span class="iransans">گزارش</span>
                                                            </button>
                                                            @endif
                                                            @break
                                                        @case("unregistered")
                                                            عملیات لغو ثبت نام با موفقیت انجام شد
                                                            @if(Session::has("result"))
                                                                <button class="btn btn-sm btn-outline-dark ms-2 report-modal" data-bs-toggle="modal" data-bs-target="#registration_result">
                                                                <i class="fa fa-magnifying-glass me-1 vertical-middle"></i>
                                                                <span class="iransans">گزارش</span>
                                                            </button>
                                                            @endif
                                                            @break
                                                    @endswitch
                                                </span>
                                    </div>
                                </div>
                                @endif
                        </div>
        </div>
        <div class="modal fade rtl" id="change_username_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="requests_history_modal" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title iransans">تغییر نام کاربری</h5>
                    </div>
                    <div class="modal-body">
                        <form id="username_form" action="{{route("UserSettings.UsernameChange")}}" method="post">
                            @csrf
                            @method('put')
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label iransans">نام کاربری جدید</label>
                                    <input class="form-control iransans text-center @error('username') is-invalid @enderror" type="text" name="username">
                                    @error('username')
                                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-menu">
                        <button class="btn btn-success iransans" form="username_form" type="submit">
                            <i class="fa fa-edit fa-1-2x me-1"></i>
                            <span class="iransans">تغییر نام کاربری</span>
                        </button>
                        <button class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                            <i class="fa fa-times fa-1-2x me-1"></i>
                            <span class="iransans">انصراف</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade rtl" id="change_password_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="requests_history_modal" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title iransans">تغییر گذرواژه</h5>
                    </div>
                    <div class="modal-body">
                        <form id="password_form" action="{{route("UserSettings.PasswordChange")}}" method="post">
                            @csrf
                            @method('put')
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label iransans">گذرواژه فعلی</label>
                                    <input class="form-control iransans text-center @error('old_password') is-invalid @enderror" type="password" name="old_password" value="{{ old("old_password") }}">
                                    @error('old_password')
                                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3 col-12">
                                    <label class="form-label iransans">گذرواژه جدید</label>
                                    <input class="form-control text-center @error('password') is-invalid @enderror" type="password" name="password" autocomplete="new-password" value="{{ old("password") }}">
                                    @error('password')
                                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3 col-12">
                                    <label class="form-label iransans">تکرار گذرواژه</label>
                                    <input class="form-control text-center" type="password" name="password_confirmation" autocomplete="new-password" value="{{ old("password") }}">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-menu">
                        <button class="btn btn-success iransans" form="password_form" type="submit">
                            <i class="fa fa-edit fa-1-2x me-1"></i>
                            <span class="iransans">تغییر گذرواژه</span>
                        </button>
                        <button class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                            <i class="fa fa-times fa-1-2x me-1"></i>
                            <span class="iransans">انصراف</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/app.js?v=').$company_information->app_version }}"></script>
@yield('scripts')
</body>
</html>
