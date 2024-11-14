@extends("staff.staff_dashboard")
@section('variables')
    <script>
        let allowed_organizations = @json($organizations);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                کاربران
                <span class="vertical-middle ms-1 text-muted">ایجاد ، جستجو ، ویرایش</span>
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
    <div class="page-content w-100">
        <div class="input-group mb-2">
            <button class="btn btn-primary d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_user_modal">
                <i class="fa fa-plus fa-1-6x"></i>
            </button>
            <input type="text" class="form-control text-center iransans" data-table="users_table" placeholder="جستجو با نام و نام کاربری" v-on:input="filter_table">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table id="users_table" class="table table-striped table-hover pointer-cursor sortArrowWhite" data-filter="[1,2]">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col" data-sortas="numeric"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>نام کاربری</span></th>
                        <th scope="col"><span>عنوان شغلی</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col" style="width: 110px"><span>آخرین بازدید</span></th>
                        <th scope="col"><span>آی پی</span></th>
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col" style="width: 150px"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="iransans">{{$user->id}}</td>
                            <td><span class="iransans">{{$user->name}}</span></td>
                            <td><span class="iransans">{{$user->username}}</span></td>
                            <td><span class="iransans">{{$user->role != null ? $user->role->name : 'ندارد'}}</span></td>
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
                            <td>
                                <div class="d-flex flex-row flex-wrap align-items-center justify-content-center gap-2 gap-lg-3">
                                    @can("activation", "StaffUsers")
                                        <div>
                                            <form hidden id="activation-form-{{ $user->id }}" action="{{ route("StaffUsers.activation",$user->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                            </form>
                                            <button form="activation-form-{{ $user->id }}" class="btn btn-sm btn-outline-dark">
                                                @if($user->inactive == 0)
                                                    <i class="far fa-lock fa-1-2x vertical-middle"></i>
                                                @elseif($user->inactive == 1)
                                                    <i class="far fa-lock-open fa-1-2x vertical-middle"></i>
                                                @endif
                                            </button>
                                        </div>
                                    @endcan
                                    @can("edit", "StaffUsers")
                                        <a role="button" class="btn btn-sm btn-outline-dark" href="{{route("StaffUsers.edit",$user->id)}}">
                                            <i class="far fa-edit fa-1-2x vertical-middle"></i>
                                        </a>
                                    @endcan
                                    @can("delete","StaffUsers")
                                        <div>
                                            <form hidden id="delete-form-{{ $user->id }}" action="{{ route("StaffUsers.destroy",$user->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                                <button type="submit" form="delete-form-{{ $user->id }}" class="dropdown-item">
                                                    <i class="fa fa-trash"></i>
                                                    <span class="iransans">حذف</span>
                                                </button>
                                            </form>
                                            <button form="delete-form-{{ $user->id }}" class="btn btn-sm btn-outline-dark">
                                                <i class="far fa-trash fa-1-2x vertical-middle"></i>
                                            </button>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11"><span class="iransans">اطلاعاتی وجود ندارد</span></td></tr>
                    @endforelse
                    </tbody>
                    <tfoot class="bg-dark">
                    <tr>
                        <td colspan="12">
                            <div class="d-flex align-items-center justify-content-start gap-2 gap-lg-4 my-1 px-2">
                                <p class="iransans white-color mb-0">
                                    مجموع :
                                    {{ count($users) }}
                                </p>
                                <p class="iransans white-color mb-0">
                                    فعال :
                                    {{  count($users->where("inactive",0)) }}
                                </p>
                                <p class="iransans white-color mb-0">
                                    غیر فعال :
                                    {{ count($users->where("inactive",1)) }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_user_modal" tabindex="-1" aria-labelledby="new_user_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد کاربر جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" action="{{route("StaffUsers.store")}}" method="post" data-type="create" v-on:submit="submit_form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
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
                            <div class="mb-3 col-12 col-lg-6">
                                <label class="form-label iransans">
                                    نام و نام خانوادگی
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control iransans text-center @error('name') is-invalid @enderror" type="text" name="name" value="{{ old("name") }}">
                                @error('name')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3 col-12 col-lg-6">
                                <label class="form-label iransans">
                                    نام کاربری
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center @error('username') is-invalid @enderror" type="text" autocomplete="off" name="username" value="{{ old("username") }}">
                                @error('username')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3 col-12 col-lg-6">
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
                            <div class="mb-3 col-12 col-lg-6">
                                <label class="form-label iransans">
                                    عنوان شغلی
                                    <strong class="red-color">*</strong>
                                </label>
                                <select class="form-control text-center @error('role_id') is-invalid is-invalid-fake @enderror selectpicker-select iransans" title="انتخاب کنید" data-size="20" data-live-search="true" name="role_id">
                                    @forelse($roles as $role)
                                        <option @if(old("role_id") && $role->id == old("role_id")) selected @endif value="{{ $role->id }}">{{ $role->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('role_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3 col-12 col-lg-6">
                                <label class="form-label iransans">
                                    کلمه عبور
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center @error('password') is-invalid @enderror" type="password" name="password" autocomplete="new-password" value="{{ old("password") }}">
                                @error('password')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3 col-12 col-lg-6">
                                <label class="form-label iransans">
                                    تکرار کلمه عبور
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center" type="password" name="password_confirmation" autocomplete="new-password" value="{{ old("password") }}">
                            </div>
                            <div class="mb-3 col-12 col-lg-6">
                                <label class="form-label iransans">
                                    پست الکترونیکی
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center @error('email') is-invalid @enderror" type="text" name="email" value="{{ old("email") }}">
                                @error('email')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3 col-12 col-lg-6">
                                <label class="form-label iransans">
                                    موبایل
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control iransans text-center @error('mobile') is-invalid @enderror" type="text" name="mobile" value="{{ old("mobile") }}">
                                @error('mobile')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3 col-12 col-lg-6">
                                <label class="col-form-label iransans black_color" for="upload_file">تصویر پرسنلی</label>
                                <s-file-browser @error('avatar') :error_class="'is-invalid'" :error_message="'{{ $message }}'" @enderror :accept='["png","jpg","jpeg"]' :size="325000" :filename_box_id="'avatar_filename'" :file_box_id="'avatar'" :file_box_name="'avatar'"></s-file-browser>
                            </div>
                            <div class="mb-3 col-12 col-lg-6">
                                <label class="col-form-label iransans black_color" for="upload_file">اسکن امضا</label>
                                <s-file-browser @error('sign') :class="'is-invalid'" :error_message="'{{ $message }}'" @enderror :accept='["png"]' :size="325000" :filename_box_id="'sign_filename'" :file_box_id="'sign'" :file_box_name="'sign'"></s-file-browser>
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
@section('scripts')
    @if($errors->has('name') || $errors->has('username') || $errors->has('password') || $errors->has('gender') || $errors->has('role_id') || $errors->has('contracts') || $errors->has('email') || $errors->has('mobile'))
        <script defer>
            $(document).ready(function (){
                let modal = new bootstrap.Modal(document.getElementById("new_user_modal"), {});
                modal.show();
            });
        </script>
    @endif
@endsection
