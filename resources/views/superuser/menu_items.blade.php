@extends('superuser.superuser_dashboard')
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i v-if="sidebar_toggle" class="sidebar-toggle fa fa-bars fa-1-6x me-4" v-cloak
               v-on:click="toggle_sidebar('open')"></i>
            <h5 class="iransans d-inline-block m-0 d-flex align-items-center">
                عناوین اصلی و فرعی منو
                <span class="vertical-middle ms-2">(ایجاد، جستجو ، ویرایش)</span>
            </h5>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content w-100">
        <div class="input-group mb-2">
            <button class="btn btn-outline-info d-flex flex-row align-items-center justify-content-center"
                    data-bs-toggle="modal" data-bs-target="#new_menu_items_modal">
                <span class="iransans create-button">عنوان جدید</span>
            </button>
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام سرفصل">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table class="table table-striped">
                    <thead class="bg-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>سرفصل</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($menu_items as $menu_item)
                        <tr>
                            <td><span class="iransans">{{ $menu_item->id }}</span></td>
                            <td><span class="iransans">{{ $menu_item->name }}</span></td>
                            <td><span class="iransans">{{ $menu_item->menu_header->name }}</span></td>
                            <td><span class="iransans">{{ $menu_item->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($menu_item->created_at)->format("Y/m/d") }}</span></td>
                            <td><span class="iransans">{{ verta($menu_item->updated_at)->format("Y/m/d") }}</span></td>
                            <td class="position-relative">
                                <div class="dropdown table-functions iransans">
                                    <a class="table-functions-button dropdown-toggle border-0 iransans info-color"
                                       type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                       aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cog fa-1-2x"></i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a role="button" href="{{ route("MenuItems.edit",$menu_item->id) }}"
                                           class="dropdown-item">
                                            <i class="fa fa-edit"></i>
                                            <span class="iransans">ویرایش</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form class="w-100" id="delete-form-{{ $menu_item->id }}"
                                              action="{{ route("MenuItems.destroy",$menu_item->id) }}" method="POST"
                                              v-on:submit="submit_form">
                                            @csrf
                                            @method("Delete")
                                            <button type="submit" form="delete-form-{{ $menu_item->id }}"
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
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_menu_items_modal" tabindex="-1" role="dialog" data-bs-backdrop="static"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ایجاد عنوان جدید</h5>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" action="{{route("MenuItems.store")}}" method="post" data-type="create"
                          v-on:submit="submit_form" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="form-group mb-3 col-12">
                                <label class="col-form-label iransans black_color" for="name">
                                    نام
                                    <strong class="red-color">*</strong>
                                </label>
                                <input type="text"
                                       class="form-control iransans text-center @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{old("name")}}">
                                @error('name')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="col-form-label iransans black_color" for="short_name">
                                    نام مختصر
                                    <strong class="red-color">*</strong>
                                </label>
                                <input type="text"
                                       class="form-control iransans text-center @error('short_name') is-invalid @enderror"
                                       id="short_name" name="short_name" value="{{old("name")}}">
                                @error('short_name')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="col-form-label iransans black_color" for="menu_header_id">
                                    سرفصل منو
                                    <strong class="red-color">*</strong>
                                </label>
                                <select class="form-control iransans text-center selectpicker-select @error('menu_header_id') is-invalid @enderror"
                                        data-live-search="true" id="menu_header_id" name="menu_header_id"
                                        title="انتخاب کنید" data-size="20">
                                    @forelse($menu_headers as $menu_header)
                                        <option value="{{$menu_header->id}}">{{$menu_header->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('menu_header_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="col-form-label iransans black_color" for="parent_id">وابستگی</label>
                                <select class="form-control iransans text-center selectpicker-select @error('parent_id') is-invalid @enderror"
                                        data-live-search="true" id="parent_id" name="parent_id" title="انتخاب کنید"
                                        data-size="20">
                                    <option value="">هیچکدام</option>
                                    @forelse($menu_items as $menu_item)
                                        <option value="{{$menu_item->id}}">{{$menu_item->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('parent_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="col-form-label iransans black_color" for="menu_action_id">عملیات
                                    وابسته</label>
                                <select class="form-control iransans text-center selectpicker-select @error('menu_action_id') is-invalid @enderror"
                                        v-on:change="main_route_change" multiple data-live-search="true"
                                        id="menu_action_id" name="menu_action_id[]" title="انتخاب کنید" data-size="20">
                                    @forelse($menu_actions as $menu_action)
                                        <option value="{{$menu_action->id}}">{{$menu_action->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('menu_action_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="col-form-label iransans black_color" for="main">عملیات اصلی</label>
                                <select class="form-control iransans text-center selectpicker-select @error('main') is-invalid @enderror"
                                        data-live-search="true" id="main" name="main" title="انتخاب کنید"
                                        data-size="20">

                                </select>
                                @error('main')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="col-form-label iransans black_color" for="route">
                                    مسیر
                                    <strong class="red-color">*</strong>
                                </label>
                                <input type="text"
                                       class="form-control text-center @error('route') is-invalid @enderror ltr"
                                       id="route" name="route" value="{{old("route")}}">
                                @error('route')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="col-form-label iransans black_color" for="priority">اولویت نمایش</label>
                                <input min="0" type="number"
                                       class="form-control text-center iransans @error('priority') is-invalid @enderror"
                                       id="priority" name="priority" value="{{old("priority") ? old("priority") : 0}}">
                                @error('priority')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="col-form-label iransans black_color" for="upload_file">آیکون</label>
                                <s-file-browser :accept="['png']" :size="325000"></s-file-browser>
                                @error('upload_file')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
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
