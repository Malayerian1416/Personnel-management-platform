@extends('superuser.superuser_dashboard')
@section('variables')
    @if(old("roles_list") != null)
        <script>
            const roles_list_data = @json(json_decode(old("roles_list"),true));
        </script>
    @endif
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i v-if="sidebar_toggle" class="sidebar-toggle fa fa-bars fa-1-6x me-4" v-cloak
               v-on:click="toggle_sidebar('open')"></i>
            <h5 class="iransans d-inline-block m-0 d-flex align-items-center">
                چرخه گردش اتوماسیون
                <span class="vertical-middle ms-2">(مشاهده ، ویرایش)</span>
            </h5>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content w-100">
        <div class="input-group mb-2">
            <button class="btn btn-outline-primary d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_automation_flow_modal">
                <span class="iransans">چرخه جدید</span>
            </button>
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام و کد ملی">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table>
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($automation_flows as $automation_flow)
                        <tr>
                            <td><span class="iransans">{{ $automation_flow->id }}</span></td>
                            <td><span class="iransans">{{ $automation_flow->name }}</span></td>
                            <td>
                                @if($automation_flow->inactive == 1)
                                    <i class="fa fa-times-circle red-color fa-1-6x"></i>
                                @elseif($automation_flow->inactive == 0)
                                    <i class="fa fa-check-circle green-color fa-1-6x"></i>
                                @endif
                            </td>
                            <td><span class="iransans">{{ $automation_flow->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($automation_flow->created_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td><span class="iransans">{{ verta($automation_flow->updated_at)->format("H:i:s Y/m/d") }}</span></td>
                            <td class="position-relative">
                                <div class="dropdown table-functions iransans">
                                    <a class="table-functions-button dropdown-toggle border-0 iransans info-color" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cog fa-1-2x"></i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <form class="w-100" id="activation-form-{{ $automation_flow->id }}" action="{{ route("AutomationFlow.activation",$automation_flow->id) }}" method="POST" v-on:submit="submit_form">
                                            @csrf
                                            <button type="submit" form="activation-form-{{ $automation_flow->id }}" class="dropdown-item">
                                                @if($automation_flow->inactive == 0)
                                                    <i class="fa fa-lock mr-1"></i>
                                                    <span>غیر فعال سازی</span>
                                                @elseif($automation_flow->inactive == 1)
                                                    <i class="fa fa-lock-open mr-1"></i>
                                                    <span>فعال سازی</span>
                                                @endif
                                            </button>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <a role="button" href="{{ route("AutomationFlow.edit",$automation_flow->id) }}" class="dropdown-item">
                                            <i class="fa fa-edit"></i>
                                            <span class="iransans">ویرایش</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form class="w-100" id="delete-form-{{ $automation_flow->id }}" action="{{ route("AutomationFlow.destroy",$automation_flow->id) }}" method="POST" v-on:submit="submit_form">
                                            @csrf
                                            @method("Delete")
                                            <button type="submit" form="delete-form-{{ $automation_flow->id }}" class="dropdown-item">
                                                <i class="fa fa-trash"></i>
                                                <span class="iransans">حذف</span>
                                            </button>
                                        </form>
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
    <div class="modal fade" id="new_automation_flow_modal" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد گردش جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" action="{{route("AutomationFlow.store")}}" method="post" data-json="roles_list" v-on:submit="submit_form">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label class="col-form-label iransans black_color" for="name">
                                    نام
                                    <strong class="red-color">*</strong>
                                </label>
                                <input type="text" class="form-control iransans text-center @error('name') is-invalid @enderror" id="name" name="name" value="{{old("name")}}">
                                @error('name')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="col-form-label iransans black_color" for="short_name">
                                    اضافه کردن عناوین شغلی
                                    <strong class="red-color">*</strong>
                                </label>
                                <select class="form-control iransans text-center selectpicker-select @error('flow_roles') is-invalid is-invalid-fake @enderror" data-live-search="true" id="roles" title="انتخاب کنید" data-size="20" v-on:change="AddRoleItem">
                                    @forelse($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('flow_roles')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="col-form-label iransans black_color">
                                    گردش(اولویت به ترتیب ثبت)
                                </label>
                                <ul class="list-group w-100 pl-0">
                                    <li class="list-group-item list-group-item-action" v-if="roles_list.length === 0"><h6 class="iransans text-center m-0">انتخابی صورت نگرفته است</h6></li>
                                    <li style="min-height: 43px" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" v-for="(role,index) in roles_list">
                                        <div class="d-flex flex-row align-items-center justify-content-between">
                                            <span class="iransans text-center ml-1" style="font-weight: 700;font-size: 13px">@{{ `${index+1} - ${role.name}` }}</span>
                                            <div v-if="role.priority > 1" class="ms-3">
                                                <select class="form-select iransans" v-on:change="MakeRoleBalance($event,role.priority)">
                                                    <option>همترازی ندارد</option>
                                                    <option v-for="role in roles_list" :value="role.priority">@{{ role.name }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-row align-items-center justify-content-between">
                                            <div class="border ps-2 pe-2">
                                                <label :for="role.slug" class="iransans mr-1">
                                                    <i class="fa fa-check-double fa-1-2x vertical-middle"></i>
                                                </label>
                                                <input type="checkbox" class="vertical-middle" value="true" :id="role.slug" v-model="role.main_role">
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-light mr-2" data-function="up" :data-slug="role.slug" v-on:click="ModifyRole">
                                                <i class="fa fa-arrow-up gray-color fa-1-2x"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-light mr-2" data-function="down" :data-slug="role.slug" v-on:click="ModifyRole">
                                                <i class="fa fa-arrow-down gray-color fa-1-2x"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-light" data-function="remove" :data-slug="role.slug" v-on:click="ModifyRole">
                                                <i class="fa fa-times gray-color fa-1-2x"></i>
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                                <small v-if="roles_list.length !== 0" class="iransans red-color">در صورت مشخص نکردن تایید کننده نهایی، عنوان شغلی انتهای لیست به عنوان تایید کننده نهایی در نظر گرفته می شود </small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-save fa-1-2x me-1"></i>
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
    @if($errors->has('name') || $errors->has('roles_list'))
        <script defer>
            $(document).ready(function (){
                let modal = new bootstrap.Modal(document.getElementById("new_automation_flow_modal"), {});
                modal.show();
            });
        </script>
    @endif
@endsection
