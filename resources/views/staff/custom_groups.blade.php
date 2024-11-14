@extends("staff.staff_dashboard")
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                گروه سفارشی
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
            <button class="btn btn-primary d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_custom_group_modal">
                <i class="fa fa-plus fa-1-6x"></i>
            </button>
            <input type="text" class="form-control text-center iransans" data-table="groups_table" placeholder="جستجو با نام گروه" v-on:input="filter_table">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table id="groups_table" class="table table-striped table-hover pointer-cursor sortArrowWhite" data-filter="[1]">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col" style="width: 80px;" data-sortas="numeric"><span>شماره</span></th>
                        <th scope="col"><span>عنوان</span></th>
                        <th scope="col" style="width: 80px"><span>پرسنل</span></th>
                        <th scope="col" style="width: 120px"><span>مشخصه</span></th>
                        <th scope="col" style="width: 70px;"><span>وضعیت</span></th>
                        <th scope="col" style="width: 150px;"><span>توسط</span></th>
                        <th scope="col" style="width: 120px;"><span>تاریخ ثبت</span></th>
                        <th scope="col" style="width: 120px;"><span>تاریخ ویرایش</span></th>
                        <th scope="col" style="width: 150px;"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($groups as $group)
                        <tr>
                            <td class="iransans">{{ $group->id }}</td>
                            <td><span class="iransans">{{ $group->name }}</span></td>
                            <td><span class="iransans">{{ count($group->employees) }}</span></td>
                            <td><span class="iransans pt-1 pb-1 ps-5 pe-5" style="background-color: {{ $group->color }}">تست</span></td>
                            <td>
                                <span class="iransans">
                                     @if($group->inactive == 1)
                                        <i class="far fa-times-circle red-color fa-1-4x vertical-middle"></i>
                                    @elseif($group->inactive == 0)
                                        <i class="far fa-check-circle green-color fa-1-4x vertical-middle"></i>
                                    @endif
                                </span>
                            </td>
                            <td><span class="iransans">{{ $group->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($group->created_at)->format("Y/m/d") }}</span></td>
                            <td><span class="iransans">{{ verta($group->updated_at)->format("Y/m/d") }}</span></td>
                            <td>
                                <div class="d-flex flex-row flex-wrap align-items-center justify-content-center gap-2 gap-lg-3">
                                    @can("activation", "CustomGroups")
                                        <div>
                                            <form hidden="" id="activation-form-{{ $group->id }}" action="{{ route("CustomGroups.activation",$group->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                            </form>
                                            <button type="submit" form="activation-form-{{ $group->id }}" class="btn btn-sm btn-outline-dark">
                                                @if($group->inactive == 0)
                                                    <i class="far fa-lock fa-1-2x vertical-middle"></i>
                                                @elseif($group->inactive == 1)
                                                    <i class="far fa-lock-open fa-1-2x vertical-middle"></i>
                                                @endif
                                            </button>
                                        </div>
                                    @endcan
                                    @can("edit", "CustomGroups")
                                        <a role="button" href="{{ route("CustomGroups.edit",$group->id) }}" class="btn btn-sm btn-outline-dark">
                                            <i class="far fa-edit fa-1-2x vertical-middle"></i>
                                        </a>
                                    @endcan
                                    @can("delete","CustomGroups")
                                        <div>
                                            <form class="w-100" id="delete-form-{{ $group->id }}" action="{{ route("CustomGroups.destroy",$group->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                            </form>
                                            <button type="submit" form="delete-form-{{ $group->id }}" class="btn btn-sm btn-outline-dark">
                                                <i class="far fa-trash fa-1-2x vertical-middle"></i>
                                            </button>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                    <tfoot class="bg-dark">
                    <tr>
                        <td colspan="12">
                            <div class="d-flex align-items-center justify-content-start gap-2 gap-lg-4 my-1 px-2">
                                <p class="iransans white-color mb-0">
                                    مجموع :
                                    {{ count($groups) }}
                                </p>
                                <p class="iransans white-color mb-0">
                                    فعال :
                                    {{  count($groups->where("inactive",0)) }}
                                </p>
                                <p class="iransans white-color mb-0">
                                    غیر فعال :
                                    {{ count($groups->where("inactive",1)) }}
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
    <div class="modal fade rtl" id="new_custom_group_modal" tabindex="-1" aria-labelledby="new_custom_group_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد گروه سفارشی جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("CustomGroups.store") }}" method="POST" data-json="employees" enctype="multipart/form-data" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-lg-9 mb-3">
                                <label class="form-label iransans">
                                    عنوان
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iransans @error('name') is-invalid @enderror" type="text" name="name" value="{{ old("name") }}">
                                @error('name')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-lg-3 mb-3">
                                <label class="form-label iransans">
                                    مشخصه رنگ
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control w-100 form-control-color @error('color') is-invalid @enderror" style="height: 31px" type="color" name="color" value="{{ old("color") ? old("color") : '#D1E6EF' }}">
                                @error('color')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-lg-9 mb-3">
                                <label class="form-label iransans">
                                    فایل اکسل لیست پرسنل
                                    <a href="{{ route("CustomGroups.excel_download") }}" class="iransans">(فایل نمونه)</a>
                                </label>
                                <s-file-browser :accept='["xls","xlsx"]' :size="2000000" :input_class="'d-inline'"></s-file-browser>
                            </div>
                            <div class="col-12 col-lg-3 align-self-center mb-3">
                                <live-transfer :target="'employees'" :elements="['#upload_file']" :required="['#upload_file']" :class="'btn btn-primary w-100 mt-1'" route="{{ route("CustomGroups.excel_upload") }}" :message="'آیا برای بارگذاری لیست پرسنل اطمینان دارید؟'">
                                    <i class="fa fa-upload fa-1-2x me-1"></i>
                                    <span class="iransans">بارگذاری فایل</span>
                                </live-transfer>
                            </div>
                            <div class="form-group col-12 mb-3">
                                <div class="mb-2">
                                    <div class="input-group-text w-100 d-flex flex-row align-items-center justify-content-between @error('employees') is-invalid @enderror">
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#employees"><i class="fa fa-magnifying-glass green-color fa-1-2x"></i></button>
                                            <span class="iransans">
                                                پرسنل بارگذاری شده :
                                                @{{ employees.length }}
                                                نفر
                                            </span>
                                        </div>
                                        <button v-if="import_errors.length > 0" type="button" class="btn btn-sm btn-outline-secondary iransans" data-bs-toggle="modal" data-bs-target="#import_errors">
                                            <i class="fa fa-triangle-exclamation red-color fa-1-2x me-1"></i>
                                            خطای بارگذاری
                                        </button>
                                    </div>
                                    @error('employees')
                                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label iransans">
                                    توضیحات
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" style="min-height: 50px" name="description">{{ old("description") }}</textarea>
                                @error('description')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
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
    <div class="modal fade" id="import_errors" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans" id="exampleModalLongTitle">مشکلات بارگذاری فایل اکسل</h6>
                </div>
                <div class="modal-body scroll-style">
                    <table class="table table-bordered text-center w-100 iransans">
                        <thead class="thead-dark">
                        <tr>
                            <th>ردیف فایل</th>
                            <th>مقدار</th>
                            <th>پیام خطا</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="error in import_errors" :key="error.row">
                            <td>@{{ error.row }}</td>
                            <td>@{{ error.national_code }}</td>
                            <td>@{{ error.message }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary iransans" data-bs-toggle="modal" data-bs-target="#new_custom_group_modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="employees" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans" id="exampleModalLongTitle">پرسنل بارگذاری شده</h6>
                </div>
                <div class="modal-body scroll-style">
                    <table class="table table-bordered text-center w-100 iransans">
                        <thead class="thead-dark">
                        <tr>
                            <th>شماره</th>
                            <th>نام</th>
                            <th>کد ملی</th>
                            <th>قرارداد</th>
                            <th>حذف</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(employee,index) in employees" :key="employee.id">
                            <td>@{{ employee.id }}</td>
                            <td>@{{ employee.name }}</td>
                            <td>@{{ employee.national_code }}</td>
                            <td>@{{ employee.contract }}</td>
                            <td>
                                <i class="fa fa-trash hover-red fa-1-2x" v-on:click="employees.splice(index,1)"></i>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary iransans" data-bs-toggle="modal" data-bs-target="#new_custom_group_modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @if($errors->has('name') || $errors->has('color') || $errors->has('employees'))
        <script defer>
            $(document).ready(function (){
                let modal = new bootstrap.Modal(document.getElementById("new_custom_group_modal"), {});
                modal.show();
            });
        </script>
    @endif
@endsection
