<template>
    <div class="modal-content">
        <input type="hidden" id="employee_id">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                ایجاد پرسنل جدید
            </h5>
        </div>
        <div class="modal-body">
            <div class="fieldset mt-2">
                <span class="legend iransans">
                    <label class="form-label iransans">
                        سازمان و قرارداد
                        <strong class="red-color">*</strong>
                    </label>
                </span>
                <div class="fieldset-body">
                    <tree-select :branch_node="true" @contract_selected="ContractSelected" dir="rtl" :is_multiple="false" :placeholder="'انتخاب کنید'" :database="$root.organizations"></tree-select>
                </div>
            </div>
            <div class="fieldset">
                <span class="legend">
                    <input id="individual" type="radio" class="vertical-middle" v-model="operation_type" value="individual">
                    <label class="iransans" for="individual">فردی</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div class="col-12 col-lg-4 mb-3">
                            <label class="form-label iransans">
                                نام
                                <strong class="red-color">*</strong>
                            </label>
                            <input id="first_name" v-model="individual.first_name" class="form-control text-center iransans" :disabled="operation_type === 'group'">
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <label class="form-label iransans">
                                نام خانوادگی
                                <strong class="red-color">*</strong>
                            </label>
                            <input id="last_name" v-model="individual.last_name" class="form-control text-center iransans" :disabled="operation_type === 'group'">
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <label class="form-label iransans">
                                کد ملی
                                <strong class="red-color">*</strong>
                            </label>
                            <input id="national_code" v-model="individual.national_code" class="form-control text-center iransans" :disabled="operation_type === 'group'">
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <label class="form-label iransans">موبایل</label>
                            <input v-model="individual.mobile" class="form-control text-center iransans" :disabled="operation_type === 'group'">
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <label class="form-label iransans">
                                شروع قرارداد
                                <strong class="red-color">*</strong>
                            </label>
                            <input id="initial_start" v-model="individual.initial_start" class="form-control text-center iransans persian_datepicker_range_from" readonly :disabled="operation_type === 'group'">
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <label class="form-label iransans">
                                پایان قرارداد
                                <strong class="red-color">*</strong>
                            </label>
                            <input id="initial_end" v-model="individual.initial_end" class="form-control text-center iransans persian_datepicker_range_to" readonly :disabled="operation_type === 'group'">
                        </div>
                    </div>
                </div>
            </div>
            <div class="fieldset">
                <span class="legend">
                    <input id="groups" type="radio" class="vertical-middle" v-model="operation_type" value="group">
                    <label class="iransans" for="groups">گروهی</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div class="col-12 col-lg-10">
                            <div class="form-check">
                                <label for="exl_ref" class="form-check-label iransans">
                                    فایل اکسل مجموعه کد ملی پرسنل
                                    <a v-if="operation_type === 'group'" :href="GetRoute('EmployeesManagement.excel_download',['NewEmployee'])" class="iransans">(فایل نمونه)</a>
                                </label>
                            </div>
                            <s-file-browser :disabled="operation_type === 'individual'" :accept='["xls","xlsx"]' :size="400000" :input_class="'d-inline'"></s-file-browser>
                        </div>
                        <div class="col-12 col-lg-2 align-self-center">
                            <button class="btn btn-primary w-100" :disabled="operation_type === 'individual'" v-on:click="ExcelUpload">
                                <i class="far fa-upload fa-1-2x me-1"></i>
                                <span class="iransans">بارگذاری فایل</span>
                            </button>
                        </div>
                        <div class="form-group col-12 mt-3">
                            <div class="mb-2">
                                <div class="input-group-text w-100 d-flex flex-row align-items-center justify-content-start g-5">
                                    <div>
                                        <button type="button" class="btn btn-outline-primary me-2" :disabled="operation_type === 'individual' || excel_employees.length === 0" data-bs-toggle="modal" data-bs-target="#employees" v-on:click="$root.$data.return_modal='#employee_operations_modal'">
                                            <i class="far fa-user-magnifying-glass fa-1-2x me-1"></i>
                                            <span class="iransans" style="line-height: 22px">پرسنل :
                                                {{excel_employees.length}}
                                                نفر
                                            </span>
                                        </button>
                                    </div>
                                    <button v-if="$root.$data.import_errors.length > 0" :disabled="operation_type === 'individual'" type="button" class="btn btn-outline-danger iransans" data-bs-toggle="modal" data-bs-target="#import_errors" v-on:click="$root.$data.return_modal='#employee_operations_modal'">
                                        <i class="far fa-triangle-exclamation fa-1-2x me-1"></i>
                                        <span class="iransans" style="line-height: 22px">خطای بارگذاری</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 p-4">
                    <input type="checkbox" class="vertical-middle" id="make_authentication" v-model="withDashboard">
                    <label class="form-label iransans fw-bold" for="make_authentication">
                        ایجاد داشبورد پرسنلی
                    </label>
                    <div class="form-text iransans">
                        با انتخاب این گزینه، سیستم به طور اتوماتیک به ازاء هر پرسنل جدید، امکان ورود به داشبورد را فراهم می کند. لذا کد ملی پرسنل به عنوان نام کاربری و گذرواژه تعریف خواهد شد.
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-menu">
            <button v-if="contract_id !== null" type="button" class="btn btn-success" v-on:click="addEmployees">
                <i class="fa fa-save fa-1-2x me-1"></i>
                <span class="iransans">ارسال و ذخیره</span>
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
    name: "EmployeeAddModal",
    mounted() {
        const self = this;
        let from = $(".persian_datepicker_range_from").pDatepicker({
            initialValue: false,
            initialValueType: 'persian',
            format: 'YYYY/MM/DD',
            autoClose: true,
            observer: true,
            calendar:{
                persian: {
                    leapYearMode: 'astronomical'
                }
            },
            formatter: function(unix) {
                var date = new persianDate(unix);
                var gregorian = date.toLocale('en').toCalendar('persian');
                return gregorian.format("YYYY/MM/DD");
            },
            onSelect: function (unix) {
                self.individual.initial_start = new persianDate(unix).toLocale('en').format("YYYY/MM/DD");
                from.touched = true;
                if (to && to.options && to.options.minDate !== unix) {
                    var cachedValue = to.getState().selected.unixDate;
                    to.options = {minDate: new persianDate(unix).add('d', 1)};
                    if (to.touched) {
                        to.setDate(cachedValue);
                    }
                }
            }
        });
        let to = $(".persian_datepicker_range_to").pDatepicker({
            initialValue: false,
            initialValueType: 'persian',
            format: 'YYYY/MM/DD',
            autoClose: true,
            observer: true,
            calendar:{
                persian: {
                    leapYearMode: 'astronomical'
                }
            },
            formatter: function(unix) {
                var date = new persianDate(unix);
                var gregorian = date.toLocale('en').toCalendar('persian');
                return gregorian.format("YYYY/MM/DD");
            },
            onSelect: function (unix) {
                self.individual.initial_end = new persianDate(unix).toLocale('en').format("YYYY/MM/DD");
                to.touched = true;
                if (from && from.options && from.options.maxDate !== unix) {
                    var cachedValue = from.getState().selected.unixDate;
                    from.options = {maxDate: new persianDate(unix).subtract('d', 1)};
                    if (from.touched) {
                        from.setDate(cachedValue);
                    }
                }
            }
        });
    },
    data(){
        return{
            operation_type : 'individual',
            withDashboard: true,
            contract_id: null,
            individual: {
                "first_name":null,
                "last_name": null,
                "national_code": null,
                "mobile": null,
                "initial_start": null,
                "initial_end": null
            },
            excel_employees: []
        }
    },
    methods:{
        GetRoute(routeName,parameter){
            return route(routeName,parameter);
        },
        ContractSelected(id){
            this.contract_id = id;
        },
        ExcelUpload(){
            const file = $("#upload_file");
            if (file.val()) {
                const self = this;
                self.$root.$data.show_loading = true;
                let data = new FormData();
                data.append("excel_file", file[0].files[0]);
                axios.post(route("EmployeesManagement.excel_upload",["NewEmployee"]), data)
                    .then(function (response) {
                        self.$root.$data.show_loading = false;
                        if (response.data !== null) {
                            if (response.data.data) {
                                self.excel_employees = response.data.data;
                                self.$root.$data.uploaded_employees["employees"] = response.data.data;
                                self.$root.$data.uploaded_employees["headers"] = ["نام","نام خانوادگی","کد ملی","موبایل","شروع","پایان"];
                            }
                            if (typeof response.data["import_errors"] !== "undefined" && response.data["import_errors"].length > 0) {
                                self.$root.$data.import_errors = response.data["import_errors"];
                            } else
                                self.$root.$data.import_errors = [];
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

            } else
                file.closest("div").find(".file_selector_box").removeClass("is-invalid").addClass("is-invalid");
        },
        addEmployees(){
            const self = this;
            let flag = 0;
            let data = {
                "contract_id" : self.contract_id,
                "type" : self.operation_type,
                "first_name" : self.individual.first_name,
                "last_name" : self.individual.last_name,
                "national_code" : self.individual.national_code,
                "mobile" : self.individual.mobile,
                "initial_start" : self.individual.initial_start,
                "initial_end" : self.individual.initial_end,
                "employees" : JSON.stringify(self.excel_employees),
                "withDashboard" : self.withDashboard
            };
            switch (self.operation_type) {
                case "individual":{
                    if (self.individual.first_name === null || self.individual.first_name === "") {
                        $("#first_name").removeClass("is-invalid").addClass("is-invalid");
                        flag = 1;
                    }
                    if (self.individual.last_name === null || self.individual.last_name === "") {
                        $("#last_name").removeClass("is-invalid").addClass("is-invalid");
                        flag = 1;
                    }
                    if (self.individual.national_code === null || self.individual.national_code === "") {
                        $("#national_code").removeClass("is-invalid").addClass("is-invalid");
                        flag = 1;
                    }
                    if (self.individual.initial_start === null || self.individual.initial_start === "") {
                        $("#initial_start").removeClass("is-invalid").addClass("is-invalid");
                        flag = 1;
                    }
                    if (self.individual.initial_end === null || self.individual.initial_end === "") {
                        $("#initial_end").removeClass("is-invalid").addClass("is-invalid");
                        flag = 1;
                    }
                    break;
                }
                case "group":{
                    if (self.excel_employees.length === 0) {
                        $("#upload_file").closest("div").find(".file_selector_box").removeClass("is-invalid").addClass("is-invalid");
                        flag = 1;
                    }
                }
            }
            if (!flag){
                bootbox.confirm({
                    message: "آیا برای ذخیره سازی اطلاعات اطمینان دارید؟",
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
                            axios.post(route("EmployeesManagement.add_new_item"), data)
                                .then(function (response) {
                                    self.$root.$data.show_loading = false;
                                    if (response?.data) {
                                        switch (response.data["result"]) {
                                            case "success": {
                                                self.$root.$data.table_data_records = [];
                                                self.$root.refresh_selects();
                                                alertify.notify(response.data["message"], 'success', "5");
                                                break;
                                            }
                                            case "fail":{
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
