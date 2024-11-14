@extends("staff.staff_dashboard")
@section('variables')
    <script>
        const allowed_organizations = @json($organizations);
        const allowed_groups = @json($custom_groups);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i v-if="sidebar_toggle" class="sidebar-toggle fa fa-bars fa-1-6x me-4" v-cloak
               v-on:click="toggle_sidebar('open')"></i>
            <h5 class="iransans d-inline-block m-0 d-flex align-items-center">
                احکام پرسنل
                <span class="vertical-middle ms-2">(مشاهده و چاپ)</span>
            </h5>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content w-100 p-3 pt-4">
        <reference-box @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked" @employee_selected="EmployeeSelected" :refs_needed="[1,2,3,5]"></reference-box>
        <div v-cloak class="row">
            <div class="col-12 text-center">
                <button v-if="reference !== null && data !== null" class="btn btn-dark mt-3" v-on:click="BatchEmployeeAppointmentLetter">
                    <i class="fa fa-print-magnifying-glass fa-1-2x me-2"></i>
                    <span class="iransans">مشاهده و چاپ</span>
                </button>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="import_errors" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="import_errors" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">خطا(های) بارگذاری فایل اکسل</h5>
                </div>
                <div class="modal-body" style="max-height: 70vh;overflow-y: auto">
                    <table class="table table-striped">
                        <thead class="bg-menu-dark white-color">
                        <tr class="iransans">
                            <th scope="col"><span>ردیف فایل</span></th>
                            <th scope="col"><span>کد ملی</span></th>
                            <th scope="col"><span>پیغام خطا</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="iransans" v-for="error in import_errors">
                            <td>@{{ error.row }}</td>
                            <td>@{{ error.national_code }}</td>
                            <td>@{{ error.message }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-toggle="modal" :data-bs-target="return_modal" v-on:click="return_modal=''">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="employees" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans" id="exampleModalLongTitle">پرسنل بارگذاری شده</h5>
                </div>
                <div class="modal-body scroll-style">
                    <table class="table table-bordered text-center w-100 iransans">
                        <thead class="bg-dark white-color">
                        <tr>
                            <th v-for="(header,index) in uploaded_employees.headers" :key="index" v-text="header"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(employee,index) in uploaded_employees.employees" :key="index">
                            <td v-for="(detail,index) in employee" :key="index" v-text="detail"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button v-if="return_modal" type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" data-bs-toggle="modal" :data-bs-target="return_modal" v-on:click="return_modal=''">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                    <button v-else type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="pdf_viewer_modal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">مشاهده احکام پرسنل</h5>
                </div>
                <div class="modal-body">
                    <div id="pdf_viewer" style="width: 100%;height: 100%">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بستن</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
