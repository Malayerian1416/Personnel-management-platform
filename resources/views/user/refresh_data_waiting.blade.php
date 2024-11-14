@extends("layouts.registration")
@section('content')
    <div id="registration" class="box shadow border-top-0 p-4">
        <div class="form-row">
            <div class="col-12 text-center">
                <i class="far fa-clock green-color fa-5x fa-beat-fade"></i>
            </div>
            <div class="col-12 text-center mt-4 mb-2">
                <h5 class="iranyekan text-center">{{"بارگذاری اطلاعات اصلاحی درخواست شده توسط کارشناس در تاریخ ".$reload_date." با موفقیت انجام شده است"}}</h5>
                <h6 class="iransans">لطفا تا بازبینی و صحت سنجی اطلاعات ارسال شده توسط کارشناس منتظر بمانید</h6>
            </div>
            <div class="col-12 mt-5 text-center">
                <a role="button" href="{{ route("logout") }}" class="btn btn-success iransans">بازگشت</a>
            </div>
        </div>
    </div>
@endsection
