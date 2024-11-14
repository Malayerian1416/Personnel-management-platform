@extends('layouts.landing')
@section('content')
    <section class="container mt-3">
        <div class="row">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb ">
                    <ol class="breadcrumb mb-0 rad25">
                        <li class="breadcrumb-item"><a href="{{route("Home")}}">صفحه اصلی</a></li>
                        <li class="breadcrumb-item active" aria-current="page">اعتبارسنجی نامه های اداری</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    <section class="container-fluid mt-3 pb-5" style="max-width: 600px">
        <div class="container px-5 py-3 box bg-page" style="min-height: 300px">
            <div class="row">
                @if($status == "approved")
                    <div id="blog_details">
                        <h1 class="pt-3 pb-2 IRANSansWeb_Medium" style="color: green">
                            <i class="fa fa-check-double fa-2x" style="vertical-align: middle"></i>
                            شناسه یکتا وارد شده در سامانه موجود می باشد
                        </h1>
                    </div>
                @endif
                <div class="col-12">
                    @if($status == "rejected")
                        <div class="alert alert-danger mt-5" role="alert">
                            <h6 class="m-0 text-center">
                                <i class="fa fa-exclamation-triangle fa-2x"></i>
                                شناسه یکتا وارد شده وجود ندارد و یا منقضی شده است
                            </h6>
                        </div>
                    @elseif($status == "approved")
                        <div>
                            <label class="text-muted IRANSansWeb_Medium pb-2">لطفا از صحت و تطابق اطلاعات نمایش داده با اطلاعات مندرج برروی برگه اطمینان حاصل فرمایید</label>
                            <ul class="list-group">
                                <li class="list-group-item bg-transparent">
                                    <i class="fas fa-arrow-left" style="vertical-align: middle"></i>
                                    <span>شناسه یکتا : </span>
                                    <span class="text-muted">{{$application->i_number}}</span>
                                </li>
                                @if($type == "request")
                                    <li class="list-group-item bg-transparent">
                                        <i class="fas fa-arrow-left" style="vertical-align: middle"></i>
                                        <span>نوع درخواست : </span>
                                        <span class="text-muted">{{$application->automation->application_name}}</span>
                                    </li>
                                @endif
                                @if($type == "request")
                                    <li class="list-group-item bg-transparent">
                                        <i class="fas fa-arrow-left" style="vertical-align: middle"></i>
                                        <span>تاریخ صدور : </span>
                                        <span class="text-muted">{{verta($application->automation->updated_at)->format("Y/m/d")}}</span>
                                    </li>
                                @endif
                                <li class="list-group-item bg-transparent">
                                    <i class="fas fa-arrow-left" style="vertical-align: middle"></i>
                                    <span>متقاضی : </span>
                                    <span class="text-muted">{{$application->employee->name}}</span>
                                </li>
                                <li class="list-group-item bg-transparent">
                                    <i class="fas fa-arrow-left" style="vertical-align: middle"></i>
                                    <span>کد ملی : </span>
                                    <span class="text-muted">{{$application->employee->national_code}}</span>
                                </li>
                                @if($type == "request" && $application->loan_amount > 0)
                                    <li class="list-group-item bg-transparent">
                                        <i class="fas fa-arrow-left" style="vertical-align: middle"></i>
                                        <span>مبلغ وام : </span>
                                        <span class="text-muted">{{number_format($application->loan_amount) . " ریال"}}</span>
                                    </li>
                                @endif
                                @if($type == "request" && $application->borrower)
                                    <li class="list-group-item bg-transparent">
                                        <i class="fas fa-arrow-left" style="vertical-align: middle"></i>
                                        <span>متضامن : </span>
                                        <span class="text-muted">{{$application->borrower}}</span>
                                    </li>
                                @endif
                                @if($type == "payslip")
                                    <li class="list-group-item bg-transparent">
                                        <i class="fas fa-arrow-left" style="vertical-align: middle"></i>
                                        <span>سال فیش حقوقی : </span>
                                        <span class="text-muted">{{$salary["employee"]["year"]}}</span>
                                    </li>
                                @endif
                                @if($type == "payslip")
                                    <li class="list-group-item bg-transparent">
                                        <i class="fas fa-arrow-left" style="vertical-align: middle"></i>
                                        <span>ماه فیش حقوقی : </span>
                                        <span class="text-muted">{{$salary["employee"]["month"]}}</span>
                                    </li>
                                @endif
                                @if($type == "payslip")
                                    <li class="list-group-item bg-transparent">
                                        <i class="fas fa-arrow-left" style="vertical-align: middle"></i>
                                        <span>جمع مزایا : </span>
                                        <span class="text-muted">{{$salary["total_advantages"]." ریال"}}</span>
                                    </li>
                                @endif
                                @if($type == "payslip")
                                    <li class="list-group-item bg-transparent">
                                        <i class="fas fa-arrow-left" style="vertical-align: middle"></i>
                                        <span>جمع کسورات : </span>
                                        <span class="text-muted">{{$salary["total_deductions"]." ریال"}}</span>
                                    </li>
                                @endif
                                @if($type == "payslip")
                                    <li class="list-group-item bg-transparent">
                                        <i class="fas fa-arrow-left" style="vertical-align: middle"></i>
                                        <span>خالص پرداختی : </span>
                                        <span class="text-muted">{{$salary["total_net"]." ریال"}}</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="col-12 text-center pt-3 pb-3">
                    <a role="button" class="btn btn-primary" href="{{route("Validation.index")}}">
                        ورود مجدد شناسه یکتا
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
