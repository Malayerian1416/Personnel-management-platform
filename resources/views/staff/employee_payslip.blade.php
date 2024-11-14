@extends("staff.staff_dashboard")
@section('variables')
    <script>
        const allowed_organizations = @json($organizations);
    </script>
    @if(old("excel_columns") != null)
        <script>
            const excel_columns_data = @json(json_decode(old("excel_columns"),true));
        </script>
    @endif
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                فیش حقوقی پرسنل
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
            <button class="btn btn-outline-primary d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#reference_selection_modal">
                <i class="fa fa-search fa-1-7x"></i>
            </button>
            <button class="btn btn-outline-primary d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_payslip_modal">
                <i class="fa fa-plus-large fa-1-6x"></i>
            </button>
            <button class="btn btn-outline-danger d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#delete_all_modal">
                <i class="fa fa-trash-can fa-1-7x"></i>
            </button>
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام ، کد ملی ، کد شناسایی ، ساز مان و قرارداد" data-table="employee_payslips_table" v-on:input="filter_table">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table id="employee_payslips_table" class="table table-striped table-hover pointer-cursor sortArrowWhite" data-filter="[1,2,3,4]">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col" style="width: 70px" data-sortas="numeric"><span>شماره</span></th>
                        <th scope="col" style="width: 200px"><span>نام</span></th>
                        <th scope="col" style="width: 100px"><span>کد ملی</span></th>
                        <th scope="col" style="width: 120px"><span>کد شناسایی</span></th>
                        <th scope="col" style="width: 60px"><span>سال</span></th>
                        <th scope="col" style="width: 80px"><span>ماه</span></th>
                        <th scope="col" style="width: 120px"><span>توسط</span></th>
                        <th scope="col" style="width: 130px"><span>تاریخ ثبت</span></th>
                        <th scope="col" style="width: 130px"><span>تاریخ ویرایش</span></th>
                        <th scope="col" style="width: 150px"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td class="iransans">{{ $employee->id }}</td>
                            <td><span class="iransans">{{ $employee->employee->name }}</span></td>
                            <td><span class="iransans">{{ $employee->employee->national_code }}</span></td>
                            <td><span class="iransans">{{ $employee->i_number }}</span></td>
                            <td><span class="iransans">{{ $employee->persian_year }}</span></td>
                            <td><span class="iransans">{{ $employee->persian_month_name }}</span></td>
                            <td><span class="iransans">{{ $employee->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($employee->created_at)->format("Y/m/d") }}</span></td>
                            <td><span class="iransans">{{ verta($employee->updated_at)->format("Y/m/d") }}</span></td>
                            <td class="position-relative">
                                <div class="dropdown table-functions iransans">
                                    <a class="table-functions-button dropdown-toggle border-0 iransans info-color" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cog fa-1-2x"></i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can("show", "EmployeePaySlips")
                                            <a role="button" data-bs-toggle="modal" data-bs-target="#pdf_viewer_modal" v-on:click="ShowPdf('{{ route("EmployeePaySlips.show",$employee->id) }}','#pdf_viewer')" class="dropdown-item">
                                                <i class="fa fa-receipt"></i>
                                                <span class="iransans">نمایش فیش حقوقی</span>
                                            </a>
                                        @endcan
                                        @can("edit", "EmployeePaySlips")
                                                <div class="dropdown-divider"></div>
                                            <a role="button" href="{{ route("EmployeePaySlips.edit",$employee->id) }}" class="dropdown-item">
                                                <i class="fa fa-edit"></i>
                                                <span class="iransans">ویرایش</span>
                                            </a>
                                        @endcan
                                            @can("delete","EmployeePaySlips")
                                                <div class="dropdown-divider"></div>
                                                <form class="w-100" id="delete-form-{{ $employee->id }}" action="{{ route("EmployeePaySlips.destroy",$employee->id) }}" method="POST" v-on:submit="submit_form">
                                                    @csrf
                                                    @method("Delete")
                                                    <button type="submit" form="delete-form-{{ $employee->id }}" class="dropdown-item">
                                                        <i class="fa fa-trash"></i>
                                                        <span class="iransans">حذف</span>
                                                    </button>
                                                </form>
                                            @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        @if($query)
                            <tr><td colspan="10"><span class="iransans">اطلاعاتی در این ماه و سال وجود ندارد</span></td></tr>
                        @else
                            <tr><td colspan="10"><span class="iransans">در انتظار انتخاب قرارداد...</span></td></tr>
                        @endif
                    @endforelse
                    </tbody>
                    <tfoot class="bg-dark">
                    <tr>
                        <td colspan="12">
                            <div class="d-flex align-items-center justify-content-start gap-2 gap-lg-4 my-1 px-2">
                                <p class="iransans white-color mb-0">
                                    مجموع :
                                    {{ count($employees) }}
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
    <div class="modal fade" id="pdf_viewer_modal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">مشاهده فیش حقوقی</h5>
                </div>
                <div class="modal-body">
                    <div id="pdf_viewer" style="width: 100%;height: 100%"></div>
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
    <div class="modal fade" id="reference_selection_modal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">جستجو در اطلاعات فیش حقوقی</h5>
                </div>
                <div class="modal-body scroll-style">
                    <div class="row">
                        <form id="search_form" class="p-3" action="{{ route("EmployeePaySlips.query") }}" method="post" v-on:submit="submit_form">
                            @csrf
                            <div class="col-12 mb-3">
                                <div>
                                    <label class="iransans mb-1">سازمان</label>
                                </div>
                                <div class="mb-2">
                                    <tree-select :branch_node="true" dir="rtl" :is_multiple="false" @contract_selected="ContractSelected" :placeholder="'انتخاب کنید'" :database="organizations"></tree-select>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label iransans">سال فیش حقوقی</label>
                                <select class="form-control iransans selectpicker-select" name="query_year">
                                    <option value="{{ verta()->subYear()->format("Y") }}">{{ verta()->subYear()->format("Y") }}</option>
                                    <option selected value="{{ verta()->format("Y") }}">{{ verta()->format("Y") }}</option>
                                </select>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label iransans">ماه فیش حقوقی</label>
                                <select class="form-control iransans selectpicker-select" name="query_month">
                                    @foreach($persian_month as $key => $month)
                                        <option @if($key + 1 == verta()->subMonth()->format("n")) selected @endif value="{{$key + 1}}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input hidden v-model="contract_id" name="contract_id">
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button v-if="contract_id !== ''" type="submit" form="search_form" class="btn btn-success iransans">
                        <i class="fa fa-search fa-1-2x me-1"></i>
                        <span class="iransans">ادامه</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="delete_all_modal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">حذف کلی اطلاعات فیش حقوقی</h5>
                </div>
                <div class="modal-body scroll-style">
                    <div class="row">
                        <form id="delete_all_form" class="p-3" action="{{ route("EmployeePaySlips.destroyAll") }}" method="post" v-on:submit="submit_form">
                            @csrf
                            @method('delete')
                            <div class="col-12 mb-3">
                                <div>
                                    <label class="iransans mb-1">سازمان</label>
                                </div>
                                <div class="mb-2">
                                    <tree-select :branch_node="true" dir="rtl" :is_multiple="false" @contract_selected="ContractSelected" :placeholder="'انتخاب کنید'" :database="organizations"></tree-select>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label iransans">سال فیش حقوقی</label>
                                <select class="form-control iransans selectpicker-select" name="delete_query_year">
                                    <option value="{{ verta()->subYear()->format("Y") }}">{{ verta()->subYear()->format("Y") }}</option>
                                    <option selected value="{{ verta()->format("Y") }}">{{ verta()->format("Y") }}</option>
                                </select>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label iransans">ماه فیش حقوقی</label>
                                <select class="form-control iransans selectpicker-select" name="delete_query_month">
                                    @foreach($persian_month as $key => $month)
                                        <option @if($key + 1 == verta()->subMonth()->format("n")) selected @endif value="{{$key + 1}}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input hidden v-model="contract_id" name="contract_id">
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button v-if="contract_id !== ''" type="submit" form="delete_all_form" class="btn btn-success iransans">
                        <i class="fa fa-search fa-1-2x me-1"></i>
                        <span class="iransans">ادامه</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rtl" id="new_payslip_modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="new_payslip_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">بارگذاری فیش حقوقی</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label iransans">انتخاب قرارداد</label>
                            <tree-select :branch_node="true" @contract_selected="ContractSelected" dir="rtl" :is_multiple="false" :placeholder="'انتخاب کنید'" :database="organizations"></tree-select>
                        </div>
                        <div v-if="contract_id !== ''" class="col-12">
                            <label class="form-label iransans">
                                انتخاب فایل اکسل
                                <a :href="GetRoute('EmployeePaySlips.excel_download',[contract_id])" class="iransans">(دانلود قالب)</a>
                            </label>
                            <s-file-browser @file_selected="file_selected = true" @file_deselected="file_selected = false" :accept='["xls","xlsx"]' :size="800000"></s-file-browser>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-menu">
                    <button v-if="file_selected" type="button" class="btn btn-success iransans" v-on:click="UploadPaySlipFile">
                        <i class="fa fa-upload fa-1-2x me-1"></i>
                        <span class="iransans">بارگذاری فایل</span>
                    </button>
                    <button v-if="payslip_employees.length > 0" type="button" class="btn btn-outline-dark iransans" data-bs-toggle="modal" data-bs-target="#payslip_table_modal">
                        <i class="fa fa-magnifying-glass-dollar fa-1-2x me-1"></i>
                        <span class="iransans">مشاهده و تایید اطلاعات</span>
                    </button>
                    <button v-if="import_errors.length > 0 && payslip_employees.length === 0" type="button" class="btn btn-danger iransans" data-bs-toggle="modal" data-bs-target="#import_errors" v-on:click="return_modal='#new_payslip_modal'">
                        <i class="fa fa-exclamation-triangle fa-1-2x me-1"></i>
                        <span class="iransans">خطای بارگذاری</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rtl" id="payslip_table_modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="payslip_table_modal" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">اطلاعات بارگذاری شده پرسنل</h5>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control iranyekan text-center mb-3" placeholder="جستجو با نام و نام خانوادگی و کد ملی" data-table="employee_payslip_table" v-on:input="filter_table">
                    <div id="table-scroll-container">
                        <div id="table-scroll" class="table-scroll fixed">
                            <table id="employee_payslip_table" data-filter="[0,1]">
                                <thead class="bg-menu-dark white-color">
                                <tr class="iransans">
                                    <th scope="col"><span>نام پرسنل</span></th>
                                    <th scope="col"><span>کد ملی</span></th>
                                    <th v-for="(column,index) in payslip_template" v-if="column.ignore !== true && index !== parseInt(national_code_index)" scope="col"><span>@{{ column.title }}</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="iransans" v-for="employee in payslip_employees" :key="employee.id">
                                    <td>@{{ employee.name }}</td>
                                    <td>@{{ employee.national_code }}</td>
                                    <td v-for="(attribute,index) in employee.columns" :key="index">
                                        <input class="form-control text-center" :class="attribute.isNumber ? 'attr_sep' : null" type="text" :value="attribute.value" v-on:input="employee.columns[index].value = Number($event.target.value.replaceAll(',',''))">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-menu">
                    <button v-if="import_errors.length > 0" type="button" class="btn btn-danger iransans" data-bs-toggle="modal" data-bs-target="#import_errors" v-on:click="return_modal='#payslip_table_modal'">
                        <i class="fa fa-exclamation-triangle fa-1-2x me-1"></i>
                        <span class="iransans">خطای بارگذاری</span>
                    </button>
                    <button v-if="payslip_employees.length > 0" type="button" class="btn btn-success iransans" data-bs-toggle="modal" data-bs-target="#select_date_modal">
                        <i class="fa fa-check-double fa-1-2x me-1"></i>
                        <span class="iransans">تایید اطلاعات و انتخاب تاریخ</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-toggle="modal" data-bs-target="#new_payslip_modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rtl" id="import_errors" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="import_errors" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">خطا(های) بارگذاری فایل اکسل</h5>
                </div>
                <div class="modal-body" style="max-height: 70vh;overflow-y: auto">
                    <table class="table table-striped">
                        <thead class="bg-menu-dark white-color">
                        <tr class="iransans">
                            <th scope="col"><span>ردیف فایل</span></th>
                            <th scope="col"><span>کد ملی</span></th>
                            <th scope="col"><span>پیغام خطا</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="iransans" v-for="error in import_errors">
                            <td>@{{ error.row }}</td>
                            <td>@{{ error.national_code }}</td>
                            <td>@{{ error.message }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-toggle="modal" :data-bs-target="return_modal" v-on:click="return_modal=''">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rtl" id="select_date_modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="select_date_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">انتخاب سال و ماه فیش حقوقی</h5>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("EmployeePaySlips.store") }}" method="POST" :data-json="'payslip_employees'" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label iransans">سال فیش حقوقی</label>
                                <select class="form-control iransans selectpicker-select" name="year">
                                    <option value="{{ verta()->subYear()->format("Y") }}">{{ verta()->subYear()->format("Y") }}</option>
                                    <option selected value="{{ verta()->format("Y") }}">{{ verta()->format("Y") }}</option>
                                </select>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label iransans">ماه فیش حقوقی</label>
                                <select class="form-control iransans selectpicker-select" name="month">
                                    @foreach($persian_month as $key => $month)
                                        <option @if($key + 1 == verta()->subMonth()->format("n")) selected @endif value="{{$key + 1}}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="submit" form="main_submit_form" class="btn btn-success iransans">
                        <i class="fa fa-check-double fa-1-2x me-1"></i>
                        <span class="iransans">تایید اطلاعات و انتخاب تاریخ</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-toggle="modal" data-bs-target="#payslip_table_modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
