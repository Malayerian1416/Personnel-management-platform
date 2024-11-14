<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                تمدید تاریخ قرارداد پرسنل
            </h5>
        </div>
        <div class="modal-body">
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">نوع عملیات</label>
                </span>
                <div class="fieldset-body">
                    <div class="form-check">
                        <input checked class="form-check-input vertical-middle" v-model="operation" value="extend" type="radio" id="extend" name="operation_type">
                        <label class="form-check-label iransans" for="extend">
                            تمدید تاریخ قرارداد
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input vertical-middle" v-model="operation" value="initial" type="radio" id="initial" name="operation_type">
                        <label class="form-check-label iransans" for="initial">
                            تغییر تاریخ اولیه قرارداد
                        </label>
                    </div>
                </div>
            </div>
            <reference-box :refs_needed="kind === 'individual' ? [4] : [1,2]" @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked"></reference-box>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">تاریخ مشخص</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check">
                                <input :disabled="reference === 'custom'" checked class="form-check-input vertical-middle" v-model="extension_type" value="constant_value" type="radio" id="constant_value" name="extension_type">
                                <label class="form-check-label iransans" for="constant_value">
                                    تمدید تاریخ با انتخاب کامل آن به صورت مشخص
                                </label>
                            </div>
                            <div class="input-group mb-3">
                                <input :disabled="extension_type !== 'constant_value'" type="text" class="form-control iransans text-center range_from" readonly v-model="start" placeholder="شروع قرارداد">
                                <input :disabled="extension_type !== 'constant_value'" type="text" class="form-control iransans text-center range_to" readonly v-model="end" placeholder="پایان قرارداد">
                            </div>
                        </div>
                        <div class="col-12" v-if="kind !== 'individual'">
                            <div class="form-check">
                                <input :disabled="reference === 'custom'" class="form-check-input vertical-middle" v-model="extension_type" value="append_value" type="radio" id="append_value" name="extension_type">
                                <label class="form-check-label iransans" for="append_value">
                                    تمدید تاریخ هر کدام از پرسنل با اضافه کردن تعداد ماه مشخص به تاریخ پایان قرارداد قبلی
                                </label>
                            </div>
                            <div class="input-group mb-3">
                                <input :disabled="extension_type !== 'append_value'" type="text" class="form-control iransans text-center month_mask" data-mask="00" placeholder="تعداد ماه های افزایش" v-model="appended_month">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="fieldset" v-if="kind !== 'individual'">
                <span class="legend">
                    <label class="iransans">تاریخ سفارشی</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input vertical-middle" v-model="reference" value="custom" type="radio" id="custom" name="reference" v-on:click="extension_type = null">
                                <label class="form-check-label iransans" for="custom">
                                    تمدید تاریخ مشخص غیر یکسان به همراه کد ملی پرسنل
                                    <a v-if="extension_type === 'unequal_value'" :href="GetRoute('EmployeesManagement.excel_download',['ContractDate'])">(فایل نمونه)</a>
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-10">
                            <s-file-browser :disabled="reference !== 'custom'" :accept="['xlsx']" :size="500000" :file_box_id="'date_file'" :filename_box_id="'date_browser_box'"></s-file-browser>
                        </div>
                        <div class="col-12 col-lg-2">
                            <button :disabled="reference !== 'custom'" class="btn btn-primary w-100" v-on:click="UploadExcelFile">
                                <i class="far fa-upload fa-1-2x me-1"></i>
                                <span class="iransans">بارگذاری فایل</span>
                            </button>
                        </div>
                        <div class="form-group col-12 mt-3">
                            <div class="mb-2">
                                <div class="input-group-text w-100 d-flex flex-row align-items-center justify-content-start g-5">
                                    <div>
                                        <button type="button" class="btn btn-outline-primary me-2" :disabled="excel_employees.length === 0" data-bs-toggle="modal" data-bs-target="#employees" v-on:click="$root.$data.return_modal='#employee_operations_modal'">
                                            <i class="far fa-user-magnifying-glass fa-1-2x me-1"></i>
                                            <span class="iransans" style="line-height: 22px">پرسنل :
                                        {{excel_employees.length}}
                                        نفر
                                    </span>
                                        </button>
                                    </div>
                                    <button v-if="$root.import_errors.length > 0" type="button" class="btn btn-outline-danger iransans" data-bs-toggle="modal" data-bs-target="#import_errors" v-on:click="$root.$data.return_modal='#employee_operations_modal'">
                                        <i class="far fa-triangle-exclamation fa-1-2x me-1"></i>
                                        <span class="iransans" style="line-height: 22px">خطای بارگذاری</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-menu">
            <button v-if="reference !== null && data !== null || reference === 'custom' && excel_employees.length > 0" type="button" class="btn btn-success" v-on:click="ExtendContract">
                <i class="far fa-calendar-check fa-1-2x me-2 vertical-middle"></i>
                <span class="iransans">تمدید قرارداد</span>
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
    name: "EmployeeDateExtensionModal",
    props:["kind"],
    data() {
        return {
            reference: null,
            data: null,
            extension_type: "constant_value",
            start: "",
            end: "",
            excel_employees: [],
            appended_month: 1,
            operation: "extend"
        }
    },
    mounted() {
        const self = this;
        let from = $(".range_from").pDatepicker({
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
            formatter: function (unix) {
                var date = new persianDate(unix);
                var gregorian = date.toLocale('en').toCalendar('persian');
                return gregorian.format("YYYY/MM/DD");
            },
            onSelect: function (unix) {
                from.touched = true;
                self.start = new persianDate(unix).toLocale('en').format("YYYY/MM/DD");
                if (to && to.options && to.options.minDate !== unix) {
                    var cachedValue = to.getState().selected.unixDate;
                    to.options = {minDate: new persianDate(unix).add('d', 1)};
                    if (to.touched) {
                        to.setDate(cachedValue);
                    }
                }
            }
        });
        let to = $(".range_to").pDatepicker({
            initialValue: false,
            initialValueType: 'persian',
            format: 'YYYY/MM/DD',
            autoClose: true,
            observer: true,
            leapYearMode: "astronomical",
            formatter: function (unix) {
                var date = new persianDate(unix);
                var gregorian = date.toLocale('en').toCalendar('persian');
                return gregorian.format("YYYY/MM/DD");
            },
            calendar:{
                persian: {
                    leapYearMode: 'astronomical'
                }
            },
            onSelect: function (unix) {
                to.touched = true;
                self.end = new persianDate(unix).toLocale('en').format("YYYY/MM/DD");
                if (from && from.options && from.options.maxDate !== unix) {
                    var cachedValue = from.getState().selected.unixDate;
                    from.options = {maxDate: new persianDate(unix).subtract('d', 1)};
                    if (from.touched) {
                        from.setDate(cachedValue);
                    }
                }
            }
        });
        const month = $(".month_mask");
        month.on("input", function () {
            if (parseInt(month.val()) > 12)
                month.val("12");
        });

    },
    methods: {
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
        UploadExcelFile() {
            const file = $("#date_file");
            if (file.val()) {
                const self = this;
                self.$root.$data.show_loading = true;
                let data = new FormData();
                data.append("date_file", file[0].files[0]);
                axios.post(route("EmployeesManagement.excel_upload", ["ContractDate"]), data)
                    .then(function (response) {
                        self.$root.$data.show_loading = false;
                        self.$root.$data.import_errors = [];
                        self.excel_employees = [];
                        file.val('');
                        if (response.data !== null) {
                            if (typeof response.data["import_errors"] !== "undefined" && response.data["import_errors"].length > 0) {
                                self.$root.$data.import_errors = response.data["import_errors"];
                            } else
                                self.$root.$data.import_data = [];
                            switch (response.data["result"]) {
                                case "success": {
                                    self.$root.$data.user_allowed_contracts = response.data?.contracts;
                                    self.$root.$data.user_allowed_groups = response.data?.groups;
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
        Validation() {
            if (this.reference === 'organization' || this.reference === 'group' || this.reference === 'individual') {
                switch (this.extension_type) {
                    case "constant_value": {
                        if (this.start !== "" && this.end !== "")
                            return true;
                        else {
                            $(".range_to,.range_from").removeClass("is-invalid").addClass("is-invalid");
                            return false;
                        }
                    }
                    case "append_value": {
                        if (this.appended_month !== 0 && this.appended_month !== null && this.appended_month !== "")
                            return true;
                        else {
                            $(".month_mask").removeClass("is-invalid").addClass("is-invalid");
                            return false;
                        }
                    }
                    default: {
                        return false;
                    }
                }
            } else if (this.reference === 'custom') {
                if (this.excel_employees.length > 0)
                    return true;
                else {
                    $("#date_browser_box").removeClass("is-invalid").addClass("is-invalid");
                    return false
                }
            } else
                return false;
        },
        ExtendContract() {
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
                            data.append("extension_type", self.extension_type);
                            data.append("operation", self.operation);
                            if (self.reference !== 'custom' && self.$data.data !== null || self.reference === 'custom' && self.excel_employees !== null) {
                                switch (self.reference) {
                                    case "organization":
                                        data.append("contract_id", self.$data.data);
                                        break;
                                    case "group":
                                        data.append("group_id", self.$data.data);
                                        break;
                                    case "custom":
                                        data.append("employees", JSON.stringify(self.excel_employees));
                                        break;
                                    case "individual":
                                        data.append("employee_id", self.data);
                                }
                            }
                            switch (self.extension_type){
                                case "constant_value":
                                    data.append("start",self.start);
                                    data.append("end",self.end);
                                    break;
                                case "append_value":
                                    data.append("appended_month",self.appended_month);
                                    break;
                            }
                            axios.post(route("EmployeesManagement.item_date_extension"), data)
                                .then(function (response) {
                                    self.$root.refresh_selects();
                                    self.$root.$data.show_loading = false;
                                    if (response?.data) {
                                        if (response.data.data) {
                                            self.$root.$data.user_allowed_contracts = response.data.data.contracts;
                                            self.$root.$data.user_allowed_groups = response.data.data.groups;
                                            self.$root.$data.table_data_records = [];
                                        }
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
        }
    }
}
</script>
