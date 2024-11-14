<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                سوابق درخواست ها
            </h5>
        </div>
        <div class="modal-body">
            <reference-box v-show="details === null" :refs_needed="kind === 'individual' ? [4] : [1,2,3]" @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked"></reference-box>
            <div v-if="details === null" class="fieldset">
                <span class="legend">
                    <label class="iransans">لیست درخواست ها</label>
                </span>
                <div class="fieldset-body">
                    <div class="input-group mb-2">
                        <input type="text" autocomplete="off" class="form-control text-center iransans" placeholder="جستجو با شناسه و نوع درخواست" data-table="request_history_table" v-on:input="SearchTable">
                        <span class="input-group-text"><i class="fa fa-search fa-1-2x"></i></span>
                    </div>
                    <div id="table-scroll-container">
                        <div id="table-scroll" class="table-scroll">
                            <table id="request_history_table" data-filter="[0,1]" class="table table-bordered table-hover iransans text-center sortArrowWhite request_history sortArrowWhite" style="min-width: 100%">
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
                                <tr class="pointer-cursor hover-blue" v-for="(automation,index) in automations" :class="automation?.automationable?.is_refused === 1 ? 'bg-refused' : automation?.automationable?.is_accepted === 1 ? 'bg-confirmed' : 'bg-progress'" :key="index" v-on:click="MoreDetails(automation.id)">
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
                                <tr v-if="automations.length === 0">
                                    <td colspan="7"><span class="iransans">رکوردی یافت نشد</span></td>
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
                                            <span class="iransans ms-2" v-text="'جمع کل درخواست ها : ' + automations.length"></span>
                                            <span class="iransans me-2">
                                        جمع کل مبالغ وام :
                                        <span v-text="TotalLoan"></span>
                                    </span>
                                        </div>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="fieldset mt-3">
                <span class="legend">
                    <label class="iransans">{{ details?.application_name }}</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label iransans">
                                نهاد درخواست کننده
                            </label>
                            <input class="form-control text-center iransans" readonly name="recipient" type="text" :value="details ? details?.automationable.recipient : null">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label iransans">
                                نام وام گیرنده (جهت ضمانت)
                            </label>
                            <input class="form-control text-center iransans" readonly name="borrower" type="text" :value="details ? details?.automationable.borrower : null">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label iransans">
                                مبلغ وام (ریال)
                            </label>
                            <input readonly class="form-control text-center iransans" autocomplete="off" name="loan_amount" type="text" :value="details ? GetSeperated(details?.automationable.loan_amount) : null">
                        </div>
                        <div class="col-12">
                            <h5 class="iransans mt-3">تایید کنندگان</h5>
                            <div v-if="details?.signs && details?.signs.length > 0" class="sign-container gap-2">
                                <div v-for="(sign,index) in details.signs" class="sign-box iranyekan bg-light mr-4 align-self-stretch" :key="index">
                                    <i class="fa fa-user-circle fa-2x mb-2"></i>
                                    <span class="text-muted" v-text="sign.user?.role?.name"></span>
                                    <span v-text="sign.user.name"></span>
                                    <span class="text-muted" dir="ltr" style="font-size: 10px" v-text="PersianDateString(sign.updated_at)"></span>
                                    <span v-if="sign.refer === 1" class="text-muted" v-text="'ارجاع شده'"></span>
                                </div>
                            </div>
                            <span v-else class="iransans text-muted">تایید کننده ای وجود ندارد</span>
                        </div>
                        <div class="col-12 mb-3">
                            <h5 class="iransans mt-3">توضیحات ثبت شده</h5>
                            <div v-if="details?.comments && details?.comments.length > 0" class="comments-container">
                                <div v-for="(comment,index) in details.comments" class="comment-box iranyekan" :key="index">
                                    <div class="commenter">
                                        <i class="fa fa-user-circle fa-2x me-2"></i>
                                        <span class="text-muted" v-text="`${comment?.user?.name} (${comment.user?.role?.name})`"></span>
                                    </div>
                                    <p class="mt-2 comment" v-text="comment.comment"></p>
                                    <span class="time-left" dir="ltr" v-text="PersianDateString(comment.updated_at)"></span>
                                </div>
                            </div>
                            <span v-else class="iransans text-muted">توضیحاتی ثبت نشده است</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-menu">
            <button v-if="details !== null && details.application_class === 'LPCA'" class="btn btn-outline-primary iransans" v-on:click="ClearDebt">
                <i class="fa fa-eraser fa-1-2x me-1"></i>
                تسویه بدهی
            </button>
            <button v-if="details !== null" class="btn btn-outline-dark iransans" data-bs-toggle="modal" data-bs-target="#pdf_viewer_modal" v-on:click="PDFPreview">
                <i class="fa fa-print-search fa-1-2x me-1"></i>
                پیش نمایش
            </button>
            <button v-if="details !== null" class="btn btn-info iransans" v-on:click="details = null">
                <i class="fa fa-arrow-alt-from-right fa-1-2x me-1"></i>
                بازگشت به لیست
            </button>
            <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" v-on:click="$root.$data.employee_operation_type=''">
                <i class="fa fa-times fa-1-2x me-1"></i>
                <span class="iransans">بستن</span>
            </button>
        </div>
    </div>
</template>

<script>
import route from "ziggy-js";
import numeral from "numeral";
import alertify from "alertifyjs";

export default {
    name: "EmployeeRequestHistoryModal",
    props:["kind"],
    data(){
        return {
            reference: null,
            data: null,
            automations: [],
            details: null,
            automation_id: null
        }
    },
    computed: {
        TotalLoan () {
            return this.GetTotalLoan();
        }
    },
    mounted() {
        const self = this;
        axios.post(route("EmployeesManagement.requests_history"), {"employee_id": self.$data.data})
            .then(function (response) {
                if (response?.data) {
                    switch (response.data.result) {
                        case "success": {
                            self.automations = response.data?.automations;
                            alertify.notify("اطلاعات با موفقیت دریافت شد", 'success', "5");
                            self.$nextTick(() => {
                                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl,{"trigger" : "hover"}));
                                $(".request_history").fancyTable({
                                    sortColumn:[0],
                                    pagination: false,
                                    perPage:0,
                                    globalSearch:false,
                                    searchable: false
                                });
                            });
                            break;
                        }
                        case "fail": {
                            alertify.notify(response.data["message"], 'error', "30");
                            break;
                        }
                    }
                }
            }).catch(function (error) {
            alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
        });
    },
    methods:{
        ReferenceChecked(ref){
            this.reference = ref;
        },
        ReferenceSetup(ref){
            this.reference = ref.type;
            this.data = ref.target;
        },
        PersianDateString(date){
            return new persianDate(new Date(date)).toLocale("en").format("YYYY/MM/DD");
        },
        GetSeperated(number){
            return `${numeral(number).format('0,0')} ریال`;
        },
        GetTotalLoan(){
            const self = this;
            const loans = self.automations.filter((loan_request) => {
                return loan_request.automationable?.is_accepted === 1 && loan_request.automationable?.inactive === 0 ? loan_request?.automationable.loan_amount > 0 : 0
            });
            let total = 0;
            if (loans) {
                loans.forEach(loan => {
                    total += loan.automationable.loan_amount;
                });
            }
            return self.GetSeperated(total);
        },
        MoreDetails(id){
            const self = this;
            this.details = self.automations.find(automation => {
                return automation.id === parseInt(id);
            });
            this.automation_id = parseInt(id);
        },
        SearchTable(e){
            let filter = e.target.value;
            let table = document.getElementById(e.target.dataset.table), columns, tr, td, i, j, txtValue;
            columns = JSON.parse(table.dataset.filter);
            tr = table.getElementsByTagName("tr");
            for (i = 1; i < tr.length; i++) {
                let strings = [];
                for (j = 0; j < columns.length; j++) {
                    td = tr[i].getElementsByTagName("td")[parseInt(columns[j])];
                    if (td) {
                        txtValue = td.innerHTML !== '' ? td.querySelector('input') !== null ? td.querySelector('input').value : td.textContent || td.innerText : null;
                        if (txtValue)
                            strings.push(txtValue);
                    }
                }
                if (strings.length) {
                    const match = strings.find(element => {
                        const clearElement = element.replace(/[()\-_!@#$%^.,]/g, '');
                        return !!clearElement.includes(filter);
                    });
                    if (match)
                        tr[i].style.display = "";
                    else
                        tr[i].style.display = "none";
                }
            }
        },
        PDFPreview(){
            const self = this;
            $('#pdf_viewer').attr('src',route("EmployeesManagement.request_preview",[self.automation_id]));
        },
        ClearDebt(){
            const self = this;
            bootbox.confirm({
                message: "آیا برای انجام عملیات اطمینان دارید؟",
                closeButton: false,
                buttons: {
                    confirm: {
                        label: 'بله',
                        className: 'btn-success',
                    },
                    cancel: {
                        label: 'خیر',
                        className: 'btn-danger',
                    }
                },
                callback: function (result) {
                    if (result === true) {
                        self.$root.$data.show_loading = true;
                        bootbox.hideAll();
                        let data = new FormData();
                        data.append("automation_id", self.automation_id.toString());
                        axios.post(route("EmployeesManagement.clear_debt"), data)
                            .then(function (response) {
                                self.$root.refresh_selects();
                                self.$root.$data.show_loading = false;
                                if (response?.data) {
                                    switch (response.data.result) {
                                        case "success": {
                                            self.automations = response.data?.automations;
                                            self.$forceUpdate();
                                            alertify.notify("اطلاعات با موفقیت ثبت شد", 'success', "5");
                                            break;
                                        }
                                        case "fail": {
                                            alertify.notify(response.data["message"], 'error', "30");
                                            break;
                                        }
                                    }
                                }
                            }).catch(function (error) {
                            self.$root.$data.show_loading = false;
                            alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");;
                        });
                    }
                }
            });
        }
    }
}
</script>
