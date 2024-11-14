@extends("layouts.registration")
@section('content')
    <div id="registration" class="box shadow wider border-top-0 p-4">
        <div class="row">
            <div class="col-6 d-flex align-items-center justify-content-start">
                <i class="fa fa-arrow-right fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="tooltip" title="بازگشت" v-on:click="WindowRelocate('{{route("step_five")}}')"></i>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-end">
                <i class="fa fa-question-circle fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="modal" title="راهنما" data-bs-target="#help_modal"></i>
            </div>
        </div>
        <h4 class="iranyekan text-left pb-2 pt-2 green-color">
            تایید و ثبت نهایی اطلاعات
        </h4>
        <div class="col-12 align-self-center">
            <form id="registration_form" method="POST" action="{{ route('register_employee') }}" enctype="multipart/form-data" v-on:submit="login">
                @csrf
                <div class="fieldset">
                    <span class="legend">
                        اطلاعات شخصی
                        <a href="{{route("step_four")}}" class="iransans" style="color: deepskyblue">(ویرایش)</a>
                    </span>
                    <div class="fieldset-body">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">
                            <div class="col">
                                <span class="iransans me-2 text-muted">نام</span>
                                <span class="iranyekan bold-font">{{$data["first_name"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">نام خانوادگی</span>
                                <span class="iranyekan bold-font">{{$data["last_name"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">نام پدر</span>
                                <span class="iranyekan bold-font">{{$data["father_name"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">تاریخ تولد</span>
                                <span class="iranyekan bold-font">{{$data["birth_date"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">محل تولد</span>
                                <span class="iranyekan bold-font">{{$data["birth_city"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">محل صدور</span>
                                <span class="iranyekan bold-font">{{$data["issue_city"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">شماره شناسنامه</span>
                                <span class="iranyekan bold-font">{{$data["id_number"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">کد ملی</span>
                                <span class="iranyekan bold-font">{{$data["national_code"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">جنسیت</span>
                                <span class="iranyekan bold-font">
                                    {{$data["gender"] == "m" ? "مرد" : "زن"}}
                                </span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">وضعیت تاهل</span>
                                <span class="iranyekan bold-font">
                                    {{$data["marital_status"] == "m" ? "متاهل" : "مجرد"}}
                                </span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">وضعیت خدمت سربازی</span>
                                <span class="iranyekan bold-font">
                                    {{($data["military_status"] == "h" ? "کارت پایان خدمت" : $data["military_status"] == "e") ? "معاف" : "در حال تحصیل"}}
                                </span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">تحصیلات</span>
                                <span class="iranyekan bold-font">{{$data["education"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">تعداد فرزندان</span>
                                <span class="iranyekan bold-font">{{$data["children_count"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">تعداد فرزندان مشمول</span>
                                <span class="iranyekan bold-font">{{$data["included_children_count"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">ایمیل</span>
                                <span class="iranyekan bold-font">
                                    {{$data["email"] == "" || $data["email"] == null ?: "-"}}
                                </span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">موبایل</span>
                                <span class="iranyekan bold-font">{{$data["mobile"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">تلفن</span>
                                <span class="iranyekan bold-font">
                                    {{$data["phone"] ?: "-"}}
                                </span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">آدرس</span>
                                <span class="iranyekan bold-font">{{$data["address"]}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fieldset">
                    <span class="legend">
                        اطلاعات شغلی و بانکی
                        <a href="{{route("step_five")}}" class="iransans" style="color: deepskyblue">(ویرایش)</a>
                    </span>
                    <div class="fieldset-body">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">
                            <div class="col">
                                <span class="iransans me-2 text-muted">محل استقرار</span>
                                <span class="iranyekan bold-font">{{$data["job_seating"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">عنوان شغل</span>
                                <span class="iranyekan bold-font">{{$data["job_title"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">نام بانک</span>
                                <span class="iranyekan bold-font">{{$data["bank_name"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">شماره حساب</span>
                                <span class="iranyekan bold-font">{{$data["bank_account"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">شماره بیمه</span>
                                <span class="iranyekan bold-font">{{$data["insurance_number"] ?: "-"}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">سابقه بیمه (روز)</span>
                                <span class="iranyekan bold-font">{{$data["insurance_days"] ?: "-"}}</span>
                            </div>
                        </div>
                        <div class="row row-cols-1 row-cols-md-2">
                            <div class="col">
                                <span class="iransans me-2 text-muted">شماره شبا</span>
                                <span class="iranyekan bold-font">{{$data["sheba_number"]}}</span>
                            </div>
                            <div class="col">
                                <span class="iransans me-2 text-muted">شماره کارت</span>
                                <span class="iranyekan bold-font">{{$data["credit_card"] ?: "-"}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fieldset">
                    <span class="legend">
                        تصویر مدارک بارگذاری شده
                        <a href="{{route("step_six")}}" class="iransans" style="color: deepskyblue">(بارگذاری مجدد)</a>
                    </span>
                    <div class="fieldset-body">
                        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-5">
                            @forelse($docs as $doc)
                                <div class="col text-center align-content-stretch">
                                    <img class="registration-doc" alt="همیاران شمال شرق" src="{{ "data:image/jpg;base64,$doc" }}"/>
                                </div>
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="w-100 text-center mt-3 pr-2 pl-2 pr-md-3 pl-md-3">
                    <button type="submit" form="registration_form" class="btn btn-success form-control iranyekan login-button">
                        <span id="login-button-text">تایید اطلاعات و ثبت نام</span>
                        <i id="login-button-icon" class="far fa-check-circle ms-2 fa-1-4x"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('modal')
    <help-modal :title="'راهنمای ویرایش اطلاعات'" :modal_id="'help_modal'" v-cloak>
        <ul class="iranyekan free-ul">
            <li>
                <p class="free-p text-justify pe-3">
                    در این قسمت کلیه اطلاعات وارد شده به منظور تایید و صحت سنجی به شما نمایش داده می شود.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    جهت ویرایش هر قسمت، با کلیک کردن برروی گزینه ویرایش می توانید مجددا اطلاعات آن گام را ویرایش نمایید.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    جهت بارگذاری مجدد تصاویر، با کلیک کردن برروی گزینه بارگذاری مجدد می توانید نسبت به حذف تصاویر قبلی و بارگذاری مجدد آن اقدام نمایید.
                </p>
            </li>
        </ul>
    </help-modal>
@endsection
