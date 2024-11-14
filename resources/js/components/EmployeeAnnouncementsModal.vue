<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                ایجاد اطلاع رسانی جدید
            </h5>
        </div>
        <div class="modal-body" style="max-height: 60vh;overflow-y: auto">
            <reference-box :refs_needed="kind === 'individual' ? [4] : [1,2,3]" @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked"></reference-box>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">مشخصات</label>
                </span>
                <div class="fieldset-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-check iransans">
                                <input type="checkbox" id="lock_dashboard" class="form-check-input" :value="true"/>
                                <label class="form-check-label fw-bold m-0" for="lock_dashboard">
                                    مشخص کردن تمایل یا عدم تمایل
                                    <span class="text-muted font-size-sm" style="font-weight: normal">(با انتخاب این گزینه، پرسنل ملزم به انتخاب تمایل و یا عدم تمایل نسبت به پیشنهاد می باشد)</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label iransans">عنوان</label>
                            <input type="text" class="form-control iransans text-center" v-model="title">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label iransans">متن پیام</label>
                            <textarea class="form-control iransans" v-model="comment"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">درخواست اطلاعات</label>
                </span>
                <div class="fieldset-body">
                    <div class="row mb-3">
                        <div class="col-12 mb-3">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control iransans text-center" placeholder="عنوان گروه اطلاعات">
                                <button class="btn btn-primary input-group-text">
                                    <i class="fa fa-plus me-2"></i>
                                    <span class="iransans font-size-sm">افزودن</span>
                                </button>
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
    name: "EmployeeAnnouncementsModal",
    props:["kind"],
    data(){
        return {
            reference: null,
            data: null,
            dashboard_lock: true,
            title: "",
            comment: "",
            repetitive: false,
            data_name:"",
            needed_data:[],
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
