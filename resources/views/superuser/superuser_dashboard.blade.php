@extends('layouts.dashboard')
@section('menu')
    <li class="nav-item mb-1">
        <button class="w-100 btn btn-toggle align-items-center justify-content-between rounded collapsed p-0" data-bs-toggle="collapse" data-bs-target="#menu-collapse" @if(Route::is(["MenuHeaders.*","MenuItems.*","MenuActions.*"])) aria-expanded="true" @else aria-expanded="false" @endif>
            <div class="d-flex align-items-center justify-content-start">
                <i class="menu-header-icon far fa-table-list fa-1-4x me-2"></i>
                <span class="menu-header-text">منو</span>
            </div>
            <i class="fa fa-angle-left menu-header-arrow ms-3 fa-1-4x"></i>
        </button>
        <i class="menu-header-icon small-sidebar-icon vertical-middle far fa-table-list fa-1-2x me-2"></i>
        <div id="menu-collapse" class="collapse iransans rounded-2 bg-menu-dark-light p-2 @if(Route::is(["MenuHeaders.*","MenuItems.*","MenuActions.*"])) show @endif">
            <ul class="btn-toggle-nav list-unstyled fw-normal small">
                <li class="p-2 @if(Route::is(["MenuHeaders.index","MenuHeaders.edit"])) active @endif">
                    <a href="{{route("MenuHeaders.index")}}" class="menu-item-link rounded iransans @if(Route::is("MenuHeaders.*")) active @endif">
                        دسته بندی عناوین
                    </a>
                </li>
                <li class="p-2 @if(Route::is(["MenuItems.index","MenuItems.edit"])) active @endif">
                    <a href="{{route("MenuItems.index")}}" class="menu-item-link rounded iransans @if(Route::is("MenuItems.*")) active @endif">
                        عناوین اصلی و فرعی
                    </a>
                </li>
                <li class="p-2 @if(Route::is(["MenuActions.index","MenuActions.edit"])) active @endif">
                    <a href="{{route("MenuActions.index")}}" class="menu-item-link rounded iransans @if(Route::is("MenuActions.*")) active @endif">
                        عملیات وابسته
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item mb-2">
        <button class="w-100 btn btn-toggle align-items-center justify-content-between rounded collapsed p-0" data-bs-toggle="collapse" data-bs-target="#user-collapse" @if(Route::is(["SuperUserUsers.*","SuperUserRoles.*"])) aria-expanded="true" @else aria-expanded="false" @endif>
            <div class="d-flex align-items-center justify-content-start">
                <i class="menu-header-icon far fa-users-cog fa-1-4x me-2"></i>
                <span class="menu-header-text">کاربران و عناوین شغلی</span>
            </div>
            <i class="fa fa-angle-left menu-header-arrow ms-3 fa-1-4x"></i>
        </button>
        <i class="menu-header-icon small-sidebar-icon vertical-middle far fa-users-cog fa-1-2x me-2"></i>
        <div id="user-collapse" class="collapse iransans rounded-2 bg-menu-dark-light p-2 @if(Route::is(["SuperUserUsers.*","SuperUserRoles.*"])) show @endif">
            <ul class="btn-toggle-nav list-unstyled fw-normal small">
                <li class="p-2 @if(Route::is("SuperUserUsers.*")) active @endif">
                    <a href="{{route("SuperUserUsers.index")}}" class="menu-item-link rounded iransans @if(Route::is("SuperUserUsers.*")) active @endif">
                        کاربران
                    </a>
                </li>
                <li class="p-2 @if(Route::is("SuperUserRoles.*")) active @endif">
                    <a href="{{route("SuperUserRoles.index")}}" class="menu-item-link rounded iransans @if(Route::is("SuperUserRoles.*")) active @endif">
                        عناوین شغلی
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item mb-1">
        <button class="w-100 btn btn-toggle align-items-center justify-content-between rounded collapsed p-0" data-bs-toggle="collapse" data-bs-target="#system-collapse" @if(Route::is(["SystemInformation.*"])) aria-expanded="true" @else aria-expanded="false" @endif>
            <div class="d-flex align-items-center justify-content-start">
                <i class="menu-header-icon far fa-cogs fa-1-4x me-2"></i>
                <span class="menu-header-text">امکانات</span>
            </div>
            <i class="fa fa-angle-left menu-header-arrow ms-3 fa-1-4x"></i>
        </button>
        <i class="menu-header-icon small-sidebar-icon vertical-middle far fa-computer fa-1-2x me-2"></i>
        <div id="system-collapse" class="collapse iransans rounded-2 bg-menu-dark-light p-2 @if(Route::is(["SystemInformation.*","AutomationFlow.*","EmployeeRequests.*","SystemOperations.*","Backup.*"])) show @endif">
            <ul class="btn-toggle-nav list-unstyled fw-normal small">
                <li class="p-2 @if(Route::is(["SystemInformation.*"])) active @endif">
                    <a href="{{route("SystemInformation.index")}}" class="menu-item-link rounded iransans @if(Route::is("SystemInformation.*")) active @endif">
                        اطلاعات سامانه
                    </a>
                </li>
                <li class="p-2 @if(Route::is(["AutomationFlow.*"])) active @endif">
                    <a href="{{route("AutomationFlow.index")}}" class="menu-item-link rounded iransans @if(Route::is("AutomationFlow.*")) active @endif">
                        چرخه گردش اتوماسیون
                    </a>
                </li>
                <li class="p-2 @if(Route::is(["SystemOperations.*"])) active @endif">
                    <a href="{{route("SystemOperations.index")}}" class="menu-item-link rounded iransans @if(Route::is("SystemOperations.*")) active @endif">
                        عملیات فنی
                    </a>
                </li>
                <li class="p-2 @if(Route::is(["Backup.*"])) active @endif">
                    <a href="{{route("Backup.index")}}" class="menu-item-link rounded iransans @if(Route::is("Backup.*")) active @endif">
                        ایجاد فایل پشتیبان اطلاعات
                    </a>
                </li>
                <li class="p-2 @if(Route::is(["EmployeeRequests.*"])) active @endif">
                    <a href="{{route("EmployeeRequests.index")}}" class="menu-item-link rounded iransans @if(Route::is("EmployeeRequests.*")) active @endif">
                        مدیریت درخواست های پرسنل
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endsection
@section('main')
    @if(Route::is("superuser_idle"))
        <div style="position: absolute;top: 0;left: 0;right: 0;bottom: 0" class="d-flex align-items-center justify-content-center">
            <img alt="همیاران شمال شرق" style="width: 20vw;height: auto" src="{{ asset("/images/idle-bg.png") }}">
        </div>
    @else
        <div class="content-header position-fixed">
            @yield('header')
        </div>
        <div class="content-page bg-white w-100">
            @yield('content')
        </div>
    @endif
@endsection
