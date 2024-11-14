<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                تبدیل قرارداد پرسنل
            </h5>
        </div>
        <div class="modal-body">
            <reference-box :refs_needed="kind === 'individual' ? [4] : [1,2,3]" @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked"></reference-box>
            <div class="fieldset">
                <span class="legend iransans">قرارداد نهایی</span>
                <div class="fieldset-body">
                    <tree-select :branch_node="true" :id="'target_contract'" @contract_selected="TargetSelected" dir="rtl" :is_multiple="false" :placeholder="'انتخاب کنید'" :database="$root.organizations"></tree-select>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-menu">
            <button v-if="reference !== null && data !== null && target !== null" type="button" class="btn btn-success" v-on:click="ConvertContract">
                <i class="far fa-exchange fa-1-2x me-2 vertical-middle"></i>
                <span class="iransans">تبدیل قرارداد</span>
            </button>
            <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" v-on:click="$root.$data.employee_operation_type=''">
                <i class="fa fa-times fa-1-2x me-2"></i>
                <span class="iransans">بستن</span>
            </button>
        </div>
    </div>
</template>

<script>
import route from "ziggy-js";

export default {
    name: "EmployeeContractConversionModal",
    props:["kind"],
    data() {
        return {
            reference: null,
            data: null,
            target: null
        }
    },
    methods:{
        ReferenceChecked(ref) {
            this.reference = ref;
            this.data = null;
            this.extension_type = 'constant_value';
        },
        ReferenceSetup(ref) {
            this.reference = ref.type;
            this.data = ref.target;
        },
        GetRoute(routeName, parameter) {
            return route(routeName, parameter);
        },
        TargetSelected(id){
            this.target = id;
        },
        Validation(){
            if (this.reference === null || this.data === null) {
                bootbox.alert("لطفا مرجع و پرسنل مربوط به آن را انتخاب نمایید");
                return false
            }
            else if (this.target === null){
                $("#target_contract").closest("div").find(".vue-treeselect__control").css("border","1px solid #ddd");
                return false
            }
            else
                return true
        },
        ConvertContract(){
            const self = this;
            $("*").removeClass("is-invalid");
            if (this.Validation()) {
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
                            data.append("target", self.target);
                            if (self.reference !== 'custom' && self.$data.data !== null || self.reference === 'custom' && self.excel_employees !== null) {
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
                            axios.post(route("EmployeesManagement.item_contract_conversion"), data)
                                .then(function (response) {
                                    self.$root.refresh_selects();
                                    self.$root.$data.show_loading = false;
                                    if (response?.data) {
                                        switch (response.data["result"]) {
                                            case "success": {
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
                                alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                            });
                        }
                    }
                });
            }
        }
    }
}
</script>
