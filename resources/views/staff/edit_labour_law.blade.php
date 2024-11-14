@extends("staff.staff_dashboard")
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                تعرفه دستمزد پرسنل - قانون کار
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
        <form id="update_form" class="p-3" action="{{ route("LabourLaw.update",$labour_law->id) }}" method="POST" data-json="advantage_columns" v-on:submit="submit_form">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        عنوان
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control text-center iransans @error('name') is-invalid @enderror" type="text" name="name" value="{{ $labour_law->name }}">
                    @error('name')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        سال
                        <strong class="red-color">*</strong>
                    </label>
                    <select class="form-control iransans selectpicker-select @error('effective_year') is-invalid @enderror" name="effective_year">
                        @for($i = 4; $i >= 0; $i--)
                            <option @if(verta()->subYears($i)->format("Y") == $labour_law->effective_year) selected @endif value="{{ verta()->subYears($i)->format("Y") }}">{{ verta()->subYears($i)->format("Y") }}</option>
                        @endfor
                    </select>
                    @error('effective_year')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        دستمزد روزانه
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control text-center iransans @error('daily_wage') is-invalid @enderror thousand_separator" type="text" name="daily_wage" value="{{ $labour_law->daily_wage }}" v-on:input="ChildAllowanceCalculate">
                    @error('daily_wage')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        کمک هزینه اقلام مصرفی خانوار
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control text-center iransans @error('household_consumables_allowance') is-invalid @enderror thousand_separator" type="text" name="household_consumables_allowance" value="{{ $labour_law->household_consumables_allowance }}">
                    @error('household_consumables_allowance')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        کمک هزینه مسکن
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control text-center iransans @error('housing_purchase_allowance') is-invalid @enderror thousand_separator" type="text" name="housing_purchase_allowance" value="{{ $labour_law->housing_purchase_allowance }}">
                    @error('housing_purchase_allowance')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        حق تاهل
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control text-center iransans @error('marital_allowance') is-invalid @enderror thousand_separator" type="text" name="marital_allowance" value="{{ $labour_law->marital_allowance }}">
                    @error('marital_allowance')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        حق اولاد
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control text-center iransans @error('child_allowance') is-invalid @enderror thousand_separator" type="text" name="child_allowance" value="{{ $labour_law->child_allowance }}">
                    @error('child_allowance')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
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
        <a role="button" href="{{ route("LabourLaw.index") }}"
           class="btn btn-outline-secondary iransans">
            <i class="fa fa-arrow-turn-right fa-1-2x me-1"></i>
            <span class="iransans">بازگشت به لیست</span>
        </a>
    </div>
@endsection
