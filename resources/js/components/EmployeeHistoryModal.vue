<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                پرونده پرسنلی
            </h5>
        </div>
        <div class="modal-body">
            <reference-box :refs_needed="kind === 'individual' ? [4] : [1,2,3]" @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked"></reference-box>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">تبدیل قرارداد</label>
                </span>
                <div class="fieldset-body">
                    <h6 class="iransans mb-3">قرارداد(های) درج شده به ترتیب از راست به چپ در تاریخ مشخص شده و توسط شخص درج شده تبدیل شده اند. </h6>
                    <div v-if="history?.conversions" class="w-100 d-flex flex-row align-items-center justify-content-start flex-wrap gap-3">
                        <div v-for="(conversion,index) in history.conversions" class="d-flex flex-row align-items-center justify-content-start gap-3">
                            <i v-if="index > 0" class="fa fa-arrow-left fa-1-6x"></i>
                            <div class="d-flex flex-column align-items-center justify-content-center gap-2 border px-5 py-3" :key="index">
                                <i class="fas fa-folder fa-3x"></i>
                                <span class="iransans bolder-font" v-text="conversion.contract.name"></span>
                                <span class="iransans text-muted" v-text="conversion.user.name"></span>
                                <span class="iransans text-muted" v-text="PersianDateString(conversion.created_at)"></span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="d-flex align-items-center justify-content-center">
                        <span class="iransans text-muted">موردی یافت نشد</span>
                    </div>
                </div>
            </div>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">تمدید قرارداد</label>
                </span>
                <div class="fieldset-body">
                    <h6 class="iransans mb-3">تاریخ(های) درج شده به ترتیب از راست به چپ در تاریخ مشخص شده و توسط شخص درج شده تمدید شده اند. </h6>
                    <div v-if="history?.extensions" class="w-100 d-flex flex-row align-items-center justify-content-start flex-wrap gap-3">
                        <div v-for="(extension,index) in history.extensions" class="d-flex flex-row align-items-center justify-content-start gap-3">
                            <i v-if="index > 0" class="fa fa-arrow-left fa-1-6x"></i>
                            <div class="d-flex flex-column align-items-center justify-content-center gap-2 border px-5 py-3" :key="index">
                                <div class="border border-dark bg-light p-2 d-flex flex-column align-items-center justify-content-center rounded-2 gap-2">
                                    <span class="iransans bolder-font" v-text="PersianDateString(extension.start)"></span>
                                    <span class="iransans">لغایت</span>
                                    <span class="iransans bolder-font" v-text="PersianDateString(extension.end)"></span>
                                </div>
                                <span class="iransans text-muted" v-text="extension.user.name"></span>
                                <span class="iransans text-muted" v-text="PersianDateString(extension.created_at)"></span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="d-flex align-items-center justify-content-center">
                        <span class="iransans text-muted">موردی یافت نشد</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-menu">
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
    name: "EmployeeHistoryModal",
    props:["kind"],
    data(){
        return {
            reference: null,
            data: null,
            history: []
        }
    },
    mounted() {
        const self = this;
        axios.post(route("EmployeesManagement.history"), {"employee_id": self.$data.data})
            .then(function (response) {
                if (response?.data) {
                    switch (response.data.result) {
                        case "success": {
                            self.history = response.data?.history;
                            alertify.notify("اطلاعات با موفقیت دریافت شد", 'success', "5");
                            break;
                        }
                        case "fail": {
                            alertify.notify(response.data["message"], 'error', "30");
                            break;
                        }
                    }
                }
            }).catch(function (error) {
            alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
        });
    },
    methods:{
        ReferenceChecked(ref){
            this.reference = ref;
        },
        ReferenceSetup(ref){
            this.reference = ref.type;
            this.data = ref.target;
        },
        PersianDateString(date){
            return new persianDate(new Date(date)).toLocale("en").format("YYYY/MM/DD");
        }
    }
}
</script>

<style scoped>

</style>
