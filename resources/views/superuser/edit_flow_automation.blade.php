@extends('superuser.superuser_dashboard')
@section('variables')
    <script>
        const roles_list_data = @json($flow_list);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i v-if="sidebar_toggle" class="sidebar-toggle fa fa-bars fa-1-6x me-4" v-cloak
               v-on:click="toggle_sidebar('open')"></i>
            <h5 class="iransans d-inline-block m-0 d-flex align-items-center">
                کاربران سیستم
                <span class="vertical-middle ms-2">(ویرایش)</span>
            </h5>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content w-100">
        <form id="update_form" class="p-3" action="{{ route("AutomationFlow.update",$automation_flow->id) }}" data-json="roles_list" method="POST" v-on:submit="submit_form">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group col-12">
                    <label class="col-form-label iransans black_color" for="name">
                        نام
                        <strong class="red-color">*</strong>
                    </label>
                    <input type="text" class="form-control iransans text-center @error('name') is-invalid @enderror" id="name" name="name" value="{{$automation_flow->name}}">
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
        <div class="row">
            <div class="mb-3 col-12 form-button-row text-center pt-4 pb-2">
                <button type="submit" form="update_form" class="btn btn-success submit_button">
                    <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
                    <span class="iransans">ارسال و ویرایش</span>
                </button>
                <a role="button" href="{{ route("AutomationFlow.index") }}"
                   class="btn btn-outline-secondary iransans">
                    <i class="fa fa-arrow-turn-right fa-1-2x me-1"></i>
                    <span class="iransans">بازگشت به لیست</span>
                </a>
            </div>
        </div>
    </div>
@endsection
