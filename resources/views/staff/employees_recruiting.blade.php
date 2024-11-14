@extends("staff.staff_dashboard")
@section('variables')
    <script>
        let table_data = @json($employees);
        let sms_bank = @json($sms_phrase_categories);
        const allowed_organizations = @json($organizations);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                ثبت نام پرسنل
                <span class="vertical-middle ms-1 text-muted">تایید ، عدم تایید ، حذف</span>
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
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle iransans group-dropdown-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    عملیات گروهی
                </button>
                <ul class="dropdown-menu wide">
                    @can('confirm','EmployeesRecruiting')
                        <li>
                            <button type="submit" form="activation-form-" class="dropdown-item btn-primary" data-bs-toggle="modal" data-bs-target="#registration_confirm_modal" v-on:click="employees=[]">
                                <i class="fa fa-check fa-1-2x vertical-middle w-15 text-center"></i>
                                <span class="iransans">تایید ثبت نام</span>
                            </button>
                        </li>
                    @endcan
                    @can('refuse','EmployeesRecruiting')
                        <li class="dropdown-divider"></li>
                        <li>
                            <button type="submit" form="activation-form-" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#refuse_registration_modal" v-on:click="employees=[]">
                                <i class="fa fa-times fa-1-2x vertical-middle w-15 text-center"></i>
                                <span class="iransans">عدم تایید ثبت نام</span>
                            </button>
                        </li>
                    @endcan
                    @can('reload_data','EmployeesRecruiting')
                        <li class="dropdown-divider"></li>
                        <li>
                            <button type="submit" form="activation-form-" class="dropdown-item btn-primary" data-bs-toggle="modal" data-bs-target="#reload_data_modal" v-on:click="employees=[]">
                                <i class="fa fa-refresh fa-1-2x vertical-middle w-15 text-center"></i>
                                <span class="iransans">بارگذاری مجدد اطلاعات</span>
                            </button>
                        </li>
                    @endcan
                </ul>
            </div>
            <input type="text" class="form-control text-center iransans" data-table="registration_table" placeholder="جستجو با نام، کدملی، سازمان، قرارداد و کد رهگیری" v-on:input="filter_table">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table id="registration_table" class="table table-hover table-striped pointer-cursor sortArrowWhite" data-filter="[1,2,3,4,6]">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col" style="width: 50px" data-sortas="numeric"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col" style="width: 100px"><span>کد ملی</span></th>
                        <th scope="col"><span>سازمان</span></th>
                        <th scope="col"><span>قرارداد</span></th>
                        <th scope="col" style="width: 110px;"><span>تلفن همراه</span></th>
                        <th scope="col" style="width: 140px"><span>کد رهگیری</span></th>
                        <th scope="col" style="width: 100px"><span>تاریخ ثبت نام</span></th>
                        <th scope="col" style="width: 80px"><span>بارگذاری مجدد</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($employees as $employee)
                        <tr @if($employee->reload_data && $employee->reload_data->is_loaded == 1) class="edited" data-bs-toggle="tooltip" title="اطلاعات درخواست شده مجدداً بارگذاری شد" @elseif($employee->reload_data && $employee->reload_data->is_loaded == 0) data-bs-toggle="tooltip" title="دارای درخواست بارگذاری مجدد اطلاعات" class="editing" @endif @contextmenu.prevent="$refs.contextMenu.open" v-on:contextmenu="RegistrationContextMenu($event,{{$employee->id}})">
                            <td class="iransans">{{ $employee->id }}</td>
                            <td><span class="iransans">{{ $employee->name }}</span></td>
                            <td><span class="iransans">{{ $employee->national_code }}</span></td>
                            <td><span class="iransans">{{ $employee->contract->organization->name }}</span></td>
                            <td><span class="iransans">{{ $employee->contract->name }}</span></td>
                            <td><span class="iransans">{{ $employee->mobile }}</span></td>
                            <td><span class="iransans">{{ $employee->tracking_code }}</span></td>
                            <td><span class="iransans">{{ verta($employee->registration_date)->format("Y/m/d") }}</span></td>
                            <td>
                                @if($employee->reload_data && $employee->reload_data->is_loaded)
                                    <i class="fa fa-check-circle fa-1-4x green-color"></i>
                                @elseif($employee->reload_data && $employee->reload_data->is_loaded == 0)
                                    <i class="fa fa-times-circle fa-1-4x red-color"></i>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="iransans" colspan="9">اطلاعاتی وجود ندارد</td>
                        </tr>
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
        <vue-context v-cloak ref="contextMenu">
            <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                <button class="dropdown-item d-flex align-items-center justify-content-start" data-bs-toggle="modal" data-bs-target="#docs_modal" v-on:click="view_employee_information">
                    <i class="fa fa-magnifying-glass fa-1-2x me-2"></i>
                    <span class="iransans">مشاهده مدارک</span>
                </button>
            </li>
            @can('confirm','EmployeesRecruiting')
                <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                    <button class="dropdown-item d-flex align-items-center justify-content-start" data-bs-toggle="modal" data-bs-target="#registration_confirm_modal">
                        <i class="fa fa-check fa-1-2x me-2"></i>
                        <span class="iransans">تایید</span>
                    </button>
                </li>
            @endcan
            @can('refuse','EmployeesRecruiting')
                <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                    <button class="dropdown-item d-flex align-items-center justify-content-start" data-bs-toggle="modal" data-bs-target="#refuse_registration_modal">
                        <i class="fa fa-times fa-1-2x me-2"></i>
                        <span class="iransans">عدم تایید</span>
                    </button>
                </li>
            @endcan
            @can('reload_data','EmployeesRecruiting')
                <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                    <button class="dropdown-item d-flex align-items-center justify-content-start" data-bs-toggle="modal" data-bs-target="#reload_data_modal">
                        <i class="fa fa-refresh fa-1-2x me-2"></i>
                        <span class="iransans">بارگذاری مجدد اطلاعات</span>
                    </button>
                </li>
            @endcan
        </vue-context>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="docs_modal" tabindex="-1" aria-labelledby="docs_modal" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <input type="hidden" id="employee_id">
                <div class="modal-header">
                    <h5 class="modal-title iransans">
                        مشاهده مدارک پرسنل
                        <span v-if="employees.length === 1" class="iransans text-muted mb-2 d-inline-block"> - @{{ employee.name }} (@{{ employee.national_code }})</span>
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="img-operation-panel">
                        <div class="d-flex flex-row align-items-center justify-content-center gap-4 p-4">
                            <button class="btn btn-sm btn-outline-secondary" v-on:click="view_employee_information;return_modal='#docs_modal'" title="مشاهده و ویرایش اطلاعات فردی و شغلی" data-bs-toggle="modal" data-bs-target="#db_information_modal">
                                <i class="img-operation-button fa fa-database fa-2x"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" v-on:click="image_operations('zoom_in')" title="بزرگنمایی">
                                <i class="img-operation-button fa fa-magnifying-glass-plus fa-2x"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" v-on:click="image_operations('zoom_out')" title="کوچک نمایی">
                                <i class="img-operation-button fa fa-magnifying-glass-minus fa-2x"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" data-bs-target="#print_modal" data-bs-toggle="modal" title="چاپ" v-on:click="image_operations('print')">
                                <i class="img-operation-button fa fa-print fa-2x"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" title="چرخش" v-on:click="image_operations('rotate')">
                                <i class="img-operation-button fa fa-rotate fa-2x"></i>
                            </button>
                        </div>
                    </div>
                    <div v-if="docs.length > 0" class="docs-preview position-relative w-100 pe-3 ps-3 pt-3 pb-3">
                        <div class="text-center" v-for="(doc,index) in docs">
                            <vue-image :id="`doc_img_preview_${index + 1}`" v-show="doc_show === index + 1" alt="همیاران شمال شرق" :img_src="doc.view" :css_class="'img-preview'" :error_image="'{{ asset("/images/image-error.svg") }}'"></vue-image>
                        </div>
                    </div>
                    <div v-else class="docs-preview position-relative w-100 pe-3 ps-3 pt-3 pb-3 d-flex align-items-center flex-column justify-content-center">
                        <div class="image-error" v-cloak>
                            <i class="fa fa-link-slash fa-1-2x text-muted"></i>
                            <div class="iransans text-muted form-text text-center">تصویری وجود ندارد!</div>
                        </div>
                    </div>
                    <div class="d-flex flex-row align-items-center justify-content-between w-100 image-thumbnails">
                        <div v-for="(doc,index) in docs" class="p-2 doc-image">
                            <vue-image :id="`doc_img_${index + 1}`" :img_src="doc.view" :css_class="'img-fluid doc-thumbnail'" :error_image="'{{ asset("/images/image-error.svg") }}'">
                                <slot>
                                    <div :id="`doc_img_overlay_${index + 1}`" :data-image_index="index + 1" class="doc-image-overlay" :class="index + 1 === 1 ? 'selected' : ''" v-on:click="select_doc_preview"></div>
                                </slot>
                            </vue-image>
                        </div>
                        <div v-if="docs.length === 0" class="alert alert-danger w-100 text-center iransans" role="alert">
                            مدارک تصویری بارگذاری نشده است
                        </div>
                    </div>
                    <div v-if="employees.length > 1" class="d-block w-100 text-center p-2">
                        <div class="d-flex flex-row align-items-center justify-content-center gap-2">
                            <button type="button" :disabled="EmployeeIndex === 0" class="btn btn-sm btn-outline-primary" v-on:click="view_information_queue('previous')">
                                <i class="fa fa-forward fa-1-2x"></i>
                            </button>
                            <span class="iransans border border-primary rounded-2 p-2 m-0" style="font-weight: 700;color: #0a58ca;line-height: 25px;min-width: 175px">
                                @{{ employee.name }} (@{{ employee.national_code }})
                            </span>
                            <button :disabled="EmployeeIndex === employees.length - 1" type="button" class="btn btn-sm btn-outline-primary" v-on:click="view_information_queue('next')">
                                <i class="fa fa-backward fa-1-2x"></i>
                            </button>
                        </div>
                        <div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="button" form="main_submit_form" class="btn btn-success submit_button" data-bs-toggle="modal" data-bs-target="#registration_confirm_modal" v-on:click="return_modal='#docs_modal'">
                        <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
                        <span class="iransans">تایید ثبت نام</span>
                    </button>
                    <button v-if="return_modal" id="close_docs_modal" type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" data-bs-toggle="modal" :data-bs-target="return_modal" v-on:click="return_modal=''">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                    <button v-else id="close_docs_modal" type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" v-on:click="docs = []">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rtl" id="db_information_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="db_information_modal" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <input type="hidden" id="employee_id">
                <div class="modal-header">
                    <h5 class="modal-title iransans">
                        مشاهده اطلاعات پرسنل
                        <span v-if="employees.length === 1" class="iransans text-muted mb-2 d-inline-block">(@{{ employee.name }} - @{{ employee.national_code }})</span>
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">نام</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.first_name">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">نام خانوادگی</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.last_name">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">کد ملی</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.national_code">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">شماره شناسنامه</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.id_number">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">نام پدر</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.father_name">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">تاریخ تولد</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.birth_date">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">محل تولد</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.birth_city">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">محل صدور</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.issue_city">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">جنسیت</label>
                            <select class="form-select iransans" v-model="employee_db_information.gender">
                                <option :selected="employee_db_information.gender === 'm'" value="m">مرد</option>
                                <option :selected="employee_db_information.gender === 'f'" value="f">زن</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">وضعیت تاهل</label>
                            <select class="form-select iransans" v-model="employee_db_information.marital_status">
                                <option :selected="employee_db_information.marital_status === 'm'" value="m">متاهل</option>
                                <option :selected="employee_db_information.marital_status === 's'" value="f">مجرد</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">خدمت سربازی</label>
                            <select class="form-select iransans" v-model="employee_db_information.military_status">
                                <option :selected="employee_db_information.military_status === 'h'" value="h">کارت پایان خدمت</option>
                                <option :selected="employee_db_information.military_status === 'e'" value="e">کارت معافیت</option>
                                <option :selected="employee_db_information.military_status === 'n'" value="n">در حال تحصیل</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">تحصیلات</label>
                            <select class="form-select iransans" v-model="employee_db_information.education">
                                <option :selected="employee_db_information.education === 'در حال تحصیل'" value="در حال تحصیل">در حال تحصیل</option>
                                <option :selected="employee_db_information.education === 'زیر دیپلم و دیپلم'" value="زیر دیپلم و دیپلم">زیر دیپلم و دیپلم</option>
                                <option :selected="employee_db_information.education === 'کاردانی'" value="کاردانی">کاردانی</option>
                                <option :selected="employee_db_information.education === 'کارشناسی'" value="کارشناسی">کارشناسی</option>
                                <option :selected="employee_db_information.education === 'کارشناسی ارشد'" value="کارشناسی ارشد">کارشناسی ارشد</option>
                                <option :selected="employee_db_information.education === 'دکتری'" value="دکتری">دکتری</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">تعداد فرزندان</label>
                            <input type="number" class="form-control text-center iransans" v-model="employee_db_information.children_count">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">فرزندان مشمول حق اولاد</label>
                            <input type="number" class="form-control text-center iransans" v-model="employee_db_information.included_children_count">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">آدرس پست الکترونیکی</label>
                            <input type="email" class="form-control" v-model="employee_db_information.email">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">شماره موبایل</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.mobile">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">تلفن ثابت</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.phone">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">آدرس منزل</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.address">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">محل استقرار</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.job_seating">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">عنوان شغل</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.job_title">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">نام بانک</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.bank_name">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">شماره حساب</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.bank_account">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">شماره کارت</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.credit_card">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">شماره شبا</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.sheba_number">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">شماره بیمه</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.insurance_number">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">سابقه بیمه (روز)</label>
                            <input type="text" class="form-control text-center iransans" v-model="employee_db_information.insurance_days">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-menu">
                    <live-transfer :target="'employee_db_information'" :class="'btn btn btn-success'" route="{{ route("EditEmployeeDatabaseInformation") }}" :message="'آیا برای ویرایش اطلاعات اطمینان دارید؟'">
                        <i class="fa fa-edit fa-1-2x me-1"></i>
                        <span class="iransans">ویرایش اطلاعات</span>
                    </live-transfer>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-toggle="modal" data-bs-target="#docs_modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rtl" id="registration_confirm_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="registration_confirm_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <input type="hidden" id="employee_id">
                <div class="modal-header">
                    <h5 class="modal-title iransans">
                        تایید ثبت نام
                    </h5>
                </div>
                <div class="modal-body" style="max-height: 70vh;overflow-y: auto">
                    <form id="confirm_submit_form" class="p-3" action="{{ route("EmployeesRecruiting.confirm") }}" method="POST" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div v-if="employees.length === 1" class="form-group col-12 mb-3">
                                <label class="form-label iransans mb-2">پرسنل انتخاب شده</label>
                                <span class="form-control iransans border rounded-2">
                                    @{{ `${employees[0].name} (${employees[0].national_code})` }}
                                </span>
                                <select hidden name="employees[]">
                                    <option :value="employees[0].id"></option>
                                </select>
                            </div>
                            <div v-else class="form-group col-12 mb-3">
                                <label class="form-label iransans mb-2">قرارداد</label>
                                <tree-select :branch_node="true" :clearable="false" @contract_selected="RegistrationContractSelected" dir="rtl" :is_multiple="false" :placeholder="'انتخاب کنید'" :database="organizations"></tree-select>
                                <label class="form-label iransans mb-2 mt-3">پرسنل</label>
                                <select hidden id="ConfirmEmployees" class="form-control iransans employees_select" multiple data-size="15" data-live-search="true" data-selected-text-format="count > 3" data-actions-box="true" name="employees[]">
                                    <option v-for="employee in employees" :key="employee.id" :value="employee.id">@{{ `${employee.name}(${employee.national_code})` }}</option>
                                </select>
                                <div v-if="employees.length === 0" class="alert alert-danger iransans text-center" role="alert">
                                    پرسنلی برای ثبت نام وجود ندارد
                                </div>
                            </div>
                            <div class="form-group col-12 mb-3">
                                <label class="form-label iransans mb-2">تاریخ شروع قرارداد</label>
                                <input :disabled="employees.length === 0" class="form-control text-center iransans @error('start_date') is-invalid @enderror persian_datepicker_range_from" tabindex="-1" readonly type="text" name="start_date" value="{{ verta()->format("Y/m/01") }}">
                            </div>
                            <div class="form-group col-12 mb-3">
                                <label class="form-label iransans mb-2">تاریخ پایان قرارداد</label>
                                <input :disabled="employees.length === 0" class="form-control text-center iransans @error('end_date') is-invalid @enderror persian_datepicker_range_to" tabindex="-1" readonly type="text" name="end_date" value="{{ verta()->addMonths(3)->subDays(verta()->format("j"))->format("Y/m/d") }}">
                            </div>
                            <div class="form-group col-12 mb-3">
                                <input :disabled="employees.length === 0" type="checkbox" name="send_sms_permission" v-model="sms_send_permission" value="true" class="form-check d-inline-block vertical-middle" tabindex="-1" v-on:change="refresh_selects">
                                <label class="form-label iransans mb-2" for="sms_send_permission">
                                    ارسال پیامک
                                </label>
                                <select :disabled="!sms_send_permission" class="form-control text-center iransans selectpicker-select mb-3 @error('sms_phrase_id') is-invalid @enderror" v-model="select_model" tabindex="-1" title="انتخاب کنید" data-container="body" data-size="10" data-live-search="true" data-place="confirm_sms_text" name="sms_phrase_id" v-on:change="place_sms_text">
                                    @forelse($sms_phrase_categories as $category)
                                        <optgroup style="font-size: 18px" label="{{ $category->name }}">
                                            @forelse($category->phrases as $phrase)
                                                <option value="{{ $phrase->id }}">{{ $phrase->name }}</option>
                                            @empty
                                            @endforelse
                                        </optgroup>
                                        <option data-divider="true"></option>
                                    @empty
                                    @endforelse
                                </select>
                                <textarea :disabled="!sms_send_permission || employees.length === 0" name="sms_text" id="confirm_sms_text" class="form-control iransans" tabindex="-1"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button v-if="employees.length !== 0" type="submit" form="confirm_submit_form" class="btn btn-success submit_button">
                        <i v-if="!button_loading" class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
                        <i v-else class="submit_button_icon fa fa-circle-notch fa-1-2x me-1 fa-spin"></i>
                        <span class="iransans">تایید و ارسال نهایی</span>
                    </button>
                    <button v-if="employees.length !== 0" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#docs_modal" v-on:click="view_employee_information();return_modal='#registration_confirm_modal'">
                        <i class="fa fa-magnifying-glass fa-1-2x me-1"></i>
                        <span class="iransans">مشاهده اطلاعات</span>
                    </button>
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
    <div class="modal fade rtl" id="reload_data_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="reload_data_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <input type="hidden" id="employee_id">
                <div class="modal-header">
                    <h5 class="modal-title iransans">
                        درخواست بارگذاری مجدد اطلاعات
                    </h5>
                </div>
                <div class="modal-body">
                    <form id="reload_submit_form" class="p-3" action="{{ route("EmployeesRecruiting.reload_data") }}" method="POST" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div v-if="employees.length === 1" class="form-group col-12 mb-3">
                                <label class="form-label iransans mb-2">پرسنل انتخاب شده</label>
                                <span class="form-control iransans border rounded-2">
                                    @{{ `${employees[0].name} (${employees[0].national_code})` }}
                                </span>
                                <select hidden="hidden" name="employees[]">
                                    <option :value="employees[0].id"></option>
                                </select>
                            </div>
                            <div v-else class="form-group col-12 mb-3">
                                <label class="form-label iransans mb-2">قرارداد</label>
                                <tree-select :branch_node="true" :clearable="false" @contract_selected="RegistrationContractSelected" dir="rtl" :is_multiple="false" :placeholder="'انتخاب کنید'" :database="organizations"></tree-select>
                                <label class="form-label iransans mb-2 mt-3">پرسنل</label>
                                <select hidden id="ReloadEmployees" class="form-control iransans employees_select" multiple data-size="15" data-live-search="true" data-selected-text-format="count > 3" data-actions-box="true" name="employees[]">
                                    <option v-for="employee in employees" :key="employee.id" :value="employee.id">@{{ `${employee.name}(${employee.national_code})` }}</option>
                                </select>
                                <div v-if="employees.length === 0" class="alert alert-danger iransans text-center" role="alert">
                                    پرسنلی برای ثبت نام وجود ندارد
                                </div>
                            </div>
                            <div class="form-group col-12 mb-3">
                                <label class="form-label iransans mb-2">انتخاب اطلاعات</label>
                                <select class="form-control text-center iransans selectpicker-select @error('db_titles') is-invalid is-invalid-fake @enderror" data-selected-text-format="count > 3" multiple data-actions-box="true" tabindex="-1" title="انتخاب کنید" data-container="body" data-size="10" data-live-search="true" name="db_titles[]">
                                    <option value="first_name">نام</option>
                                    <option value="last_name">نام خانوادگی</option>
                                    <option value="father_name">نام پدر</option>
                                    <option value="birth_date">تاریخ تولد</option>
                                    <option value="birth_city">محل تولد</option>
                                    <option value="issue_city">محل صدور</option>
                                    <option value="id_number">شماره شناسنامه</option>
                                    <option value="national_code">کد ملی</option>
                                    <option value="gender">جنسیت</option>
                                    <option value="marital_status">وضعیت تاهل</option>
                                    <option value="military_status">خدمت سربازی</option>
                                    <option value="education">تحصیلات</option>
                                    <option value="children_count">تعداد فرزندان</option>
                                    <option value="included_children_count">فرزندان مشمول حق اولاد</option>
                                    <option value="email">آدرس پست الکترونیکی</option>
                                    <option value="mobile">شماره موبایل</option>
                                    <option value="phone">تلفن ثابت</option>
                                    <option value="address">آدرس منزل</option>
                                    <option value="job_seating">محل استقرار</option>
                                    <option value="job_title">عنوان شغل</option>
                                    <option value="bank_name">نام بانک</option>
                                    <option value="bank_account">شماره حساب</option>
                                    <option value="credit_card">شماره کارت</option>
                                    <option value="sheba_number">شماره شبا</option>
                                    <option value="insurance_number">شماره بیمه</option>
                                    <option value="insurance_days">سابقه بیمه</option>
                                </select>
                                @error('db_titles')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12 mb-3">
                                <label class="form-label iransans mb-2">انتخاب مدارک</label>
                                <select class="form-control text-center iransans selectpicker-select @error('doc_titles') is-invalid is-invalid-fake @enderror" data-selected-text-format="count > 3" multiple data-actions-box="true" tabindex="-1" title="انتخاب کنید" data-container="body" data-size="10" data-live-search="true" name="doc_titles[]">
                                    <option value="birth_certificate">تصویر صفحات شناسنامه</option>
                                    <option value="national_card">تصویر کارت ملی</option>
                                    <option value="military_certificate">تصویر کارت پایان خدمت</option>
                                    <option value="education_certificate">تصویر آخرین مدرک تحصیلی</option>
                                    <option value="personal_photo">تصویر عکس پرسنلی</option>
                                    <option value="insurance_confirmation">تصویر تاییدیه بیمه</option>
                                </select>
                                @error('doc_titles')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12 mb-3">
                                <input :disabled="employees.length === 0" type="checkbox" name="send_sms_permission" v-model="sms_send_permission" value="true" class="form-check d-inline-block vertical-middle" tabindex="-1">
                                <label class="form-label iransans mb-2" for="sms_send_permission">
                                    ارسال پیامک
                                </label>
                                <select :disabled="!sms_send_permission" class="form-control text-center iransans selectpicker-select mb-3" v-model="select_model" tabindex="-1" title="انتخاب کنید" data-container="body" data-size="10" data-live-search="true" data-place="reload_sms_text" name="sms_phrase_id" v-on:change="place_sms_text">
                                    @forelse($sms_phrase_categories as $category)
                                        <optgroup style="font-size: 18px" label="{{ $category->name }}">
                                            @forelse($category->phrases as $phrase)
                                                <option value="{{ $phrase->id }}">{{ $phrase->name }}</option>
                                            @empty
                                            @endforelse
                                        </optgroup>
                                        <option data-divider="true"></option>
                                    @empty
                                    @endforelse
                                </select>
                                <textarea :disabled="!sms_send_permission || employees.length === 0" name="sms_text" id="reload_sms_text" class="form-control iransans @error('sms_text') is-invalid is-invalid-fake @enderror" tabindex="-1">
                                    {{old('sms_text')}}
                                </textarea>
                                @error('sms_text')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button v-if="employees.length !== 0" type="submit" form="reload_submit_form" class="btn btn-success submit_button">
                        <i v-if="!button_loading" class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
                        <i v-else class="submit_button_icon fa fa-circle-notch fa-1-2x me-1 fa-spin"></i>
                        <span class="iransans">ارسال نهایی</span>
                    </button>
                    <button v-if="employees.length !== 0" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#docs_modal" v-on:click="view_employee_information();return_modal='#registration_confirm_modal'">
                        <i class="fa fa-magnifying-glass fa-1-2x me-1"></i>
                        <span class="iransans">مشاهده مدارک</span>
                    </button>
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
    <div class="modal fade rtl" id="refuse_registration_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="refuse_registration_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <input type="hidden" id="employee_id">
                <div class="modal-header">
                    <h5 class="modal-title iransans">
                        عدم تایید ثبت نام
                    </h5>
                </div>
                <div class="modal-body">
                    <form id="refuse_submit_form" class="p-3" action="{{ route("EmployeesRecruiting.refuse") }}" method="POST" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div v-if="employees.length === 1" class="form-group col-12 mb-3">
                                <label class="form-label iransans mb-2">پرسنل انتخاب شده</label>
                                <span class="form-control iransans border rounded-2">
                                    @{{ `${employees[0].name} (${employees[0].national_code})` }}
                                </span>
                                <select hidden="hidden" name="employees[]">
                                    <option :value="employees[0].id"></option>
                                </select>
                            </div>
                            <div v-else class="form-group col-12 mb-3">
                                <label class="form-label iransans mb-2">قرارداد</label>
                                <tree-select :branch_node="true" :clearable="false" @contract_selected="RegistrationContractSelected" dir="rtl" :is_multiple="false" :placeholder="'انتخاب کنید'" :database="organizations"></tree-select>
                                <label class="form-label iransans mb-2 mt-3">پرسنل</label>
                                <select hidden id="RefuseEmployees" class="form-control iransans employees_select" multiple data-size="15" data-live-search="true" data-selected-text-format="count > 3" data-actions-box="true" name="employees[]">
                                    <option v-for="employee in employees" :key="employee.id" :value="employee.id">@{{ `${employee.name}(${employee.national_code})` }}</option>
                                </select>
                                <div v-if="employees.length === 0" class="alert alert-danger iransans text-center" role="alert">
                                    پرسنلی برای ثبت نام وجود ندارد
                                </div>
                            </div>
                            <div class="form-group col-12 mb-3">
                                <input class="form-check d-inline-block vertical-middle" type="checkbox" id="delete_employees" name="delete_employees" value="true">
                                <label class="form-label d-inline iransans mb-2" for="delete_employees">
                                    <strong>حذف از لیست ثبت نام</strong>
                                    <span class="text-muted d-inline">
                                            (با انتخاب این گزینه، اطلاعات پرسنل از لیست موقت پرسنل قرارداد حذف و امکان ثبت نام مجدد میسر نمی باشد)
                                        </span>
                                </label>
                            </div>
                            <div class="form-group col-12 mb-3">
                                <input :disabled="employees.length === 0" type="checkbox" name="send_sms_permission" v-model="sms_send_permission" value="true" class="form-check d-inline-block vertical-middle" tabindex="-1" v-on:change="refresh_selects">
                                <label class="form-label iransans mb-2" for="sms_send_permission">
                                    ارسال پیامک
                                </label>
                                <select :disabled="!sms_send_permission" class="form-control text-center iransans selectpicker-select mb-3 @error('sms_phrase_id') is-invalid @enderror" v-model="select_model" tabindex="-1" title="انتخاب کنید" data-container="body" data-size="10" data-live-search="true" data-place="refuse_sms_text" name="sms_phrase_id" v-on:change="place_sms_text">
                                    @forelse($sms_phrase_categories as $category)
                                        <optgroup style="font-size: 18px" label="{{ $category->name }}">
                                            @forelse($category->phrases as $phrase)
                                                <option value="{{ $phrase->id }}">{{ $phrase->name }}</option>
                                            @empty
                                            @endforelse
                                        </optgroup>
                                        <option data-divider="true"></option>
                                    @empty
                                    @endforelse
                                </select>
                                <textarea :disabled="!sms_send_permission || employees.length === 0" name="sms_text" id="refuse_sms_text" class="form-control iransans" tabindex="-1"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button v-if="employees.length !== 0" type="submit" form="refuse_submit_form" class="btn btn-danger submit_button">
                        <i v-if="!button_loading" class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
                        <i v-else class="submit_button_icon fa fa-circle-notch fa-1-2x me-1 fa-spin"></i>
                        <span class="iransans">عدم تایید و ارسال نهایی</span>
                    </button>
                    <button v-if="employees.length !== 0" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#docs_modal" v-on:click="view_employee_information();return_modal='#registration_confirm_modal'">
                        <i class="fa fa-magnifying-glass fa-1-2x me-1"></i>
                        <span class="iransans">مشاهده اطلاعات</span>
                    </button>
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
