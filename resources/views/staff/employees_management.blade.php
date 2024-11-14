@extends("staff.staff_dashboard")
@section('variables')
    <script>
        const allowed_organizations = @json($organizations);
        const allowed_groups = @json($custom_groups);
        const sms_bank = @json($sms_phrase_categories);
        const applications_data = @json($applications);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                مدیریت پرسنل
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
        <div class="offcanvas offcanvas-end" tabindex="-1" id="employee_management_menu" aria-labelledby="employee_management_menu">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title iransans fw-bolder">منوی عملیات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="fieldset">
                    <span class="legend iransans">جستجوی کلی</span>
                    <div class="fieldset-body find-employees">
                        <select class="form-control text-center iransans selectpicker-select find-employees-select" data-size="15" data-live-search="true" title="نام و نام خانوادگی و کد ملی ..." v-on:change="AdvancedEmployeeSearch">
                            <option v-if="EmployeesFound.length > 0" v-for="employee in EmployeesFound" :key="employee.id" :value="employee.id">@{{ employee.name + " - " + employee.national_code }}</option>
                            <option v-if="RegistrationFound.length > 0" v-for="employee in RegistrationFound" :key="employee.id">@{{ employee.name + " - " + employee.national_code }}</option>
                        </select>
                    </div>

                </div>
                <div class="fieldset">
                    <span class="legend iransans">عملیات جمعی</span>
                    <div class="fieldset-body">
                        <div class="d-flex flex-row align-items-start justify-content-start flex-wrap gap-2">
                            @can('add_new_item',"EmployeesManagement")
                                <div data-bs-toggle="tooltip" title="ایجاد پرسنل">
                                    <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'add_employee';employee_operation_kind = 'groups'">
                                        <i class="fad fa-user-plus fa-2x"></i>
                                    </button>
                                </div>
                            @endcan
                            @can('delete_item',"EmployeesManagement")
                                <div data-bs-toggle="tooltip" title="حذف پرسنل">
                                    <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'remove_employee';employee_operation_kind = 'groups'">
                                         <i class="fad fa-user-minus fa-2x"></i>
                                    </button>
                                </div>
                            @endcan
                                <div data-bs-toggle="tooltip" title="تعدیل پرسنل">
                                    <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'detach_employee';employee_operation_kind = 'groups'; modal_size='modal-lg'">
                                        <i class="fad fa-users-slash fa-2x"></i>
                                    </button>
                                </div>
                            @can('item_status',"EmployeesManagement")
                                <div data-bs-toggle="tooltip" title="مسدود سازی/رفع مسدودی حساب  کاربری پرسنل">
                                    <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'employee_status';employee_operation_kind = 'groups'">
                                        <i class="fad fa-user-shield fa-2x"></i>
                                    </button>
                                </div>
                            @endcan
                            @can('item_auth',"EmployeesManagement")
                                 <div data-bs-toggle="tooltip" title="تغییر و بازنشانی نام کاربری و گذرواژه پرسنل">
                                     <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'employee_auth';employee_operation_kind = 'groups'">
                                         <i class="fad fa-user-lock fa-2x"></i>
                                     </button>
                                 </div>
                            @endcan
                            @can('item_data_refresh',"EmployeesManagement")
                            <div data-bs-toggle="tooltip" title="درخواست بازنشانی اطلاعات و یا مدارک پرسنل">
                                <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'employee_refresh_data';employee_operation_kind = 'groups'; modal_size='modal-lg'">
                                    <i class="fad fa-user-gear fa-2x"></i>
                                </button>
                            </div>
                            @endcan
                            @can('item_date_extension',"EmployeesManagement")
                                 <div data-bs-toggle="tooltip" title="تمدید تاریخ قرارداد پرسنل">
                                     <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'employee_date_extension';employee_operation_kind = 'groups'; modal_size='modal-lg'">
                                         <i class="fad fa-user-clock fa-2x"></i>
                                     </button>
                                 </div>
                            @endcan
                            @can('item_contract_conversion',"EmployeesManagement")
                                 <div data-bs-toggle="tooltip" title="انتقال قرارداد پرسنل">
                                     <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'employee_contract_conversion';employee_operation_kind = 'groups'; modal_size='modal-lg'">
                                         <i class="fad fa-user-tag fa-2x"></i>
                                     </button>
                                 </div>
                            @endcan
                            @can('get_deleted_employees',"EmployeesManagement")
                                 <div data-bs-toggle="tooltip" title="پرسنل حذف شده">
                                     <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'employee_deleted';employee_operation_kind = 'groups'; modal_size='modal-xl'">
                                         <i class="fad fa-recycle fa-2x"></i>
                                     </button>
                                 </div>
                            @endcan
                            @can('item_excel_list',"EmployeesManagement")
                                 <div data-bs-toggle="tooltip" title="لیست اکسل پرسنل">
                                     <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'employee_excel_list';employee_operation_kind = 'groups'; modal_size='modal-lg'">
                                         <i class="fad fa-file-spreadsheet fa-2x"></i>
                                     </button>
                                 </div>
                            @endcan
                            @can('send_ticket',"EmployeesManagement")
                                 <div data-bs-toggle="tooltip" title="ارسال تیکت به پرسنل">
                                     <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'employee_send_ticket';employee_operation_kind = 'groups'; modal_size='modal-lg'">
                                         <i class="fad fa-messages fa-2x"></i>
                                     </button>
                                 </div>
                            @endcan
                            @can('send_sms',"EmployeesManagement")
                                 <div data-bs-toggle="tooltip" title="ارسال پیامک به پرسنل">
                                     <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'employee_send_sms';employee_operation_kind = 'groups'; modal_size='modal-lg'">
                                         <i class="fad fa-message-sms fa-2x"></i>
                                     </button>
                                 </div>
                            @endcan
                            @can('item_batch_application',"EmployeesManagement")
                                 <div data-bs-toggle="tooltip" title="ایجاد درخواست جدید">
                                     <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'employee_create_application';employee_operation_kind = 'groups'; modal_size='modal-xl'">
                                         <i class="fad fa-memo-circle-check fa-2x"></i>
                                     </button>
                                 </div>
                            @endcan
                            @can('announceEmployee',"EmployeesManagement")
                                 <div data-bs-toggle="tooltip" title="ایجاد اطلاع رسانی جدید">
                                     <button class="employee-action-button btn btn-outline-dark" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="employee_operation_type = 'employee_announcements';employee_operation_kind = 'groups'; modal_size='modal-lg'">
                                         <i class="fad fa-bullhorn fa-2x"></i>
                                     </button>
                                 </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="input-group mb-2">
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#reference_selection_modal" aria-controls="reference_selection_modal">
                <i class="far fa-users fa-1-4x ps-2 pe-2"></i>
            </button>
            <input type="text" autocomplete="off" class="form-control text-center iransans" placeholder="جستجو با نام، نام خانوادگی ، کد ملی ، شماره شناسنامه و سازمان" data-table="employees_table" v-on:input="filter_table">
            <button class="btn btn-success" type="button" data-bs-toggle="offcanvas" data-bs-target="#employee_management_menu" aria-controls="employee_management_menu">
                <i class="far fa-gear fa-1-4x ps-2 pe-2"></i>
            </button>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table id="employees_table" class="table table-hover no-sort table-striped" data-filter="[1,2,3,4,5]">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col" style="width: 70px"><span>شماره</span></th>
                        <th scope="col" style="width: 100px"><span>نام</span></th>
                        <th scope="col" style="width: 140px"><span>نام خانوادگی</span></th>
                        <th scope="col" style="width: 100px"><span>کد ملی</span></th>
                        <th scope="col" style="width: 100px"><span>شماره شناسنامه</span></th>
                        <th scope="col" style="width: 300px"><span>سازمان</span></th>
                        <th scope="col" style="width: 180px"><span>محل استقرار</span></th>
                        <th scope="col" style="width: 100px"><span>تلفن همراه</span></th>
                        <th scope="col" style="width: 50px"><span>حساب کاربری</span></th>
                        <th scope="col" style="width: 90px"><span>توسط</span></th>
                    </tr>
                    </thead>
                    <tbody v-if="table_data_records.length > 0">
                    <tr v-cloak v-for="employee in table_data_records" class="iransans pointer-cursor" :title="employee.detached === 1 ? 'تعدیل شده' : ''"  :data-employee_id="employee.id" :key="employee.id" @contextmenu.prevent="$refs.contextMenu.open" v-on:contextmenu="employee_id = employee.id">
                        <td hidden="hidden">
                            <label :for="`employee_${employee.id}`" class="w-100">
                                <input class="form-check-input vertical-middle m-0" style="font-size: 13px" type="radio" :id="`employee_${employee.id}`">
                            </label>
                        </td>
                        <td :class="employee.detached === 1 ? 'bg-secondary text-white' : ''"><span class="pointer-cursor" v-text="employee.id"></span></td>
                        <td :class="employee.detached === 1 ? 'bg-secondary text-white' : ''"><span class="pointer-cursor" v-text="employee.first_name"></span></td>
                        <td :class="employee.detached === 1 ? 'bg-secondary text-white' : ''"><span class="pointer-cursor" v-text="employee.last_name"></span></td>
                        <td :class="employee.detached === 1 ? 'bg-secondary text-white' : ''"><span class="pointer-cursor" v-text="employee.national_code"></span></td>
                        <td :class="employee.detached === 1 ? 'bg-secondary text-white' : ''"><span class="pointer-cursor" v-text="employee.id_number"></span></td>
                        <td :class="employee.detached === 1 ? 'bg-secondary text-white' : ''"><span class="pointer-cursor" v-text="employee.contract.organization.name"></span></td>
                        <td :class="employee.detached === 1 ? 'bg-secondary text-white' : ''"><span class="pointer-cursor" v-text="employee.job_seating"></span></td>
                        <td :class="employee.detached === 1 ? 'bg-secondary text-white' : ''"><span class="pointer-cursor" v-text="employee.mobile"></span></td>
                        <td :class="employee.detached === 1 ? 'bg-secondary text-white' : ''">
                            <span class="pointer-cursor">
                                <i v-if="employee?.user?.inactive === 0" title="فعال" class="fa fa-check-circle fa-1-2x green-color vertical-middle"></i>
                                <i v-else-if="employee?.user?.inactive === 1" title="غیرفعال" class="fa fa-times-circle fa-1-2x red-color vertical-middle"></i>
                                <i v-else title="فاقد داشبورد" class="fa fa-triangle-exclamation fa-1-2x yellow-color vertical-middle"></i>
                            </span>
                        </td>
                        <td :class="employee.detached === 1 ? 'bg-secondary text-white' : ''"><span class="pointer-cursor" v-text="employee?.registrant_user?.name"></span></td>
                    </tr>
                    </tbody>
                    <tbody v-else>
                    <tr >
                        <td colspan="14">
                            <span class="iransans">در انتظار انتخاب قرارداد و یا گروه سفارشی...</span>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot class="bg-dark">
                    <tr>
                        <td colspan="12">
                            <div class="d-flex align-items-center justify-content-start gap-2 gap-lg-4 my-1 px-2">
                                <p class="iransans white-color mb-0">
                                    مجموع :
                                    @{{ table_data_records.length }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <vue-context ref="contextMenu">
        <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
            <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-toggle="modal" data-bs-target="#docs_modal" v-on:click="view_employee_information">
                <i class="fa fa-images-user fa-1-4x me-2"></i>
                <span class="iransans">مشاهده مدارک</span>
            </button>
        </li>
        <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
            <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-toggle="modal" data-bs-target="#db_information_modal" v-on:click="individual_operation">
                <i class="fa fa-database fa-1-4x me-2"></i>
                <span class="iransans">مشاهده اطلاعات</span>
            </button>
        </li>
        @can('item_status',"EmployeesManagement")
            <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'employee_status')">
                    <i class="fa fa-user-shield fa-1-4x me-2"></i>
                    <span class="iransans">مسدودسازی / رفع مسدودی حساب کاربری</span>
                </button>
            </li>
        @endcan
        @can('item_auth',"EmployeesManagement")
             <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                 <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'employee_auth')">
                     <i class="fa fa-user-lock fa-1-4x me-2"></i>
                     <span class="iransans">تغییر و بازنشانی اطلاعات ورود</span>
                 </button>
             </li>
        @endcan
        @can('item_data_refresh',"EmployeesManagement")
             <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                 <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'employee_refresh_data')">
                     <i class="fa fa-user-gear fa-1-4x me-2"></i>
                     <span class="iransans">درخواست بازنشانی اطلاعات و یا مدارک</span>
                 </button>
             </li>
        @endcan
        <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
            <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'detach_employee')">
                <i class="fa fa-trash-undo fa-1-4x me-2"></i>
                <span class="iransans">تعیین وضعیت تعدیل پرسنل</span>
            </button>
        </li>
        @can('item_date_extension',"EmployeesManagement")
             <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                 <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'employee_date_extension')">
                     <i class="fa fa-user-clock fa-1-4x me-2"></i>
                     <span class="iransans">تمدید تاریخ قرارداد</span>
                 </button>
             </li>
        @endcan
        @can('item_contract_conversion',"EmployeesManagement")
             <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                 <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'employee_contract_conversion')">
                     <i class="fa fa-user-tag fa-1-4x me-2"></i>
                     <span class="iransans">انتقال قرارداد</span>
                 </button>
             </li>
        @endcan
        @can('requests_history',"EmployeesManagement")
             <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                 <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'employee_request_history_view')">
                     <i class="fa fa-file-search fa-1-4x me-2"></i>
                     <span class="iransans">مشاهده سوابق درخواست ها</span>
                 </button>
             </li>
        @endcan
        @can('item_batch_application',"EmployeesManagement")
             <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                 <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'employee_create_application')">
                     <i class="fa fa-memo-circle-check fa-1-4x me-2"></i>
                     <span class="iransans">ایجاد درخواست جدید</span>
                 </button>
             </li>
        @endcan
        @can('send_ticket',"EmployeesManagement")
             <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                 <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'employee_send_ticket')">
                     <i class="fa fa-messages fa-1-4x me-2"></i>
                     <span class="iransans">ارسال تیکت پشتیبانی</span>
                 </button>
             </li>
        @endcan
        @can('get_tickets',"EmployeesManagement")
             <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                 <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'employee_ticket_history')">
                     <i class="fa fa-message-check fa-1-4x me-2"></i>
                     <span class="iransans">سوابق تیکت ها</span>
                 </button>
             </li>
        @endcan
        @can('send_sms',"EmployeesManagement")
             <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                 <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'employee_send_sms')">
                     <i class="fa fa-message-sms fa-1-4x me-2"></i>
                     <span class="iransans">ارسال پیامک</span>
                 </button>
             </li>
        @endcan
        @can('history',"EmployeesManagement")
             <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                 <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'employee_history_view')">
                     <i class="fa fa-folder-search fa-1-4x me-2"></i>
                     <span class="iransans">مشاهده پرونده پرسنلی</span>
                 </button>
             </li>
        @endcan
        @can('announcementEmployee',"EmployeesManagement")
             <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                 <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'employee_announcements')">
                     <i class="fa fa-bullhorn fa-1-4x me-2"></i>
                     <span class="iransans">ایجاد اطلاع رسانی جدید</span>
                 </button>
             </li>
        @endcan
        @can('delete_item',"EmployeesManagement")
             <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">
                 <button class="w-100 dropdown-item d-flex align-items-center justify-content-start" data-bs-target="#employee_operations_modal" data-bs-toggle="modal" @click="individual_operation($event,'remove_employee')">
                     <i class="fa fa-trash-can fa-1-4x me-2"></i>
                     <span class="iransans">حذف کامل</span>
                 </button>
             </li>
        @endcan
    </vue-context>
@endsection
@section('modals')
    <div class="modal fade rtl" id="docs_modal" tabindex="-1" aria-labelledby="docs_modal" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <input type="hidden" id="employee_id">
                <div class="modal-header">
                    <h5 class="modal-title iransans">
                        مشاهده مدارک پرسنل
                        <span class="iransans text-muted mb-2 d-inline-block"> - @{{ `${employee.name} (${employee.national_code})` }}</span>
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="img-operation-panel">
                        <div class="d-flex flex-row align-items-center justify-content-center gap-4 p-4">
                            <button class="btn btn-sm btn-outline-secondary" v-on:click="image_operations('zoom_in')" title="بزرگنمایی">
                                <i class="img-operation-button fa fa-magnifying-glass-plus fa-2x"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" v-on:click="image_operations('zoom_out')" title="کوچک نمایی">
                                <i class="img-operation-button fa fa-magnifying-glass-minus fa-2x"></i>
                            </button>
                            <a role="button" :href="docs ? docs[doc_show-1]?.view : null" class="btn btn-sm btn-outline-secondary" title="دانلود" target="_blank">
                                <i class="img-operation-button fa fa-download fa-2x"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-secondary" title="چرخش" v-on:click="image_operations('rotate')">
                                <i class="img-operation-button fa fa-rotate fa-2x"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" title="حذف" v-on:click="deleteImage(docs[doc_show - 1])">
                                <i class="img-operation-button fa fa-trash-can fa-2x"></i>
                            </button>
                        </div>
                    </div>
                    <div v-if="docs.length > 0" class="docs-preview position-relative w-100 pe-3 ps-3 pt-3 pb-3" v-on:wheel="zoomOperation">
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
                </div>
                <div class="modal-footer bg-menu">
                    <button v-if="return_modal" type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" data-bs-toggle="modal" :data-bs-target="return_modal" v-on:click="return_modal=''">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                    <button v-else type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" v-on:click="docs = []">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rtl" id="db_information_modal" tabindex="-1" aria-labelledby="docs_modal" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <input type="hidden" id="employee_id">
                <div class="modal-header">
                    <h5 class="modal-title iransans">
                        مشاهده اطلاعات پرسنل
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">نام</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.first_name">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">نام خانوادگی</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.last_name">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">کد ملی</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.national_code">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">شماره شناسنامه</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.id_number">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">نام پدر</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.father_name">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">تاریخ تولد</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.birth_date">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">محل تولد</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.birth_city">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">محل صدور</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.issue_city">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">جنسیت</label>
                            <select class="form-select iransans" v-model="employees.gender">
                                <option :selected="employees.gender === 'm'" value="m">مرد</option>
                                <option :selected="employees.gender === 'f'" value="f">زن</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">وضعیت تاهل</label>
                            <select class="form-select iransans" v-model="employees.marital_status">
                                <option :selected="employees.marital_status === 'm'" value="m">متاهل</option>
                                <option :selected="employees.marital_status === 's'" value="s">مجرد</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">خدمت سربازی</label>
                            <select class="form-select iransans" v-model="employees.military_status">
                                <option :selected="employees.military_status === 'h'" value="h">کارت پایان خدمت</option>
                                <option :selected="employees.military_status === 'e'" value="e">کارت معافیت</option>
                                <option :selected="employees.military_status === 'n'" value="n">در حال تحصیل</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">تحصیلات</label>
                            <select class="form-select iransans" v-model="employees.education">
                                <option :selected="employees.education === 'در حال تحصیل'" value="در حال تحصیل">در حال تحصیل</option>
                                <option :selected="employees.education === 'زیر دیپلم و دیپلم'" value="زیر دیپلم و دیپلم">زیر دیپلم و دیپلم</option>
                                <option :selected="employees.education === 'کاردانی'" value="کاردانی">کاردانی</option>
                                <option :selected="employees.education === 'کارشناسی'" value="کارشناسی">کارشناسی</option>
                                <option :selected="employees.education === 'کارشناسی ارشد'" value="کارشناسی ارشد">کارشناسی ارشد</option>
                                <option :selected="employees.education === 'دکتری'" value="دکتری">دکتری</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">تعداد فرزندان</label>
                            <input type="number" class="form-control text-center iransans" v-model="employees.children_count">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">فرزندان مشمول حق اولاد</label>
                            <input type="number" class="form-control text-center iransans" v-model="employees.included_children_count">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">آدرس پست الکترونیکی</label>
                            <input type="email" class="form-control" v-model="employees.email">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">شماره موبایل</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.mobile">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">تلفن ثابت</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.phone">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">آدرس منزل</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.address">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">محل استقرار</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.job_seating">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">عنوان شغل</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.job_title">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">نام بانک</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.bank_name">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">شماره حساب</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.bank_account">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">شماره کارت</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.credit_card">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">شماره شبا</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.sheba_number">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">شماره بیمه</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.insurance_number">
                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                            <label class="form-label iransans mb-2">سابقه بیمه (روز)</label>
                            <input type="text" class="form-control text-center iransans" v-model="employees.insurance_days">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-menu">
                    @can("edit_item","EmployeesManagement")
                        <live-transfer :target="'employees'" :class="'btn btn btn-success'" route="{{ route("EmployeesManagement.edit_item") }}" :message="'آیا برای ویرایش اطلاعات اطمینان دارید؟'">
                            <i class="fa fa-edit fa-1-2x me-1"></i>
                            <span class="iransans">ویرایش اطلاعات</span>
                        </live-transfer>
                    @endcan
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="reference_selection_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">انتخاب مرجع پرسنل</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div>
                                <label class="iransans mb-1">سازمان</label>
                            </div>
                            <div class="mb-2">
                                <tree-select :branch_node="true" dir="rtl" :is_multiple="false" :placeholder="'انتخاب کنید'" :database="organizations" @contract_selected="EmployeeManagementContractSelected"></tree-select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div>
                                <label class="iransans mb-1">گروه سفارشی</label>
                            </div>
                            <select class="form-control iransans selectpicker-select mb-2" title="انتخاب کنید" id="group_reference" data-size="15" data-live-search="true" v-model="groups" v-on:change="GetGroupEmployees">
                                <option v-for="group in user_allowed_groups" :value="group.id">@{{ group.name }}</option>
                            </select>
                        </div>
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
    <div class="modal fade" id="employee_operations_modal" data-bs-backdrop="static" role="dialog">
        <div class="modal-dialog modal-dialog-centered" :class="modal_size" role="document">
            <employee-add-modal @update_table="EmployeeManagementContractSelected" :kind="employee_operation_kind" v-if="employee_operation_type === 'add_employee'"></employee-add-modal>
            <employee-remove-modal @update_table="EmployeeManagementContractSelected" :kind="employee_operation_kind" v-if="employee_operation_type === 'remove_employee'"></employee-remove-modal>
            <employee-detach-modal @update_table="EmployeeManagementContractSelected" :kind="employee_operation_kind" v-if="employee_operation_type === 'detach_employee'"></employee-detach-modal>
            <employee-status-modal @update_table="EmployeeManagementContractSelected" :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_status'"></employee-status-modal>
            <employee-authentication-modal :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_auth'"></employee-authentication-modal>
            <employee-refresh-data-modal :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_refresh_data'"></employee-refresh-data-modal>
            <employee-date-extension-modal :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_date_extension'"></employee-date-extension-modal>
            <employee-contract-conversion-modal @update_table="EmployeeManagementContractSelected" :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_contract_conversion'"></employee-contract-conversion-modal>
            <employee-applications-modal @update_table="EmployeeManagementContractSelected" :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_create_application'"></employee-applications-modal>
            <employee-history-modal :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_history_view'"></employee-history-modal>
            <employee-request-history-modal :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_request_history_view'"></employee-request-history-modal>
            <employee-excel-list-modal :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_excel_list'"></employee-excel-list-modal>
            <employee-send-sms-modal :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_send_sms'"></employee-send-sms-modal>
            <employee-deleted-modal @update_table="EmployeeManagementContractSelected" :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_deleted'"></employee-deleted-modal>
            <employee-ticket-modal :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_send_ticket'"></employee-ticket-modal>
            <employee-ticket-history-modal :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_ticket_history'"></employee-ticket-history-modal>
            <employee-announcements-modal :kind="employee_operation_kind" v-if="employee_operation_type === 'employee_announcements'"></employee-announcements-modal>
        </div>
    </div>
    <div class="modal fade" id="employees" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans" id="exampleModalLongTitle">پرسنل بارگذاری شده</h5>
                </div>
                <div class="modal-body" style="max-height: 80vh;overflow-y: auto">
                    <table class="table table-bordered text-center w-100 iransans">
                        <thead class="bg-dark white-color">
                        <tr>
                            <th v-for="(header,index) in uploaded_employees.headers" :key="index" v-text="header"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(employee,index) in uploaded_employees.employees" :key="index">
                            <td v-for="(detail,index) in employee" :key="index" v-text="detail"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button v-if="return_modal" type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" data-bs-toggle="modal" :data-bs-target="return_modal" v-on:click="return_modal=''">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                    <button v-else type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
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
                            <td>@{{ error.value }}</td>
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
    <div class="modal fade" id="pdf_viewer_modal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">چاپ مستندات</h6>
                </div>
                <div class="modal-body p-0">
                    <embed
                        class="printer-dialog m-auto" id="pdf_viewer"
                        type="application/pdf"
                        frameBorder="0"
                        scrolling="auto"
                        style="max-width: 100%;max-height: 100%"
                    />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-toggle="modal" data-bs-target="#employee_operations_modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
