<template>
    <div class="modal-content">
        <input type="hidden" id="employee_id">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                تعدیل پرسنل
            </h5>
        </div>
        <div class="modal-body">
            <reference-box :refs_needed="kind === 'individual' ? [4] : [1,2,3]" @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked"></reference-box>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">نوع عملیات</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div class="col-12">
                            <div>
                                <input checked class="form-check-input vertical-middle mt-0" v-model="operation" value="detach" type="radio" id="detach_option" name="attach_status">
                                <label class="form-check-label iransans" for="detach_option">
                                    تعدیل پرسنل و یا مرجع انتخاب شده
                                </label>
                            </div>
                            <div class="mt-2">
                                <input class="form-check-input vertical-middle mt-0" v-model="operation" value="attach" type="radio" id="attach_option" name="attach_status">
                                <label class="form-check-label iransans" for="attach_option">
                                    لغو تعدیل و اتصال مجدد پرسنل و یا مرجع انتخاب شده
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-menu">
            <button v-if="reference !== null && data !== null" type="button" class="btn btn-danger" data-type="selected" v-on:click="DetachEmployees">
                <i class="fa fa-trash-can-check fa-1-2x me-1 vertical-middle"></i>
                <span class="iransans" v-text="operation === 'detach' ? 'تعدیل پرسنل':'لغو تعدیل پرسنل'"></span>
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
    name: "EmployeeDetachModal",
    props:["kind"],
    data(){
        return {
            reference: null,
            data: null,
            operation: 'detach'
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
        DetachEmployees(){
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
                        data.append("reference", self.reference);
                        data.append("operation",self.operation);
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
                        axios.post(route("EmployeesManagement.detach_item"), data)
                            .then(function (response) {
                                self.$root.refresh_selects();
                                self.$root.$data.show_loading = false;
                                if (response?.data) {
                                    switch (response.data["result"]) {
                                        case "success": {
                                            self.$root.$data.user_allowed_contracts = response.data?.contracts;
                                            self.$root.$data.user_allowed_groups = response.data?.groups;
                                            self.$root.$data.table_data_records = [];
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
                            alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");;
                        });
                    }
                }
            });
        }
    }
}
</script>

<style scoped>

</style>
