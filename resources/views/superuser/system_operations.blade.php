@extends('superuser.superuser_dashboard')
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                عملیات فنی سیستم
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
        <div class="row">
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="accordion" id="SystemOptimization">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="SystemOptimizationHeading">
                            <button class="accordion-button iransans" type="button" data-bs-toggle="collapse" data-bs-target="#SystemOptimizationBody" aria-expanded="true" aria-controls="SystemOptimizationBody">
                                بهینه سازی سیستم (Optimization)
                            </button>
                        </h2>
                        <div id="SystemOptimizationBody" class="accordion-collapse collapse show" aria-labelledby="SystemOptimizationHeading" data-bs-parent="#SystemOptimization">
                            <div class="accordion-body">
                                <details class="iransans mb-3">
                                    <summary class="mb-2">
                                        آخرین بهینه سازی موفق :
                                        <b class="blue-color">
                                            @if($optimizations->isNotEmpty())
                                                {{ verta($optimizations->first()->created_at)->format("H:i:s Y/m/d") }}
                                            @else
                                                انجام نشده است
                                            @endif
                                        </b>
                                    </summary>
                                    <div class="ps-3">
                                        <h6 class="iransans">عملیات اخیر:</h6>
                                        @forelse($optimizations as $optimization)
                                            <p class="pb-1 pr-3 mb-1 text-muted">{{"$loop->iteration. ".$optimization->user->name . " - " . verta($optimization->created_at)->format("H:i:s Y/m/d")}}</p>
                                        @empty
                                            <p class="text-muted">اطلاعاتی وجود ندارد</p>
                                        @endforelse
                                    </div>
                                </details>
                                <button type="submit" form="update_form" class="btn btn-outline-primary submit_button w-100">
                                    <span class="iransans">بهینه سازی</span>
                                </button>
                                <form hidden id="update_form" class="p-3" action="{{ route("System.optimize") }}" method="POST" v-on:submit="submit_form">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="accordion" id="SystemCacheConfig">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="SystemCacheConfigHeading">
                            <button class="accordion-button iransans" type="button" data-bs-toggle="collapse" data-bs-target="#SystemCacheConfigBody" aria-expanded="true" aria-controls="SystemCacheConfigBody">
                                ذخیره تنظیمات سیستم (Cache Config)
                            </button>
                        </h2>
                        <div id="SystemCacheConfigBody" class="accordion-collapse collapse show" aria-labelledby="SystemCacheConfigHeading" data-bs-parent="#SystemCacheConfig">
                            <div class="accordion-body">
                                <details class="iransans mb-3">
                                    <summary class="mb-2">
                                        آخرین ذخیره سازی موفق :
                                        <b class="blue-color">
                                            @if($optimizations->isNotEmpty())
                                                {{ verta($optimizations->first()->created_at)->format("H:i:s Y/m/d") }}
                                            @else
                                                انجام نشده است
                                            @endif
                                        </b>
                                    </summary>
                                    <div class="ps-3">
                                        <h6 class="iransans">عملیات اخیر:</h6>
                                        @forelse($optimizations as $optimization)
                                            <p class="pb-1 pr-3 mb-1 text-muted">{{"$loop->iteration. ".$optimization->user->name . " - " . verta($optimization->created_at)->format("H:i:s Y/m/d")}}</p>
                                        @empty
                                            <p class="text-muted">اطلاعاتی وجود ندارد</p>
                                        @endforelse
                                    </div>
                                </details>
                                <button type="submit" form="update_form" class="btn btn-outline-primary submit_button w-100">
                                    <span class="iransans">ذخیره سازی تنظیمات</span>
                                </button>
                                <form hidden id="update_form" class="p-3" action="{{ route("System.optimize") }}" method="POST" v-on:submit="submit_form">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
