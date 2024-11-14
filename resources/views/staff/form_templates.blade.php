@extends("staff.staff_dashboard")
@section('variables')
    <script>
        let applications_data = @json($applications);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">

            <h5 class="iransans d-inline-block m-0">قالب فرم درخواست های پرسنل</h5>
            <span>(ایجاد، جستجو و ویرایش)</span>
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
    <div class="page-content w-100">
        <div class="input-group mb-2">
            <button class="btn btn-outline-info d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_template_modal">
                <i class="fa fa-plus fa-1-4x me-1"></i>
                <span class="iransans create-button">قالب جدید</span>
            </button>
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام قالب">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table>
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>فرم</span></th>
                        <th scope="col"><span>پس زمینه</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($form_templates as $form_template)
                        <tr>
                            <td><span class="iransans">{{ $form_template->id }}</span></td>
                            <td><span class="iransans">{{ $form_template->name }}</span></td>
                            <td><span class="iransans">{{ $form_template->application->name }}</span></td>
                            <td>
                                @if($form_template->background)
                                    <span class="iransans">{{ $form_template->background }}</span>
                                @else
                                    <i class="fa fa-times-circle fa-1-4x red-color"></i>
                                @endif
                            </td>
                            <td><span class="iransans">{{ $form_template->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($form_template->created_at)->format("Y/m/d") }}</span></td>
                            <td><span class="iransans">{{ verta($form_template->updated_at)->format("Y/m/d") }}</span></td>
                            <td class="position-relative">
                                <div class="dropdown table-functions iransans">
                                    <a class="table-functions-button dropdown-toggle border-0 iransans info-color" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cog fa-1-2x"></i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can("edit", "FormTemplates")
                                            <a role="button" href="{{ route("FormTemplates.edit",$form_template->id) }}" class="dropdown-item">
                                                <i class="fa fa-edit"></i>
                                                <span class="iransans">ویرایش</span>
                                            </a>
                                        @endcan
                                        @can("delete","FormTemplates")
                                            <div class="dropdown-divider"></div>
                                            <form class="w-100" id="delete-form-{{ $form_template->id }}" action="{{ route("FormTemplates.destroy",$form_template->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                                <button type="submit" form="delete-form-{{ $form_template->id }}" class="dropdown-item">
                                                    <i class="fa fa-trash"></i>
                                                    <span class="iransans">حذف</span>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_template_modal" tabindex="-1" aria-labelledby="new_template_modal" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ایجاد قالب جدید</h5>
                </div>
                    <div class="modal-body p-0" id="template_modal_body" style="background-color: #cccccc">
                        <form-template method="post"></form-template>
                    </div>
                <div class="modal-footer bg-menu">
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
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
