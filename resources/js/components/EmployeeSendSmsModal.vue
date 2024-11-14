<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                ارسال پیامک
            </h5>
        </div>
        <div class="modal-body">
            <reference-box :refs_needed="kind === 'individual' ? [4] : [1,2,3]" @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked"></reference-box>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">متن پیام</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="iransans pb-2">
                                تعداد کاراکترهای تایپ شده :
                                <span class="bolder-font pe-2">
                                    {{charCount}}
                                </span>
                                <span class="text-muted">(جهت ارسال پیام در یک پیامک، حداکثر 70 کاراکتر فارسی مجاز می باشد)</span>
                            </h6>
                            <textarea class="form-control iransans" style="height: 200px" v-model="message" v-on:input="CountChar"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-menu">
            <button v-if="reference !== null && data !== null && message !== null && message !== ''" type="button" class="btn btn-success" v-on:click="SendSms">
                <i class="far fa-paper-plane-top fa-1-2x me-1 vertical-middle"></i>
                <span class="iransans">ارسال پیامک</span>
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
import alertify from "alertifyjs";

export default {
    name: "EmployeeSendSmsModal",
    props:["kind"],
    data(){
        return {
            reference: null,
            data: null,
            message: "",
            charCount: 0
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
        CountChar(e){
            this.charCount = e.target.value.length;
        },
        SendSms(){
            const self = this;
            if (this.message) {
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
                            data.append("message", self.message);
                            axios.post(route("EmployeesManagement.send_sms"), data)
                                .then(function (response) {
                                    self.$root.$data.show_loading = false;
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
                                }).catch(function (error) {
                                self.$root.$data.show_loading = false;
                                alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                            });
                        }
                    }
                });
            }
            else
                bootbox.alert("لطفا جهت ادامه متن پیام جهت ارسال را وارد نمایید");
        }
    }
}
</script>

<style scoped>

</style>
