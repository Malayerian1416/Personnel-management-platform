@extends("staff.staff_dashboard")
@section('variables')
    <script>
        let is_parent_sub = @json($contract->is_parent === 1);
    </script>
    @if($contract->is_parent === 0)
        <script>
            let table_data = @json($contract->pre_employees);
        </script>
    @endif
    @if($contract->children->isNotEmpty())
        <script>
            let old_children_list = @json($contract->children);
        </script>
    @endif
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                قراردادها
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
        <form id="update_form" class="p-3" action="{{ route("Contracts.update",$contract->id) }}" method="POST" :data-json="is_parent === false ? 'employees' : 'children_subset_list'" enctype="multipart/form-data" v-on:submit="submit_form">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans" for="is_parent">
                        نوع قرارداد
                    </label>
                    <span v-if="is_parent === false" class="iransans fw-bold d-block">
                        مستقل و بدون زیرمجموعه
                    </span>
                    <span v-else class="iransans fw-bold d-block">
                        وابسته و دارای زیرمجموعه
                    </span>
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        سازمان
                    </label>
                    <select class="form-control text-center iransans @error('organization_id') is-invalid is-invalid-fake @enderror selectpicker-select" title="انتخاب کنید" size="20" data-live-search="true" name="organization_id">
                        @forelse($organizations as $organization)
                            <option @if($organization->id == $contract->organization->id) selected @endif value="{{ $organization->id }}">{{ $organization->name }}</option>
                        @empty
                        @endforelse
                    </select>
                    @error('organization_id')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        نام
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control text-center iransans @error('name') is-invalid @enderror" type="text" name="name" value="{{ $contract->name }}">
                    @error('name')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-12 mb-3">
                    <label class="form-label iransans mb-2">شماره</label>
                    <input class="form-control text-center iransans @error('number') is-invalid @enderror" type="text" name="number" value="{{ $contract->number }}">
                </div>
                <div class="form-group col-12 mb-3">
                    <label class="form-label iransans mb-2">تاریخ شروع</label>
                    <input class="form-control text-center iransans @error('start_date') is-invalid @enderror persian_datepicker_range_from" tabindex="-1" readonly type="text" name="start_date" value="{{ verta($contract->start_date)->format("Y/m/d") }}">
                    @error('start_date')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-12 mb-3">
                    <label class="form-label iransans mb-2">تاریخ پایان</label>
                    <input class="form-control text-center iransans @error('end_date') is-invalid @enderror persian_datepicker_range_to" tabindex="-1" readonly type="text" name="end_date" value="{{ verta($contract->end_date)->format("Y/m/d") }}">
                    @error('end_date')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                @if($contract->pre_employees->isNotEmpty())
                    <div class="col-12 mb-3 mt-3">
                        <button type="button" class="btn btn-outline-dark w-100" data-bs-target="#edit_contract_employees_modal" data-bs-toggle="modal">
                            <i class="fa fa-users fa-1-2x me-1"></i>
                            <span class="iransans">لیست پرسنل بارگذاری شده</span>
                        </button>
                    </div>
                @endif
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">مستندات</label>
                    <m-file-browser :accept="['png','jpg','bmp','tiff','pdf','xlsx','txt']" :size="325000"></m-file-browser>
                </div>
            </div>
            <div class="row" v-if="is_parent === false">
                <div class="form-group mb-3 col-12 col-lg-11">
                    <label class="form-label iransans">
                        پرسنل
                        <a href="{{ route("Contracts.excel_download") }}" class="button">(دانلود فایل اکسل نمونه)</a>
                    </label>
                    <s-file-browser :accept="['xlsx']" :size="500000" :file_box_name="'excel_file'" :file_box_id="'excel_file'" :filename_box_id="'excel_absolute_browser_box'"></s-file-browser>
                </div>
                <div class="col-12 col-lg-1 align-self-center mb-3">
                    <live-transfer :target="'employees'" :elements="['#excel_file']" :required="['#excel_file']" :class="'btn btn-primary w-100 mt-1'" route="{{ route("Contracts.excel_upload") }}" :message="'آیا برای بارگذاری لیست پرسنل اطمینان دارید؟'">
                        <i class="fa fa-upload fa-1-2x me-1"></i>
                        <span class="iransans">بارگذاری</span>
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
            </div>
            <div class="row" v-else>
                <div class="col-12 mb-3">
                    <label class="form-label iransans mb-3">
                        زیرمجموعه های قرارداد
                    </label>
                    <ul class="list-group">
                        <li v-for="(child,index) in old_children_subset_list" class="list-group-item d-flex align-items-center justify-content-between">
                            <span class="iransans">@{{ `${child.id} - ${child.name}` }}</span>
                            <div>
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#edit_contract_employees_modal" v-on:click="display_pre_employees($event,index,child.id)">
                                    <i class="fa fa-edit fa-1-4x"></i>
                                </button>
                                <live-transfer :target="'children_subset_list'" :class="'btn btn-outline-danger'" :operation_id="child.id" route="{{ route("Subsets.destroy") }}" :message="'آیا برای حذف این زیرمجموعه اطمینان دارید؟'">
                                    <i class="fa fa-trash-can fa-1-4x"></i>
                                </live-transfer>
                            </div>
                        </li>
                        <li v-if="old_children_subset_list.length === 0" class="list-group-item d-flex align-items-center justify-content-center">
                            <span class="iransans text-muted">زیرمجموعه ای ایجاد نشده است</span>
                        </li>
                    </ul>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-outline-dark d-block mb-3 w-100" data-bs-target="#new_subset_modal" data-bs-toggle="modal">
                        <i class="far fa-plus fa-1-2x me-1 vertical-middle"></i>
                        <span class="iransans">ایجاد زیرمجموعه جدید</span>
                    </button>
                    <ul class="list-group @error('children_subset_list') is-invalid is-invalid-fake @enderror">
                        <li v-for="(child,index) in children_subset_list" class="list-group-item d-flex align-items-center justify-content-between">
                            <span class="iransans">@{{ child.name }}</span>
                            <div>
                                <button type="button" class="btn btn-outline-danger" v-on:click="children_subset_list.splice(index,1)">
                                    <i class="fa fa-trash-can fa-1-4x"></i>
                                </button>
                            </div>
                        </li>
                        <li v-if="children_subset_list.length === 0" class="list-group-item d-flex align-items-center justify-content-center @error('children_subset_list') is-invalid @enderror">
                            <span class="iransans text-muted">زیرمجموعه ای ایجاد نشده است</span>
                        </li>
                    </ul>
                    @error('children_subset_list')
                    <div class="invalid-feedback iransans small_font">{{ $message }}</div>
                    @enderror
                </div>
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
        <a role="button" href="{{ route("Contracts.index") }}"
           class="btn btn-outline-secondary iransans">
            <i class="fa fa-arrow-turn-right fa-1-2x me-1"></i>
            <span class="iransans">بازگشت به لیست</span>
        </a>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="edit_contract_employees_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ویرایش پرسنل</h5>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="mb-3 mb-md-0 col-12 col-md-3 col-lg-2">
                            <label class="form-label iransans">نام</label>
                            <input class="form-control text-center iransans" type="text" id="new_name">
                        </div>
                        <div class="mb-3 mb-md-0 col-12 col-md-3 col-lg-2">
                            <label class="form-label iransans">
                                کد ملی
                            </label>
                            <input class="form-control text-center iransans number_masked" data-mask="0000000000" type="text" id="new_national_code">
                        </div>
                        <div class="mb-3 mb-md-0 col-12 col-md-3 col-lg-2">
                            <label class="form-label iransans">موبایل</label>
                            <input class="form-control text-center iransans number_masked" data-mask="00000000000" type="text" id="new_mobile">
                        </div>
                        <div class="mb-md-0 col-12 col-md-2 col-lg-1 align-self-end text-center">
                            @if($contract->is_parent === 0)
                                <live-transfer :target="'table_data_records'" :elements="['#new_name','#new_national_code','#new_mobile']" :required="['#new_name','#new_national_code']" :class="'btn btn-outline-success w-100'" :operation_id="{{ $contract->id }}" route="{{ route("Contracts.pre_employee_operation","add") }}" :message="'آیا برای افزودن پرسنل جدید اطمینان دارید؟'">
                                    <i class="fa fa-plus fa-1-2x me-1"></i>
                                    <span class="iransans">ایجاد پرسنل جدید</span>
                                </live-transfer>
                            @else
                                <live-transfer :target="'table_data_records'" :elements="['#new_name','#new_national_code','#new_mobile']" :required="['#new_name','#new_national_code']" :class="'btn btn-outline-success w-100'" :operation_id="contract_id" route="{{ route("Contracts.pre_employee_operation","add") }}" :message="'آیا برای افزودن پرسنل جدید اطمینان دارید؟'">
                                    <i class="fa fa-plus fa-1-2x me-1"></i>
                                    <span class="iransans">ایجاد پرسنل جدید</span>
                                </live-transfer>
                            @endif
                        </div>
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-text">
                            @if($contract->is_parent === 0)
                                <live-transfer :target="'table_data_records'" :elements="['#delete_all_employee']" :class="'btn btn-sm btn-outline-danger'" :operation_id="{{ $contract->id }}" route="{{ route("Contracts.pre_employee_operation","delete_all") }}" :message="'آیا برای حذف کلیه پرسنل اطمینان دارید؟'">
                                    <i class="fa fa-trash-can fa-1-2x"></i>
                                </live-transfer>
                            @else
                                <live-transfer :target="'table_data_records'" :elements="['#delete_all_employee']" :class="'btn btn-sm btn-outline-danger'" :operation_id="contract_id" route="{{ route("Contracts.pre_employee_operation","delete_all") }}" :message="'آیا برای حذف کلیه پرسنل اطمینان دارید؟'">
                                    <i class="fa fa-trash-can fa-1-2x"></i>
                                </live-transfer>
                            @endif
                            <input type="checkbox" value="all" title="در صورت انتخاب این گزینه کلیه پرسنل به انضمام پرسنلی که ثبت نام کرده اند، حذف خواهند شد" class="ms-2" id="delete_all_employee">
                            <label class="iransans ms-1" title="در صورت انتخاب این گزینه کلیه پرسنل به انضمام پرسنلی که ثبت نام کرده اند، حذف خواهند شد" for="delete_all_employee">کلیه پرسنل</label>
                        </div>
                        <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام ، کد ملی و موبایل" data-table="search_table" v-on:input="filter_table">
                        <div class="input-group-text">
                            <i class="fa fa-search fa-1-2x"></i>
                        </div>
                    </div>
                    <div class="table-responsive scroll-35">
                        <table id="search_table" class="table table-striped table-bordered iransans text-center edit-page-table dynamic-table" data-filter="[1,2,3]">
                            <thead class="bg-menu-dark white-color">
                            <tr>
                                <th>شماره</th>
                                <th>نام</th>
                                <th>کد ملی</th>
                                <th>موبایل</th>
                                <th>توسط</th>
                                <th>ایجاد</th>
                                <th>ویرایش</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="row in table_data_records" :key='row.id'>
                                <td>
                                    <span>@{{ row.id }}</span>
                                </td>
                                <td>
                                    <input class="form-control iransans text-center" :id="`name_${row.id}`" :value="row.name">
                                </td>
                                <td>
                                    <input class="form-control iransans text-center number_masked" data-mask="0000000000" :id="`national_code_${row.id}`" :value="row.national_code">
                                </td>
                                <td>
                                    <input class="form-control iransans text-center" :id="`mobile_${row.id}`" :value="row.mobile">
                                </td>
                                <td>
                                    <span>@{{ row.user.name }}</span>
                                </td>
                                <td>
                                    <span>@{{ to_persian_date(row.created_at) }}</span>
                                </td>
                                <td>
                                    <span>@{{ to_persian_date(row.updated_at) }}</span>
                                </td>
                                <td>
                                    <live-transfer :target="'table_data_records'" :elements="[`#name_${row.id}`,`#national_code_${row.id}`,`#mobile_${row.id}`]" :required="[`#name_${row.id}`,`#national_code_${row.id}`]" :class="'btn btn-sm btn-outline-primary'" :operation_id="row.id" route="{{ route("Contracts.pre_employee_operation","edit") }}" :message="'آیا برای ویرایش پرسنل اطمینان دارید؟'">
                                        <i class="fa fa-edit fa-1-2x"></i>
                                    </live-transfer>
                                    <live-transfer :target="'table_data_records'" :class="'btn btn-sm btn-outline-danger'" :operation_id="row.id" route="{{ route("Contracts.pre_employee_operation","delete") }}" :message="'آیا برای حذف پرسنل اطمینان دارید؟'">
                                        <i class="fa fa-trash-can fa-1-2x"></i>
                                    </live-transfer>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rtl" id="new_subset_modal" tabindex="-1" role="dialog" aria-labelledby="new_subset_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد زیرمجموعه جدید</h6>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group mb-3 col-12">
                            <label class="form-label iransans">
                                عنوان
                            </label>
                            <input class="form-control text-center iransans" type="text" id="child_name">
                        </div>
                        <div class="form-group mb-3 col-12 col-lg-9">
                            <label class="form-label iransans">
                                بارگذاری مشخصات پرسنل مربوطه
                                <a href="{{ route("Contracts.excel_download") }}" class="button">(دانلود فایل اکسل نمونه)</a>
                            </label>
                            <s-file-browser :accept="['xlsx','xls']" :size="500000" :file_box_name="'excel_file'" :file_box_id="'excel_file'" :filename_box_id="'excel_browser_box'"></s-file-browser>
                        </div>
                        <div class="col-12 col-lg-3 align-self-center mb-3">
                            <live-transfer :target="'employees'" :elements="['#excel_file']" :required="['#excel_file']" :class="'btn btn-primary w-100 mt-1'" route="{{ route("Contracts.excel_upload") }}" :message="'آیا برای بارگذاری لیست پرسنل اطمینان دارید؟'">
                                <i class="fa fa-upload fa-1-2x me-1"></i>
                                <span class="iransans">بارگذاری فایل</span>
                            </live-transfer>
                        </div>
                        <div class="form-group col-12 mb-3">
                            <div class="mb-2">
                                <div class="input-group-text w-100 d-flex flex-row align-items-center justify-content-between @error('employees') is-invalid @enderror">
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#employees" v-on:click="return_modal='#new_subset_modal'"><i class="fa fa-magnifying-glass green-color fa-1-2x"></i></button>
                                        <span class="iransans">
                                                پرسنل بارگذاری شده :
                                                @{{ employees.length }}
                                                نفر
                                            </span>
                                    </div>
                                    <button v-if="import_errors.length > 0" type="button" class="btn btn-sm btn-outline-secondary iransans" data-bs-toggle="modal" data-bs-target="#import_errors" v-on:click="return_modal='#new_subset_modal'">
                                        <i class="fa fa-triangle-exclamation red-color fa-1-2x me-1"></i>
                                        خطای بارگذاری
                                    </button>
                                </div>
                                @error('employees')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success submit_button" v-on:click="add_subset">
                        <i class="fa fa-plus fa-1-2x me-1"></i>
                        <span class="iransans">ایجاد</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x ms-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="import_errors" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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
                    <button v-if="return_modal" type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" data-bs-toggle="modal" :data-bs-target="return_modal" v-on:click="return_modal=''">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                    <button v-else type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
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
                            <th>ردیف</th>
                            <th>نام</th>
                            <th>کد ملی</th>
                            <th>موبایل</th>
                            <th>حذف</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(employee,index) in employees" :key="employee.id">
                            <td>@{{ ++index }}</td>
                            <td>@{{ employee.name }}</td>
                            <td>@{{ employee.national_code }}</td>
                            <td>@{{ employee.mobile }}</td>
                            <td>
                                <i class="fa fa-trash hover-red fa-1-2x" v-on:click="employees.splice(index,1)"></i>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button v-if="return_modal" type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" data-bs-toggle="modal" :data-bs-target="return_modal" v-on:click="return_modal=''">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                    <button v-else type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
