@extends("staff.staff_dashboard")
@section('variables')
    <script>
        const allowed_organizations = @json($organizations);
        const employee_request_data = @json($records);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                اتوماسیون درخواست های پرسنل
                <span class="vertical-middle ms-1 text-muted">مشاهده ، تایید ، عدم تایید</span>
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
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام، کد ملی، سازمان، قرارداد و نوع درخواست" data-table="requests_table" v-on:input="filter_table">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table id="requests_table" class="table table-striped table-hover pointer-cursor sortArrowWhite" data-filter="[1,2,3,4,5]">
                    <thead class="bg-light black-color">
                    <tr class="iransans">
                        <th scope="col" style="width: 70px" data-sortas="numeric"><span>شماره</span></th>
                        <th scope="col" style="width: 130px"><span>پرسنل</span></th>
                        <th scope="col" style="width: 100px"><span>کد ملی</span></th>
                        <th scope="col"><span>سازمان</span></th>
                        <th scope="col"><span>قرارداد</span></th>
                        <th scope="col" style="width: 110px"><span>نوع</span></th>
                        <th scope="col" style="width: 120px"><span>توسط</span></th>
                        <th scope="col" style="width: 100px"><span>تاریخ ثبت</span></th>
                        <th scope="col" style="width: 100px"><span>تاریخ ویرایش</span></th>
                    </tr>
                    </thead>
                    <tbody v-cloak v-if="employee_requests.length > 0">
                    <tr class="iransans pointer-cursor" :class="request.is_read === 0 ? 'unread' : null" v-for="(request,index) in employee_requests" data-bs-toggle="modal" data-bs-target="#request_details_modal" v-on:click="GetRequestDetails(request.id)">
                        <td>@{{ request.id }}</td>
                        <td><span>@{{ request.employee.name }}</span></td>
                        <td><span>@{{ request.employee.national_code }}</span></td>
                        <td><span>@{{ request.employee.contract.organization.name }}</span></td>
                        <td><span>@{{ request.employee.contract.name }}</span></td>
                        <td><span>@{{ request.application_name }}</span></td>
                        <td><span>@{{ request.employee.name }}</span></td>
                        <td><span>@{{ PersianDateString(request.created_at) }}</span></td>
                        <td><span>@{{ PersianDateString(request.updated_at) }}</span></td>
                    </tr>
                    </tbody>
                    <tbody v-cloak v-else>
                    <tr><td colspan="9"><span class="iransans">اطلاعاتی وجود ندارد</span></td></tr>
                    </tbody>
                    <tfoot class="bg-dark">
                    <tr>
                        <td colspan="12">
                            <div class="d-flex align-items-center justify-content-start gap-2 gap-lg-4 my-1 px-2">
                                <p class="iransans white-color mb-0">
                                    مجموع :
                                    @{{ Object.keys(employee_requests).length }}
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
    <div class="modal fade rtl" id="request_details_modal" tabindex="-1" aria-labelledby="request_details_modal" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">مشاهده اطلاعات اتوماسیون</h5>
                </div>
                <div class="modal-body" v-if="request !== []" style="height: calc(100% - 54px);overflow-y: auto">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active iransans" id="request_details-tab" data-bs-toggle="tab" data-bs-target="#request_details-tab-pane" type="button" role="tab" aria-controls="request_details-tab-pane" aria-selected="false">جزئیات درخواست</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link iransans" id="employee_details-tab" data-bs-toggle="tab" data-bs-target="#employee_details-tab-pane" type="button" role="tab" aria-controls="employee_details-tab-pane" aria-selected="false">اطلاعات پرسنل</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link iransans" id="image_docs-tab" data-bs-toggle="tab" data-bs-target="#image_docs-tab-pane" type="button" role="tab" aria-controls="image_docs-tab-pane" aria-selected="false">مدارک تصویری</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link iransans" id="payslip-tab" data-bs-toggle="tab" data-bs-target="#payslip-tab-pane" type="button" role="tab" aria-controls="payslip-tab-pane" aria-selected="false">آخرین فیش حقوقی</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link iransans" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-tab-pane" type="button" role="tab" aria-controls="history-tab-pane" aria-selected="false">سوابق درخواست ها</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link iransans" id="preview-tab" data-bs-toggle="tab" data-bs-target="#preview-tab-pane" type="button" role="tab" aria-controls="preview-tab-pane" aria-selected="false">پیش نمایش</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active p-3" id="request_details-tab-pane" role="tabpanel" aria-labelledby="request_details-tab" tabindex="0">
                            <div class="alert alert-primary iransans" role="alert">
                                <h5 class="m-0 text-center">
                                    درخواست
                                    @{{ request.application_name }}
                                </h5>
                            </div>
                            <div class="row">
                                <div v-if="request.application_class === 'LPCA' || request.application_class === 'ECA'" class="col-12 mb-3">
                                    <label class="form-label iransans">
                                        نهاد درخواست کننده
                                    </label>
                                    <input :disabled="request.application_class !== 'LPCA' && request.application_class !== 'ECA'" class="form-control text-center iransans" v-model="data_recipient" name="recipient" type="text">
                                </div>
                                <div v-if="request.application_class === 'LPCA'" class="col-12 mb-3">
                                    <label class="form-label iransans">
                                        نام وام گیرنده (جهت ضمانت)
                                    </label>
                                    <input :disabled="request.application_class !== 'LPCA'" class="form-control text-center iransans" name="borrower" type="text" v-model="data_borrower">
                                </div>
                                <div v-if="request.application_class === 'LPCA'" class="col-12 mb-3">
                                    <label class="form-label iransans">
                                        مبلغ وام (ریال)
                                    </label>
                                    <input :disabled="request.application_class !== 'LPCA'" class="form-control text-center iransans loan_amount_input" autocomplete="off" name="loan_amount" type="text" v-model="data_loan_amount">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label iransans">
                                        ثبت توضیحات در اتوماسیون
                                    </label>
                                    <textarea class="form-control iransans" v-model="request_comment"></textarea>
                                </div>
                                <div v-if="request.current_priority === 1" class="col-12 mb-3">
                                    <label class="form-label iransans">
                                        در صورت عدم تایید درخواست، دلیل آن را شرح دهید
                                    </label>
                                    <textarea id="request_reject_reason" class="form-control iransans" v-model="request_reject_reason"></textarea>
                                </div>
                                <div class="col-12">
                                    <h5 class="iransans mt-3">تایید کنندگان</h5>
                                    <div v-if="request?.signs && request?.signs.length > 0" class="sign-container" style="column-gap: 5px">
                                        <div v-for="(sign,index) in request.signs" class="sign-box iranyekan bg-light mr-4 align-self-stretch" :key="index">
                                            <i class="fa fa-user-circle fa-2x mb-2"></i>
                                            <span class="text-muted" v-text="sign.user.role.name"></span>
                                            <span v-text="sign.user.name"></span>
                                            <span class="text-muted" dir="ltr" style="font-size: 10px" v-text="to_persian_date(sign.updated_at)"></span>
                                            <span v-if="sign.refer === 1" class="text-muted" v-text="'ارجاع شده'"></span>
                                        </div>
                                    </div>
                                    <span v-else class="iransans text-muted">شما اولین نفر در گردش اتوماسیون هستید</span>
                                </div>
                                <div class="col-12">
                                    <h5 class="iransans mt-3">توضیحات ثبت شده</h5>
                                    <div v-if="request?.comments && request?.comments.length > 0" class="comments-container">
                                        <div v-for="(comment,index) in request.comments" class="comment-box iranyekan" :key="index">
                                            <div class="commenter">
                                                <i class="fa fa-user-circle fa-2x me-2"></i>
                                                <span class="text-muted" v-text="`${comment.user.name} (${comment.user.role.name})`"></span>
                                            </div>
                                            <p class="mt-2 comment" v-text="comment.comment"></p>
                                            <span class="time-left" dir="ltr" v-text="to_persian_date(comment.updated_at)"></span>
                                        </div>
                                    </div>
                                    <span v-else class="iransans text-muted">توضیحاتی ثبت نشده است</span>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade p-3" id="employee_details-tab-pane" role="tabpanel" aria-labelledby="employee_details-tab" tabindex="0">

                            <div v-if="request.length !== 0" class="row employee_information mt-3">
                                <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                                    <label class="form-label iransans mb-2">نام</label>
                                    <input type="text" class="form-control text-center iransans" v-model="first_name = request.employee.first_name">
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                                    <label class="form-label iransans mb-2">نام خانوادگی</label>
                                    <input type="text" class="form-control text-center iransans" v-model="last_name = request.employee.last_name">
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                                    <label class="form-label iransans mb-2">کد ملی</label>
                                    <input type="text" class="form-control text-center iransans" readonly v-model="national_code = request.employee.national_code">
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                                    <label class="form-label iransans mb-2">شماره شناسنامه</label>
                                    <input type="text" class="form-control text-center iransans" v-model="id_number = request.employee.id_number">
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                                    <label class="form-label iransans mb-2">نام پدر</label>
                                    <input type="text" class="form-control text-center iransans" v-model="father_name = request.employee.father_name">
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                                    <label class="form-label iransans mb-2">جنسیت</label>
                                    <select class="form-select iransans" v-model="gender = request.employee.gender">
                                        <option :selected="request.employee.gender === 'm'" value="m">مرد</option>
                                        <option :selected="request.employee.gender === 'f'" value="f">زن</option>
                                    </select>
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                                    <label class="form-label iransans mb-2">شماره موبایل</label>
                                    <input type="text" class="form-control text-center iransans" v-model="mobile = request.employee.mobile">
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                                    <label class="form-label iransans mb-2">محل استقرار</label>
                                    <input type="text" class="form-control text-center iransans" v-model="job_seating = request.employee.job_seating">
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                                    <label class="form-label iransans mb-2">عنوان شغل</label>
                                    <input type="text" class="form-control text-center iransans" v-model="job_title = request.employee.job_title">
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                                    <label class="form-label iransans mb-2">تاریخ شروع قرارداد جاری</label>
                                    <input ref="contract_start_date" type="text" class="form-control text-center iransans contract_date" :value="PersianDateString(request.automationable?.data_array?.active_contract_date?.start)">
                                </div>
                                <div class="form-group col-12 col-md-6 col-lg-3 mb-3">
                                    <label class="form-label iransans mb-2">تاریخ پایان قرارداد جاری</label>
                                    <input ref="contract_end_date" type="text" class="form-control text-center iransans contract_date" :value="PersianDateString(request.automationable?.data_array?.active_contract_date?.end)">
                                </div>
                                <div class="col-12">
                                    <button type="button" class="w-100 btn btn-primary iransans" v-on:click="EditUserData">
                                        ویرایش اطلاعات
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="image_docs-tab-pane" role="tabpanel" aria-labelledby="image_docs-tab" tabindex="0">
                            <div class="img-operation-panel">
                                <div class="d-flex flex-row align-items-center justify-content-center gap-4 p-4">
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
                        </div>
                        <div class="tab-pane fade p-3" id="payslip-tab-pane" role="tabpanel" aria-labelledby="payslip-tab" tabindex="0">
                            <div v-if="request?.automationable?.data_array?.payslip?.year_month !== ''">
                                <div class="alert alert-primary iransans" role="alert">
                                    <h5 class="m-0 text-center" v-text="request.length !== 0 && request?.automationable?.data_array?.payslip?.year_month ? `فیش حقوقی ${request?.automationable?.data_array?.payslip?.year_month}` : 'ندارد'"></h5>
                                </div>
                            <table class="payslip">
                                <tr style="background: whitesmoke">
                                    <td colspan="2" class="iransans text-center fw-bold" style="width: 50%">مزایا</td>
                                    <td colspan="2" class="iransans text-center" style="width: 50%">کسورات</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;width: 30%">
                                        <div>
                                            <div class="iransans pb-2" v-for="(advantage,index) in request?.automationable?.data_array?.payslip.advantages" v-if="parseInt(advantage['value']) > 0" :key="index" v-text="advantage['title']"></div>
                                        </div>
                                    </td>
                                    <td style="vertical-align: top;width: 20%">
                                        <div>
                                            <div class="iransans pb-2" v-for="(advantage,index) in request?.automationable?.data_array?.payslip.advantages" v-if="parseInt(advantage['value']) > 0" :key="index" v-text="GetSeperated(advantage['value'])"></div>
                                        </div>
                                    </td>
                                    <td style="vertical-align: top;width: 30%">
                                        <div>
                                            <div class="iransans pb-2" v-for="(deduction,i) in request?.automationable?.data_array?.payslip.deductions" v-if="parseInt(deduction['value']) > 0" :key="i" v-text="deduction['title']"></div>
                                        </div>
                                    </td>
                                    <td style="vertical-align: top;width: 20%">
                                        <div>
                                            <div class="iransans pb-2" v-for="(deduction,i) in request?.automationable?.data_array?.payslip.deductions" v-if="parseInt(deduction['value']) > 0" :key="i" v-text="GetSeperated(deduction['value'])"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tfoot>
                                <tr style="background: whitesmoke">
                                    <td><span class="iransans">جمع کل مزایا </span></td>
                                    <td><span class="iransans fw-bolder font-size-lg" v-text="GetSeperated(request?.automationable?.data_array?.payslip.total_advantages)"></span></td>
                                    <td><span class="iransans">جمع کل کسورات </span></td>
                                    <td><span class="iransans fw-bolder font-size-lg" v-text="GetSeperated(request?.automationable?.data_array?.payslip.total_deductions)"></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="border-0"></td>
                                    <td colspan="2">
                                        <span class="iransans font-size-lg fw-bold me-3">قابل پرداخت : </span>
                                        <span class="iransans fw-bolder font-size-xl" v-text="GetSeperated(request?.automationable?.data_array?.payslip.total_net)"></span>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                            </div>
                            <div class="text-center" v-else>
                                <div class="alert alert-danger iransans" role="alert">
                                    اطلاعاتی وجود ندارد
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade p-3" id="history-tab-pane" role="tabpanel" aria-labelledby="history-tab" tabindex="0">
                            <h6 class="iransans text-muted mt-2 mb-3">
                                در جدول زیر ، تمامی سوابق درخواست های پرسنل اعم از تایید شده ، تایید نشده و در جریان (به غیر از درخواست فعلی) نمایش داده شده است
                            </h6>
                            <div id="table-scroll-container">
                                <div id="table-scroll" class="table-scroll">
                                    <table class="table table-bordered iransans text-center sortArrowWhite request_history" style="min-width: 100%">
                                        <thead class="bg-dark white-color">
                                        <tr>
                                            <th scope="col">شناسه یکتا</th>
                                            <th scope="col">نوع درخواست</th>
                                            <th scope="col">تاریخ ایجاد</th>
                                            <th scope="col">تاریخ رسیدگی</th>
                                            <th scope="col">وضعیت</th>
                                            <th scope="col">تسویه</th>
                                            <th scope="col">توسط</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(automation,index) in request?.employee?.automations" v-if="automation.id !== request.id" :class="automation?.automationable?.is_refused === 1 ? 'bg-refused' : automation?.automationable?.is_accepted === 1 ? 'bg-confirmed' : 'bg-progress'" :key="index">
                                            <td style="background: inherit">
                                                <span v-text="automation?.automationable?.i_number"></span>
                                            </td>
                                            <td style="background: inherit">
                                                <span v-text="automation?.application_name"></span>
                                            </td>
                                            <td style="background: inherit">
                                                <span v-text="PersianDateString(automation?.created_at)"></span>
                                            </td>
                                            <td style="background: inherit">
                                                <span v-text="PersianDateString(automation?.updated_at)"></span>
                                            </td>
                                            <td style="background: inherit">
                                                <span v-text="automation?.automationable?.is_refused === 1 ? 'تایید نشده' : automation?.automationable?.is_accepted === 1 ? 'تایید شده' : 'در جریان'"></span>
                                            </td>
                                            <td style="background: inherit">
                                                <i class="fa fa-check green-color" v-if="automation?.automationable?.loan_amount > 0 && automation?.automationable?.inactive === 1"></i>
                                                <i class="fa fa-times red-color" v-else-if="automation?.automationable?.loan_amount > 0 && automation?.automationable?.inactive === 0"></i>
                                                <i class="fa fa-dash" v-else></i>
                                            </td>
                                            <td style="background: inherit">
                                                <span class="iransans" v-text="automation.user.name"></span>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="7">
                                                <div class="d-flex flex-row align-items-center justify-content-between w-100">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <div class="me-2">
                                                            <i class="fas fa-square fa-1-2x vertical-middle" style="color: #d1e7dd"></i>
                                                            <span class="iransans">تایید شده</span>
                                                        </div>
                                                        <div class="me-2">
                                                            <i class="fas fa-square fa-1-2x vertical-middle" style="color: #f8d7da"></i>
                                                            <span class="iransans">تایید نشده</span>
                                                        </div>
                                                        <div class="me-2 me-lg-0">
                                                            <i class="fas fa-square fa-1-2x vertical-middle" style="color: #fff3cd"></i>
                                                            <span class="iransans">در جریان</span>
                                                        </div>
                                                    </div>
                                                    <span class="iransans ms-2">
                                                        جمع کل درخواست ها :
                                                        @{{ request?.employee?.automations.length }}
                                                        عدد
                                                    </span>
                                                    <span class="iransans me-2">
                                                        جمع کل مبالغ وام :
                                                        <span v-text="GetTotalLoan(request?.employee?.id)"></span>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane position-relative fade p-3" id="preview-tab-pane" role="tabpanel" aria-labelledby="preview-tab" tabindex="0">
                            <div class="position-absolute d-flex flex-column align-items-center justify-content-center gap-3" style="z-index: 10;top: 50%;left:50%;transform: translate(-50%,-50%)">
                                <i class="fad fa-spinner-third fa-spin fa-3x"></i>
                                <span class="iransans">در حال آماده سازی...</span>
                            </div>
                            <div id="pdf_viewer" style="width: 100%;height: 70vh;z-index: 100;position: relative">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="button" class="btn btn-outline-primary iransans" v-on:click="RefreshRequestData">
                        <i class="fa fa-refresh fa-1-2x vertical-middle me-1"></i>
                        <span class="iransans">بروزرسانی اطلاعات ضمیمه</span>
                    </button>
                    <button type="button" class="btn btn-success" v-on:click="ConfirmRequest">
                        <i class="fa fa-check fa-1-2x me-1 vertical-middle"></i>
                        <span class="iransans">تایید درخواست و ادامه</span>
                    </button>
                    <button type="button" class="btn btn-danger" v-on:click="RejectRequest">
                        <i class="fa fa-times fa-1-2x me-1 vertical-middle"></i>
                        <span class="iransans">عدم تایید درخواست و ارجاع</span>
                    </button>
                    <button id="close_modal" type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" v-on:click="ResetRequestModal">
                        <i class="fa fa-times fa-1-2x me-1 vertical-middle"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
