<template>
    <div class="fieldset">
                <span class="legend iransans">
                    <label class="form-label iransans">
                        انتخاب مرجع
                    </label>
                </span>
        <div class="fieldset-body">
            <div class="row">
                <div v-if="refs_needed.includes(1)" class="col-12 mb-3">
                    <div class="form-check">
                        <input v-if="refs_needed.length > 1" id="org_ref" :checked="refs_needed[0] === 1" class="form-check-input vertical-middle" type="radio" value="organization" v-model="reference" name="reference" v-on:change="refresh">
                        <label for="org_ref" class="iransans form-check-label">سازمان و قرارداد</label>
                    </div>
                    <tree-select :branch_node="true" @contract_selected="ContractSelected" :disabled="refs_needed.length > 1 && reference !== 'organization'" :id="'delete_employee_contract'" dir="rtl" :is_multiple="false" :selected="contract_id" :placeholder="'انتخاب کنید'" :database="$root.organizations"></tree-select>
                    <select v-if="refs_needed.includes(5)" class="form-control iransans mt-3 select-selectpicker" multiple :disabled="reference !== 'organization'" id="contract_employees" title="انتخاب فردی" data-size="30" data-live-search="true" v-on:change="EmployeeSelected">
                        <option v-for="employee in contract_employees" :value="employee.id">{{ employee.name }}</option>
                    </select>
                </div>
                <div v-if="refs_needed.includes(2)" class="col-12 mb-3">
                    <div class="form-check">
                        <input v-if="refs_needed.length > 1" id="grp_ref" :checked="refs_needed[0] === 2" class="form-check-input vertical-middle" type="radio" value="group" v-model="reference" name="reference" v-on:change="refresh">
                        <label for="grp_ref" class="iransans form-check-label">گروه سفارشی</label>
                    </div>
                    <select class="form-control iransans select-selectpicker" :disabled="refs_needed.length > 1 && reference !== 'group'" id="group_employees" title="انتخاب کنید" data-size="30" data-live-search="true" v-on:change="GroupSelected">
                        <option v-for="group in $root.user_allowed_groups" :value="group.id">{{ group.name }}</option>
                    </select>
                </div>
                <div v-if="refs_needed.includes(3)" class="col-12 col-lg-10">
                    <div class="form-check">
                        <input v-if="refs_needed.length > 1" id="exl_ref" :checked="refs_needed[0] === 3" class="form-check-input vertical-middle" type="radio" value="custom" v-model="reference" name="reference" v-on:change="refresh">
                        <label for="exl_ref" class="form-check-label iransans">
                            فایل اکسل مجموعه کد ملی پرسنل
                            <a v-if="reference === 'custom'" :href="GetRoute('EmployeesManagement.excel_download',['NationalCode'])" class="iransans">(فایل نمونه)</a>
                        </label>
                    </div>
                    <s-file-browser :disabled="refs_needed.length > 1 && reference !== 'custom'" :accept='["xls","xlsx"]' :size="400000" :input_class="'d-inline'"></s-file-browser>
                </div>
                <div v-if="refs_needed.includes(3)" class="col-12 col-lg-2 align-self-center">
                    <button class="btn btn-primary w-100" :disabled="refs_needed.length > 1 && reference !== 'custom'" v-on:click="ExcelUpload">
                        <i class="far fa-upload fa-1-2x me-1"></i>
                        <span class="iransans">بارگذاری فایل</span>
                    </button>
                </div>
                <div v-if="refs_needed.includes(3)" class="form-group col-12 mt-3">
                    <div class="mb-2">
                        <div class="input-group-text w-100 d-flex flex-row align-items-center justify-content-start g-5">
                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" :disabled="refs_needed.length > 1 && reference !== 'custom'" data-bs-toggle="modal" data-bs-target="#employees" v-on:click="$root.$data.return_modal='#employee_operations_modal'">
                                    <i class="far fa-user-magnifying-glass fa-1-2x me-1"></i>
                                    <span class="iransans" style="line-height: 22px">پرسنل :
                                        {{excel_employees.length}}
                                        نفر
                                    </span>
                                </button>
                            </div>
                            <button v-if="$root.import_errors.length > 0" :disabled="refs_needed.length > 1 && reference !== 'custom'" type="button" class="btn btn-outline-danger iransans" data-bs-toggle="modal" data-bs-target="#import_errors" v-on:click="$root.$data.return_modal='#employee_operations_modal'">
                                <i class="far fa-triangle-exclamation fa-1-2x me-1"></i>
                                <span class="iransans" style="line-height: 22px">خطای بارگذاری</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="refs_needed.includes(4)" class="col-12 d-flex flex-row align-items-center justify-content-start gap-3 flex-wrap">
                    <span class="iransans border p-2">{{ $root.$data.employees.name}}</span>
                    <span class="iransans border p-2">{{ $root.$data.employees.national_code}}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import route from "ziggy-js";
import alertify from "alertifyjs";

export default {
    name: "ReferenceBox",
    props: ["refs_needed","headers"],
    mounted() {
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) )
            $('.select-selectpicker').selectpicker('mobile');
        else
            $('.select-selectpicker').selectpicker();
        switch (this.refs_needed[0]) {
            case 1:
                this.reference = "organization";
                this.refresh();
                break;
            case 2:
                this.reference = "group";
                this.refresh();
                break;
            case 3:
                this.reference = "custom";
                this.refresh();
                break;
            case 4:
                this.$emit("reference_selected",{"type":'individual',"target":this.$root.$data.employees.id});
                break;
            default:
                this.reference = null;
                this.refresh();
                break;
        }
    },
    data(){
        return {
            selected:[],
            employees: [],
            contract_employees: [],
            excel_employees: [],
            contract_id : null,
            group_id : null,
            operation: 'lock',
            reference: 'organization'
        }
    },
    methods:{
        GetRoute(routeName,parameter){
            return route(routeName,parameter);
        },
        refresh(){
            this.$emit("reference_check",this.reference);
            this.$nextTick(function (){
                $(".select-selectpicker").selectpicker('val','').selectpicker('refresh');
            });
        },
        ContractSelected(id){
            this.$emit("reference_selected",{"type":this.reference,"target":id});
            const self = this;
            self.contract_employees = [];
            self.show_loading = true;
            axios.post(route("EmployeeFinancialAdvantages.get_employees"), {"contract_id":id}).then(function (response) {
                self.show_loading = false;
                if (response.data){
                    switch (response.data.result){
                        case "success":{
                            self.contract_employees = response.data?.employees;
                            self.$forceUpdate();
                            self.$nextTick(() => {
                                $("#contract_employees").selectpicker('val','').selectpicker('refresh');
                            });
                            break;
                        }
                        case "fail": {
                            alertify.notify(response.data.message, 'error', "30");
                            break;
                        }
                    }
                }
            }).catch(function (error) {
                self.show_loading = false;
                alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
            });
        },
        GroupSelected(e){
            this.$emit("reference_selected",{"type":this.reference,"target":parseInt(e.target.value)});
        },
        EmployeeSelected(e){
            this.$emit("employee_selected",$(e.target).val());
        },
        ExcelUpload() {
            const file = $("#upload_file");
            if (file.val()) {
                const self = this;
                self.$root.$data.show_loading = true;
                let data = new FormData();
                data.append("excel_file", file[0].files[0]);
                axios.post(route("EmployeesManagement.excel_upload",["NationalCode"]), data)
                    .then(function (response) {
                        self.$root.$data.show_loading = false;
                        if (response.data !== null) {
                            switch (response.data["result"]) {
                                case "success": {
                                    if (response.data.employees) {
                                        self.excel_employees = response.data.data;
                                        self.$root.$data.uploaded_employees["employees"] = response.data.data;
                                        self.$root.$data.uploaded_employees["headers"] = Array.isArray(self.$props.headers) ? self.$props.headers : ["شماره", "نام", "کد ملی", "قرارداد"];
                                        self.$emit("reference_selected",{"type":self.reference,"target":self.excel_employees});
                                    }
                                    if (typeof response.data["import_errors"] !== "undefined" && response.data["import_errors"].length > 0) {
                                        self.$root.$data.import_errors = response.data["import_errors"];
                                    } else
                                        self.$root.$data.import_errors = [];
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
                    // console.error(error.response.data);    // ***
                    // console.error(error.response.status);  // ***
                    // console.error(error.response.headers); // ***
                });

            } else
                file.closest("div").find(".file_selector_box").removeClass("is-invalid").addClass("is-invalid");
        }
    }
}
</script>

<style scoped>

</style>
