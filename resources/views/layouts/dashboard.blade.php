<!doctype html>
<html lang="en">
<head>
    @routes
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>همیاران شمال شرق - داشبورد</title>
    <link href="{{ asset('css/app.css?v=').$company_information->app_version }}" rel="stylesheet">
    @yield('styles')
    @yield('variables')
    <script>
        const agent = @json($agent);
        const logged_user = @json(['user' => auth()->check() ? auth()->user()->id : null,
        'role' => auth()->check() && auth()->user()->role != null ? auth()->user()->role->id : null,]);
    </script>
</head>
<body>
<div id="app" class="app-container" style="background: #000000">
    <loading v-show="show_loading" v-cloak></loading>
    <div v-cloak v-if="sidebar_toggle" class="mobile_sidebar w-100 bg-dark">
        <div class="w-100 d-flex align-items-center justify-content-start px-4 py-3">
            <button class="btn btn-sm btn-outline-light" v-on:click="toggle_sidebar">
                <i class="fas fa-bars fa-2x"></i>
            </button>
            <h5 class="iranyekan ms-3 mb-0 white-color">همیاران شمال شرق</h5>
        </div>
    </div>
    <div class="d-flex flex-row align-items-stretch justify-content-between rtl">
        <div class="sidebar bg-menu-dark" :class="sidebar_toggle ? 'bg-black' : null" v-on:mouseenter="desktop_toggle_sidebar('maximize-hover')" v-on:mouseleave="desktop_toggle_sidebar('minimize-static')">
            <div class="d-flex align-items-center justify-content-between w-100">
                <a href="{{ route("staff_idle") }}" class="d-flex align-items-center justify-content-start text-white text-decoration-none w-100">
                    <img v-cloak v-if="!is_static_sidebar && !sidebar_toggle" class="dashboard-logo" :class="!is_static_sidebar ? 'mx-auto' : null" alt="همیاران شمال شرق" src="{{ asset("/images/logo-original.svg") }}"/>
                    <span class="company_name iranyekan mb-0 white-color">داشبورد مدیریتی</span>
                </a>
                <i v-cloak v-if="desktop_sidebar_toggle && is_static_sidebar" class="fad fa-chevrons-right fa-1-3x small-sidebar-button white-color pointer-cursor hover-scale" v-on:click="desktop_toggle_sidebar('minimize')"></i>
                <i v-cloak v-if="!is_static_sidebar && !sidebar_toggle" class="fad fa-chevrons-left fa-1-3x small-sidebar-button white-color pointer-cursor hover-scale" v-on:click="desktop_toggle_sidebar('maximize-static')"></i>
            </div>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto sidebar-menu">
                @yield('menu')
            </ul>
            <hr>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    @if($avatar)
                        <img src="{{ "data:image/png;base64,$avatar" }}" alt="avatar" width="40" height="40" class="rounded-circle me-2">
                    @else
                        @if($user->gender == "m")
                            <img src="{{ asset("/images/male.svg") }}" alt="avatar" width="40" height="40" class="rounded-circle bg-white me-2">
                        @elseif($user->gender == "f")
                            <img src="{{ asset("/images/female.svg") }}" alt="avatar" width="40" height="40" class="rounded-circle bg-white me-2">
                        @else
                            <i class="fa fa-user-circle fa-3x rounded-circle me-2"></i>
                        @endif
                    @endif
                    <div class="account-name">
                        <strong class="iransans text-bold">{{ $user->name }}</strong>
                        <strong class="iransans text-muted">
                            @if($user->role)
                                {{ $user->role->name }}
                            @elseif($user->is_super_user)
                                {{ "مدیر آی تی" }}
                            @elseif($user->is_admin)
                                {{ "مدیر سامانه" }}
                            @elseif($user->is_staff)
                                {{ "کارشناس" }}
                            @elseif($user->is_user)
                                {{ "پرسنل" }}
                            @else
                                {{ "کاربر سامانه" }}
                            @endif
                        </strong>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                    <li>
                        <a class="dropdown-item iransans" role="button" data-bs-toggle="modal" data-bs-target="#change_username_modal">
                            تغییر نام کاربری
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item iransans" role="button" data-bs-toggle="modal" data-bs-target="#change_password_modal">
                            تغییر گذرواژه
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item iransans" href="{{ route("logout") }}">
                            خروج از حساب
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="content @if(Route::is(["superuser_idle","staff_idle"])) idle @endif w-100 position-relative">
            @yield('main')
        </div>
        <div aria-live="polite" aria-atomic="true" class="position-relative">
            <div class="toast-container position-fixed top-0 end-0 p-3">
                @if($errors->any())
                    <div role="alert" id="fail_toast" aria-live="assertive" aria-atomic="true" class="toast text-bg-danger" data-bs-autohide="false">
                        <div class="toast-header">
                            <i class="far fa-times-circle fa-1-4x red-color me-2"></i>
                            <span class="me-auto iransans fw-bolder" style="line-height: 20px;font-size: 12px">خطا در عملیات!</span>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body py-4 px-3">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li class="iransans mb-1">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                @if(session()->has("result"))
                    <div role="alert" id="success_toast" aria-live="assertive" aria-atomic="true" class="toast @if(session("result") == "success") text-bg-success @elseif(session("result") == "warning") text-bg-warning @else text-bg-primary @endif"  data-bs-autohide="true">
                        <div class="toast-header">
                            @if(session("result") == "success")
                                <i class="far fa-check-circle fa-1-4x green-color me-2"></i>
                                <span class="me-auto iransans fw-bolder" style="line-height: 20px;font-size: 12px">عملیات موفق</span>
                            @elseif(session("result") == "warning")
                                <i class="far fa-triangle-exclamation fa-1-4x yellow-color me-2"></i>
                                <span class="me-auto iransans fw-bolder" style="line-height: 20px;font-size: 12px">توجه!</span>
                            @else
                                <i class="far fa-question-circle fa-1-4x blue-color me-2"></i>
                                <span class="me-auto iransans fw-bolder" style="line-height: 20px;font-size: 12px">عملیات نامشخص</span>
                            @endif
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body py-4 px-3">
                            <span class="iransans">
                                @switch(session("message"))
                                    @case("unknown")
                                        نتیجه عملیات نامشخص می باشد
                                        @break
                                    @case("successful")
                                        عملیات با موفقیت انجام شد
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
                                        @break
                                    @case("employee_data_reload")
                                        عملیات درخواست بازنشانی اطلاعات با موفقیت انجام شد
                                        @break
                                    @case("unregistered")
                                        عملیات لغو ثبت نام با موفقیت انجام شد
                                        @break
                                @endswitch
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="modal fade rtl" id="print_modal" tabindex="-1" aria-labelledby="print_modal" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content print-preview">
                    <embed
                        class="printer-dialog m-auto" id="doc_print" name="doc_print"
                        type="application/pdf"
                        frameBorder="0"
                        scrolling="auto"
                        height="100%"
                        width="100%"
                    />
                    <div class="modal-footer">
                        <div class="d-flex flex-row align-items-center justify-content-center gap-3">
                            <div>
                                <select class="form-control iransans selectpicker-select" v-model="print_page_size" v-on:change="page_setup">
                                    <option data-icon="fa fa-file fa-1-2x" @if(Session::has('page-config') && Session::get('page-config')->isNotEmpty() && Session::get('page-config')["page"] == 'A4') selected @endif selected value="A4">صفحه A4</option>
                                    <option data-icon="fa fa-file fa-1-2x" @if(Session::has('page-config') && Session::get('page-config')->isNotEmpty() && Session::get('page-config')["page"] == 'A5') selected @endif value="A5">صفحه A5</option>
                                </select>
                            </div>
                            <div>
                                <select class="form-control iransans selectpicker-select" v-model="print_page_orientation" v-on:change="page_setup">
                                    <option data-icon="fa fa-retweet fa-1-2x" @if(Session::has('page-config') && Session::get('page-config')->isNotEmpty() && Session::get('page-config')["orientation"] == 'portrait') selected @endif selected value="portrait">عمودی</option>
                                    <option data-icon="fa fa-retweet fa-1-2x" @if(Session::has('page-config') && Session::get('page-config')->isNotEmpty() && Session::get('page-config')["orientation"] == 'landscape') selected @endif value="landscape">افقی</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-secondary iransans" id="close_print_dialog" data-bs-dismiss="modal">
                            <i class="fa fa-times fa-1-2x me-1"></i>
                            <span class="iransans">انصراف</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @if(Session::has("registration_result"))
            <div class="modal fade" id="registration_result" tabindex="-1" aria-labelledby="registration_result" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title iransans" id="exampleModalLongTitle">گزارش عملیات ثبت نام</h6>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered text-center w-100 iransans">
                                <thead class="thead-dark">
                                <tr>
                                    <th>نام</th>
                                    <th>کد ملی</th>
                                    <th>گزارش</th>
                                </tr>
                                @forelse(json_decode(Session::get("registration_result"),true) as $result)
                                    <tr>
                                        <td class="bg-{{ $result["opt"][0] }}">{{ $result["name"] }}</td>
                                        <td class="bg-{{ $result["opt"][0] }}">{{ $result["national_code"] }}</td>
                                        <td class="bg-{{ $result["opt"][0] }}">{{ $result["opt"][1] }}</td>
                                    </tr>
                                @empty
                                @endforelse
                                </thead>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary iransans" data-bs-dismiss="modal">
                                <i class="fa fa-times fa-1-2x me-1 vertical-middle"></i>
                                بستن
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(session("import_errors"))
            <div class="modal fade" id="import_errors" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title iransans" id="exampleModalLongTitle">مشکلات بارگذاری فایل اکسل</h6>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered text-center w-100 iransans">
                                <thead class="thead-dark">
                                <tr>
                                    <th>ردیف فایل</th>
                                    <th>مقدار</th>
                                    <th>پیام خطا</th>
                                </tr>
                                @forelse(session("import_errors") as $import_error)
                                    <tr>
                                        <td>{{ $import_error["row"] }}</td>
                                        <td>{{ $import_error["value"] }}</td>
                                        <td>{{ $import_error["message"] }}</td>
                                    </tr>
                                @empty
                                @endforelse
                                </thead>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="modal fade rtl" id="change_username_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="requests_history_modal" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title iransans">تغییر نام کاربری</h5>
                    </div>
                    <div class="modal-body">
                        <form id="username_form" action="{{route("StaffSettings.UsernameChange")}}" method="post">
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
                        <form id="password_form" action="{{route("StaffSettings.PasswordChange")}}" method="post">
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
        @yield('modals')
    </div>
</div>
<script src="{{ asset('js/app.js?v=').time().$company_information->app_version }}"></script>
@yield('scripts')
</body>
</html>
