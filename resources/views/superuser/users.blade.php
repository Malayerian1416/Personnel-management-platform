@extends('superuser.superuser_dashboard')
@section('variables')
    <script>
        let allowed_organizations = @json($organizations);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i v-if="sidebar_toggle" class="sidebar-toggle fa fa-bars fa-1-6x me-4" v-cloak
               v-on:click="toggle_sidebar('open')"></i>
            <h5 class="iransans d-inline-block m-0 d-flex align-items-center">
                کاربران سیستم
                <span class="vertical-middle ms-2">(ایجاد، جستجو ، ویرایش)</span>
            </h5>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content w-100">
        <div class="input-group mb-2">
            <button class="btn btn-outline-info d-flex flex-row align-items-center justify-content-center"
                    data-bs-toggle="modal" data-bs-target="#new_user_modal">
                <span class="iransans create-button">کاربر جدید</span>
            </button>
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام سرفصل">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table class="table table-striped">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>نام کاربری</span></th>
                        <th scope="col"><span>عنوان شغلی</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>آخرین بازدید</span></th>
                        <th scope="col"><span>آی پی</span></th>
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><span class="iransans">{{$user->id}}</span></td>
                            <td><span class="iransans">{{$user->name}}</span></td>
                            <td><span class="iransans">{{$user->username}}</span></td>
                            <td><span class="iransans">{{$user->role->name}}</span></td>
                            <td><span class="iransans">{{$user->user->name}}</span></td>
                            <td>
                                @if($user->last_activity)
                                    <span class="iransans">{{verta($user->last_activity)->format("H:i:s Y/m/d")}}</span>
                                @else
                                    <span class="iransans"></span>
                                @endif
                            </td>
                            <td><span class="iransans">{{$user->last_ip_address}}</span></td>
                            <td>
                                <span class="iransans">
                                    @if($user->inactive == 1)
                                        <i class="far fa-times-circle red-color fa-1-4x vertical-middle"></i>
                                    @elseif($user->inactive == 0)
                                        <i class="far fa-check-circle green-color fa-1-4x vertical-middle"></i>
                                    @endif
                                </span>
                            </td>
                            <td><span class="iransans">{{verta($user->created_at)->format("Y/m/d")}}</span></td>
                            <td><span class="iransans">{{verta($user->updated_at)->format("Y/m/d")}}</span></td>
                            <td class="position-relative">
                                <div class="dropdown table-functions iransans">
                                    <a class="table-functions-button dropdown-toggle border-0 iransans info-color"
                                       type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                       aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cog fa-1-2x"></i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <form class="w-100" id="activation-form-{{ $user->id }}"
                                              action="{{ route("SuperUserUsers.activation",$user->id) }}" method="POST"
                                              v-on:submit="submit_form">
                                            @csrf
                                            <button type="submit" form="activation-form-{{ $user->id }}"
                                                    class="dropdown-item">
                                                @if($user->inactive == 0)
                                                    <i class="fa fa-lock"></i>
                                                    <span>غیر فعال سازی</span>
                                                @elseif($user->inactive == 1)
                                                    <i class="fa fa-lock-open"></i>
                                                    <span>فعال سازی</span>
                                                @endif
                                            </button>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <a role="button" href="{{ route("SuperUserUsers.edit",$user->id) }}"
                                           class="dropdown-item">
                                            <i class="fa fa-edit"></i>
                                            <span class="iransans">ویرایش</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a role="button" href="{{ route("SuperUserUsers.edit",$user->id) }}"
                                           class="dropdown-item">
                                            <i class="fa fa-chart-line"></i>
                                            <span class="iransans">گزارشات</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form class="w-100" id="delete-form-{{ $user->id }}"
                                              action="{{ route("SuperUserUsers.destroy",$user->id) }}" method="POST"
                                              v-on:submit="submit_form">
                                            @csrf
                                            @method("Delete")
                                            <button type="submit" form="delete-form-{{ $user->id }}"
                                                    class="dropdown-item">
                                                <i class="fa fa-trash"></i>
                                                <span class="iransans">حذف</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11"><span class="iransans">اطلاعاتی وجود ندارد</span></td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_user_modal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ایجاد کاربر جدید</h5>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" action="{{route("SuperUserUsers.store")}}" method="post"
                          data-type="create" v-on:submit="submit_form" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="form-group mb-3 col-12">
                                <input type="checkbox" class="vertical-middle" name="is_admin"
                                       @if(old("is_admin")) checked @endif value="1">
                                <label class="form-label iransans fw-bold">
                                    تعریف حساب از نوع مدیر سیستم
                                </label>
                                <div class="form-text iransans">
                                    حساب مدیر سیستم دارای دسترسی کامل به تمامی امکانات و قابلیت ها در سامانه می باشد.قبل
                                    از انتخاب این گزینه، از اعطای آن به حساب کاربر مطمئن شوید.
                                </div>
                            </div>
                            <div class="mb-3 col-12">
                                <label class="form-label iransans">
                                    قراردادهای مجاز
                                    <strong class="red-color">*</strong>
                                </label>
                                <div class="@error('contracts') is-invalid is-invalid-fake @enderror">
                                    <tree-select dir="rtl" :name="'contracts[]'" :branch_node="false" :is_multiple="true" :selected="{{ json_encode(old("contracts")) }}" :placeholder="'انتخاب کنید'" :database="organizations" @error('contracts') :validation_error="true" @enderror></tree-select>
                                </div>
                                @error('contracts')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    نام و نام خانوادگی
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control iransans text-center @error('name') is-invalid @enderror"
                                       type="text" name="name" value="{{ old("name") }}">
                                @error('name')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    نام کاربری
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center @error('username') is-invalid @enderror"
                                       type="text" autocomplete="off" name="username" value="{{ old("username") }}">
                                @error('username')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    جنسیت
                                    <strong class="red-color">*</strong>
                                </label>
                                <select class="form-control text-center iransans @error('gender') is-invalid @enderror" name="gender">
                                    <option value="m">مرد</option>
                                    <option value="f">زن</option>
                                </select>
                                @error('gender')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    کلمه عبور
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center @error('password') is-invalid @enderror"
                                       type="password" name="password" autocomplete="new-password"
                                       value="{{ old("password") }}">
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
                                       autocomplete="new-password" value="{{ old("password") }}">
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    عنوان شغلی
                                    <strong class="red-color">*</strong>
                                </label>
                                <select class="form-control text-center @error('role_id') is-invalid @enderror selectpicker-select iransans"
                                        data-size="20" data-live-search="true" name="role_id">
                                    @forelse($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('role_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    پست الکترونیکی
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center @error('email') is-invalid @enderror" type="text"
                                       name="email" value="{{ old("email") }}">
                                @error('email')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    موبایل
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control iransans text-center @error('mobile') is-invalid @enderror"
                                       type="text" name="mobile" value="{{ old("mobile") }}">
                                @error('mobile')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="col-form-label iransans black_color" for="upload_file">تصویر
                                    پرسنلی</label>
                                <s-file-browser @error('avatar') :error_class="'is-invalid'"
                                                :error_message="'{{ $message }}'"
                                                @enderror :accept='["png","jpg","jpeg"]' :size="325000"
                                                :filename_box_id="'avatar_filename'" :file_box_id="'avatar'"
                                                :file_box_name="'avatar'"></s-file-browser>
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="col-form-label iransans black_color" for="upload_file">اسکن امضا</label>
                                <s-file-browser @error('sign') :class="'is-invalid'" :error_message="'{{ $message }}'"
                                                @enderror :accept='["png"]' :size="325000"
                                                :filename_box_id="'sign_filename'" :file_box_id="'sign'"
                                                :file_box_name="'sign'"></s-file-browser>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="main_submit_form" class="btn btn-success">
                        <i class="fa fa-check fa-1-2x me-1"></i>
                        <span class="iransans">ارسال و ذخیره</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
