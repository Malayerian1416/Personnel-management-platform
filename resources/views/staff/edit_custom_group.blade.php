@extends("staff.staff_dashboard")
@section('variables')
    <script>
        let old_employee_list = @json($group->employees);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                گروه سفارشی
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
        <form id="main_submit_form" class="p-3" action="{{ route("CustomGroups.update",$group->id) }}" data-json="employees" method="POST" v-on:submit="submit_form">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-12 col-lg-10 mb-3">
                    <label class="form-label iransans">
                        عنوان
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control text-center iransans @error('name') is-invalid @enderror" type="text" name="name" value="{{ $group->name }}">
                    @error('name')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-12 col-lg-2 mb-3">
                    <label class="form-label iransans">
                        مشخصه رنگ
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control w-100 form-control-color @error('color') is-invalid @enderror" style="height: 31px" type="color" name="color" value="{{ $group->color }}">
                    @error('color')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-12 col-lg-10 mb-3">
                    <label class="form-label iransans">
                        فایل اکسل لیست پرسنل
                        <a href="{{ route("CustomGroups.excel_download") }}" class="iransans">(فایل نمونه)</a>
                    </label>
                    <s-file-browser :accept='["xls","xlsx"]' :size="2000000"></s-file-browser>
                </div>
                <div class="col-12 col-lg-2 align-self-center mb-3">
                    <live-transfer :target="'employees'" :elements="['#upload_file']" :required="['#upload_file']" :class="'btn btn-outline-primary w-100 mt-1'" route="{{ route("CustomGroups.excel_upload") }}" :message="'آیا برای بارگذاری لیست پرسنل اطمینان دارید؟'">
                        <i class="fa fa-upload fa-1-2x me-1"></i>
                        <span class="iransans">بارگذاری فایل</span>
                    </live-transfer>
                </div>
                <div class="col-12 mb-3">
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
                                <i class="fa fa-ban red-color fa-1-2x me-1"></i>
                                خطای بارگذاری
                            </button>
                        </div>
                        @error('employees')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-lg-10 mb-3">
                    <label class="form-label iransans">
                        پرسنل عضو گروه
                    </label>
                    <select class="form-control iransans selectpicker-select" id="member_id" title="انتخاب کنید" data-container="body" data-size="20" data-live-search="true">
                        <option v-for="employee in old_employees" :value="employee.id">@{{ `${employee.employee.name} (${employee.employee.national_code})` }}</option>
                    </select>
                </div>
                <div class="col-12 col-lg-2 align-self-end mb-3">
                    <div class="btn-group w-100" role="group" aria-label="Basic example">
                    <live-transfer :target="'old_employees'" :elements="['#member_id']" :required="['#member_id']" :class="'btn btn-outline-danger'" route="{{ route("CustomGroups.delete_employee") }}" :message="'آیا برای حذف عضویت پرسنل انتخاب شده اطمینان دارید؟'">
                        <i class="fa fa-user-slash fa-1-2x me-1"></i>
                        <span class="iransans">حذف عضویت</span>
                    </live-transfer>
                    <input type="hidden" id="group_id" value="{{ $group->id }}">
                    <live-transfer :target="'old_employees'" :class="'btn btn-outline-danger'" :elements="['#group_id']" :required="['#group_id']" route="{{ route("CustomGroups.delete_employee") }}" :message="'آیا برای حذف عضویت تمامی پرسنل گروه اطمینان دارید؟'">
                        <i class="fa fa-trash-can fa-1-2x me-1"></i>
                        <span class="iransans">حذف کل پرسنل</span>
                    </live-transfer>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label iransans">
                        توضیحات
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror" style="min-height: 50px" name="description">{{ $group->description }}</textarea>
                    @error('description')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </form>
    </div>
@endsection
@section("footer")
    <div class="content-footer-container d-flex align-items-center justify-content-center gap-3 flex-wrap">
        <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
            <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
            <span class="iransans">ارسال و ویرایش</span>
        </button>
        <a role="button" href="{{ route("CustomGroups.index") }}" class="btn btn-outline-secondary iransans">
            <i class="fa fa-arrow-turn-right fa-1-2x me-1"></i>
            <span class="iransans">بازگشت به لیست</span>
        </a>
    </div>
@endsection
@section('modals')
    <div class="modal fade" id="import_errors" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">مشکلات بارگذاری فایل اکسل</h6>
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
                    <button type="button" class="btn btn-secondary iransans" data-bs-dismiss="modal">بستن</button>
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
                    <button type="button" class="btn btn-secondary iransans" data-bs-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
@endsection
