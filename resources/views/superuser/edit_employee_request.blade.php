@extends('superuser.superuser_dashboard')
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i v-if="sidebar_toggle" class="sidebar-toggle fa fa-bars fa-1-6x me-4" v-cloak
               v-on:click="toggle_sidebar('open')"></i>
            <h5 class="iransans d-inline-block m-0 d-flex align-items-center">
                مدیریت درخواست های پرسنل
                <span class="vertical-middle ms-2">(ویرایش)</span>
            </h5>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content w-100">
        <form id="main_submit_form" class="p-3" action="{{ route("EmployeeRequests.update",$employee_request->id) }}" method="POST"
              enctype="multipart/form-data" v-on:submit="submit_form">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-row">
                    <div class="form-group mb-3 col-12">
                        <label class="form-label iransans">
                            نام
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control text-center iransans @error('name') is-invalid @enderror" type="text" name="name" value="{{ $employee_request->name }}">
                        @error('name')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-3 col-12">
                        <label class="form-label iransans">
                            کلاس
                            <strong class="red-color">*</strong>
                        </label>
                        <input class="form-control text-center @error('application_form_type') is-invalid is-invalid-fake @enderror" type="text" name="application_form_type" value="{{ $employee_request->application_form_type }}">
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
                                <option @if($employee_request->flow_id == $flow->id) selected @endif value="{{$flow->id}}">{{$flow->name}}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('slug')
                        <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-3 col-12 form-button-row text-center pt-4 pb-2">
                        <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                            <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
                            <span class="iransans">ارسال و ویرایش</span>
                        </button>
                        <a role="button" href="{{ route("EmployeeRequests.index") }}" class="btn btn-outline-secondary iransans">
                            <i class="fa fa-arrow-turn-right fa-1-2x me-1"></i>
                            <span class="iransans">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>
        </form>
    </div>
@endsection
