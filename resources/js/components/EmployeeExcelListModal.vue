<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                لیست اکسل پرسنل
            </h5>
        </div>
        <div class="modal-body">
            <reference-box :refs_needed="kind === 'individual' ? [4] : [1,2,3]" @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked"></reference-box>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">انتخاب عناوین و مشخصات</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="first_name" class="form-check-input vertical-middle" :value="true" v-model="employee_data.name"/>
                                <label class="form-check-label" for="first_name">نام و نام خانوادگی</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="first_name" class="form-check-input vertical-middle" :value="true" v-model="employee_data.first_name"/>
                                <label class="form-check-label" for="first_name">نام</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="last_name" class="form-check-input vertical-middle" :value="true" v-model="employee_data.last_name"/>
                                <label class="form-check-label" for="last_name">نام خانوادگی</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="last_name" class="form-check-input vertical-middle" :value="true" v-model="employee_data.national_code"/>
                                <label class="form-check-label" for="last_name">کد ملی</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="id_number" class="form-check-input vertical-middle" :value="true" v-model="employee_data.id_number"/>
                                <label class="form-check-label" for="id_number">شماره شناسنامه</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="gender" class="form-check-input vertical-middle" :value="true" v-model="employee_data.gender"/>
                                <label class="form-check-label" for="gender">جنسیت</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="father_name" class="form-check-input vertical-middle" :value="true" v-model="employee_data.father_name"/>
                                <label class="form-check-label" for="father_name">نام پدر</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="birth_date" class="form-check-input vertical-middle" :value="true" v-model="employee_data.birth_date"/>
                                <label class="form-check-label" for="birth_date">تاریخ تولد</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="birth_city" class="form-check-input vertical-middle" :value="true" v-model="employee_data.birth_city"/>
                                <label class="form-check-label" for="birth_city">محل تولد</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="issue_city" class="form-check-input vertical-middle" :value="true" v-model="employee_data.issue_city"/>
                                <label class="form-check-label" for="issue_city">محل صدور</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="education" class="form-check-input vertical-middle" :value="true" v-model="employee_data.education"/>
                                <label class="form-check-label" for="education">میزان تحصیلات</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="marital_status" class="form-check-input vertical-middle" :value="true" v-model="employee_data.marital_status"/>
                                <label class="form-check-label" for="marital_status">وضعیت تاهل</label>
                            </div>
                        </div>

                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="children_count" class="form-check-input vertical-middle" :value="true" v-model="employee_data.children_count"/>
                                <label class="form-check-label" for="children_count">تعداد کل فرزندان</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="included_children_count" class="form-check-input vertical-middle" :value="true" v-model="employee_data.included_children_count"/>
                                <label class="form-check-label" for="included_children_count">فرزندان مشمول حق اولاد</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="insurance_number" class="form-check-input vertical-middle" :value="true" v-model="employee_data.insurance_number"/>
                                <label class="form-check-label" for="insurance_number">شماره بیمه</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="insurance_days" class="form-check-input vertical-middle" :value="true" v-model="employee_data.insurance_days"/>
                                <label class="form-check-label" for="insurance_days">سابقه بیمه</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="military_status" class="form-check-input vertical-middle" :value="true" v-model="employee_data.military_status"/>
                                <label class="form-check-label" for="military_status">وضعیت نظام وظیفه</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="address" class="form-check-input vertical-middle" :value="true" v-model="employee_data.address"/>
                                <label class="form-check-label" for="address">آدرس</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="phone" class="form-check-input vertical-middle" :value="true" v-model="employee_data.phone"/>
                                <label class="form-check-label" for="phone">تلفن ثابت</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="mobile" class="form-check-input vertical-middle" :value="true" v-model="employee_data.mobile"/>
                                <label class="form-check-label" for="mobile">تلفن همراه</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="bank_name" class="form-check-input vertical-middle" :value="true" v-model="employee_data.bank_name"/>
                                <label class="form-check-label" for="bank_name">نام بانک</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="bank_account" class="form-check-input vertical-middle" :value="true" v-model="employee_data.bank_account"/>
                                <label class="form-check-label" for="bank_account">شماره حساب</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="credit_card" class="form-check-input vertical-middle" :value="true" v-model="employee_data.credit_card"/>
                                <label class="form-check-label" for="credit_card">شماره کارت</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="sheba_number" class="form-check-input vertical-middle" :value="true" v-model="employee_data.sheba_number"/>
                                <label class="form-check-label" for="sheba_number">شماره شبا</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="job_seating" class="form-check-input vertical-middle" :value="true" v-model="employee_data.job_seating"/>
                                <label class="form-check-label" for="job_seating">محل اسقرار</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="job_title" class="form-check-input vertical-middle" :value="true" v-model="employee_data.job_title"/>
                                <label class="form-check-label" for="job_title">عنوان شغل</label>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="form-check iransans">
                                <input type="checkbox" id="email" class="form-check-input vertical-middle" :value="true" v-model="employee_data.email"/>
                                <label class="form-check-label" for="email">پست الکترونیکی</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-menu">
            <button v-if="reference !== null && data !== null" type="button" class="btn btn-success" v-on:click="RequestInformation">
                <i class="far fa-file-plus fa-1-2x me-1 vertical-middle"></i>
                <span class="iransans">ایجاد فایل</span>
            </button>
            <a v-if="download.set" :href="download.file" role="button" class="btn btn-dark" download="EmployeeCustomList.xlsx">
                <span class="iransans">دانلود فایل</span>
            </a>
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
    name: "EmployeeExcelListModal",
    props:["kind"],
    data(){
        return {
            reference: null,
            data: null,
            employee_data: {
                "name" : true,
                "first_name" : false,
                "last_name" : false,
                "national_code" : true,
                "gender" : false,
                "father_name" : true,
                "birth_date" : true,
                "birth_city" : true,
                "issue_city" : true,
                "id_number" : true,
                "education" : false,
                "marital_status" : false,
                "children_count" : false,
                "included_children_count" : false,
                "insurance_number" : false,
                "insurance_days" : false,
                "military_status" : false,
                "address" : true,
                "phone" : true,
                "mobile" : true,
                "bank_name" : false,
                "bank_account" : false,
                "credit_card" : false,
                "sheba_number" : false,
                "job_seating" : false,
                "job_title" : false,
                "email" : false
            },
            download: {"set":false,"file":""}
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
            const texts = Object.values(this.employee_data).some(val => val === true);
            if (texts) {
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
                            axios.post(route("EmployeesManagement.item_excel_list"), data, { responseType: 'blob' })
                                .then(function (response) {
                                    self.$root.$data.show_loading = false;
                                    const url = window.URL.createObjectURL(new Blob([response.data],{ type: 'application/vnd.ms-excel' }));
                                    self.download.set = true;
                                    self.download.file = url;
                                }).catch(function (error) {
                                self.$root.$data.show_loading = false;
                                alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                            });
                        }
                    }
                });
            }
            else
                bootbox.alert("لطفا جهت ادامه حداقل یک گزینه از عناوین مشخصات را انتخاب نمایید");
        }
    }
}
</script>

<style scoped>

</style>
