@extends('superuser.superuser_dashboard')
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i v-if="sidebar_toggle" class="sidebar-toggle fa fa-bars fa-1-6x me-4" v-cloak
               v-on:click="toggle_sidebar('open')"></i>
            <h5 class="iransans d-inline-block m-0 d-flex align-items-center">
                مدیریت درخواست های پرسنل
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
                <span class="iransans create-button">درخواست جدید</span>
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
                        <th scope="col"><span>کلاس</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td><span class="iransans">{{ $request->id }}</span></td>
                            <td><span class="iransans">{{ $request->name }}</span></td>
                            <td><span class="iransans">{{ $request->application_form_type }}</span></td>
                            <td><span class="iransans">{{ $request->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($request->created_at)->format("Y/m/d") }}</span>
                            </td>
                            <td><span class="iransans">{{ verta($request->updated_at)->format("Y/m/d") }}</span>
                            </td>
                            <td class="position-relative">
                                <div class="dropdown table-functions iransans">
                                    <a class="table-functions-button dropdown-toggle border-0 iransans info-color"
                                       type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                       aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cog fa-1-2x"></i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a role="button" href="{{ route("EmployeeRequests.edit",$request->id) }}"
                                           class="dropdown-item">
                                            <i class="fa fa-edit"></i>
                                            <span class="iransans">ویرایش</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form class="w-100" id="delete-form-{{ $request->id }}"
                                              action="{{ route("EmployeeRequests.destroy",$request->id) }}" method="POST"
                                              v-on:submit="submit_form">
                                            @csrf
                                            @method("Delete")
                                            <button type="submit" form="delete-form-{{ $request->id }}"
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
    <div class="modal fade rtl" id="new_menu_header_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ایجاد درخواست جدید</h5>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("EmployeeRequests.store") }}" method="POST"
                          enctype="multipart/form-data" v-on:submit="submit_form">
                        @csrf
                        <div class="form-row">
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    نام
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iransans @error('name') is-invalid @enderror" type="text" name="name" value="{{ old("name") }}">
                                @error('name')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    کلاس
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center @error('application_form_type') is-invalid is-invalid-fake @enderror" type="text" name="application_form_type" value="{{ old("application_form_type") }}">
                                @error('application_form_type')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    گردش
                                    <strong class="red-color">*</strong>
                                </label>
                                <select class="form-control iransans selectpicker-select @error('slug') is-invalid is-invalid-fake @enderror" title="انتخاب کنید" data-max-size="15" data-live-search="true" name="flow_id">
                                    @forelse($flows as $flow)
                                        <option @if(old("flow_id") && old("flow_id") == $flow->id) selected @endif value="{{$flow->id}}">{{$flow->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('slug')
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
