<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                ارسال تیکت پشتیبانی
            </h5>
        </div>
        <div class="modal-body">
            <reference-box :refs_needed="kind === 'individual' ? [4] : [1,2,3]" @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked"></reference-box>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">جزئیات پیام</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="iransans form-label">
                                عنوان پیام
                                <small class="red-color">*</small>
                            </label>
                            <input type="text" class="form-control iransans" v-model="subject">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="iransans form-label">
                                متن پیام
                                <small class="red-color">*</small>
                            </label>
                            <textarea class="form-control iransans" style="height: 100px" v-model="message"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="iransans form-label">فایل ضمیمه (اختیاری)</label>
                            <s-file-browser :file_box_name="'attachment'" :file_box_id="'attachment'" :filename_box_id="'attachment_browser_box'" :accept="['xlsx','xls','png','jpg','tiff','pdf','doc','docx','rtf','txt']" :size="1524000"></s-file-browser>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-menu">
            <button v-if="reference !== null && data !== null && message !== null && message !== '' && subject !== null && subject !== ''" type="button" class="btn btn-success" v-on:click="SendSms">
                <i class="far fa-ticket fa-1-2x me-1 vertical-middle"></i>
                <span class="iransans">ارسال تیکیت</span>
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
    name: "EmployeeTicketsModal",
    props:["kind"],
    data(){
        return {
            reference: null,
            data: null,
            message: "",
            subject: ""
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
        SendSms(){
            const self = this;
            if (this.message && this.subject) {
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
                            const attachment = $("#attachment");
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
                            data.append("subject", self.subject);
                            data.append("message", self.message);
                            attachment.val() ? data.append("attachment", attachment[0].files[0]) : null;
                            axios.post(route("EmployeesManagement.send_ticket"), data)
                                .then(function (response) {
                                    self.$root.$data.show_loading = false;
                                    switch (response.data["result"]) {
                                        case "success": {
                                            self.$root.$data.user_allowed_contracts = response.data?.contracts;
                                            self.$root.$data.user_allowed_groups = response.data?.groups;
                                            self.$root.$data.table_data_records = [];
                                            alertify.notify(response.data["message"], 'success', "5");
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
                bootbox.alert("لطفا عنوان و متن پیام را وارد نمایید");
        }
    }
}
</script>
