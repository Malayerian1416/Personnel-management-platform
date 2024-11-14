<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                بازنشانی اطلاعات ورود پرسنل
            </h5>
        </div>
        <div class="modal-body">
            <reference-box :refs_needed="kind === 'individual' ? [4] : [1,2,3]" @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked"></reference-box>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">اطلاعات ورود</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div class="col-12">
                            <div>
                                <input checked class="form-check-input vertical-middle mt-0" v-model="auth_type" value="national_code" type="radio" id="national_code_option" name="auth_words">
                                <label class="form-check-label iransans" for="national_code_option">
                                    بازنشانی اطلاعات ورود به کد ملی پرسنل
                                </label>
                            </div>
                            <div class="mt-2">
                                <input class="form-check-input vertical-middle mt-0" v-model="auth_type" value="custom" type="radio" id="custom_option" name="auth_words">
                                <label class="form-check-label iransans" for="custom_option">
                                    بازنشانی اطلاعات ورود به صورت سفارشی
                                </label>
                                <input type="text" :disabled="auth_type !== 'custom'" class="form-control mt-2 text-center auth_data_password" placeholder="گذرواژه" v-model="password">
                                <div class="form-text green-color iransans text-justify">
                                    * در این عملیات نام کاربری در هر صورت به کد ملی تغییر می یابد؛ اما با انتخاب گزینه ورود اطلاعات به صورت سفارشی می توان گذرواژه جدیدی را به جای کد ملی برای پرسنل تعیین نمود
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-menu">
            <button v-if="reference !== null && data !== null" type="button" class="btn btn-success" v-on:click="ResetAuth">
                <i class="far fa-refresh fa-1-2x me-1 vertical-middle"></i>
                <span class="iransans">بازنشانی اطلاعات</span>
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
    name: "EmployeeAuthenticationModal",
    props:["kind"],
    data(){
        return {
            reference: null,
            data: null,
            auth_type: "national_code",
            password: null
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
        ResetAuth(){
            const self = this;
            $("*").removeClass("is-invalid");
            if (this.auth_type === "custom" && this.password !== "" || this.auth_type === "national_code") {
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
                            data.append("auth_type",self.auth_type)
                            if (self.data !== null) {
                                switch (self.reference) {
                                    case "organization":
                                        data.append("contract_id", self.data);
                                        break;
                                    case "group":
                                        data.append("group_id", self.data);
                                        break;
                                    case "custom":
                                        data.append("employees", JSON.stringify(self.data));
                                        break;
                                    case "individual":
                                        data.append("employee_id", self.data);
                                }
                            }
                            if (self.auth_type === "custom")
                                data.append("password", self.password);
                            axios.post(route("EmployeesManagement.item_auth"), data)
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
                            });
                        }
                    }
                });
            }
            else
                this.password === "" ? $(".auth_data_password").removeClass("is-invalid").addClass("is-invalid") : "";
        }
    }
}
</script>

<style scoped>

</style>
