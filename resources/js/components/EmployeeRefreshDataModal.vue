<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                صدور درخواست بازنشانی اطلاعات و مدارک پرسنل
            </h5>
        </div>
        <div class="modal-body">
            <reference-box :refs_needed="kind === 'individual' ? [4] : [1,2,3]" @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked"></reference-box>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">انتخاب عناوین و مشخصات</label>
                </span>
                <div class="fieldset-body">
                    <div class="row mb-3">
                        <div class="col-12 mb-3">
                            <label class="form-label iransans">متن نمایش در درخواست</label>
                            <textarea class="form-control iransans" v-model="request_text"></textarea>
                        </div>
                        <h6 class="fw-bold iransans green-color">عناوین مدارک</h6>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="birth_certificate" class="form-check-input vertical-middle" :value="true" v-model="employee_data.files.birth_certificate"/>
                                <label class="form-check-label" for="birth_certificate">صفحات شناسنامه</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="national_card" class="form-check-input vertical-middle" :value="true" v-model="employee_data.files.national_card"/>
                                <label class="form-check-label" for="national_card">کارت ملی</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="military_certificate" class="form-check-input vertical-middle" :value="true" v-model="employee_data.files.military_certificate"/>
                                <label class="form-check-label" for="military_certificate">کارت پایان خدمت</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="education_certificate" class="form-check-input vertical-middle" :value="true" v-model="employee_data.files.education_certificate"/>
                                <label class="form-check-label" for="education_certificate">مدرک تحصیلی</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="personal_photo" class="form-check-input vertical-middle" :value="true" v-model="employee_data.files.personal_photo"/>
                                <label class="form-check-label" for="personal_photo">عکس پرسنلی</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="insurance_confirmation" class="form-check-input vertical-middle" :value="true" v-model="employee_data.files.insurance_confirmation"/>
                                <label class="form-check-label" for="insurance_confirmation">تاییدیه بیمه</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans mb-0">
                                <input type="checkbox" id="sign_sample" class="form-check-input vertical-middle" :value="true" v-model="employee_data.files.sign_sample"/>
                                <label class="form-check-label" for="sign_sample">نمونه امضاء</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <h6 class="fw-bold iransans green-color">عناوین اطلاعات</h6>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="first_name" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.first_name"/>
                                <label class="form-check-label" for="first_name">نام</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="last_name" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.last_name"/>
                                <label class="form-check-label" for="last_name">نام خانوادگی</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="gender" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.gender"/>
                                <label class="form-check-label" for="gender">جنسیت</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="father_name" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.father_name"/>
                                <label class="form-check-label" for="father_name">نام پدر</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="birth_date" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.birth_date"/>
                                <label class="form-check-label" for="birth_date">تاریخ تولد</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="birth_city" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.birth_city"/>
                                <label class="form-check-label" for="birth_city">محل تولد</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="issue_city" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.issue_city"/>
                                <label class="form-check-label" for="issue_city">محل صدور</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="id_number" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.id_number"/>
                                <label class="form-check-label" for="id_number">شماره شناسنامه</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="education" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.education"/>
                                <label class="form-check-label" for="education">میزان تحصیلات</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="marital_status" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.marital_status"/>
                                <label class="form-check-label" for="marital_status">وضعیت تاهل</label>
                            </div>
                        </div>

                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="children_count" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.children_count"/>
                                <label class="form-check-label" for="children_count">تعداد کل فرزندان</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="included_children_count" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.included_children_count"/>
                                <label class="form-check-label" for="included_children_count">فرزندان مشمول حق اولاد</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="insurance_number" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.insurance_number"/>
                                <label class="form-check-label" for="insurance_number">شماره بیمه</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="insurance_days" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.insurance_days"/>
                                <label class="form-check-label" for="insurance_days">سابقه بیمه</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="military_status" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.military_status"/>
                                <label class="form-check-label" for="military_status">وضعیت نظام وظیفه</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="address" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.address"/>
                                <label class="form-check-label" for="address">آدرس</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="phone" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.phone"/>
                                <label class="form-check-label" for="phone">تلفن ثابت</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="mobile" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.mobile"/>
                                <label class="form-check-label" for="mobile">تلفن همراه</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="bank_name" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.bank_name"/>
                                <label class="form-check-label" for="bank_name">نام بانک</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="bank_account" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.bank_account"/>
                                <label class="form-check-label" for="bank_account">شماره حساب</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="credit_card" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.credit_card"/>
                                <label class="form-check-label" for="credit_card">شماره کارت</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="sheba_number" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.sheba_number"/>
                                <label class="form-check-label" for="sheba_number">شماره شبا</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="job_seating" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.job_seating"/>
                                <label class="form-check-label" for="job_seating">محل اسقرار</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="job_title" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.job_title"/>
                                <label class="form-check-label" for="job_title">عنوان شغل</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="email" class="form-check-input vertical-middle" :value="true" v-model="employee_data.texts.email"/>
                                <label class="form-check-label" for="email">پست الکترونیکی</label>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="lock_dashboard" class="form-check-input" :value="true" v-model="dashboard_lock"/>
                                <label class="form-check-label fw-bold m-0" for="lock_dashboard">
                                    غیرفعال کردن داشبورد
                                    <span class="text-muted font-size-sm" style="font-weight: normal">(با انتخاب این گزینه، آیتم های داشبورد موقتاً تا تکمیل فرم درخواست اطلاعات و مدارک توسط شما غیر فعال خواهند گردید)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-menu">
            <button v-if="reference !== null && data !== null" type="button" class="btn btn-success" v-on:click="RequestInformation">
                <i class="far fa-database fa-1-2x me-1 vertical-middle"></i>
                <span class="iransans">ثبت درخواست</span>
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

export default {
    name: "EmployeeRefreshDataModal",
    props:["kind"],
    data(){
        return {
            reference: null,
            data: null,
            dashboard_lock: true,
            request_text: "همکار گرامی، لطفا اطلاعات و یا مدارک تصویری درخواست شده توسط کارشناس مربوطه را با دقت و به طور صحیح وارد و یا بارگذاری نمایید",
            employee_data: {
                "files" : {
                    "birth_certificate" : false,
                    "national_card" : false,
                    "military_certificate" : false,
                    "education_certificate" : false,
                    "personal_photo" : false,
                    "insurance_confirmation" : false,
                    "sign_sample" : false,
                },
                "texts" : {
                    "first_name" : false,
                    "last_name" : false,
                    "gender" : false,
                    "father_name" : false,
                    "birth_date" : false,
                    "birth_city" : false,
                    "issue_city" : false,
                    "id_number" : false,
                    "education" : false,
                    "marital_status" : false,
                    "children_count" : false,
                    "included_children_count" : false,
                    "insurance_number" : false,
                    "insurance_days" : false,
                    "military_status" : false,
                    "address" : false,
                    "phone" : false,
                    "mobile" : false,
                    "bank_name" : false,
                    "bank_account" : false,
                    "credit_card" : false,
                    "sheba_number" : false,
                    "job_seating" : false,
                    "job_title" : false,
                    "email" : false
                }
            }
        }
    },
    methods:{
        ReferenceChecked(ref){
            this.reference = ref;
        },
        ReferenceSetup(ref){
            this.reference = ref.type;
            this.data = ref.target;
        },
        RequestInformation(){
            const self = this;
            const files = Object.values(this.employee_data.files).some(val => val === true);
            const texts = Object.values(this.employee_data.texts).some(val => val === true);
            if (files || texts) {
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
                            data.append("reference", self.reference);
                            data.append("dashboard_lock", self.dashboard_lock);
                            if (self.$data.data !== null) {
                                switch (self.reference) {
                                    case "organization":
                                        data.append("contract_id", self.$data.data);
                                        break;
                                    case "group":
                                        data.append("group_id", self.$data.data);
                                        break;
                                    case "custom":
                                        data.append("employees", JSON.stringify(self.$data.data));
                                        break;
                                    case "individual":
                                        data.append("employee_id", self.data);
                                }
                            }
                            data.append("data",JSON.stringify(self.employee_data));
                            data.append("title",self.request_text);
                            axios.post(route("EmployeesManagement.item_data_refresh"), data)
                                .then(function (response) {
                                    self.$root.refresh_selects();
                                    self.$root.$data.show_loading = false;
                                    if (response?.data) {
                                        switch (response.data["result"]) {
                                            case "success": {
                                                alertify.notify(response.data["message"], 'success', "5");
                                                break;
                                            }
                                            case "warning": {
                                                alertify.notify(response.data["message"], 'warning', "20");
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
                                alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                                // console.error("Error response:");
                                // console.error(error.response.data);
                                // console.error(error.response.status);
                                // console.error(error.response.headers);
                            });
                        }
                    }
                });
            }
            else
                bootbox.alert("لطفا جهت ادامه حداقل یک گزینه از عناوین درخواست را انتخاب نمایید");
        }
    }
}
</script>

<style scoped>

</style>
