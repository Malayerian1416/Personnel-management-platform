@extends("layouts.registration")
@section('content')
    <div id="registration" class="box shadow border-top-0 p-4">
        <div class="form-row">
            <div class="col-12 text-center">
                <i class="far fa-check-circle green-color fa-5x fa-beat-fade"></i>
            </div>
            <div class="col-12 text-center mt-4 mb-2">
                <h4 class="iranyekan text-center">ثبت نام شما با موفقیت انجام شد</h4>
            </div>
            <div class="col-12 text-center">
                <h5 class="iranyekan text-center">{{ "کد رهگیری : " . session("register.tracking_code") }}</h5>
                <h6 class="iranyekan text-muted text-center mt-3">«همکار گرامی؛ شما می توانید با وارد کردن کد رهگیری در قسمت پیگیری ثبت نام در صفحه ورود به سامانه، از نتیجه ثبت نام خود مطلع شوید»</h6>
            </div>
        </div>
    </div>
    @php(session()->forget("register.tracking_code"))
@endsection
