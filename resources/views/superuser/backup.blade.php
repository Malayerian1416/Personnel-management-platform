@extends('superuser.superuser_dashboard')
@section('variables')
    <script>
        const backup_data = @json($backups);
        const is_backup = true;
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                ایجاد پشتیبان اطلاعات
            </h5>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-light">
                <i class="fa fa-circle-question fa-1-4x green-color"></i>
            </button>
            <a role="button" class="btn btn-sm btn-outline-light" href={{route("staff_idle")}}>
                <i class="fa fa-times fa-1-4x gray-color"></i>
            </a>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content w-100 p-3">
        <div class="input-group mb-2">
            <button class="btn btn-primary d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_organization_modal">
                <i class="fa fa-plus fa-1-6x"></i>
            </button>
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام" data-table="organizations_table" v-on:input="filter_table">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table id="organizations_table" class="table table-hover table-striped pointer-cursor sortArrowWhite" data-filter="[1]">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col" data-sortas="numeric"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col" style="width: 150px"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody v-cloak v-if="Backups.length > 0">
                    <tr v-for="backup in Backups" :key="backup.id">
                        <td class="iransans">@{{ backup.id }}</td>
                        <td><span class="iransans">@{{ backup.name }}</span></td>
                        <td>
                            <i v-if="backup.status === 'f'" class="fa fa-times red-color fa-1-2x vertical-middle" data-bs-toggle="popover" :data-bs-title="backup.exception"></i>
                            <i v-else-if="backup.status === 'r'" class="fad fa-bars-progress fa-fade fa-1-2x vertical-middle"></i>
                            <i v-else-if="backup.status === 's'" class="fa fa-check green-color fa-1-2x vertical-middle"></i>
                        </td>
                        <td><span class="iransans">@{{ backup.user.name }}</span></td>
                        <td><span class="iransans" v-text="to_persian_date(backup.created_at)"></span></td>
                        <td><span class="iransans" v-text="to_persian_date(backup.updated_at)"></span></td>
                        <td>
                            <div v-if="backup.status === 's' || backup.status === 'f'" class="d-flex flex-row flex-wrap align-items-center justify-content-center gap-2 gap-lg-3">
                                <a v-if="backup.status === 's'" download role="button" class="btn btn-sm btn-outline-dark" :href="GetRoute('Backup.download',[backup.download])">
                                    <i class="far fa-download fa-1-2x vertical-middle"></i>
                                </a>
                                <div>
                                    <form hidden :id="`delete-form-${backup.id}`" :action="GetRoute('Backup.destroy',[backup.id])" method="POST" v-on:submit="submit_form">
                                        @csrf
                                        @method("Delete")
                                    </form>
                                    <button :form="`delete-form-${backup.id}`" class="btn btn-sm btn-outline-dark">
                                        <i class="far fa-trash fa-1-2x vertical-middle"></i>
                                    </button>
                                </div>
                            </div>
                            <div v-else class="text-center">
                                <i class="fad fa-bars-progress fa-fade fa-1-2x vertical-middle"></i>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                    <tbody v-cloak v-else>
                    <tr>
                        <td colspan="7"><span class="iransans">اطلاعاتی وجود ندارد</span></td>
                    </tr>
                    </tbody>
                    <tfoot class="bg-dark">
                    <tr>
                        <td colspan="7" class="py-2 px-3">
                            <div class="d-flex align-items-center justify-content-start gap-2 gap-lg-4 my-1 px-2">
                                <p class="iransans white-color mb-0">
                                    مجموع :
                                    @{{ Backups.length }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_organization_modal" tabindex="-1" aria-labelledby="new_organization_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد فایل پشتیبان جدید</h6>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label iransans">
                                نام
                                <strong class="red-color">*</strong>
                            </label>
                            <input class="form-control text-center iransans" type="text" v-model="name">
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-check-inline">
                                <input checked class="form-check-input vertical-middle" type="radio" name="BackupType" id="Database" value="Database" v-model="BackupType">
                                <label class="form-check-label iransans" for="Database">
                                    اطلاعات پایگاه داده
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <input class="form-check-input vertical-middle" type="radio" name="BackupType" id="EmployeeDocs" value="EmployeeDocs" v-model="BackupType">
                                <label class="form-check-label iransans" for="EmployeeDocs">
                                    کلیه اطلاعات به همراه نرم افزار
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-menu">
                    <button class="btn btn-success submit_button" v-on:click="BackupInformation">
                        <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
                        <span class="iransans">ارسال و ذخیره</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @if($errors->has('name'))
        <script defer>
            $(document).ready(function (){
                let modal = new bootstrap.Modal(document.getElementById("new_organization_modal"), {});
                modal.show();
            });
        </script>
    @endif
@endsection
