@extends('superuser.superuser_dashboard')
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i v-if="sidebar_toggle" class="sidebar-toggle fa fa-bars fa-1-6x me-4" v-cloak
               v-on:click="toggle_sidebar('open')"></i>
            <h5 class="iransans d-inline-block m-0 d-flex align-items-center">
                عناوین شغلی
                <span class="vertical-middle ms-2">(ایجاد ، جستجو ، ویرایش)</span>
            </h5>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content w-100">
        <div class="input-group mb-2">
            <button class="btn btn-outline-info d-flex flex-row align-items-center justify-content-center"
                    data-bs-toggle="modal" data-bs-target="#new_role_modal">
                <span class="iransans create-button">عنوان شغلی جدید</span>
            </button>
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام سرفصل">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table class="table table-striped">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span><span>تاریخ ثبت</span></span></th>
                        <th scope="col"><span><span>تاریخ ویرایش</span></span></th>
                        <th scope="col"><span><span>عملیات</span></span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td><span class="iransans">{{$role->id}}</span></td>
                            <td><span class="iransans">{{$role->name}}</span></td>
                            @if($role->inactive == 0)
                                <td><span class="iransans"><i
                                                class="far fa-check-circle green-color fa-1-4x"></i></span></td>
                            @elseif($role->inactive == 1)
                                <td><span class="iransans"><i class="far fa-times-circle red-color fa-1-4x"></i></span>
                                </td>
                            @endif
                            <td><span class="iransans">{{$role->user ? $role->user->name : 'نامشخص'}}</span></td>
                            <td><span class="iransans">{{verta($role->created_at)->format("Y/m/d")}}</span></td>
                            <td><span class="iransans">{{verta($role->updated_at)->format("Y/m/d")}}</span></td>
                            <td class="position-relative">
                                <div class="dropdown table-functions iransans">
                                    <a class="table-functions-button dropdown-toggle border-0 iransans info-color"
                                       type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                       aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cog fa-1-2x"></i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <form class="w-100" id="activation-form-{{ $role->id }}"
                                              action="{{ route("SuperUserRoles.activation",$role->id) }}" method="POST"
                                              v-on:submit="submit_form">
                                            @csrf
                                            <button type="submit" form="activation-form-{{ $role->id }}"
                                                    class="dropdown-item">
                                                @if($role->inactive == 0)
                                                    <i class="fa fa-lock"></i>
                                                    <span>غیر فعال سازی</span>
                                                @elseif($role->inactive == 1)
                                                    <i class="fa fa-lock-open"></i>
                                                    <span>فعال سازی</span>
                                                @endif
                                            </button>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <a role="button" href="{{ route("SuperUserRoles.edit",$role->id) }}"
                                           class="dropdown-item">
                                            <i class="fa fa-edit"></i>
                                            <span class="iransans">ویرایش</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form class="w-100" id="delete-form-{{ $role->id }}"
                                              action="{{ route("SuperUserRoles.destroy",$role->id) }}" method="POST"
                                              v-on:submit="submit_form">
                                            @csrf
                                            @method("Delete")
                                            <button type="submit" form="delete-form-{{ $role->id }}"
                                                    class="dropdown-item">
                                                <i class="fa fa-trash"></i>
                                                <span class="iransans">حذف</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10"><span class="iransans">اطلاعاتی وجود ندارد</span></td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_role_modal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ایجاد عنوان شغلی جدید</h5>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" action="{{route("SuperUserRoles.store")}}" method="post"
                          data-type="create" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div class="form-group mb-3 col-md-12">
                                <label class="col-form-label iransans black_color" for="name">
                                    نام عنوان شغلی
                                    <strong class="red-color">*</strong>
                                </label>
                                <input type="text"
                                       class="form-control iransans text-center @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{old("name")}}">
                                @error('name')
                                <span class="invalid-feedback iransans" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <label class="col-form-label iransans black_color">
                                دسترسی منو
                                <strong class="red-color">*</strong>
                            </label>
                            @forelse($menu_headers as $menu_header)
                                @if($menu_header->items->isNotEmpty())
                                    <div class="form-group mb-3 col-md-12 col-lg-3 col-xl-2 iransans">
                                        <ul class="menu-list main-menu-list border">
                                            <li class="bg-dark white-color role-menu-header-text-container">
                                                <h5 class="role-menu-header-text iransans">
                                                    {{$menu_header->name}}
                                                    <span>
                                                <i class="far fa-check-square fa-1-4x all-checkboxes ms-3"
                                                   v-on:click="select_all_checkboxes"></i>
                                                <i class="far fa-square fa-1-4x all-checkboxes"
                                                   v-on:click="deselect_all_checkboxes"></i>
                                            </span>
                                                </h5>
                                            </li>
                                            @forelse($menu_header->items as $menu_item)
                                                @if($menu_item->parent_id == null)
                                                    <li class="lev1-menu-list">
                                <span class="d-flex flex-row align-items-center justify-content-start mt-1"
                                      style="font-weight: 600;font-size: 14px;color: #1a5d2b">
                                    <i class="fa fa-bullseye me-2 vertical-middle"></i>
                                    {{$menu_item->name}}
                                </span>
                                                        @if($menu_item->actions)
                                                            <ul class="menu-list sub-menu-list">
                                                                @foreach($menu_item->actions as $action)
                                                                    <li class="menu-item-selectable"
                                                                        v-on:click="menu_action_checkmark">
                                                                        <input class="me-1" type="checkbox"
                                                                               name="role_menu[]"
                                                                               value="{{$menu_item->id."#".$action->id."#".$menu_item->route.".".$action->action}}"
                                                                               v-on:click="menu_action_checkmark">
                                                                        <span>{{$action->name}}</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                        @if($menu_item->children)
                                                            <ul class="menu-list">
                                                                @foreach($menu_item->children as $child)
                                                                    <li>
                                                <span class="d-flex flex-row align-items-center justify-content-start"
                                                      style="font-weight: 500;font-size: 13px;color: #2a733c">
                                                    <i class="fa fa-bullseye me-2 vertical-middle"></i>
                                                    {{$child->name}}
                                                </span>
                                                                        @if($child->actions)
                                                                            <ul class="menu-list sub-menu-list">
                                                                                @foreach($child->actions as $action)
                                                                                    <li class="menu-item-selectable"
                                                                                        v-on:click="menu_action_checkmark">
                                                                                        <input class="me-1"
                                                                                               type="checkbox"
                                                                                               name="role_menu[]"
                                                                                               value="{{$child->id."#".$action->id."#".$child->route.".".$action->action}}"
                                                                                               v-on:click="menu_action_checkmark">
                                                                                        <span>{{$action->name}}</span>
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endif
                                            @empty
                                            @endforelse
                                        </ul>
                                    </div>
                                    @error('name')
                                    <span class="invalid-feedback iransans small_font"
                                          role="alert">{{ $message }}</span>
                                    @enderror
                                @endif
                            @empty
                            @endforelse
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="main_submit_form" class="btn btn-success">
                        <i class="fa fa-check fa-1-2x me-1"></i>
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
