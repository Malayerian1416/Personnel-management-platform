@extends("staff.staff_dashboard")
@section('variables')
    <script>
        const payslip_employees_data = @json(json_decode($payslip->contents,true));
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                فیش حقوقی پرسنل
                <span class="vertical-middle ms-1 text-muted">ویرایش</span>
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
    <div class="page-content edit w-100">
        <form id="update_form" action="{{ route("EmployeePaySlips.update",$payslip->id) }}" data-json="payslip_employees" method="POST" v-on:submit="submit_form">
            @csrf
            @method('PUT')
            <div class="ps-3 pe-3 pt-4 pb-4 mb-3 alert alert-success iransans rounded-2 d-flex flex-row flex-wrap justify-content-start align-items-center gap-3">
                <span class="iransans">{{ "ویرایش مفاد فیش حقوقی " }}<b class="green-color">{{ "{$payslip->employee->name}({$payslip->employee->national_code})" }}</b></span>
                <span class="iransans">{{ "[ // ".$payslip->employee->contract->organization->name }}</span>
                <span class="iransans">{{ "// ".$payslip->employee->contract->name }}</span>
                <span class="iransans">{{ "// {$payslip->persian_month_name} ماه {$payslip->persian_year} ]" }}</span>
            </div>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">کارکرد</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div v-for="(payslip,index) in payslip_employees" v-if="payslip.type === 'function'" class="col-12 col-lg-2" :key="index">
                            <label class="form-label iransans">@{{ payslip.title }}</label>
                            <input type="text" class="form-control text-center iransans" :class="payslip.isNumber ? 'thousand_separator' : ''" :value="payslip.value" v-on:input="payslip_employees[index].value = Number($event.target.value.replaceAll(',',''))">
                        </div>
                    </div>
                </div>
            </div>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">مزایا</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div v-for="(payslip,index) in payslip_employees" v-if="payslip.type === 'advantage'" class="col-12 col-lg-2" :key="index">
                            <label class="form-label iransans">@{{ payslip.title }}</label>
                            <input type="text" class="form-control text-center iransans" :class="payslip.isNumber ? 'thousand_separator' : ''" :value="payslip.value" v-on:input="payslip_employees[index].value = Number($event.target.value.replaceAll(',',''))">
                        </div>
                    </div>
                </div>
            </div>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">کسورات</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div v-for="(payslip,index) in payslip_employees" class="col-12 col-lg-2" v-if="payslip.type === 'deduction'" :key="index">
                            <label class="form-label iransans">@{{ payslip.title }}</label>
                            <input type="text" class="form-control text-center iransans" :class="payslip.isNumber ? 'thousand_separator' : ''" :value="payslip.value" v-on:input="payslip_employees[index].value = Number($event.target.value.replaceAll(',',''))">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section("footer")
    <div class="content-footer-container d-flex align-items-center justify-content-center gap-3 flex-wrap">
        <button type="submit" form="update_form" class="btn btn-success submit_button">
            <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
            <span class="iransans">ارسال و ویرایش</span>
        </button>
        <a role="button" href="{{ route("EmployeePaySlips.index") }}"
           class="btn btn-outline-secondary iransans">
            <i class="fa fa-arrow-turn-right fa-1-2x me-1"></i>
            <span class="iransans">بازگشت به لیست</span>
        </a>
    </div>
@endsection
