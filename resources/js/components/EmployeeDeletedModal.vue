<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                پرسنل حذف شده
            </h5>
        </div>
        <div class="modal-body" style="max-height: 80vh;overflow-y: auto">
            <div id="table-scroll-container">
                <div id="table-scroll" class="table-scroll">
                    <table id="search_table" class="table table-striped no-sort" data-filter="[3,4,5]" style="min-width: 100%">
                        <thead class="bg-menu-dark white-color">
                        <tr class="iransans">
                            <th scope="col" style="width: 70px"><span>شماره</span></th>
                            <th scope="col"><span>نام</span></th>
                            <th scope="col"><span>کد ملی</span></th>
                            <th scope="col"><span>ش.شناسنامه</span></th>
                            <th scope="col"><span>سازمان</span></th>
                            <th scope="col"><span>قرارداد</span></th>
                            <th scope="col"><span>تلفن همراه</span></th>
                            <th scope="col"><span>توسط</span></th>
                            <th scope="col"><span>عملیات</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="iransans" v-for="(employee,index) in DeletedEmployees" :key="index">
                            <td>{{employee.id}}</td>
                            <td>{{employee.name}}</td>
                            <td>{{employee.national_code}}</td>
                            <td>{{employee.id_number}}</td>
                            <td>{{employee.contract.organization.name}}</td>
                            <td>{{employee.contract.name}}</td>
                            <td>{{employee.mobile}}</td>
                            <td>{{employee.registrant_user.name}}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="بازگردانی پرسنل" v-on:click="RecoverEmployee(employee.id)">
                                    <i class="fad fa-rotate-left fa-1-6x vertical-middle"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
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
    name: "EmployeeDeletedModal",
    props:["kind"],
    data(){
        return {
            DeletedEmployees: []
        }
    },
    mounted() {
        const self = this;
        self.$root.$data.show_loading = true;
        axios.post(route("EmployeesManagement.get_deleted_employees"))
            .then(function (response) {
                self.$root.$data.show_loading = false;
                if (response?.data) {
                    self.DeletedEmployees = response.data?.deleted_employees;
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
    },
    methods:{
        RecoverEmployee(id){
            const self = this;
            if (id) {
                bootbox.confirm({
                    message: "آیا برای بازگردانی پرسنل انتخاب شده اطمینان دارید؟",
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
                            data.append("employee_id", id);
                            axios.post(route("EmployeesManagement.recover_employee"), data)
                                .then(function (response) {
                                    self.$root.$data.show_loading = false;
                                    if (response?.data) {
                                        switch (response.data["result"]) {
                                            case "success": {
                                                self.DeletedEmployees = response.data?.deleted_employees;
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
            else
                bootbox.alert("لطفا جهت ادامه متن پیام جهت ارسال را وارد نمایید");
        }
    }
}
</script>

<style scoped>

</style>
