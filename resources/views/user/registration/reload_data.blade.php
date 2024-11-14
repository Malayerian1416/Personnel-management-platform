@extends("layouts.registration")
@section('content')
    <div id="registration" class="box wider shadow border-top-0 p-4">
        <div class="row">
            <div class="col-6 d-flex align-items-center justify-content-start">
                <i class="fa fa-arrow-right fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="tooltip" title="بازگشت" v-on:click="WindowRelocate('{{route("login")}}')"></i>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-end">
                <i class="fa fa-question-circle fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="tooltip" title="راهنما"></i>
            </div>
        </div>
        <h4 class="iranyekan text-left pt-2 pb-2 green-color" v-cloak>
            اصلاح و ارسال مجدد اطلاعات
        </h4>
        <div class="col-12 align-self-center text-center">
            <img class="introduction-image" alt="hello" src="{{ asset("/images/registration/reload_data.svg") }}" v-cloak/>
        </div>
        <div class="col-12 align-self-center">
            <form id="reload_data_form" method="post" action="{{ route('registration_reload_data',$reload_data->id) }}" enctype="multipart/form-data" v-on:submit="login">
                <h6 class="iransans bolder-font">لطفا اطلاعات درخواست شده توسط کارشناس ثبت نام را دوباره به صورت صحیح وارد و یا بارگذاری فرمایید:</h6>
                @csrf
                <div class="row">
                    @forelse($reload_data->docs as $doc)
                        <div class="col-12 col-lg-6 mb-3">
                            <label class="form-label iransans">
                                {{ $doc["name"] }}
                            </label>
                            <m-file-browser @error($doc["data"]) class="is-invalid is-invalid-fake" :error_class="'is-invalid'" @enderror file_box_id="{{$doc["data"]}}" file_box_name="{{$doc["data"]."[]"}}" filename_box_id="{{$doc["data"]."_box"}}" :accept='["jpg","jpeg","png","svg","gif","tiff","bmp"]' :size="2097152"></m-file-browser>
                            @error($doc["data"])
                            <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    @empty
                    @endforelse
                </div>
                <div class="row">
                    @forelse($reload_data->databases as $database)
                        <div class="col-12 col-lg-6 mb-3">
                            <label class="form-label iransans">
                                {{ $database["name"] }}
                            </label>
                            <input type="text" class="iransans form-control text-center b-form @error($database["data"]) is-invalid @enderror" name="{{$database["data"]}}">
                            @error($database["data"])
                            <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    @empty
                    @endforelse
                </div>
                @error('loaded')
                <div class="alert alert-danger iransans" role="alert">
                    اطلاعات قبلا بارگذاری شده است!
                </div>
                @enderror
                @error("logical_error")
                <div class="alert alert-danger iransans" role="alert">
                    مشکلی در سامانه ثبت نام رخ داده است. لطفا چند لحظه بعد مجددا اقدام فرمایید
                </div>
                @enderror
            </form>
            <button type="submit" form="reload_data_form" class="btn btn-success form-control b-form iranyekan login-button">
                <span id="login-button-text">ارسال اطلاعات</span>
                <i id="login-button-icon" class="fa fa-upload ms-2 fa-1-4x"></i>
            </button>
        </div>
    </div>
@endsection
