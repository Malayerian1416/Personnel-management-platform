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
            گام آخر - بارگذاری تصویر مدارک
        </h4>
        <div class="col-12 align-self-center text-center">
            <img class="introduction-image" alt="hello" src="{{ asset("/images/registration/registration_image_6.svg") }}"/>
        </div>
        @if($docs)
            <div class="fieldset">
                    <span class="legend">
                        تصویر مدارک بارگذاری شده
                    </span>
                <div class="fieldset-body">
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-5">
                        @forelse($docs as $doc)
                            <div class="col text-center">
                                <img class="registration-doc" alt="همیاران شمال شرق" src="{{ "data:image/jpg;base64,$doc" }}"/>
                            </div>
                        @empty
                        @endforelse
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <a role="button" href="{{route("step_six",["clear"])}}" class="w-100 btn btn-sm btn-outline-danger iransans">
                                حذف تصاویر بارگذاری شده
                                <i class="fa fa-trash-can fa-1-2x ms-2 vertical-middle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <a href="{{route("check_up")}}" role="button" class="btn btn-success form-control iranyekan login-button mt-3">
                <span id="login-button-text">گام بعدی</span>
                <i id="login-button-icon" class="fa fa-arrow-left ms-2 fa-1-4x"></i>
            </a>
        @else
            <div class="col-12 align-self-center">
                <form id="registration_form" method="POST" action="{{ route('store_image_documents') }}" enctype="multipart/form-data" v-on:submit="login">
                    @csrf
                    <div class="row">
                        <div class="form-group mb-3 col-12 col-lg-6 mid-white-color">
                            <label class="form-label iranyekan text-muted" for="upload_file">صفحات شناسنامه (همه صفحات به صورت فایل های جدا)</label>
                            <m-file-browser :accept='["jpg","jpeg","png","svg","gif","tiff","bmp"]' :file_box_name="'birth_certificate[]'"  @error('birth_certificate') class="is-invalid is-invalid-fake" :error_class="'is-invalid'" @enderror :size="2097152" :input_class="'registration-input-text'"></m-file-browser>
                            @error('birth_certificate')
                            <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3 col-12 col-lg-6">
                            <label class="form-label iranyekan text-muted" for="upload_file">کارت ملی (هر دو سمت در یک تصویر)</label>
                            <s-file-browser :accept='["jpg","jpeg","png","svg","gif","tiff","bmp"]' :file_box_name="'national_card'" :size="2097152" @error('national_card') class="is-invalid is-invalid-fake" :error_class="'is-invalid'" @enderror :input_class="'registration-input-text'"></s-file-browser>
                            @error('national_card')
                            <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        @if(session('register.gender') == "m")
                            <div class="form-group mb-3 col-12 col-lg-6">
                                <input type="hidden" name="male_gender" value="true">
                                <label class="form-label iranyekan text-muted" for="upload_file">کارت پایان خدمت (هر دو سمت در یک تصویر)</label>
                                <s-file-browser :accept='["jpg","jpeg","png","svg","gif","tiff","bmp"]' :file_box_name="'military_certificate'" :size="2097152" @error('military_certificate') class="is-invalid is-invalid-fake" :error_class="'is-invalid'" @enderror :input_class="'registration-input-text'"></s-file-browser>
                                @error('military_certificate')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                        <div class="form-group mb-3 col-12 col-lg-6">
                            <label class="form-label iranyekan text-muted" for="upload_file">آخرین مدرک تحصیلی</label>
                            <s-file-browser :accept='["jpg","jpeg","png","svg","gif","tiff","bmp"]' :file_box_name="'education_certificate'" :size="2097152" @error('education_certificate') class="is-invalid is-invalid-fake" :error_class="'is-invalid'" @enderror :input_class="'registration-input-text'"></s-file-browser>
                            @error('education_certificate')
                            <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3 col-12 col-lg-6">
                            <label class="form-label iranyekan text-muted" for="upload_file">عکس پرسنلی</label>
                            <s-file-browser :accept='["jpg","jpeg","png","svg","gif","tiff","bmp"]' :size="2097152" @error('personal_photo') class="is-invalid is-invalid-fake" :error_class="'is-invalid'" @enderror :file_box_name="'personal_photo'" :input_class="'registration-input-text'"></s-file-browser>
                            @error('personal_photo')
                            <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        @if(session('register.insurance') != null)
                            <div class="form-group mb-3 col-12 col-lg-6">
                                <input type="hidden" name="insurance_confirmed" value="true">
                                <label class="form-label iranyekan text-muted" for="upload_file">تاییدیه بیمه</label>
                                <s-file-browser :accept='["jpg","jpeg","png","svg","gif","tiff","bmp"]' :size="2097152" @error('insurance_confirmation') class="is-invalid is-invalid-fake" :error_class="'is-invalid'" @enderror :file_box_name="'insurance_confirmation'" :input_class="'registration-input-text'"></s-file-browser>
                                @error('insurance_confirmation')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                    @error('sms_fail')
                    <div class="alert alert-danger iransans" role="alert">
                        {{ $message }}
                    </div>
                    @enderror
                    @error('logical_error')
                    <div class="alert alert-danger iransans mt-3" role="alert">
                        مشکلی در سامانه ثبت نام رخ داده است. لطفا چند لحظه بعد مجددا اقدام فرمایید
                    </div>
                    @enderror
                    <div class="col-12 text-center pt-2 pb-2 d-flex align-items-center justify-content-center">
                        <i class="@if(Route::is("step_one")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                        <i class="@if(Route::is("step_two")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                        <i class="@if(Route::is("step_three")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                        <i class="@if(Route::is("step_four")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                        <i class="@if(Route::is("step_five")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                        <i class="@if(Route::is("step_six")) fas @else far @endif fa-circle green-color ms-1 fa-1-2x"></i>
                    </div>
                    <button type="submit" form="registration_form" class="btn btn-success form-control iranyekan login-button">
                        <span id="login-button-text">بارگذاری تصویر مدارک</span>
                        <i id="login-button-icon" class="fa fa-upload ms-2 fa-1-4x"></i>
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
@section('modal')
    <help-modal :title="'راهنمای بارگذاری فایل های تصویر مدارک'" :modal_id="'help_modal'" v-cloak>
        <h5 class="iransans mb-3 border border-danger p-3 red-color text-justify">بارگذاری اطلاعات در این مرحله و با توجه به حجم فایل های انتخاب شده، ممکن است کمی زمان بر باشد؛ لذا خواهشمندیم تا ارسال تمامی اطلاعات و دریافت کد رهگیری تامل نموده از بستن صفحه مرورگر خود اجتناب کنید.</h5>
        <ul class="iranyekan free-ul">
            <li>
                <p class="free-p text-justify pe-3">
                    لطفا تمامی مدارک را با دستگاه اسکنر اسکن نموده و هرگز از دوربین گوشی خود برای تهیه اسکن مدارک استفاده نکنید. بدیهی است تصاویری که با دوربین گوشی ایجاد شده باشند، مورد تایید قرار نخواهند گرفت.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    کیفیت تصاویر باید به گونه ای باشد که تمامی اطلاعات مندرج در آن قابل خواندن باشد؛ در صورت عدم خوانایی اطلاعات مندرج در تصاویر، ثبت نام شما تایید نخواهد شد.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    فرمت های فایل قابل قبول به ازای هر فایل عبارتند از : <strong class="red-color">jpg, jpeg, png, svg, gif, tiff, bmp</strong>
                    و فرمت دیگری قابل قبول نمی باشد.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    حداکثر حجم مجاز برای بارگذاری به ازای هر فایل،  <strong class="red-color">2 مگابایت</strong>
                    می باشد.
                </p>
            </li>
            <li>
                <p class="free-p text-justify pe-3">
                    برای بارگذاری صفحات شناسنامه مطابق تصویر نمونه زیر، باید تصویر هر صفحه و یا هر دو صفحه در یک فایل باشد.در نتیجه امکان انتخاب چند فایل به طور همزمان در جعبه انتخاب فایل شناسنامه میسر است.
                </p>
                <div class="w-100 text-center">
                    <img alt="help" class="img-fluid" src="{{ asset("/images/help/h_register_6_1.png?v=dda") }}">
                </div>
            </li>
            <li class="mt-4">
                <p class="free-p text-justify pe-3">
                    تصویر کارت ملی و دوره ضروری خدمت سربازی باید مطابق تصویر نمونه زیر، هر دو روی کارت در یک فایل کنار همدیگر و یا در بالا و پایین یک دیگر قرار گیرد.
                </p>
                <div class="w-100 text-center">
                    <img alt="help" class="img-fluid" src="{{ asset("/images/help/h_register_6_2.png?v=dda") }}">
                </div>
            </li>
            <li class="mt-4">
                <p class="free-p text-justify pe-3">
                    جعبه انتخاب فایل تصویر مدارک دوره ضروری خدمت سربازی و سابقه بیمه به ترتیب فقط در صورت انتخاب جنسیت مرد و درج سابقه بیمه نمایش داده خواهند شد.
                </p>
            </li>
            <li class="mt-4">
                <p class="free-p text-justify pe-3">
                    تصویر هر مدرک باید به صورت کامل و بدون پشت زمینه و سایز تمام تصویر تهیه گردد؛ تصاویری که مطابق نمونه اشتباه زیر ارسال شوند، مورد تایید قرار نخواهند گرفت.
                </p>
                <div class="w-100 text-center">
                    <img alt="help" class="img-fluid" src="{{ asset("/images/help/h_register_6_3.png?v=ffd34") }}">
                </div>
            </li>
        </ul>
    </help-modal>
@endsection
