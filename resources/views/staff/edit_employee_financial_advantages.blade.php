@extends("staff.staff_dashboard")
@section('variables')
    <script>
        const allowed_organizations = @json($organizations);
        const advantage_columns_data = @json(json_decode($financial_info->advantages),true);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                مقادیر مالی احکام کارگزینی
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
    <div class="page-content w-100">
        <form id="update_form" class="p-3" action="{{ route("EmployeeFinancialAdvantages.update",$financial_info->id) }}" method="POST" data-json="advantage_columns" v-on:submit="submit_form">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="alert alert-secondary iransans" role="alert">
                        <span class="iransans fw-bold">{{"اطلاعات مالی "."{$financial_info->employee->name} {$financial_info->employee->national_code} (سال موثر =  {$financial_info->effective_year})"}}</span>
                    </div>
                </div>
                <div class="col-12 mb-2">
                    <label class="form-label iransans">دستمزد روزانه</label>
                    <input class="form-control text-center iransans thousand_separator" type="text" value="{{$financial_info->daily_wage}}" id="daily_wage" name="daily_wage">
                </div>
                <div class="col-12 mb-2">
                    <label class="form-label iransans">پایه سنوات</label>
                    <input class="form-control text-center iransans thousand_separator" type="text" value="{{$financial_info->prior_service}}" id="prior_service" name="prior_service">
                </div>
                <div class="col-12 mb-2">
                    <label class="form-label iransans">روزهای کارکرد</label>
                    <input class="form-control text-center iransans" type="number" max="31" min="1" value="{{$financial_info->working_days}}" id="working_days" name="working_days" v-on:input="parseInt($event.target.value) > 31 ? $event.target.value = 31 : ''">
                </div>
                <div class="col-12 mb-2">
                    <label class="form-label iransans">گروه شغلی</label>
                    <input class="form-control text-center iransans" type="number" max="20" min="1" value="{{$financial_info->occupational_group}}" id="occupational_group" name="occupational_group" v-on:input="parseInt($event.target.value) > 20 ? $event.target.value = 20 : ''">
                </div>
                <div class="col-12 mb-2">
                    <label class="form-label iransans">فرزندان تحت تکفل</label>
                    <input class="form-control text-center iransans" type="number" max="20" min="0" value="{{$financial_info->count_of_children}}" id="count_of_children" name="count_of_children" v-on:input="parseInt($event.target.value) > 20 ? $event.target.value = 20 : ''">
                </div>
                <div class="col-12 mb-2">
                    <label class="form-label iransans mb-1">سال مؤثر</label>
                    <select class="form-control iransans selectpicker-select" name="effective_year">
                        @for($i = 5; $i >= 0; $i--)
                            <option @if(verta()->format("Y") == $financial_info->effective_year) selected @endif value="{{ verta()->subYears($i)->format("Y") }}">{{ verta()->subYears($i)->format("Y") }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 mb-2">
                    <label class="form-label iransans">افزودن مزایا</label>
                    <div class="input-group">
                        <input id="advantage_title" class="form-control text-center iransans" type="text" placeholder="عنوان مزایا">
                        <input id="advantage_value" class="form-control text-center iransans thousand_separator" type="text" placeholder="مبلغ مزایا">
                        <button type="button" class="btn btn-sm btn-outline-primary input-group-text pe-3 ps-3" v-on:click="AddAdvantage"><i class="fa fa-plus fa-1-2x"></i></button>
                    </div>
                </div>
                <div class="col-12 mb-2">
                    <label class="form-label iransans">جدول مزایا</label>
                    <ul class="list-group">
                        <li v-if="advantage_columns.length === 0" class="list-group-item d-flex align-items-center justify-content-center">
                            <span class="iransans">عنوانی اضافه نشده است</span>
                        </li>
                        <li v-for="(advantage,index) in advantage_columns" :key="index" class="list-group-item d-flex align-items-center justify-content-center flex-column gap-2">
                            <div class="input-group">
                                <input class="iransans text-center form-control" :value="advantage.title" v-on:input="advantage.title = $event.target.value">
                                <input :id="`advantage_value_${index}`" class="iransans text-center form-control thousand_separator" :value="advantage.value" v-on:input="advantage.value = $event.target.value">
                                <button type="button" class="btn btn-sm btn-outline-danger input-group-text pe-3 ps-3" v-on:click="advantage_columns.splice(index,1)"><i class="fa fa-trash-can fa-1-2x"></i></button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('footer')
    <div class="content-footer-container d-flex align-items-center justify-content-center gap-3 flex-wrap">
        <button type="submit" form="update_form" class="btn btn-success submit_button">
            <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
            <span class="iransans">ارسال و ویرایش</span>
        </button>
        <a role="button" href="{{ route("EmployeeFinancialAdvantages.index") }}"
           class="btn btn-outline-secondary iransans">
            <i class="fa fa-arrow-turn-right fa-1-2x me-1"></i>
            <span class="iransans">بازگشت به لیست</span>
        </a>
    </div>
@endsection
