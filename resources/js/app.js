import './bootstrap';
import Vue from 'vue'
import route from 'ziggy-js';
import AutoNumeric from "autonumeric";
window.AutoNumeric = AutoNumeric;
window.PDFObject = require('pdfobject');
import numeral from 'numeral';
window.numeral = numeral;
window.persianDate = require('persian-date');
import pDatepicker from 'm-persian-datepicker';
window.pDatepicker = pDatepicker;
import alertify from 'alertifyjs';
window.alertify = alertify;
import VueContext from 'vue-context';
require('bootstrap-select');
require('bootstrap-select/js/i18n/defaults-fa_IR');
require('owl.carousel');
window.bootbox = require("bootbox/dist/bootbox.all.min");
import fancyTable from 'jquery.fancytable';
window.fancyTable = fancyTable;
import * as ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import Chart from 'chart.js/auto';
let locale = {
    OK: 'قبول',
    CONFIRM: 'تایید',
    CANCEL: 'انصراف',
};
bootbox.addLocale('custom', locale);
bootbox.setDefaults({
    locale: "custom",
    show: true,
    backdrop: true,
    closeButton: false,
    animate: true,
    className: "bootbox-modal"
});
window.Draggabilly = require('draggabilly');
window.BootstrapMenu = require('bootstrap-menu');
window.toastr = require('toastr');
let FormTemplate = Vue.component('form-template', require('./components/FormTemplate').default);
let EditableParagraph = Vue.component('editable-paragraph', require('./components/EditableParagraph').default);
let DataTable = Vue.component('data-table', require('./components/DataTable').default);
let MultipleFileBrowser = Vue.component('m-file-browser', require('./components/MultipleFileBrowser').default);
let SingleFileBrowser = Vue.component('s-file-browser', require('./components/SingleFileBrowser').default);
let Loading = Vue.component('loading', require('./components/Loading').default);
let RegistrationLoading = Vue.component('r-loading', require('./components/RegistrationLoading').default);
let UploadLoading = Vue.component('upload-loading', require('./components/UploadLoading').default);
let AxiosButton = Vue.component('axios-button', require('./components/AxiosButton').default);
let LiveTransfer = Vue.component('live-transfer', require('./components/LiveTransfer').default);
let EditableDataTable = Vue.component('editable-data-table', require('./components/EditableDataTable').default);
let TimeCounterButton = Vue.component('time-counter-button', require('./components/TimeCounterButton').default);
let Progress = Vue.component('progress-steps', require('./components/Progress').default);
let HelpModal = Vue.component('help-modal', require('./components/HelpModal').default);
let VueImage = Vue.component('vue-image', require('./components/VueImage').default);
let TableOperations = Vue.component('table-operations', require('./components/TableOperations').default);
let TreeSelect = Vue.component('tree-select', require('./components/TreeSelect').default);
let ReferenceBox = Vue.component('reference-box', require('./components/ReferenceBox').default);
let EmployeeAddModal = Vue.component('employee-add-modal', require('./components/EmployeeAddModal').default);
let EmployeeRemoveModal = Vue.component('employee-remove-modal', require('./components/EmployeeRemoveModal').default);
let EmployeeDetachModal = Vue.component('employee-detach-modal', require('./components/EmployeeDetachModal').default);
let EmployeeStatusModal = Vue.component('employee-status-modal', require('./components/EmployeeStatusModal').default);
let EmployeeAuthenticationModal = Vue.component('employee-authentication-modal', require('./components/EmployeeAuthenticationModal').default);
let EmployeeRefreshDataModal = Vue.component('employee-refresh-data-modal', require('./components/EmployeeRefreshDataModal').default);
let EmployeeDateExtensionModal = Vue.component('employee-date-extension-modal', require('./components/EmployeeDateExtensionModal').default);
let EmployeeContractConversionModal = Vue.component('employee-contract-conversion-modal', require('./components/EmployeeContractConversionModal').default);
let EmployeeApplicationsModal = Vue.component('employee-applications-modal', require('./components/EmployeeApplicationsModal').default);
let EmployeeHistoryModal = Vue.component('employee-history-modal', require('./components/EmployeeHistoryModal').default);
let EmployeeRequestHistoryModal = Vue.component('employee-request-history-modal', require('./components/EmployeeRequestHistoryModal').default);
let EmployeeExcelListModal = Vue.component('employee-excel-list-modal', require('./components/EmployeeExcelListModal.vue').default);
let EmployeeSendSmsModal = Vue.component('employee-send-sms-modal', require('./components/EmployeeSendSmsModal.vue').default);
let EmployeeDeletedModal = Vue.component('employee-deleted-modal', require('./components/EmployeeDeletedModal.vue').default);
let EmployeeTicketsModal = Vue.component('employee-ticket-modal', require('./components/EmployeeTicketsModal.vue').default);
let EmployeeTicketHistoryModal = Vue.component('employee-ticket-history-modal', require('./components/EmployeeTicketHistoryModal.vue').default);
let EmployeeAnnouncementsModal = Vue.component('employee-announcements-modal', require('./components/EmployeeAnnouncementsModal.vue').default);
const app = new Vue({
    el: '#app',
    data:{
        withEmail: false,
        withSMS: true,
        show_loading: false,
        show_upload_loading: false,
        button_loading: false,
        import_errors: [],
        table_data_records : typeof table_data !== "undefined" ? table_data : [],
        verify_code_resend_time : typeof resend_time !== "undefined" ? resend_time : false,
        end_user_checkbox: false,
        registration_intro_section: 1,
        sidebar_toggle: false,
        desktop_sidebar_toggle: false,
        is_static_sidebar: localStorage.getItem("is_static_sidebar") !== "NaN" && localStorage.getItem("is_static_sidebar") !== "null" ? parseInt(localStorage.getItem("is_static_sidebar")) : 1,
        docs:[],
        doc_show: 1,
        image_error:false,
        employee_db_information:'',
        locale: {
            OK: 'I Suppose',
            CONFIRM: 'Go Ahead',
            CANCEL: 'Maybe Not'
        },
        is_parent: typeof is_parent_sub !== "undefined" ? is_parent_sub : false,
        old_children_subset_list: typeof old_children_list !== "undefined" ? old_children_list : [],
        children_subset_list: typeof children_list !== "undefined" ? children_list : [],
        print_preview_percent:typeof page_config !== "undefined" ? page_config !== null ? page_config.percent : 75 : 75,
        print_page_size: typeof page_config !== "undefined" ? page_config !== null ? page_config.page : 'A4' : 'A4',
        print_page_orientation: typeof page_config !== "undefined" ? page_config !== null ? page_config.orientation : 'portrait' : 'portrait',
        print_route : typeof print_url !== "undefined" ? print_url : '',
        image_rotation: 0,
        selected_employees: [],
        employee: [],
        employees: [],
        old_employees: typeof old_employee_list !== "undefined" ? old_employee_list : [],
        sms_send_permission: true,
        select_model: typeof select_model_data !== "undefined" ? select_model_data : [],
        operation_select_model: null,
        sms_collection: typeof sms_bank !== "undefined" ? sms_bank : [],
        return_modal:'',
        modal:'',
        table_base: 'contract',
        organizations:typeof allowed_organizations !== "undefined" ? allowed_organizations : [],
        user_allowed_contracts: typeof allowed_contracts !== "undefined" ? allowed_contracts : [],
        user_allowed_groups: typeof allowed_groups !== "undefined" ? allowed_groups : [],
        contracts: '',
        groups: '',
        contract_id: typeof contract_id_data !== "undefined" ? contract_id_data : '',
        operation_employee_type : 'individual',
        tree_select_contract_id: '',
        modal_size: 'modal-lg',
        employee_operation_type: '',
        employee_operation_kind: '',
        import_data: [],
        uploaded_employees: {"header":[],"employees":[]},
        applications: typeof applications_data !== "undefined" ? applications_data : [],
        pdf_viewer: null,
        last_excel_column: 0,
        add_remove_excel_column: 0,
        excel_columns: typeof excel_columns_data !== "undefined" ? excel_columns_data : [],
        multi_contract_id: [],
        file_selected: false,
        payslip_employees: typeof payslip_employees_data !== "undefined" ? payslip_employees_data : [],
        payslip_template: [],
        national_code_index: null,
        payslip_view_route: null,
        employee_advantages: typeof employee_advantages_data !== "undefined" ? employee_advantages_data : [],
        advantage_columns: typeof advantage_columns_data !== "undefined" ? advantage_columns_data : [],
        roles_list: typeof roles_list_data !== "undefined" ? roles_list_data : [],
        reference: null,
        data: null,
        employee_requests: typeof employee_request_data !== "undefined" ? employee_request_data : [],
        request: [],
        request_comment: "",
        request_reject_reason: "",
        first_name: "",
        last_name: "",
        national_code: "",
        id_number: "",
        father_name: "",
        gender: "",
        mobile: "",
        job_seating: "",
        job_title: "",
        start_date: "",
        end_date: "",
        name:"",
        organization: "",
        description:"",
        excel_column_index: 0,
        old_contract_id: 0,
        employee_id: null,
        data_recipient: null,
        data_borrower: null,
        data_loan_amount: null,
        EmployeeIndex: 0,
        PluginsLoading: false,
        RegistrationData: {"data":[],"loading":true},
        AutomationsData: {"data":[],"loading":true},
        TicketsData: {"data":[],"loading":true},
        UnregisteredData: {"data":[],"loading":true},
        RefreshesData: {"data":[],"loading":true},
        ExpiredData: {"data":[],"loading":true},
        RequestChart: {"data":[],"loading":true},
        RegistrationChart: {"data":[],"loading":true},
        VisitChart: {"data":[],"loading":true},
        MoneySeparator: null,
        UserTickets: typeof user_tickets_data !== "undefined" ? user_tickets_data : [],
        UserTicketDetails: [],
        UserMessage: "",
        UserPayslips: typeof user_payslips_data !== "undefined" ? user_payslips_data : [],
        UserPayslip: [],
        UserPayslipDetails: [],
        EmployeesFound: [],
        RegistrationFound: [],
        BackupType: "Database",
        Backups: typeof backup_data !== "undefined" ? backup_data : [],
        subjects: typeof user_tickets_data !== "undefined" ? user_tickets_data : [],
        subject: typeof subject_data !== "undefined" ? subject_data : []
    },
    computed: {
        MainExcelColumns(){
            const columns = [];
            const letters = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
            letters.forEach((letter) => {
                columns.push(letter);
            });
            for (let i = 0 ; i < letters.length ; i++){
                for (let j = 0 ; j < letters.length ; j++)
                    columns.push(`${letters[i] + letters[j]}`);
            }
            return columns;
        },
    }
    ,
    components: {
        VueContext,
        data_table: DataTable,
        m_file_browser: MultipleFileBrowser,
        s_file_browser: SingleFileBrowser,
        loading: Loading,
        upload_loading: UploadLoading,
        axios_button: AxiosButton,
        live_transfer: LiveTransfer,
        editable_data_table: EditableDataTable,
        time_counter_button: TimeCounterButton,
        progress_steps: Progress,
        help_modal: HelpModal,
        vue_image: VueImage,
        table_operations: TableOperations,
        tree_select: TreeSelect,
        employee_add_modal: EmployeeAddModal,
        employee_remove_modal: EmployeeRemoveModal,
        employee_detach_modal: EmployeeDetachModal,
        employee_status_modal: EmployeeStatusModal,
        reference_box : ReferenceBox,
        employee_authentication_modal: EmployeeAuthenticationModal,
        employee_refresh_data_modal: EmployeeRefreshDataModal,
        employee_date_extension_modal: EmployeeDateExtensionModal,
        employee_contract_conversion_modal: EmployeeContractConversionModal,
        employee_applications_modal: EmployeeApplicationsModal,
        employee_history_modal: EmployeeHistoryModal,
        employee_request_history_modal: EmployeeRequestHistoryModal,
        employee_excel_list_modal: EmployeeExcelListModal,
        employee_send_sms_modal: EmployeeSendSmsModal,
        employee_deleted_modal: EmployeeDeletedModal,
        employee_tickets_modal: EmployeeTicketsModal,
        employee_ticket_history_modal: EmployeeTicketHistoryModal,
        employee_announcements_modal: EmployeeAnnouncementsModal,
        form_template: FormTemplate,
        editable_paragraph: EditableParagraph,
        r_loading: RegistrationLoading
    },
    created() {
        const self = this;
        function BackupStream(){
            axios.post(route("Backup.stream")).then(function (response) {
                self.Backups = response?.data;
            }).catch(function (error) {
                alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
            });
        }
        if (typeof is_backup !== "undefined") {
            console.log("ok");
            setInterval(BackupStream, 4000);
        }
        if($(window).innerWidth() <= 900) {
            self.sidebar_toggle = true;
            self.desktop_sidebar_toggle = false;
            $(".information-box,.content-header,.content-footer,.menu-header-icon.small-sidebar-icon,.sidebar-menu .nav-item,.company_name,.account-name,.small-sidebar-button,.sidebar,.dashboard-logo").removeClass("small-sidebar");
        }
        else {
            self.desktop_sidebar_toggle = true;
            self.sidebar_toggle = false;
        }
    },
    mounted() {
        if(typeof logged_user !== "undefined") {
            Echo.connector.pusher.connection.bind('connected', () => {
                Echo.private(`notifications.${logged_user.user}`)
                    .listen('NewNotification', (notification) => {
                        alertify.notify(notification.message, 'success', "7");
                        switch (notification.type) {
                            case "request": {
                                self.AutomationsData.loading = true;
                                axios.post(route("IdlePlugins"),{"type":"automations"})
                                    .then(function (response) {
                                        if (response?.data) {
                                            self.AutomationsData.data = response.data;
                                            self.AutomationsData.loading = false;
                                        }
                                    }).catch(function (error) {
                                    alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                                });
                                break;
                            }
                            case "preRegister": {
                                self.UnregisteredData.loading = true;
                                axios.post(route("IdlePlugins"),{"type":"unregistered"})
                                    .then(function (response) {
                                        if (response?.data) {
                                            self.UnregisteredData.data = response.data;
                                            self.UnregisteredData.loading = false;
                                        }
                                    }).catch(function (error) {
                                    alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                                });
                                break;
                            }
                            case "register": {
                                self.RegistrationData.loading = true;
                                axios.post(route("IdlePlugins"),{"type":"registration"})
                                    .then(function (response) {
                                        if (response?.data) {
                                            self.RegistrationData.data = response.data;
                                            self.RegistrationData.loading = false;
                                        }
                                    }).catch(function (error) {
                                    alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                                });
                                break;
                            }
                            case "ticket": {
                                self.TicketsData.loading = true;
                                axios.post(route("IdlePlugins"),{"type":"tickets"})
                                    .then(function (response) {
                                        if (response?.data) {
                                            self.TicketsData.data = response.data;
                                            self.TicketsData.loading = false;
                                        }
                                    }).catch(function (error) {
                                    alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                                });
                                break;
                            }
                            case "refresh": {
                                self.RefreshesData.loading = true;
                                axios.post(route("IdlePlugins"),{"type":"refreshes"})
                                    .then(function (response) {
                                        if (response?.data) {
                                            self.RefreshesData.data = response.data;
                                            self.RefreshesData.loading = false;
                                        }
                                    }).catch(function (error) {
                                    alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                                });
                                break;
                            }
                        }
                    });
            });
        }
        Chart.defaults.font.family = "IRANSansXFaNum";
        if($(".idle_plugins").length){
            const self = this;
            axios.post(route("IdlePlugins"),{"type":"all"})
                .then(function (response) {
                    self.RegistrationData.loading = false;
                    self.AutomationsData.loading = false;
                    self.TicketsData.loading = false;
                    self.UnregisteredData.loading = false;
                    self.RefreshesData.loading = false;
                    self.ExpiredData.loading = false;
                    self.RegistrationChart.loading = false;
                    self.RequestChart.loading = false;
                    self.VisitChart.loading = false;
                    console.log(response.data);
                    if (response?.data) {
                        self.RegistrationData.data =  response.data?.registration;
                        self.AutomationsData.data = response.data?.automations;
                        self.TicketsData.data = response.data?.tickets;
                        self.UnregisteredData.data = response.data?.unregistered;
                        self.RefreshesData.data = response.data?.refreshes;
                        self.ExpiredData.data = response.data?.expired;
                        self.RegistrationChart.data = response.data.registrationChart;
                        self.RequestChart.data = response.data.requestChart;
                        self.VisitChart.data = response.data.visitChart;
                        self.$nextTick(() => {
                            new Chart(
                                document.getElementById('requests_chart'),
                                {
                                    type: 'line',
                                    options:{
                                        responsive: true,
                                        plugins: {
                                            legend: {
                                                display: false,
                                            }
                                        },
                                    },
                                    data: {
                                        labels: self.RequestChart.data.map(row => row.month),
                                        datasets: [
                                            {
                                                borderColor: "#198754FF",
                                                data: self.RequestChart.data.map(row => row.count),
                                            }
                                        ]
                                    }
                                }
                            );
                            new Chart(
                                document.getElementById('registration_chart'),
                                {
                                    type: 'line',
                                    options:{
                                        responsive: true,
                                        plugins: {
                                            legend: {
                                                display: false,
                                            }
                                        },
                                    },
                                    data: {
                                        labels: self.RegistrationChart.data.map(row => row.month),
                                        datasets: [
                                            {
                                                borderColor: "#198754FF",
                                                data: self.RegistrationChart.data.map(row => row.count)
                                            }
                                        ]
                                    }
                                }
                            );
                            new Chart(
                                document.getElementById('visit_chart'),
                                {
                                    type: 'bar',
                                    options:{
                                        responsive: true,
                                        plugins: {
                                            legend: {
                                                display: false,
                                            }
                                        },
                                    },
                                    data: {
                                        labels: self.VisitChart.data.map(row => row.month),
                                        datasets: [
                                            {
                                                data: self.VisitChart.data.map(row => row.count)
                                            }
                                        ]
                                    }
                                }
                            );
                        });
                    }
                }).catch(function (error) {
                self.RegistrationData.loading = false;
                self.AutomationsData.loading = false;
                self.TicketsData.loading = false;
                self.UnregisteredData.loading = false;
                self.RefreshesData.loading = false;
                self.ExpiredData.loading = false;
                self.RegistrationChart.loading = false;
                self.RequestChart.loading = false;
                self.VisitChart.loading = false;
                alertify.notify("عدم توانایی در انجام عملیات پلاگین" + `(${error})`, 'error', "30");
            });
        }
        if($("#editor").length > 0) {
            ClassicEditor.create(document.querySelector('#editor'), {
                language: 'fa'
            }).catch(error => {
                bootbox.alert("ویرایشگر متن بارگذاری نشده است" + ` ${error} `)
            });
        }
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl,{"trigger" : "hover"}));
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
        const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
        $(".table:not(.no-sort)").fancyTable({
            sortColumn:[0],
            pagination: false,
            perPage:0,
            globalSearch:false,
            searchable: false
        });
        if ($("#fail_toast").length) {
            const message_toast = document.getElementById('fail_toast')
            const toast = new bootstrap.Toast(message_toast);
            toast.show();
        }
        if ($("#success_toast").length) {
            const message_toast = document.getElementById('success_toast')
            const toast = new bootstrap.Toast(message_toast);
            toast.show();
        }
        if (this.is_static_sidebar === 0 && this.sidebar_toggle === false)
            $(".information-box,.content-header,.content-footer,.menu-header-icon.small-sidebar-icon,.sidebar-menu .nav-item,.company_name,.account-name,.small-sidebar-button,.sidebar,.dashboard-logo").addClass("small-sidebar");
        const self = this;
        $(document).ready(function (){
            String.prototype.toEnglishDigits = function() {
                return this.replace(/[۰-۹]/g, (chr) => {
                    let persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
                    return persian.indexOf(chr);
                });
            };
            let selectpicker = $(".selectpicker-select");
            if(selectpicker.length) {
                $('.selectpicker-select').selectpicker();
                selectpicker.each(function () {
                    if ($(this).hasClass("is-invalid")) {
                        $(this).closest("div").find(".dropdown-toggle").addClass("is-invalid");
                        $(this).closest("div").find(".dropdown-toggle.bs-placeholder").addClass("is-invalid");
                    }
                });
                selectpicker.on("change", function () {
                    $(this).closest("div").find(".dropdown-toggle").removeClass("is-invalid-select");
                });
            }
            $(".find-employees").find("input[type='search']").on("input",(e) => {
                const keyword = e.target.value;
                self.EmployeesFound = []; self.RegistrationFound = [];
                if (keyword.length > 2) {
                    axios.post(route("EmployeesManagement.find_employees"), {"keyword": keyword})
                        .then(function (response) {
                            if (response?.data) {
                                switch (response.data.result) {
                                    case "success": {
                                        if (response.data?.employees)
                                            self.EmployeesFound = response.data.employees;
                                        else if(response.data?.registration)
                                            self.RegistrationFound = response.data?.registration;
                                        else
                                            self.EmployeesFound = []; self.RegistrationFound = [];
                                        self.$nextTick(function (){
                                            const select = $(".find-employees-select");
                                            select.selectpicker('refresh');
                                        });
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
                }
                else{
                    self.EmployeesFound = []; self.RegistrationFound = [];
                    self.$nextTick(function (){
                        const select = $(".find-employees-select");
                        select.selectpicker('refresh');
                    });
                }
            });
            this.MoneySeparator = $(".thousand_separator").length ? new AutoNumeric.multiple('.thousand_separator',['integer',{'digitGroupSeparator':',','watchExternalChanges':true}]) : null;
            let date_masked = $(".date_masked");
            if (date_masked.length) {
                date_masked.pDatepicker({
                    initialValue: true,
                    initialValueType: 'persian',
                    format: 'YYYY/MM/DD',
                    autoClose: true,
                    responsive: true,
                    scroll: true,
                    calendar:{
                        persian: {
                            leapYearMode: 'astronomical'
                        }
                    },
                    formatter: function (unix) {
                        var date = new persianDate(unix);
                        var gregorian = date.toLocale('en').toCalendar('persian');
                        return gregorian.format("YYYY/MM/DD");
                    }
                });
            }
            let date_picker_from = $(".persian_datepicker_range_from");
            let date_picker_to = $(".persian_datepicker_range_to");
            if (date_picker_from.length && date_picker_to.length) {
                let from = date_picker_from.pDatepicker({
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
                        if (to && to.options && to.options.minDate !== unix) {
                            var cachedValue = to.getState().selected.unixDate;
                            to.options = {minDate: new persianDate(unix).add('d', 1)};
                            if (to.touched) {
                                to.setDate(cachedValue);
                            }
                        }
                    }
                });
                let to = date_picker_to.pDatepicker({
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
            }
            let number_masked = $(".number_masked");
            if (number_masked) {
                number_masked.each(function () {
                    Inputmask().mask($(this));
                });
            }
            if ($(".alert-success").length){
                if ($(".report-modal").length === 0) {
                    setTimeout(function () {
                        $('.information-box').fadeOut(2000, function () {
                            $(this).remove();
                        })
                    }, 4000);
                }
            }
            $("input[type='number']").change(function (){
                if ($(this).val() === "")
                    $(this).val("0");
            });
            document.addEventListener("click",function (e){
                if($("header").has(e.target).length === 0 || $(e.target).hasClass("toolbar-menu") || $(e.target).hasClass("nav-item")){
                    const tabs = $("header .tab-pane");
                    const nav_links = $("header .nav-link");
                    tabs.each(function (){
                        $(this).hasClass("active") && $(this).hasClass("show") ? $(this).removeClass(["active","show"]) : null;
                    });
                    nav_links.each(function (){
                        $(this).hasClass("active") ? $(this).removeClass("active") : null;
                    });
                }
            });
            if ($(".table-scroll").length){
                let table = document.getElementsByClassName('table-scroll');
                $(table).css("max-height",`calc(100vh - ${document.getElementById("table-scroll-container").offsetTop}px - 15px)`);
            }
        });
        window.addEventListener("resize",function (){
            if ($(".table-scroll").length) {
                let table = document.getElementsByClassName('table-scroll');
                $(table).css("max-height", `calc(100vh - ${document.getElementById("table-scroll-container").offsetTop}px - 15px)`);
            }
            const self = this;
            if($(window).innerWidth() <= 900) {
                self.sidebar_toggle = true;
                self.desktop_sidebar_toggle = false;
                $(".information-box,.content-header,.content-footer,.menu-header-icon.small-sidebar-icon,.sidebar-menu .nav-item,.company_name,.account-name,.small-sidebar-button,.sidebar,.dashboard-logo").removeClass("small-sidebar");
            }
            else {
                self.desktop_sidebar_toggle = true;
                self.sidebar_toggle = false;
            }
        });
    },
    watch:{
        'Backups': {
            handler: function () {
                setInterval(this.BackupStream,4000);
            }
        }
    },
    methods:{
        ContractSelected(id){
            this.contract_id = id;
        },
        MultiContractSelected(id){
            this.multi_contract_id.push(id);
        },
        ContractDeselected(id){
            const deselected = this.multi_contract_id.findIndex((item) => { return item === id });
            if(deselected)
                this.multi_contract_id.splice(deselected,1);
            this.$forceUpdate();
        },
        ReferenceChecked(ref) {
            this.reference = ref;
            this.data = null;
        },
        ReferenceSetup(ref) {
            this.reference = ref.type;
            this.data = ref.target;
            this.$forceUpdate();
        },
        EmployeeSelected(employees){
            this.data = employees;
            this.$forceUpdate();
        },
        refresh_selects(){
            this.select_model = '';
            this.$nextTick(function (){
                $(".selectpicker-select").each(function (){
                    let val = $(this).val() ? $(this).val() : null;
                    $(this).selectpicker('destroy').selectpicker('val', `${val}`).selectpicker('render');
                });
            });
        },
        recaptcha(e){
            e.preventDefault();
            axios.post("/recaptcha")
                .then(function (response) {
                    if (response.data !== null)
                        $('.captcha-image').html(response.data.captcha);
                }).catch(function (){
                alertify.error("کد امنیتی تولید نمیشود!");
            });
        },
        isJson(text){
            return /^[\],:{}\s]*$/.test(text.replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''));
        },
        submit_form(e){
            const self = this;
            e.preventDefault();
            if (typeof e.target.dataset.json !== "undefined"){
                if (self.isJson(e.target.dataset.json)){
                    const variables = JSON.parse(e.target.dataset.json);
                    variables.forEach((variable) => {
                        let input = document.createElement("INPUT");
                        input.setAttribute("type", "text");
                        input.setAttribute("hidden", "true");
                        input.setAttribute("name", variable);
                        input.setAttribute("value", JSON.stringify(self.$data[variable]));
                        $(input).appendTo(`#${e.target.id}`);
                    });
                }
                else {
                    const variable = e.target.dataset.json;
                    let input = document.createElement("INPUT");
                    input.setAttribute("type", "text");
                    input.setAttribute("hidden", "true");
                    input.setAttribute("name", variable);
                    input.setAttribute("value", JSON.stringify(self.$data[variable]));
                    $(input).appendTo(`#${e.target.id}`);
                }
            }
            bootbox.confirm({
                message: "آیا برای ایجاد تغییرات و ذخیره سازی اطمینان دارید؟",
                buttons: {
                    confirm: {
                        label: 'بله',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'خیر',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result === true) {
                        // $(".submit_button").prop("disabled",true);
                        // $(".submit_button_icon").removeClass("fa-database").addClass("fa-rotate").addClass("fa-spin");
                        self.button_loading = true;
                        self.show_loading = true;
                        e.target.submit();
                    }
                }
            });
        },
        main_route_change() {
            let main = $("#main");
            main.find('option').remove();
            $("#menu_action_id option:selected").map(function () {
                main.append(`<option value=${$(this).val()}>${$(this).text()}</option>`);
            });
            $(main).selectpicker('destroy').selectpicker();
        },
        menu_action_checkmark(e) {
            e.stopPropagation();
            $(e.currentTarget).children('input[type="checkbox"]').prop('checked',!$(e.currentTarget).children('input[type="checkbox"]').prop('checked'));
        },
        select_all_checkboxes(e){
            $(e.target).closest('ul').find('input[type="checkbox"]').prop("checked",true);
        },
        deselect_all_checkboxes(e){
            $(e.target).closest('ul').find('input[type="checkbox"]').prop("checked",false);
        },
        login(){
            $(".login-button").prop("disabled",true);
            document.getElementById("login-button-icon").className = 'fa fa-spinner-third fa-spin fa-1-6x';
            $("#login-button-text").text('');
            this.show_loading = true;
        },
        to_persian_date(date){
            return new persianDate(new Date(date)).format("HH:mm:ss YYYY/MM/DD")
        },
        filter_table(e){
            let filter = e.target.value;
            let table = document.getElementById(e.target.dataset.table), columns, tr, td, i, j, txtValue;
            columns = JSON.parse(table.dataset.filter);
            tr = table.getElementsByTagName("tr");
            for (i = 1; i < tr.length; i++) {
                let strings = [];
                for (j = 0; j < columns.length; j++) {
                    td = tr[i].getElementsByTagName("td")[parseInt(columns[j])];
                    if (td) {
                        txtValue = td.innerHTML !== '' ? td.querySelector('input') !== null ? td.querySelector('input').value : td.textContent || td.innerText : null;
                        if (txtValue)
                            strings.push(txtValue);
                    }
                }
                if (strings.length) {
                    const match = strings.find(element => {
                        const clearElement = element.replace(/[()\-_!@#$%^.,]/g, '');
                        return !!clearElement.includes(filter);
                    });
                    if (match)
                        tr[i].style.display = "";
                    else
                        tr[i].style.display = "none";
                }
            }
        },
        jump_input(e,next,max,id){
            const button = $(".submit-button");
            if (e.currentTarget.value.length > 0) {
                if (e.currentTarget.value.length === 1) {
                    if (next < max)
                        $(`#${id}${String(next + 1)}`).focus();
                    else if (next === max)
                        $(`#${id}${next}`).blur();
                }
            }
            button.prop("disabled",$("input[type='text'],input[type='number'],select").val() === "");
        },
        dragElement(e,parent) {
            const parentNode = $(`${parent}`);
            let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
            const element = e.currentTarget;
            const width = window.innerWidth, height = parentNode.height();
            const elementWidth = element.offsetWidth, elementHeight = element.offsetHeight;
            element.onmousedown = dragMouseDown;
            function dragMouseDown(e) {
                e.preventDefault();
                pos3 = e.clientX;
                pos4 = e.clientY;
                document.onmouseup = closeDragElement;
                document.onmousemove = elementDrag;
            }
            function elementDrag(e) {
                e.preventDefault();
                pos1 = pos3 - e.clientX;
                pos2 = pos4 - e.clientY;
                pos3 = e.clientX;
                pos4 = e.clientY;
                let left = (element.offsetLeft - pos1) <= 11 ? 11 : (element.offsetLeft - pos1) >= (width - elementWidth - 11) ? (width - elementWidth - 11) : (element.offsetLeft - pos1);
                let top = (element.offsetTop - pos2) < 11 ? 11 : (element.offsetTop - pos2 + elementHeight) >= height + 11 ? (height - elementHeight + 11) : (element.offsetTop - pos2);
                element.style.left = left + "px";
                element.style.top = top + "px";
            }
            function closeDragElement() {
                document.onmouseup = null;
                document.onmousemove = null;
            }
        },
        toggle_sidebar(){
            if (window.innerWidth <= 900)
               $(".sidebar").toggleClass("open");
        },
        desktop_toggle_sidebar(opt){
            if (window.innerWidth > 900){
                switch (opt){
                    case "minimize":{
                        this.is_static_sidebar = 0;
                        $(".information-box,.content-header,.content-footer,.menu-header-icon.small-sidebar-icon,.sidebar-menu .nav-item,.company_name,.account-name,.small-sidebar-button,.sidebar,.dashboard-logo").addClass("small-sidebar");
                        $(".dashboard-logo").css("display","inline");
                        break;
                    }
                    case "maximize-hover":{
                        $(".information-box,.content-header,.content-footer,.menu-header-icon.small-sidebar-icon,.sidebar-menu .nav-item,.company_name,.account-name,.small-sidebar-button,.sidebar,.dashboard-logo").removeClass("small-sidebar");
                        $(".dashboard-logo").css("display","none");
                        break;
                    }
                    case "minimize-static":{
                        if (!this.is_static_sidebar) {
                            $(".information-box,.content-header,.content-footer,.menu-header-icon.small-sidebar-icon,.sidebar-menu .nav-item,.company_name,.account-name,.small-sidebar-button,.sidebar,.dashboard-logo").addClass("small-sidebar");
                            $(".dashboard-logo").css("display","inline");
                        }

                        break;
                    }
                    case "maximize-static":{
                        this.is_static_sidebar = 1;
                        $(".information-box,.content-header,.content-footer,.menu-header-icon.small-sidebar-icon,.sidebar-menu .nav-item,.company_name,.account-name,.small-sidebar-button,.sidebar,.dashboard-logo").removeClass("small-sidebar");
                        $(".dashboard-logo").css("display","inline");
                        break;
                    }
                }
                localStorage.setItem("is_static_sidebar",this.is_static_sidebar.toString());
            }
        },
        view_employee_information(e,id){
            const self = this;
            const selected_id = typeof id !== "undefined" ? parseInt(id) : self.employees.length ? self.employees[0].id : self.employee_id ?? null;
            this.image_rotation = 0;
            if (selected_id !== null) {
                this.table_data_records.find((item, index) => {
                    if (item.id === selected_id) {
                        self.docs = item.docs;
                        self.employee = {
                            "id": item.id,
                            "name": item.name,
                            "national_code": item.national_code
                        }
                        if (typeof id !== "undefined") {
                            self.employees = [{
                                "id": item.id,
                                "name": item.name,
                                "national_code": item.national_code
                            }];
                        }
                        self.employee_db_information = typeof item.database !== "undefined" ? item.database : item;
                    }
                });
            }
        },
        view_information_queue(direction){
            const self = this;
            let employee_id = null;
            let next_index = this.employees.findIndex((item) => {
                return item.id === self.employee.id;
            });
            if (next_index >= 0){
                switch (direction){
                    case "previous":{
                        if(next_index > 0) {
                            employee_id = self.employees[--next_index]?.id;
                            self.EmployeeIndex = next_index;
                        }
                        break;
                    }
                    case "next":{
                        if(next_index < self.employees.length - 1) {
                            employee_id = self.employees[++next_index]?.id;
                            self.EmployeeIndex = next_index;
                        }
                        break;
                    }
                }
                if (employee_id) {
                    this.doc_show = 1;
                    this.table_data_records.find((item) => {
                        if (item.id === employee_id) {
                            self.docs = item.docs;
                            self.employee = {
                                "id": item.id,
                                "name": item.name,
                                "national_code": item.national_code
                            }
                            self.employee_db_information = typeof item.database !== "undefined" ? item.database : item;
                        }
                    });
                }
            }
        },
        select_doc_preview(e){
            this.doc_show = parseInt(e.target.dataset.image_index);
            $(".doc-image-overlay").each(function (){
                $(this).hasClass("selected") ? $(this).removeClass("selected") : '';
            });
            $(`#doc_img_overlay_${this.doc_show}`).addClass("selected");
        },
        image_operations(operation){
            const self = this;
            const img = $(`#doc_img_preview_${self.doc_show}`);
            const height = parseInt(img.prop("naturalHeight"));
            switch (operation){
                case "zoom_in":{
                    if(parseInt(img.css("height")) <= height + (height * 1.25))
                        img.css("height",`${parseInt(img.css("height")) + 50}px`);
                    break;
                }
                case "zoom_out":{
                    if(parseInt(img.css("height")) >= (height / 3))
                        img.css("height",`${parseInt(img.css("height")) - 50}px`);
                    break;
                }
                case "print":{
                    let href = self.docs[self.doc_show - 1]["print"];
                    $("#doc_print").attr("src",href);
                    $("#print_modal_cancel").attr("data-bs-target","#docs_modal");
                    $("#print_modal").show();
                    break;
                }
                case "rotate":{
                    if(self.image_rotation === 270) {
                        self.image_rotation = 0;
                        img.css("transform",`rotate(0deg)`);
                    }
                    else
                        img.css("transform",`rotate(${self.image_rotation + 90}deg)`);
                    self.image_rotation += 90;
                    break;
                }
            }
        },
        zoomOperation(e){
            e.preventDefault();
            const self = this;
            const img = $(`#doc_img_preview_${self.doc_show}`);
            const height = parseInt(img.prop("naturalHeight"));
            if (e.deltaY < 0) {
                if (parseInt(img.css("height")) <= height + (height * 2))
                    img.css("height", `${parseInt(img.css("height")) + 50}px`);
            }
            else if (event.deltaY > 0){
                if(parseInt(img.css("height")) >= (height / 3))
                    img.css("height",`${parseInt(img.css("height")) - 50}px`);
            }
        },
        docs_direction(direction){
                const self = this;
                let current_index = this.employees.findIndex((item) => {
                    return item.id === self.employee.id;
                });
                let disability = null;
                switch (direction){
                    case "previous":{
                        current_index > 0 ? disability = false : disability = true;
                        break;
                    }
                    case "next":{
                        current_index < self.employees.length - 1 ? disability = false : disability = true;
                        break;
                    }
                }
                return disability;
        },
        page_setup(){
            this.show_loading = true;
            let config = {"page":this.print_page_size,"orientation":this.print_page_orientation};
            $("#doc_print").attr("src",this.print_route + `/${JSON.stringify(config)}`);
        },
        place_sms_text(e){
            const self = this;
            this.sms_collection.forEach((category) => {
                return category.phrases.some((phrase) => {
                    if (phrase.id === parseInt(self.select_model))
                        $(`#${e.target.dataset.place}`).val(phrase.text);
                    return true;
                });
            });
        },
        individual_operation(e,modal){
            const id = parseInt(this.employee_id);
            const self = this;
            if (id !== null){
                this.table_data_records.find((item) => {
                    if (item.id === id) {
                        self.employees = item;
                    }
                });
                $(`#employee_${id}`).prop("checked",true);
            }
            if (modal) {
                this.employee_operation_type = modal;
                this.employee_operation_kind = "individual";
            }
        },
        select_employee(e){
            const id = parseInt(e.target.value);
            const self = this;
            let duplicate = this.employees.some((item) => {
                return item.id === id;
            });
            if (!duplicate){
                this.selected_employees.find((item) => {
                    if (item.id === id) {
                        self.employees.push({
                            "id": item.id,
                            "name": item.name,
                            "national_code": item.national_code
                        });
                    }
                });
            }
            $(e.target).selectpicker('val','').selectpicker('render');
        },
        GetGroupEmployees(e){
            const self = this;
            const id = e.target.value;
            self.table_data_records = [];
            self.$nextTick(function (){
                $("#group_reference").selectpicker('val','').selectpicker('render');
            });
            self.show_loading = true;
            axios.post(route("EmployeesManagement.get_employees"), {"reference_type":"group","reference_id":id}).then(function (response) {
                self.show_loading = false;
                if (response.data){
                    switch (response.data.result){
                        case "success":{
                            self.table_data_records = response.data.employees;
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
        add_subset(){
            let child_name = $("#child_name");
            const self = this;
            if (child_name.val()){
                self.children_subset_list.push({
                    "name" : child_name.val(),
                    "employees" : self.employees
                });
                child_name.val('');
                $("#excel_file, #excel_browser_box").val('');
                self.employees = [];
                self.import_errors = [];
                alertify.success("زیرمجموعه با موفقیت ایجاد شد");
            }
            else
                child_name.toggleClass("is-invalid");
        },
        display_pre_employees(e,index,id){
            this.table_data_records = this.old_children_subset_list[index]?.pre_employees;
            this.contract_id = id;
        },
        remove_all_invalids(){
            const elements = $("*");
            elements.removeClass("is-invalid");
            elements.closest("div").find(".vue-treeselect__control").css("border","1px solid #ddd");
        },
        delete_employees_type(e){
            e.target.checked === true ? this.operation_employee_type = 'individual' : this.operation_employee_type = '';
        },
        reset_employee_operation_fields(){
            $(`
                #n_employee_firstname,
                #n_employee_lastname,
                #n_employee_national_code,
                #n_employee_mobile,
                #n_employee_start_date,
                #n_employee_end_date,
                #d_selected_employees
                `).val('');
            this.employees = [];
            this.tree_select_contract_id = '';
            this.operation_select_model = null;
        },
        AdvancedEmployeeSearch(e){
            const id = parseInt(e.target.value);
            if (id){
                this.EmployeesFound.find((employee) => {
                    if (employee.id === id)
                        this.table_data_records = [employee];
                });
                this.EmployeesFound = [];
                this.$nextTick(() => {
                    $(".find-employees-select").selectpicker('destroy').selectpicker('render');
                });
            }
        },
        ExcelColumnsCreation(){
            const self = this;
            const count = parseInt(this.last_excel_column) > 702 ? 702 : this.last_excel_column;
            for (let i = 0 ; i < count ; i++)
                self.excel_columns.push({
                        "column": `${self.MainExcelColumns[i]}`,
                        "ignore": false,
                        "title": "",
                        "type": "information",
                        "isNumber": true
                    });
        },
        ExcelColumnsNumber(operation){
            const self = this;
            const amount = parseInt(self.add_remove_excel_column);
            const count = self.excel_columns.length + amount > 702 ? 702 : self.excel_columns.length + amount;
            switch (operation){
                case "increase":{
                    for (let i = self.excel_columns.length ; i < count ; i++)
                        self.excel_columns.push({
                            "column": `${self.MainExcelColumns[i]}`,
                            "ignore": false,
                            "title": "",
                            "type": "information",
                            "isNumber": true
                        });
                    break;
                }
                case "decrease":{
                    self.excel_columns.splice((self.excel_columns.length - amount),amount);
                    break;
                }
            }
            self.$forceUpdate();
        },
        GetRoute(name,parameters){
            return route(name,parameters);
        },
        UploadPaySlipFile(){
            const self = this;
            const excel_file = document.getElementById("upload_file");
            if (excel_file.value !== null && this.contract_id !== ''){
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
                            self.$root.$data.show_loading = true;
                            let data = new FormData();
                            data.append("excel_file", excel_file.files[0]);
                            data.append("contract_id", self.contract_id);
                            axios.post(route("EmployeePaySlips.excel_upload"), data)
                                .then(function (response) {
                                    excel_file.value = '';
                                    self.file_selected = false;
                                    self.$root.$data.show_loading = false;
                                    if (typeof response.data !== "undefined") {
                                        self.payslip_employees = response.data?.data?.results ? response.data.data.results : [];
                                        self.payslip_template = response.data?.data?.template ? JSON.parse(response.data.data.template.columns) : [];
                                        self.national_code_index = response.data?.data?.template?.national_code_index >= 0 ? response.data.data.template.national_code_index : null;
                                        self.import_errors = response.data?.import_errors ? response.data.import_errors : [];
                                        const modalElement = document.getElementById("payslip_table_modal");
                                        modalElement.addEventListener('shown.bs.modal', () => {
                                            self.$nextTick(()=>{
                                                new AutoNumeric.multiple('.attr_sep',['integer',{'digitGroupSeparator':',','watchExternalChanges':true}]);
                                            });
                                        })
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
                })
            }
        },
        UploadEmployeeAdvantages(){
            const self = this;
            const excel_file = document.getElementById("upload_file");
            if (excel_file.value !== null && this.contract_id !== ''){
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
                            data.append("excel_file", excel_file.files[0]);
                            data.append("contract_id", self.contract_id);
                            axios.post(route("EmployeeFinancialAdvantages.excel_upload"), data)
                                .then(function (response) {
                                    excel_file.value = '';
                                    self.file_selected = false;
                                    self.$root.$data.show_loading = false;
                                    if (typeof response.data !== "undefined") {
                                        self.employee_advantages = response.data?.data?.results ? response.data.data.results : [];
                                        self.advantage_columns = response.data?.data?.advantage_columns ? response.data.data.advantage_columns : [];
                                        self.import_errors = response.data?.import_errors ? response.data.import_errors : [];
                                        const modalElement = document.getElementById("employee_advantages_table_modal");
                                        modalElement.addEventListener('shown.bs.modal', () => {
                                            self.$nextTick(()=>{
                                                new AutoNumeric.multiple('.attr_sep',['integer',{'digitGroupSeparator':',','watchExternalChanges':true}]);
                                            });
                                        })
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
                })
            }
        },
        ShowPdf(route,viewer){
            PDFObject.embed(route,viewer);
        },
        SoloContractSelected(id){
            const self = this;
            self.employee = [];
            self.advantage_columns = [];
            self.show_loading = true;
            axios.post(route("EmployeeFinancialAdvantages.get_employees"), {"contract_id":id}).then(function (response) {
                self.show_loading = false;
                if (response.data){
                    switch (response.data.result){
                        case "success":{
                            self.employees = response.data?.employees;
                            self.$nextTick(() => {
                                $("#employees").selectpicker('refresh');
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
        SelectEmployee(e){
            this.employee = this.employees.find(employee => {
                return employee.id === parseInt(e.target.value);
            });
            this.$forceUpdate();
            this.$nextTick(() => {
                $(".separator").each(function() {
                    AutoNumeric.getAutoNumericElement(document.querySelector(`#${$(this).attr("id")}`)) === null ? new AutoNumeric(`#${$(this).attr("id")}`,['integer',{'digitGroupSeparator':',','watchExternalChanges':true}]) : '';
                });
            });
        },
        AddAdvantage(){
            const title = $("#advantage_title");const value = $("#advantage_value");
            if (title.val() !== "" && value.val() !== "") {
                this.advantage_columns.push({"title": title.val(), "value": value.val()});
                title.val('');value.val('');
            }
        },
        ChildAllowanceCalculate(e) {
            const value = parseInt(e.target.value.replaceAll(",", ""));
            if (value === null)
                e.target.value = 0;
            else
                $("input[name='child_allowance']").val((value * 30) / 10);
        },
        AddRoleItem(e){
            const roles = $(e.target);
            let duplicate,slug;
            if (roles.val()){
                do {
                    slug = Math.floor(Math.random() * 100) + 10;
                    duplicate = this.roles_list.find((item) => {
                        return item.slug === slug
                    });
                }
                while (typeof duplicate !== "undefined")
                const priority = this.roles_list.length > 0 ? this.roles_list.length + 1 : 1
                this.roles_list.push({"name":roles.find('option:selected').text(),"id":roles.val(),"slug":slug,"same":0,"priority":priority,"main_role":false});
            }
        },
        ModifyRole(e){
            if (this.roles_list.length > 0) {
                const index = this.roles_list.findIndex( item => {
                    return item.slug === Number(e.currentTarget.dataset.slug);
                });
                const max_index = this.roles_list.length - 1;
                if (index >= 0) {
                    switch (e.currentTarget.dataset.function) {
                        case "up": {
                            if (index > 0)
                                this.roles_list.splice(index - 1,2,this.roles_list[index],this.roles_list[index - 1]);
                            break;
                        }
                        case "down": {
                            if (index < max_index) {
                                this.roles_list.splice(index, 2,this.roles_list[index + 1],this.roles_list[index]);
                            }
                            break;
                        }
                        case "remove": {
                            this.roles_list.splice(index, 1);
                            break;
                        }
                    }
                    this.roles_list.forEach((role,index) => {
                        role.priority = index + 1;
                    })
                }
            }
        },
        MakeRoleBalance(e,priority){
            const same_priority = parseInt(e.target.value) ? parseInt(e.target.value) : 0;
            const current = this.roles_list.find( item => {
                return item.priority === priority;
            });
            current.same = same_priority;
            this.$forceUpdate();
        },
        BatchEmployeeAppointmentLetter(){
            const self = this;
            if (this.reference && this.data) {
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
                            self.show_loading = true;
                            bootbox.hideAll();
                            let data = new FormData();
                            if (self.reference === "organization" && Array.isArray(self.data))
                                data.append("reference", "selection");
                            else
                                data.append("reference", self.reference);
                            switch (self.reference) {
                                case "organization":
                                    Array.isArray(self.data) ? data.append("selected_employees", JSON.stringify(self.data)) : data.append("contract_id", self.data);
                                    break;
                                case "group":
                                    data.append("group_id", self.data);
                                    break;
                                case "custom":
                                    data.append("employees", JSON.stringify(self.data));
                                    break;
                            }
                            axios.post(route("EmployeeAppointmentLetter.batch_print"), data)
                                .then(function (response) {
                                    self.show_loading = false;
                                    if (response?.data) {
                                        if (response.data?.data?.view) {
                                            self.ShowPdf(response.data.data.view, "#pdf_viewer");
                                            const modal = new bootstrap.Modal(document.getElementById("pdf_viewer_modal"), {});
                                            modal.show();
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
                                self.show_loading = false;
                                alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                            });
                        }
                    }
                });
            }
        },
        GetRequestDetails(id){
            const self = this;
            self.show_loading = true;
            axios.post(route("EmployeeRequestsAutomation.seen"), {"id" : id})
                .then(function (response) {
                    self.show_loading = false;
                    if (response?.data) {
                        self.employee_requests = response.data?.automations;
                        self.request = self.employee_requests.find( request => {return request.id === id});
                        self.data_recipient = self.request.automationable?.recipient;
                        self.data_borrower = self.request.automationable?.borrower;
                        self.data_loan_amount = self.request.automationable?.loan_amount ?? 0;
                        self.$nextTick(() => {
                            self.docs = self.request.employee.docs;
                            $(".request_history").fancyTable({
                                sortColumn:0,
                                pagination: false,
                                perPage:0,
                                globalSearch:false,
                                searchable: false,
                            });
                            $(".contract_date").pDatepicker({
                                initialValue: false,
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
                            });
                            self.$forceUpdate();
                            self.ShowPdf(route("EmployeeRequestsAutomation.preview",[self.request.id]), "#pdf_viewer");
                        });
                        switch (response.data["result"]) {
                            case "fail": {
                                alertify.notify(response.data["message"], 'error', "30");
                                break;
                            }
                        }
                    }
                }).catch(function (error) {
                self.show_loading = false;
                alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
            });
        },
        GetSeperated(number){
            return `${numeral(number).format('0,0')} ریال`;
        },
        PersianDateString(date,time = null){
            if (time)
                return new persianDate(new Date(date)).toLocale("en").format("H:m:s YYYY/MM/DD");
            return new persianDate(new Date(date)).toLocale("en").format("YYYY/MM/DD");
        },
        GetTotalLoan(id){
            const self = this;
            const loans = self.request?.employee?.automations.filter((loan_request) => {
                if (loan_request.employee_id === parseInt(id))
                    return loan_request.automationable?.is_accepted === 1 && loan_request.automationable?.inactive === 0 ? loan_request?.automationable?.loan_amount > 0 : 0
            });
            let total = 0;
            if (loans) {
                loans.forEach(loan => {
                    total += loan.automationable?.loan_amount;
                });
            }
            return self.GetSeperated(total);
        },
        RefreshRequestData(){
            const self = this;
            bootbox.confirm({
                message: "آیا برای بروزرسانی اطلاعات درخواست اطمینان دارید؟",
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
                        self.show_loading = true;
                        bootbox.hideAll();
                        axios.post(route("EmployeeRequestsAutomation.refresh_data"), {"id" : self.request.id})
                            .then(function (response) {
                                self.show_loading = false;
                                if (response.data) {
                                    switch (response.data["result"]) {
                                        case "success": {
                                            self.employee_requests = response.data?.automations;
                                            self.request = self.employee_requests.find( request => {return request.id === self.request.id});
                                            alertify.notify(response.data["message"], 'success', "5");
                                            break;
                                        }
                                        case "fail": {
                                            alertify.notify(response.data["message"], 'error', "30");
                                            break;
                                        }
                                    }
                                }
                            }).catch(function (error) {
                            self.show_loading = false;
                            alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");;
                        });
                    }
                }
            });
        },
        ResetRequestModal(){
            const self = this;
            $("#request_details-tab").click();
            self.request = [];
        },
        ConfirmRequest(){
            const self = this;
            bootbox.confirm({
                message: "آیا برای تایید درخواست اطمینان دارید؟",
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
                        self.show_loading = true;
                        bootbox.hideAll();
                        axios.post(route("EmployeeRequestsAutomation.confirm"), {
                            "id" : self.request.id,
                            "recipient" : self.data_recipient,
                            "borrower" : self.data_borrower,
                            "loan_amount" : self.data_loan_amount !== 0 ? self.data_loan_amount.toString().replaceAll(",","") : null,
                            "comment" : self.request_comment
                        }).then(function (response) {
                                self.show_loading = false;
                                if (response?.data) {
                                    self.employee_requests = response.data?.automations;
                                    $("#close_modal").click();
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
                            self.show_loading = false;
                            alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                        });
                    }
                }
            });
        },
        RejectRequest(){
            const self = this;
            if (self.request.current_priority === 1 && self.request_reject_reason === "") {
                bootbox.alert("لطفا دلیل عدم تایید درخواست را در قسمت مشخص شده شرح دهید");
                $("#request_reject_reason").removeClass("is-invalid is-invalid-fake").addClass("is-invalid is-invalid-fake");
            }
            else {
                bootbox.confirm({
                    message: self.request.current_priority === 1 ? "آیا برای عدم تایید درخواست و ارجاع به پرسنل اطمینان دارید؟" : "آیا برای عدم تایید درخواست و ارجاع به سمت قبلی اطمینان دارید؟",
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
                            self.show_loading = true;
                            bootbox.hideAll();
                            axios.post(route("EmployeeRequestsAutomation.reject"), {
                                "id": self.request.id,
                                "comment": self.request_comment
                            }).then(function (response) {
                                    self.show_loading = false;
                                    if (response?.data) {
                                        self.employee_requests = response.data?.automations;
                                        $("#close_modal").click();
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
                                self.show_loading = false;
                                alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                            });
                        }
                    }
                });
            }
        },
        EditUserData(){
            const self = this;
            let flag = 0;
            const parent = $(".employee_information");
            parent.children().each(function () {
                const element = $(this).find("input");
                if(element.val() === "") {
                    element.removeClass("is-invalid").addClass("is-invalid");
                    flag = 1;
                }
            });
            if (flag === 0){
                bootbox.confirm({
                    message: "آیا برای ویرایش اطلاعات پرسنل اطمینان دارید؟",
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
                            self.show_loading = true;
                            bootbox.hideAll();
                            axios.post(route("EmployeeRequestsAutomation.edit_employee_information"), {
                                "automation_id": self.request.id,
                                "id": self.request.employee.id,
                                "first_name": self.first_name,
                                "last_name": self.last_name,
                                "id_number": self.id_number,
                                "father_name": self.father_name,
                                "gender": self.gender,
                                "mobile": self.mobile,
                                "job_seating": self.job_seating,
                                "job_title": self.job_title,
                                "start": $(self.$refs.contract_start_date).val(),
                                "end": $(self.$refs.contract_end_date).val()
                            }).then(function (response) {
                                    self.show_loading = false;
                                    if (response?.data) {
                                        self.employee_requests = response.data?.automations;
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
                                self.show_loading = false;
                                alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                            });
                        }
                    }
                });
            }
        },
        WindowRelocate(route){
            window.location.href = route;
        },
        RegisterEmployee(){
            const self = this;
            if (self.organization === "" || self.name === "" || self.national_code === "" || self.mobile === "")
                bootbox.alert("لطفا اطلاعات را به طور کامل وارد نمایید");
            else {
                bootbox.confirm({
                    message: "آیا برای ارسال اطلاعات اطمینان دارید؟",
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
                            self.show_loading = true;
                            bootbox.hideAll();
                            let form_data = new FormData();
                            form_data.append("name",self.name);
                            form_data.append("national_code",self.national_code);
                            form_data.append("organization",self.organization);
                            form_data.append("mobile",self.mobile);
                            form_data.append("description",self.description);
                            axios.post(route("unregistered_employees"), form_data).then(function (response) {
                                self.show_loading = false;
                                bootbox.alert(response?.data?.message);
                            }).catch(function (error) {
                                self.show_loading = false;
                                alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                            });
                        }
                    }
                });
            }
        },
        EmployeeManagementContractSelected(id){
            if (typeof id === "undefined" || id === null)
                id = this.old_contract_id;
            const self = this;
            self.table_data_records = [];
            self.$nextTick(function (){
                $("#group_reference").selectpicker('val','').selectpicker('render');
            });
            self.show_loading = true;
            axios.post(route("EmployeesManagement.get_employees"), {"reference_type":"contract","reference_id":id}).then(function (response) {
                self.show_loading = false;
                if (response.data){
                    switch (response.data.result){
                        case "success":{
                            self.table_data_records = response.data.employees;
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
        RegistrationContextMenu(e,id){
            this.employee_id = parseInt(id);
            const self = this;
            $(".employees_select").selectpicker("destroy");
            self.employees = [];
            self.employees.push(self.table_data_records.find(employee => { return employee.id === parseInt(id)}));
        },
        RegistrationContractSelected(id){
            const self = this;
            const select = $(".employees_select");
            self.employees = self.table_data_records.filter(employee => {return employee.contract_id === parseInt(id)});
            self.$nextTick(()=>{
                if(self.employees.length > 1) {
                    select.prop("hidden",false);
                    select.selectpicker('destroy').selectpicker('refresh');
                }
                else {
                    select.prop("hidden",true);
                    select.selectpicker('destroy');
                }
            });
        },
        EditMessage(e,id){
            $(`#message_${id}`).val(e.target.innerText)
        },
        SetUnregisteredEmployee(e,id,form){
            switch (form){
                case "confirm_form":{
                    $(`#${form}`).attr("action",route("UnregisteredEmployees.confirm",[id]));
                    break;
                }
                case "reject_form":{
                    $(`#${form}`).attr("action",route("UnregisteredEmployees.refuse",[id]))
                    break;
                }
            }
        },
        UnRegContractSelected(id){
            $("#contract_id").val(id);
        },
        RefreshDataContextMenu(e,id){
            this.employee_id = parseInt(id);
            const self = this;
            self.employees = [];
            const data = self.table_data_records.find(entry => { return entry.id === parseInt(id)});
            if (data) {
                self.employees = data.employee;
                self.reference = data;
                $("#confirm_form").attr("action", route("RefreshDataEmployees.confirm", [data.id]));
                $("#refuse_form").attr("action", route("RefreshDataEmployees.refuse", [data.id]));
            }
        },
        insert_employee_information(e,id){
            const self = this;
            if (self.employees) {
                self.docs = self.employees.docs;
                self.employee = {
                    "id": self.employees.id,
                    "name": self.employees.name,
                    "national_code": self.employees.national_code
                }
                self.employee_db_information = self.employees;
            }
        },
        OpenUserTicket(e,ticket_id){
            if (ticket_id){
                const self = this;
                self.UserTicketDetails = self.UserTickets.find(ticket => {return ticket.id === parseInt(ticket_id)});
            }
        },
        SendUserTicket(){
            const self = this;
            if (self.UserMessage){
                self.show_loading = true;
                let form_data = new FormData();
                form_data.append("room_id",self.UserTicketDetails.id);
                form_data.append("message",self.UserMessage);
                if ($("#upload_file").val())
                    form_data.append("attachment",document.getElementById("upload_file").files[0])
                axios.post(route("UserTickets.send"), form_data).then(function (response) {
                    self.show_loading = false;
                    self.UserTickets = response.data?.tickets;
                    self.UserTicketDetails = self.UserTickets.find(ticket => {return ticket.id === self.UserTicketDetails.id});
                }).catch(function (error) {
                    self.show_loading = false;
                    alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                });
            }
        },

        OpenUserPayslip(e,payslip_id){
            const self = this;
            self.UserPayslip = self.UserPayslips.find(payslip => {return payslip.id === parseInt(payslip_id)});
            if (payslip_id) {
                axios.post(route("UserPaySlips.published"), {"payslip_id": payslip_id}).then(function (response) {
                    self.show_loading = false;
                    self.UserPayslipDetails = response.data?.published;
                }).catch(function (error) {
                    self.show_loading = false;
                    alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                });
            }
        },
        BackupInformation(){
            const self = this;
            if (self.name && self.BackupType) {
                self.show_loading = true;
                axios.post(route("Backup.backup"), {"name": self.name, "backup_type": self.BackupType}).then(function (response) {
                    self.show_loading = false;
                    if (response?.data){
                        switch (response.data.result){
                            case "success":{
                                alertify.success(response.data.message);
                                self.Backups = response.data?.backups;
                                break;
                            }
                            case "failed":{
                                alertify.warning(response.data.message);
                                break;
                            }
                        }
                    }
                }).catch(function (error) {
                    self.show_loading = false;
                    alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                });
            }
        },
        deleteImage(doc){
            const self = this;
            bootbox.confirm({
                message: "آیا برای حذف این تصویر اطمینان دارید؟",
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
                        axios.post(route("docs.image_delete"), {"path": doc.path, "id": self.employee_id}).then(function (response) {
                            self.show_loading = false;
                            if (response?.data) {
                                switch (response.data.result) {
                                    case "success": {
                                        let index = self.table_data_records.findIndex(employee => {return employee.id === self.employee_id})
                                        self.table_data_records[index] = response.data.employee;
                                        self.docs = response.data.employee?.docs;
                                        alertify.success(response.data.message);
                                        break;
                                    }
                                    case "failed": {
                                        alertify.warning(response.data.message);
                                        break;
                                    }
                                }
                            }
                        }).catch(function (error) {
                            self.show_loading = false;
                            alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                        });
                    }
                }
            });
        },
        openTickets(event,subjectId){
            const self = this;
            const allSubjects = document.querySelectorAll(".subjects");
            allSubjects.forEach(element => element.classList.remove("active"));
            event.currentTarget.classList.add("active");
            self.subject = self.subjects.find(subject => {
                return subject.id === parseInt(subjectId);
            });
        },
        ticketSearch(e){
            const self = this;
            const filter = e.target.value;
            self.subject = [];
            const filtered = self.subjects.filter(subject => {
                return subject?.user?.name.indexOf(filter) > -1 ||
                    subject?.user?.employee?.national_code.indexOf(filter) === 0 ||
                    subject?.subject.indexOf(filter) > -1;

            });
            let ids = [];
            filtered.forEach(item => {
                ids.push(`ticket_${item.id}`);
            });
            const parent = document.querySelector(".ticket-box-information");
            Array.from(parent.children).forEach(child => {
                const node = document.getElementById(child.id);
                if (child.id !== "searchBox") {
                    const exist = ids.findIndex(id => {
                        return id === child.id;
                    });
                    if (exist === -1)
                        node.classList.add("d-none");
                    else
                        node.classList.remove("d-none");
                }
            });
        }
    }
});
