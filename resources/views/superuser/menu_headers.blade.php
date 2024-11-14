@extends('superuser.superuser_dashboard')
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i v-if="sidebar_toggle" class="sidebar-toggle fa fa-bars fa-1-6x me-4" v-cloak
               v-on:click="toggle_sidebar('open')"></i>
            <h5 class="iransans d-inline-block m-0 d-flex align-items-center">
                دسته بندی عناوین منو
                <span class="vertical-middle ms-2">(ایجاد، جستجو ، ویرایش)</span>
            </h5>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content w-100">
        <div class="input-group mb-2">
            <button class="btn btn-outline-info d-flex flex-row align-items-center justify-content-center"
                    data-bs-toggle="modal" data-bs-target="#new_menu_header_modal">
                <span class="iransans create-button">دسته بندی جدید</span>
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
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($menu_headers as $menu_header)
                        <tr>
                            <td><span class="iransans">{{ $menu_header->id }}</span></td>
                            <td><span class="iransans">{{ $menu_header->name }}</span></td>
                            <td>
                                @if($menu_header->inactive == 1)
                                    <i class="far fa-times-circle red-color fa-1-4x vertical-middle"></i>
                                @elseif($menu_header->inactive == 0)
                                    <i class="far fa-check-circle green-color fa-1-4x vertical-middle"></i>
                                @endif
                            </td>
                            <td><span class="iransans">{{ $menu_header->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($menu_header->created_at)->format("Y/m/d") }}</span>
                            </td>
                            <td><span class="iransans">{{ verta($menu_header->updated_at)->format("Y/m/d") }}</span>
                            </td>
                            <td class="position-relative">
                                <div class="dropdown table-functions iransans">
                                    <a class="table-functions-button dropdown-toggle border-0 iransans info-color"
                                       type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                       aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cog fa-1-2x"></i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <form class="w-100" id="activation-form-{{ $menu_header->id }}"
                                              action="{{ route("MenuHeaders.activation",$menu_header->id) }}"
                                              method="POST" v-on:submit="submit_form">
                                            @csrf
                                            <button type="submit" form="activation-form-{{ $menu_header->id }}"
                                                    class="dropdown-item">
                                                @if($menu_header->inactive == 0)
                                                    <i class="fa fa-lock"></i>
                                                    <span>غیر فعال سازی</span>
                                                @elseif($menu_header->inactive == 1)
                                                    <i class="fa fa-lock-open"></i>
                                                    <span>فعال سازی</span>
                                                @endif
                                            </button>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <a role="button" href="{{ route("MenuHeaders.edit",$menu_header->id) }}"
                                           class="dropdown-item">
                                            <i class="fa fa-edit"></i>
                                            <span class="iransans">ویرایش</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form class="w-100" id="delete-form-{{ $menu_header->id }}"
                                              action="{{ route("MenuHeaders.destroy",$menu_header->id) }}" method="POST"
                                              v-on:submit="submit_form">
                                            @csrf
                                            @method("Delete")
                                            <button type="submit" form="delete-form-{{ $menu_header->id }}"
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
    <div class="modal fade rtl" id="new_menu_header_modal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ایجاد دسته بندی جدید</h5>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("MenuHeaders.store") }}" method="POST"
                          enctype="multipart/form-data" v-on:submit="submit_form">
                        @csrf
                        <div class="form-row">
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    نام
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iransans @error('name') is-invalid @enderror"
                                       type="text" name="name" value="{{ old("name") }}">
                                @error('name')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">نام مختصر</label>
                                <input class="form-control text-center" type="text" name="short_name"
                                       value="{{ old("short_name") }}">
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    مشخصه
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iransans @error('slug') is-invalid @enderror"
                                       type="text" name="slug" value="{{ old("slug") }}">
                                @error('slug')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">آیکون</label>
                                <s-file-browser :accept="['png']" :size="325000"></s-file-browser>
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
