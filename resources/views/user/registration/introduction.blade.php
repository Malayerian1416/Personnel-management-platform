@extends("layouts.introduction")
@section('content')
    <div id="introduction" class="box shadow border-top-0 p-4">
        <div class="row">
            <div class="col-6 d-flex align-items-center justify-content-start">
                <i class="fa fa-arrow-right fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="tooltip" title="بازگشت" v-on:click="WindowRelocate('{{route("login")}}')"></i>
            </div>
        </div>
        <div class="align-self-center text-center">
            <img v-if="registration_intro_section === 1" class="introduction-image" alt="hello" src="{{ asset("/images/help/registration_intro_1.svg") }}" v-cloak/>
            <img v-if="registration_intro_section === 2" class="introduction-image" alt="help" src="{{ asset("/images/help/registration_intro_2.svg") }}" v-cloak/>
            <img v-if="registration_intro_section === 3" class="introduction-image" alt="agreement" src="{{ asset("/images/help/registration_intro_3.svg") }}" v-cloak/>
        </div>
        <div class="col-12 align-self-center">
            <h4 v-if="registration_intro_section === 1" class="iranyekan text-right mb-3 pr-2 pl-2 pr-md-3 pl-md-3 green-color" v-cloak>
                به سامانه ثبت نام پرسنل خوش آمدید!
            </h4>
            <h4 v-if="registration_intro_section === 2" class="iranyekan text-right mb-4 pr-2 pl-2 pr-md-3 pl-md-3 green-color" v-cloak>
                کاربر گرامی؛
            </h4>
            <h4 v-if="registration_intro_section === 3" class="iranyekan text-right mb-4 pr-2 pl-2 pr-md-3 pl-md-3 green-color" v-cloak>
                توافق نامه کاربر نهایی سامانه
            </h4>
            <h6 v-if="registration_intro_section === 1" class="iransans introduction-description text-right pr-2 pl-2 pr-md-3 pl-md-3 text-justify" v-cloak>
                بسیار خرسندیم که در راه افتخارآمیز خدمت رسانی به شما پرسنل معزز و گرانقدر از تمامی ظرفیت‌ها و زیرساخت‌های ممکن استفاده کرده ایم و خوشبختانه در این مسیر توفیقات بیشماری نیز حاصل شده است که بی تردید تمامی آن در نتیجه لطف خداوند متعال و صبر و حمایت ارزنده شما عزیزان بوده است.
            </h6>
            <h6 v-if="registration_intro_section === 2" class="iransans text-right introduction-description pr-2 pl-2 pr-md-3 pl-md-3 text-justify" v-cloak>
                -
                در طول مراحل ثبت نام، جهت توضیح درباره قوانین و ساختار صحیح ورود اطلاعات، با کلیک کردن بر روی نمایه تعبیه شده به شکل
                <i class="fa fa-question-circle fa-1-4x me-1 ms-1 info-color vertical-middle"></i>
                و مشاهده نکات آن می توانید از بروز مشکلات و مغایرت ها در اطلاعات ورودی و در نتیجه عدم تایید ثبت نام شما توسط کارشناس سازمان جلوگیری به عمل آورید.
            </h6>
            <h6 v-if="registration_intro_section === 2"  class="iransans text-right introduction-description pr-2 pl-2 pr-md-3 pl-md-3 text-justify" v-cloak>
                -
                در هر مرحله از ثبت نام، با کلیک کردن بر روی نمایه تعبیه شده به شکل
                <i class="fa fa-arrow-right fa-1-4x info-color me-1 ms-1 vertical-middle"></i>
                می توانید به اطلاعات وارد شده در مرحله قبل دسترسی پیدا کرده و آن ها را ویرایش نمایید. بدیهی است در صورت ویرایش شماره تلفن همراه، مراحل فعال سازی آن تکرار خواهد شد.
            </h6>
            <div v-if="registration_intro_section === 3" class="iransans form-control b-form w-100 pr-2 pl-2 pr-md-3 pl-md-3 elua-container" v-cloak>
                <p class="text-justify free-p" style="line-height: 15px">
                    <b>در صورت بروز هریک از اعمال غیر قانونی ذیل، شرکت همیاران شمال شرق مجاز می باشد که از کلیه منابع و اطلاعات موجود پرسنل در سامانه در مراجع قضایی و اداری استفاده نماید:</b>
                </p>
                <ul class="free-ul">
                    <li><p class="text-justify free-p">هرگونه سوءاستفاده از نامه های اداری که به تایید مدیرعامل سازمان نرسیده باشد.</p></li>
                    <li><p class="text-justify free-p">جعل انواع نامه های اداری و تغییر تاریخ اعتبار آن و یا امضای مدیرعامل.</p></li>
                    <li><p class="text-justify free-p"> سوءاستفاده از گواهی اشتغال به کار در موءسساتی که مورد تایید شرکت همیاران شمال شرق نمی باشند.</p></li>
                    <li><p class="text-justify free-p"> هرگونه استفاده از فیش حقوقی به غیر از دریافت وام از موسسات مورد تایید شرکت همیاران شمال شرق که گواهی کسر از حقوق درخواست شده در سامانه به تایید شرکت رسیده باشد.</p></li>
                </ul>
            </div>
            <div v-if="registration_intro_section === 3" class="mt-3 pr-2 pl-2 pr-md-3 pl-md-3 text-right" v-cloak>
                <input type="checkbox" v-model="end_user_checkbox" class="vertical-middle" id="elua">
                <label for="elua" class="iransans d-inline">اینجانب تمامی مفاد توافق نامه را مطالعه کرده و موافقت خود را با آن اعلام می نمایم</label>
            </div>
        </div>
        <div class="col-12 text-center">
            <i v-if="registration_intro_section !== 1" class="far fa-circle green-color"></i>
            <i v-else class="fas fa-circle green-color"></i>
            <i v-if="registration_intro_section !== 2" class="far fa-circle green-color"></i>
            <i v-else class="fas fa-circle green-color"></i>
            <i v-if="registration_intro_section !== 3" class="far fa-circle green-color"></i>
            <i v-else class="fas fa-circle green-color"></i>
        </div>
        <div class="col-12 align-self-center">
            <div v-if="registration_intro_section <= 2" class="w-100 text-center mt-3 pr-2 pl-2 pr-md-3 pl-md-3" v-cloak>
                <button class="btn btn-outline-success iranyekan w-100" v-on:click="registration_intro_section++">
                    <span>ادامه</span>
                </button>
            </div>
            <div v-if="registration_intro_section === 3 && end_user_checkbox" class="w-100 text-center mt-3 pr-2 pl-2 pr-md-3 pl-md-3" v-cloak>
                <a role="button" href="{{ route("step_one") }}" class="btn btn-success iranyekan w-100">
                    <span>شروع ثبت نام</span>
                    <i class="fa fa-play-circle fa-1-2x ms-2"></i>
                </a>
            </div>
        </div>
    </div>
@endsection
