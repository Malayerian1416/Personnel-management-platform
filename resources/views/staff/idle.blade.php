@extends('staff.staff_dashboard')
@section('main')
    <div class="container-fluid pt-4 px-4 idle_plugins">
        <div class="row g-4" style="color: rgb(25, 135, 84)">
            <div class="col-sm-6 col-xl-2 align-self-stretch">
                <div class="bg-dark rounded d-flex align-items-center justify-content-between p-4 h-100 position-relative pointer-cursor" onclick="location.href='{{route("EmployeesRecruiting.index")}}'">
                    <div v-show="RegistrationData.loading" class="plugins-loading">
                        <div class="d-flex align-items-center justify-content-center w-100 h-100">
                            <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted"></i>
                        </div>
                    </div>
                    <i class="fa fa-folder-open fa-3x text-success"></i>
                    <div class="ms-3">
                        <h5 class="mb-2 iransans white-color">ثبت نام</h5>
                        <h1 class="mb-0 iranyekan bold-font text-success text-center" v-cloak>@{{RegistrationData.data?.count}}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2 align-self-stretch">
                <div class="bg-dark rounded d-flex align-items-center justify-content-between p-4 h-100 position-relative pointer-cursor" onclick="location.href='{{route("EmployeeRequestsAutomation.index")}}'">
                    <div v-show="AutomationsData.loading" class="plugins-loading">
                        <div class="d-flex align-items-center justify-content-center w-100 h-100">
                            <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted"></i>
                        </div>
                    </div>
                    <i class="fa fa-memo fa-3x text-success"></i>
                    <div class="ms-3">
                        <h5 class="mb-2 iransans white-color">اتوماسیون اداری</h5>
                        <h1 class="mb-0 iranyekan bold-font text-success text-center" v-cloak>@{{AutomationsData.data?.count}}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2 align-self-stretch">
                <div class="bg-dark rounded d-flex align-items-center justify-content-between p-4 h-100 position-relative pointer-cursor" onclick="location.href='{{route("Tickets.index")}}'">
                    <div v-show="TicketsData.loading" class="plugins-loading">
                        <div class="d-flex align-items-center justify-content-center w-100 h-100">
                            <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted"></i>
                        </div>
                    </div>
                    <i class="fa fa-headset fa-3x text-success"></i>
                    <div class="ms-3">
                        <h5 class="mb-2 iransans white-color">تیکت ها</h5>
                        <h1 class="mb-0 iranyekan bold-font text-success text-center" v-cloak>@{{TicketsData.data?.count}}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2 align-self-stretch">
                <div class="bg-dark rounded d-flex align-items-center justify-content-between p-4 h-100 position-relative pointer-cursor" onclick="location.href='{{route("RefreshDataEmployees.index")}}'">
                    <div v-show="RefreshesData.loading" class="plugins-loading">
                        <div class="d-flex align-items-center justify-content-center w-100 h-100">
                            <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted" v-cloak></i>
                        </div>
                    </div>
                    <i class="fa fa-user-pen fa-3x text-success"></i>
                    <div class="ms-3">
                        <h5 class="mb-2 iransans white-color">تایید اطلاعات</h5>
                        <h1 class="mb-0 iranyekan bold-font text-success text-center" v-cloak>@{{RefreshesData.data?.count}}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2 align-self-stretch">
                <div class="bg-dark rounded d-flex align-items-center justify-content-between p-4 h-100 position-relative">
                    <div v-show="ExpiredData.loading" class="plugins-loading">
                        <div class="d-flex align-items-center justify-content-center w-100 h-100">
                            <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted"></i>
                        </div>
                    </div>
                    <i class="fa fa-calendar-circle-exclamation fa-3x text-success"></i>
                    <div class="ms-3">
                        <h5 class="mb-2 iransans white-color">منقضی شده</h5>
                        <h1 class="mb-0 iranyekan bold-font text-success text-center" v-cloak>@{{ExpiredData.data?.count}}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2 align-self-stretch">
                <div class="bg-dark rounded d-flex align-items-center justify-content-between p-4 h-100 position-relative pointer-cursor" onclick="location.href='{{route("UnregisteredEmployees.index")}}'">
                    <div v-show="UnregisteredData.loading" class="plugins-loading">
                        <div class="d-flex align-items-center justify-content-center w-100 h-100">
                            <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted"></i>
                        </div>
                    </div>
                    <i class="fa fa-user-plus fa-3x text-success"></i>
                    <div class="ms-3">
                        <h5 class="mb-2 iransans white-color">پیش ثبت نام</h5>
                        <h1 class="mb-0 iranyekan bold-font text-success text-center" v-cloak>@{{UnregisteredData.data?.count}}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid pt-4 px-4 idle_plugins">
        <div class="row g-4">
            <div class="col-sm-12 col-xl-4 align-self-stretch">
                <div class="bg-secondary text-center rounded p-4 bg-dark h-100 position-relative">
                    <div v-show="RequestChart.loading" class="plugins-loading">
                        <div class="d-flex align-items-center justify-content-center w-100 h-100">
                            <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0 text-success iransans">تعداد درخواست های پرسنل به تفکیک ماه (6 ماهه اخیر)</h6>
                    </div>
                    <canvas id="requests_chart" width="427" height="213" style="display: block; box-sizing: border-box; height: 213px; width: 427px;"></canvas>
                </div>
            </div>
            <div class="col-sm-12 col-xl-4 align-self-stretch">
                <div class="bg-secondary text-center rounded p-4 bg-dark h-100 position-relative">
                    <div v-show="RegistrationChart.loading" class="plugins-loading">
                        <div class="d-flex align-items-center justify-content-center w-100 h-100">
                            <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0 text-success iransans">تعداد ثبت نام پرسنل به تفکیک ماه (6 ماهه اخیر)</h6>
                    </div>
                    <canvas id="registration_chart" width="427" height="213" style="display: block; box-sizing: border-box; height: 213px; width: 427px;"></canvas>
                </div>
            </div>
            <div class="col-sm-12 col-xl-4 align-self-stretch">
                <div class="bg-secondary text-center rounded p-4 bg-dark h-100 position-relative">
                    <div v-show="VisitChart.loading" class="plugins-loading">
                        <div class="d-flex align-items-center justify-content-center w-100 h-100">
                            <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0 text-success iransans">تعداد بازدید وبسایت به تفکیک ماه (6 ماهه اخیر)</h6>
                    </div>
                    <canvas id="visit_chart" width="427" height="213" style="display: block; box-sizing: border-box; height: 213px; width: 427px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid pt-4 px-4 idle_plugins">
        <div class="row g-4">
            @can('index','EmployeesRecruiting')
                <div class="col-sm-12 col-xl-6 align-self-stretch">
                    <div class="bg-secondary text-center rounded p-4 bg-dark h-100 position-relative">
                        <div v-show="RegistrationData.loading" class="plugins-loading">
                            <div class="d-flex align-items-center justify-content-center w-100 h-100">
                                <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0 white-color iransans bold-font text-success">
                                ثبت نام
                            </h6>
                            <a class="iransans" href="{{route("EmployeeRequestsAutomation.index")}}">نمایش همه</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-dark text-center align-middle table-hover mb-0 iransans white-color no-sort">
                                <thead>
                                <tr class="text-muted">
                                    <th scope="col">نام</th>
                                    <th scope="col">کد ملی</th>
                                    <th scope="col">سازمان</th>
                                    <th scope="col">تاریخ</th>
                                </tr>
                                </thead>
                                <tbody v-cloak v-if="RegistrationData.data?.count !== 0">
                                <tr v-for="(employee,index) in RegistrationData.data.records" :key="index">
                                    <td>@{{employee.name}}</td>
                                    <td>@{{employee.national_code}}</td>
                                    <td>@{{employee.contract.organization.name}}</td>
                                    <td v-text="PersianDateString(employee.created_at)"></td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <td colspan="5" class="text-center p-4">
                                        <small class="iranyekan text-muted">اطلاعاتی وجود ندارد</small>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endcan
            @can('index','EmployeeRequestsAutomation')
                <div class="col-sm-12 col-xl-6 align-self-stretch">
                    <div class="bg-secondary text-center rounded p-4 bg-dark position-relative h-100">
                        <div v-show="AutomationsData.loading" class="plugins-loading">
                            <div class="d-flex align-items-center justify-content-center w-100 h-100">
                                <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0 white-color iransans bold-font text-success">
                                اتوماسیون اداری
                            </h6>
                            <a class="iransans" href="{{route("EmployeeRequestsAutomation.index")}}">نمایش همه</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-center table-dark align-middle table-hover mb-0 iransans white-color no-sort">
                                <thead>
                                <tr class="text-muted">
                                    <th scope="col">نام</th>
                                    <th scope="col">سازمان</th>
                                    <th scope="col">نوع</th>
                                    <th scope="col">تاریخ</th>
                                </tr>
                                </thead>
                                <tbody v-cloak v-if="AutomationsData?.data?.count !== 0">
                                <tr v-for="(automation,index) in AutomationsData.data.records" :key="index">
                                    <td>@{{automation.employee.name}}</td>
                                    <td>@{{automation.employee.contract.organization.name}}</td>
                                    <td>@{{automation.application_name}}</td>
                                    <td v-text="PersianDateString(automation.created_at)"></td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <td colspan="5" class="text-center p-4">
                                        <small class="iranyekan text-muted">اطلاعاتی وجود ندارد</small>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
    <div class="container-fluid pt-4 px-4 pb-4 idle_plugins">
        <div class="row g-4">
            @can('index','UnregisteredEmployees')
                <div class="col-sm-12 col-md-6 col-xl-4 align-self-stretch">
                    <div class="h-100 bg-secondary rounded p-4 bg-dark position-relative h-100">
                        <div v-show="UnregisteredData.loading" class="plugins-loading">
                            <div class="d-flex align-items-center justify-content-center w-100 h-100">
                                <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0 iransans text-success">درخواست های پیش ثبت نام</h6>
                            <a class="iransans" href="{{route("UnregisteredEmployees.index")}}">نمایش همه</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-center table-dark align-middle table-hover mb-0 iransans white-color no-sort">
                                <thead>
                                <tr class="text-muted">
                                    <th scope="col">نام</th>
                                    <th scope="col">کد ملی</th>
                                    <th scope="col">سازمان</th>
                                    <th scope="col">تاریخ</th>
                                </tr>
                                </thead>
                                <tbody v-cloak v-if="UnregisteredData.data?.count !== 0">
                                <tr v-for="(employee,index) in UnregisteredData.data.records" :key="index">
                                    <td v-cloak>@{{employee.name}}</td>
                                    <td v-cloak>@{{employee.national_code}}</td>
                                    <td v-cloak>@{{employee.organization.name}}</td>
                                    <td v-cloak v-text="PersianDateString(employee.created_at)"></td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <td colspan="5" class="text-center p-4" style="border: none">
                                        <small class="iranyekan text-muted">اطلاعاتی وجود ندارد</small>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endcan
            @can('index','RefreshDataEmployees')
                <div class="col-sm-12 col-md-6 col-xl-4 position-relative align-self-stretch">
                    <div class="h-100 bg-secondary rounded p-4 bg-dark position-relative h-100">
                        <div v-show="RefreshesData.loading" class="plugins-loading">
                            <div class="d-flex align-items-center justify-content-center w-100 h-100">
                                <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0 iransans text-success">تایید اطلاعات</h6>
                            <a class="iransans" href="{{route("UnregisteredEmployees.index")}}">نمایش همه</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-center table-dark align-middle table-hover mb-0 iransans white-color no-sort">
                                <thead>
                                <tr class="text-muted">
                                    <th scope="col">نام</th>
                                    <th scope="col">کد ملی</th>
                                    <th scope="col">سازمان</th>
                                    <th scope="col">تاریخ</th>
                                </tr>
                                </thead>
                                <tbody v-cloak v-if="RefreshesData.data?.count !== 0">
                                <tr v-for="(employee,index) in RefreshesData.data.records" :key="index">
                                    <td>@{{employee.employee.name}}</td>
                                    <td>@{{employee.employee.national_code}}</td>
                                    <td>@{{employee.employee.contract.organization.name}}</td>
                                    <td v-text="PersianDateString(employee.created_at)"></td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <td colspan="5" class="text-center p-4" style="border: none">
                                        <small class="iranyekan text-muted">اطلاعاتی وجود ندارد</small>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endcan
            @can('index','Tickets')
                <div class="col-sm-12 col-md-6 col-xl-4 position-relative align-self-stretch">
                    <div v-cloak class="h-100 bg-secondary rounded p-4 bg-dark h-100">
                        <div v-show="TicketsData.loading" class="plugins-loading">
                            <div class="d-flex align-items-center justify-content-center w-100 h-100">
                                <i class="fad fa-spinner-third fa-1-6x fa-spin text-muted"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="mb-0 iransans text-success">تیکت های پشتیبانی</h6>
                            <a class="iransans" href="">نمایش همه</a>
                        </div>
                        <div v-cloak v-if="TicketsData.data?.count !== 0">
                            <div class="d-flex align-items-center border-bottom py-3" v-for="(ticket,index) in TicketsData.data.records" :key="index">
                                <img class="rounded-circle flex-shrink-0" src="{{asset("/images/ticket_user.png")}}" alt="" style="width: 40px; height: 40px;">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-0 iransans white-color">@{{ ticket.employee.name }}</h6>
                                        <small class="iransans text-muted" v-text="PersianDateString(ticket.updated_at,true)"></small>
                                    </div>
                                    <span class="iransans white-color">@{{ ticket.message }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="p-5 d-flex align-items-center justify-content-center">
                            <small class="iranyekan text-muted">اطلاعاتی وجود ندارد</small>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
    <div v-cloak class="container-fluid px-4 idle_plugins pb-3">
        <div class="bg-dark rounded p-4">
            <div class="d-flex flex-row align-items-center justify-content-between flex-wrap gap-2 gap-lg-0">
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center justify-content-start gap-2">
                        <img alt="همیاران شمال شرق" class="pb-1" style="width: 15px;height: auto" src="{{asset("/images/logo.png")}}">
                        <span class="iransans white-color">
                            همیاران شمال شرق, کلیه حقوق محفوظ است.
                            {{"(".verta()->format("F Y").")"}}
                        </span>
                    </div>
                    <span class="iransans text-muted">نسخه 3.0</span>
                </div>
                <div class="d-flex flex-column gap-2">
                    <span class="iransans white-color">طراحی و توسعه</span>
                    <span class="iransans text-muted">مسعود ملایریان</span>
                </div>
            </div>
        </div>
    </div>
@endsection
