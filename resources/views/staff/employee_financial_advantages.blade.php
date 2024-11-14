@extends("staff.staff_dashboard")
@section('variables')
    <script>
        const allowed_organizations = @json($organizations);
        {{--const allowed_contracts = @json($contracts);--}}
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                مقادیر مالی احکام کارگزینی
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
            <button class="btn btn-outline-primary d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_solo_advantage_modal">
                <i class="fa fa-user-cog fa-1-6x"></i>
            </button>
            <button class="btn btn-outline-primary d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_advantage_modal">
                <i class="fa fa-users-cog fa-1-6x"></i>
            </button>
            <button class="btn btn-outline-danger d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#delete_all_modal">
                <i class="fa fa-trash-can fa-1-7x"></i>
            </button>
            <input type="text" class="form-control text-center iransans" data-table="financials_table" placeholder="جستجو با نام و کد ملی" v-on:input="filter_table">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table id="financials_table" class="table table-striped table-hover pointer-cursor sortArrowWhite" data-filter="[1,2,3]">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col" style="width: 100px"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col" style="width: 110px"><span>سال مؤثر</span></th>
                        <th scope="col" style="width: 150px"><span>توسط</span></th>
                        <th scope="col" style="width: 150px"><span>تاریخ ثبت</span></th>
                        <th scope="col" style="width: 150px"><span>تاریخ ویرایش</span></th>
                        <th scope="col" style="width: 200px"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td><span class="iransans">{{ $employee->id }}</span></td>
                            <td><span class="iransans">{{ $employee->employee->name }}</span></td>
                            <td><span class="iransans">{{ $employee->effective_year }}</span></td>
                            <td><span class="iransans">{{ $employee->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($employee->created_at)->format("Y/m/d") }}</span></td>
                            <td><span class="iransans">{{ verta($employee->updated_at)->format("Y/m/d") }}</span></td>
                            <td>
                                <div class="d-flex flex-row flex-wrap align-items-center justify-content-center gap-2 gap-lg-3">
                                    @can("edit", "EmployeeFinancialAdvantages")
                                        <a role="button" href="{{ route("EmployeeFinancialAdvantages.edit",$employee->id) }}" class="btn btn-sm btn-outline-dark">
                                            <i class="far fa-edit fa-1-2x vertical-middle"></i>
                                        </a>
                                    @endcan
                                        @can("delete","EmployeeFinancialAdvantages")
                                            <div>
                                                <form hidden id="delete-form-{{ $employee->id }}" action="{{ route("EmployeeFinancialAdvantages.destroy",$employee->id) }}" method="POST" v-on:submit="submit_form">
                                                    @csrf
                                                    @method("Delete")
                                                </form>
                                                <button type="submit" form="delete-form-{{ $employee->id }}" class="btn btn-sm btn-outline-dark">
                                                    <i class="fa fa-trash fa-1-2x vertical-middle"></i>
                                                </button>
                                            </div>
                                        @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        @if($query)
                            <tr><td colspan="7"><span class="iransans">اطلاعاتی در این سال وجود ندارد</span></td></tr>
                        @else
                            <tr><td colspan="7"><span class="iransans">در انتظار انتخاب قرارداد...</span></td></tr>
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
    <div class="modal fade" id="reference_selection_modal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">جستجو در اطلاعات</h6>
                </div>
                <div class="modal-body scroll-style">
                    <div class="row">
                        <form id="search_form" class="p-3" action="{{ route("EmployeeFinancialAdvantages.query") }}" method="post">
                            @csrf
                            <div class="col-12 mb-3">
                                <label class="form-label iransans mb-1">سازمان</label>
                                <tree-select :branch_node="true" dir="rtl" :is_multiple="false" @contract_selected="ContractSelected" :placeholder="'انتخاب کنید'" :database="organizations"></tree-select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label iransans mb-1">سال مؤثر</label>
                                <select class="form-control iransans selectpicker-select" name="effective_year">
                                    @for($i = 5; $i >= 0; $i--)
                                        <option @if(verta()->format("Y") == verta()->subYears($i)->format("Y")) selected @endif value="{{ verta()->subYears($i)->format("Y") }}">{{ verta()->subYears($i)->format("Y") }}</option>
                                    @endfor
                                </select>
                            </div>
                            <input hidden v-model="contract_id" name="contract_id">
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button v-if="contract_id !== ''" type="submit" form="search_form" class="btn btn-success iransans">
                        <i class="fa fa-search fa-1-2x me-1"></i>
                        <span class="iransans">جستجو</span>
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
                    <h6 class="modal-title iransans">حذف کلی اطلاعات مالی احکام کارگزینی</h6>
                </div>
                <div class="modal-body scroll-style">
                    <div class="row">
                        <form id="delete_all_form" class="p-3" action="{{ route("EmployeeFinancialAdvantages.destroyAll") }}" method="post" v-on:submit="submit_form">
                            @csrf
                            @method('delete')
                            <div class="col-12 mb-3">
                                <label class="iransans mb-1">سازمان</label>
                                <tree-select :branch_node="true" dir="rtl" :is_multiple="false" @contract_selected="ContractSelected" :placeholder="'انتخاب کنید'" :database="organizations"></tree-select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label iransans mb-1">سال مؤثر</label>
                                <select class="form-control iransans selectpicker-select" name="effective_year">
                                    @for($i = 5; $i >= 0; $i--)
                                        <option @if(verta()->format("Y") == verta()->subYears($i)->format("Y")) selected @endif value="{{ verta()->subYears($i)->format("Y") }}">{{ verta()->subYears($i)->format("Y") }}</option>
                                    @endfor
                                </select>
                            </div>
                            <input hidden v-model="contract_id" name="contract_id">
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button v-if="contract_id !== ''" type="submit" form="delete_all_form" class="btn btn-danger iransans">
                        <i class="fa fa-trash-can fa-1-2x me-1"></i>
                        <span class="iransans">حذف</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rtl" id="new_solo_advantage_modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="new_advantage_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">بارگذاری اطلاعات مالی حکم کارگزینی(فردی)</h6>
                </div>
                <div class="modal-body">
                    <form id="solo_main_submit_form" action="{{route("EmployeeFinancialAdvantages.store_solo")}}" method="post" data-json="advantage_columns" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label iransans">انتخاب قرارداد</label>
                                <tree-select :branch_node="true" @contract_selected="SoloContractSelected" dir="rtl" :is_multiple="false" :placeholder="'انتخاب کنید'" :database="organizations"></tree-select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label iransans">انتخاب پرسنل</label>
                                <select id="employees" name="employee_id" class="form-control iransans selectpicker-select" title="انتخاب کنید" size="20" data-live-search="true" v-on:change="SelectEmployee">
                                    <option v-for="employee in employees" :key="employee.id" :value="employee.id">@{{ employee.name }} - @{{ employee.national_code }}</option>
                                </select>
                            </div>
                            <div v-if="employee.length !== 0" class="col-12 col-lg-6 mb-2">
                                <label class="form-label iransans">دستمزد روزانه</label>
                                <input class="form-control text-center iransans separator" type="text" value="0" id="daily_wage" name="daily_wage">
                            </div>
                            <div v-if="employee.length !== 0" class="col-12 col-lg-6 mb-2">
                                <label class="form-label iransans">پایه سنوات</label>
                                <input class="form-control text-center iransans separator" type="text" value="0" id="prior_service" name="prior_service">
                            </div>
                            <div v-if="employee.length !== 0" class="col-12 col-lg-6 mb-2">
                                <label class="form-label iransans">روزهای کارکرد</label>
                                <input class="form-control text-center iransans" type="number" max="31" min="1" value="1" id="working_days" name="working_days" v-on:input="parseInt($event.target.value) > 31 ? $event.target.value = 31 : ''">
                            </div>
                            <div v-if="employee.length !== 0" class="col-12 col-lg-6 mb-2">
                                <label class="form-label iransans">گروه شغلی</label>
                                <input class="form-control text-center iransans" type="number" max="20" min="1" value="1" id="occupational_group" name="occupational_group" v-on:input="parseInt($event.target.value) > 20 ? $event.target.value = 20 : ''">
                            </div>
                            <div v-if="employee.length !== 0" class="col-12 col-lg-6 mb-2">
                                <label class="form-label iransans">فرزندان تحت تکفل</label>
                                <input class="form-control text-center iransans" type="number" max="20" min="0" value="0" id="occupational_group" name="count_of_children" v-on:input="parseInt($event.target.value) > 20 ? $event.target.value = 20 : ''">
                            </div>
                            <div v-if="employee.length !== 0" class="col-12 col-lg-6 mb-2">
                                <label class="form-label iransans mb-1">سال مؤثر</label>
                                <select class="form-control iransans selectpicker-select" name="effective_year">
                                    @for($i = 5; $i >= 0; $i--)
                                        <option @if(verta()->format("Y") == verta()->subYears($i)->format("Y")) selected @endif value="{{ verta()->subYears($i)->format("Y") }}">{{ verta()->subYears($i)->format("Y") }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div v-if="employee.length !== 0" class="col-12 mb-2">
                                <label class="form-label iransans">افزودن مزایا</label>
                                <div class="input-group">
                                    <input id="advantage_title" class="form-control text-center iransans" type="text" placeholder="عنوان مزایا">
                                    <input id="advantage_value" class="form-control text-center iransans separator" type="text" placeholder="مبلغ مزایا">
                                    <button type="button" class="btn btn-sm btn-outline-primary input-group-text pe-3 ps-3" v-on:click="AddAdvantage"><i class="fa fa-plus fa-1-2x"></i></button>
                                </div>
                            </div>
                            <div v-if="employee.length !== 0" class="col-12 mb-2">
                                <label class="form-label iransans">جدول مزایا</label>
                                <ul class="list-group">
                                    <li v-if="advantage_columns.length === 0" class="list-group-item d-flex align-items-center justify-content-center">
                                        <span class="iransans">عنوانی اضافه نشده است</span>
                                    </li>
                                    <li v-for="(advantage,index) in advantage_columns" :key="index" class="list-group-item d-flex align-items-center justify-content-center flex-column gap-2">
                                        <div class="input-group">
                                            <input class="iransans text-center form-control" :value="advantage.title" v-on:input="advantage.title = $event.target.value">
                                            <input :id="`advantage_value_${index}`" class="iransans text-center form-control separator" :value="advantage.value" v-on:input="advantage.value = $event.target.value">
                                            <button type="button" class="btn btn-sm btn-outline-danger input-group-text pe-3 ps-3" v-on:click="advantage_columns.splice(index,1)"><i class="fa fa-trash-can fa-1-2x"></i></button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button v-if="employee.length !== 0" type="submit" form="solo_main_submit_form" class="btn btn-success iransans">
                        <i class="fa fa-save fa-1-2x me-1"></i>
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
    <div class="modal fade rtl" id="new_advantage_modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="new_advantage_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">بارگذاری اطلاعات مالی حکم کارگزینی(گروهی)</h6>
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
                                <a href="{{route("EmployeeFinancialAdvantages.excel_download")}}" class="iransans">(دانلود قالب)</a>
                            </label>
                            <s-file-browser :accept='["xls","xlsx"]' :size="800000"></s-file-browser>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="button" class="btn btn-success iransans" v-on:click="UploadEmployeeAdvantages">
                        <i class="fa fa-upload fa-1-2x me-1"></i>
                        <span class="iransans">بارگذاری فایل</span>
                    </button>
                    <button v-if="employee_advantages.length > 0" type="button" class="btn btn-outline-dark iransans" data-bs-toggle="modal" data-bs-target="#employee_advantages_table_modal">
                        <i class="fa fa-magnifying-glass-dollar fa-1-2x me-1"></i>
                        <span class="iransans">مشاهده اطلاعات بارگذاری شده</span>
                    </button>
                    <button v-if="import_errors.length > 0 && employee_advantages.length === 0" type="button" class="btn btn-danger iransans" data-bs-toggle="modal" data-bs-target="#import_errors" v-on:click="return_modal='#new_payslip_modal'">
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
    <div class="modal fade rtl" id="employee_advantages_table_modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="employee_advantages_table_modal" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">اطلاعات بارگذاری شده پرسنل</h6>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control iranyekan text-center mb-3" placeholder="جستجو با نام و نام خانوادگی و کد ملی" data-table="employee_advantages_table" v-on:input="filter_table">
                    <div id="table-scroll-container">
                        <div id="table-scroll" class="table-scroll fixed">
                            <table id="employee_advantages_table" data-filter="[0,1]">
                                <thead class="bg-menu-dark white-color">
                                <tr class="iransans">
                                    <th scope="col"><span>نام پرسنل</span></th>
                                    <th scope="col"><span>کد ملی</span></th>
                                    <th scope="col"><span>دستمزد روزانه</span></th>
                                    <th scope="col"><span>پایه سنوات</span></th>
                                    <th scope="col"><span>روزهای کارکرد</span></th>
                                    <th scope="col"><span>گروه شغلی</span></th>
                                    <th scope="col"><span>اولاد تحت تکفل</span></th>
                                    <th v-for="(column,index) in advantage_columns" :key="index" scope="col"><span>@{{ column }}</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="iransans" v-for="(employee,index) in employee_advantages" :key="employee.id">
                                    <td>@{{ employee.name }}</td>
                                    <td>@{{ employee.national_code }}</td>
                                    <td><input class="form-control text-center attr_sep" type="text" :value="employee.daily_wage" v-on:input="employee.daily_wage = Number($event.target.value.replaceAll(',',''))"></td>
                                    <td><input class="form-control text-center attr_sep" type="text" :value="employee.prior_service" v-on:input="employee.prior_service = Number($event.target.value.replaceAll(',',''))"></td>
                                    <td><input class="form-control text-center attr_sep" type="text" :value="employee.working_days" v-on:input="employee.working_days = Number($event.target.value.replaceAll(',',''))"></td>
                                    <td><input class="form-control text-center attr_sep" type="text" :value="employee.occupational_group" v-on:input="employee.occupational_group = Number($event.target.value.replaceAll(',',''))"></td>
                                    <td><input class="form-control text-center attr_sep" type="text" :value="employee.count_of_children" v-on:input="employee.count_of_children = Number($event.target.value.replaceAll(',',''))"></td>
                                    <td v-for="(attribute,index) in employee.advantages" :key="index">
                                        <input class="form-control text-center attr_sep" type="text" :value="attribute.value" v-on:input="employee.advantages[index].value = Number($event.target.value.replaceAll(',',''))">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-menu">
                    <button v-if="import_errors.length > 0" type="button" class="btn btn-danger iransans" data-bs-toggle="modal" data-bs-target="#import_errors" v-on:click="return_modal='#employee_advantages_table_modal'">
                        <i class="fa fa-exclamation-triangle fa-1-2x me-1"></i>
                        <span class="iransans">خطای بارگذاری</span>
                    </button>
                    <button v-if="employee_advantages.length > 0" type="button" class="btn btn-success iransans" data-bs-toggle="modal" data-bs-target="#select_date_modal">
                        <i class="fa fa-check-double fa-1-2x me-1"></i>
                        <span class="iransans">تایید و انتخاب سال موثر</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-toggle="modal" data-bs-target="#new_advantage_modal">
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
                    <h6 class="modal-title iransans">خطا(های) بارگذاری فایل اکسل</h6>
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
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-toggle="modal" data-bs-target="#new_advantage_modal">
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
                    <h6 class="modal-title iransans">انتخاب سال مؤثر</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("EmployeeFinancialAdvantages.store") }}" method="POST" :data-json="JSON.stringify(['employee_advantages','advantage_columns'])" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label iransans">سال مؤثر در تاریخ شروع قرارداد</label>
                                <select class="form-control iransans selectpicker-select" name="year">
                                    @for($i = 5; $i >= 0; $i--)
                                        <option @if(verta()->format("Y") == verta()->subYears($i)->format("Y")) selected @endif value="{{ verta()->subYears($i)->format("Y") }}">{{ verta()->subYears($i)->format("Y") }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="submit" form="main_submit_form" class="btn btn-success iransans">
                        <i class="fa fa-save fa-1-2x me-1"></i>
                        <span class="iransans">ارسال و ذخیره</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-toggle="modal" data-bs-target="#employee_advantages_table_modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
