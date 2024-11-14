<style>
    @font-face {
        font-family: 'Iransans';
        font-style: normal;
        font-weight: normal;
        src: url({{ storage_path('/fonts/iransans.ttf') }}) format('truetype');
    }
    @font-face {
        font-family: 'Iranyekan';
        font-style: normal;
        font-weight: normal;
        src: url({{ storage_path('/fonts/iranyekan.ttf') }}) format('truetype');
    }
    @font-face {
        font-family: 'Mitra';
        font-style: normal;
        font-weight: normal;
        src: url({{ storage_path('/fonts/mitra.ttf') }}) format('truetype');
    }
    @font-face {
        font-family: 'Nastaliq';
        font-style: normal;
        font-weight: normal;
        src: url({{ storage_path('/fonts/nastaliq.ttf') }}) format('truetype');
    }
    @font-face {
        font-family: 'Nazanin';
        font-style: normal;
        font-weight: normal;
        src: url({{ storage_path('/fonts/nazanin.ttf') }}) format('truetype');
    }
    @font-face {
        font-family: 'Nazanin';
        font-style: normal;
        font-weight: bold;
        src: url({{ storage_path('/fonts/nazanin_bold.ttf') }}) format('truetype');
    }
    @font-face {
        font-family: 'Titr';
        font-style: normal;
        font-weight: normal;
        src: url({{ storage_path('/fonts/titr.ttf') }}) format('truetype');
    }
    .iransans{
        font-family: Iransans,'sans-serif';
    }
    .iranyekan{
        font-family: Iranyekan,'sans-serif';
    }
    .nazanin{
        font-family: Nazanin,'sans-serif';
    }
    .mitra{
        font-family: Mitra,'sans-serif';
    }
    .nastaliq{
        font-family: Nazanin,'sans-serif';
    }
    .titr{
        font-family: Titr,'sans-serif';
    }
    body{
        direction: rtl;
        font-weight: normal;
    }
    .logo{
        width: 25px;
        height: auto;
        margin-bottom: 5px;
    }
    .bold{
        font-weight: bold;
    }
    table{
        border-collapse: collapse;
        table-layout: fixed;
        width: 100%;
    }
    table td{
        border: 1px solid #bdbebf;
        font-family: Nazanin, 'sans-serif';
        font-size: 13px;
        padding: 6px 4px;
    }
    table th{
        border: 1px solid #bdbebf;
        padding: 8px 5px;
    }
    table td div{
        margin: 5px 2px;
    }
    .header tr td:first-child{

    }
    .sign{
        margin-right: auto;
        margin-left: auto;
        object-fit: cover;
    }
    @page {
        margin-top: 35px;
        margin-bottom: 35px;
        margin-right: 40px;
        margin-left: 40px;
        size: 210mm 297mm;
    }
    p{
        text-align: justify;
    }
</style>
<body>
<table class="header">
    <tr>
        <td colspan="4" style="text-align: center;background-color: #f1f1f1;">
            <div style="text-align: center">
                <div><img alt="logo" class="logo" src="{{ "data:image/png;base64,$logo" }}"></div>
                <div class="titr" style="font-size: 16px">همیاران شمال شرق (سهامی خاص) - شماره ثبت : 47651 - شناسه ملی : 10380641381</div>
            </div>
        </td>
    </tr>
    <tr>
        <th style="width: 15%" rowspan="6" class="titr"><span style="font-weight: 500;font-size: 14px">الف) مشخصات کارگر</span></th>
        <td style="width: 20%"><span>1- نام : </span><span class="bold">{{$application->employee->first_name}}</span></td>
        <td style="width: 27%"><span>2 - نام خانوادگی : </span><span class="bold">{{$application->employee->last_name}}</span></td>
        <td style="width: 38%;"><span>3 - شماره شناسنامه : </span><span class="bold">{{$application->employee->id_number}}</span></td>
    </tr>
    <tr>
        <td><span>4 - تاریخ تولد : </span><span class="bold">{{$application->employee->birth_date}}</span></td>
        <td><span>5 - محل صدور : </span><span class="bold">{{$application->employee->issue_city}}</span></td>
        <td><span>6 - کد ملی : </span><span class="bold">{{$application->employee->national_code}}</span></td>
    </tr>
    <tr>
        <td><span>7 - نام پدر : </span><span class="bold">{{$application->employee->father_name}}</span></td>
        <td><span>8 - محل تولد : </span><span class="bold">{{$application->employee->birth_city}}</span></td>
        <td><span>9 - عنوان شغل : </span><span class="bold">{{$application->employee->job_title}}</span></td>
    </tr>
    <tr>
        <td><span>10 - تعداد اولاد : </span><span class="bold">{{$application->employee->children_count}}</span></td>
        <td><span>11 - وضعیت تاهل : </span><span class="bold">{{$application->employee->marital_word}}</span></td>
        <td><span>12 - وضعیت سربازی : </span><span class="bold">{{$application->employee->military_word}}</span></td>
    </tr>
    <tr>
        <td><span>13 - شماره بیمه : </span><span class="bold">{{$application->employee->insurance_number}}</span></td>
        <td>14 - گروه شغلی : <span></span><span class="bold">{{$application->automationable->data_array["active_salary_details"]["occupational_group"]}}</span></td>
        <td><span>15 - محل خدمت : </span><span class="bold">{{$application->employee->contract->organization->name}}</span></td>
    </tr>
    <tr>
        <td><span>16- جنسیت : </span><span class="bold">{{$application->employee->gender_word}}</span></td>
        <td><span>17 - شماره پرسنلی : </span><span class="bold">{{$application->employee->id}}</span></td>
        <td><span>18 - آخرین مدرک تحصیلی : </span><span class="bold">{{$application->employee->education}}</span></td>
    </tr>
    <tr>
        <th rowspan="2" class="titr"><span style="font-weight: 500;font-size: 14px">ب) مشخصات کارفرما</span></th>
        <td colspan="2"><span>19 - نام شرکت : </span><span class="bold">{{$company_information->short_name}}</span></td>
        <td><span>{{ "20 - ".$company_information->ceo_title." : " }}</span><span class="bold">{{$company_information->ceo->name}}</span></td>
    </tr>
    <tr>
        <td colspan="2"><span>21 - نشانی قانونی : </span><span class="bold">{{$company_information->address}}</span></td>
        <td><span>22 - شماره ثبت : </span><span class="bold">{{$company_information->registration_number}}</span></td>
    </tr>
</table>
<table>
    <tbody>
    <tr>
        <td colspan="4">
            <span>23 - مدت قرارداد : </span>
            <span>از تاریخ  </span>
            <span class="bold">{{verta($application->automationable->data_array["active_contract_date"]["start"])->format("Y/m/d")}}</span>
            <span>  لغایت  </span>
            <span class="bold">{{verta($application->automationable->data_array["active_contract_date"]["end"])->format("Y/m/d")}}</span>
            <span>  تعیین می گردد. </span>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <p>24 - ساعات کار قرارداد : کارگر موظف است در مقابل حق السعی دریافتی به طور منظم و مطابق با برنامه زمان بندی کارفرما در هفته 44 ساعت انجام وظیفه نماید در صورت ارجاع کار اضافی در ایام تعطیل و غیر تعطیل نسبت به پرداخت اضافه کار وفق مقررات اقدام می گردد. بدیهی است در صورت عدم انجام کار واگذاری به هر دلیل کارفرما مجاز به فسخ قرارداد می باشد و کارگر حق هرگونه اعتراض را از خود سلب و ساقط نمود.</p>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center"><span>25 - شرح قرارداد</span></td>
        <td colspan="2" style="text-align: center"><span>26 - دستمزد و مزایا(ماهانه)</span></td>
    </tr>
    <tr>
        <td colspan="2" style="width: 50%">
            <p>
                - این قرارداد به استناد ماده 10 قانون کار فی مابین شرکت
                <span class="bold">{{ $company_information->short_name }}</span>
                به نمایندگی جناب آقای
                <span class="bold">{{ $company_information->ceo->name }}</span>
                به عنوان کارفرما و
                {{ $application->employee->gender_refer }}
                <span class="bold">{{ $application->employee->name }}</span>
                به عنوان کارگر می باشد.
            </p>
            <p>- چنانچه کار مشابه در جای دیگر به کارگر ارجاع شود بدون هیچگونه عذری آنرا انجام خواهد داد. نوع کار و محل کار به کارگر اعلام شده و عدم قبول به منزله ترک کار و فسخ قرارداد می باشد.</p>
            <p>- کارفرما هیچگونه تعهدی در قبال سرویس ایاب و ذهاب ندارد.</p>
            <p>- به موجب ماده 148 قانون ، کارفرما مکلف است کارگر را نزد سازمان تامین اجتماعی یا سایر دستگاه های بیمه گذار بیمه نماید.</p>
            <p>- سایر مواردی که در این قرارداد پیش بینی نشده است تابع قانون کار و تأمین اجتماعی و مقررات تبعی آنهاست.</p>
            <p>
                - شرح حکم : تمامی موارد و مدت های مندرج در این قرارداد به تبع قرارداد شماره
                <span class="bold">{{ $number }}</span>
                مورخ
                <span class="bold">{{verta($application->automationable->data_array["active_contract_date"]["start"])->format("Y/m/d")}}</span>
                انجام پذیرفته است.
            </p>
        </td>
        <td style="vertical-align: top;width: 35%">
            <div>مزد شغل روزانه</div>
            <div>مزد شغل ماهانه</div>
            @if($application->automationable->data_array["active_salary_details"]["prior_service"] > 0)
                <div>پایه سنوات ماهانه</div>
            @endif
            <div>دستمزد ماهیانه(مزد مبنا)</div>
            <div>حق اولاد ماهیانه</div>
            @if(array_key_exists("marital_allowance",$application->automationable->data_array["active_salary_details"]))
                <div>حق تاهل</div>
            @endif
            <div>کمک هزینه مسکن</div>
            <div>کمک هزینه اقلام مصرفی خانوار(بن ماهیانه)</div>
{{--            @if(count($application->automationable->data_array["active_salary_details"]["advantages"]) > 0)--}}
{{--                @foreach($application->automationable->data_array["active_salary_details"]["advantages"] as $advantage)--}}
{{--                    <div>{{$advantage["title"]}}</div>--}}
{{--                @endforeach--}}
{{--            @endif--}}
            <div>جمع سایر مزایا</div>
            <div>جمع کل مزایای ماهانه</div>
            <div>جمع حقوق و مزایای ماهانه</div>
        </td>
        <td style="vertical-align: top;text-align: center;width: 15%;">
            <div>{{number_format($application->automationable->data_array["active_salary_details"]["daily_wage"])." ریال"}}</div>
            <div>{{number_format($application->automationable->data_array["active_salary_details"]["base_salary"])." ریال"}}</div>
            @if($application->automationable->data_array["active_salary_details"]["prior_service"] > 0)
                <div>{{number_format($application->automationable->data_array["active_salary_details"]["prior_service"])." ریال"}}</div>
            @endif
            <div>{{number_format($application->automationable->data_array["active_salary_details"]["monthly_wage"])." ریال"}}</div>
            <div>{{number_format($application->automationable->data_array["active_salary_details"]["child_allowance"])." ریال"}}</div>
            @if(array_key_exists("marital_allowance",$application->automationable->data_array["active_salary_details"]))
                <div>{{number_format($application->automationable->data_array["active_salary_details"]["marital_allowance"])." ریال"}}</div>
            @endif
            <div>{{number_format($application->automationable->data_array["active_salary_details"]["housing_purchase_allowance"])." ریال"}}</div>
            <div>{{number_format($application->automationable->data_array["active_salary_details"]["household_consumables_allowance"])." ریال"}}</div>
{{--            @if(count($application->automationable->data_array["active_salary_details"]["advantages"]) > 0)--}}
{{--                @foreach($application->automationable->data_array["active_salary_details"]["advantages"] as $advantage)--}}
{{--                    <div>{{number_format($advantage["value"])." ریال"}}</div>--}}
{{--                @endforeach--}}
{{--            @endif--}}
            <div>{{number_format(array_sum(array_column($application->automationable->data_array["active_salary_details"]["advantages"],"value")))." ریال"}}</div>
            <div>{{number_format($application->automationable->data_array["active_salary_details"]["advantage_total"])." ریال"}}</div>
            <div>{{number_format($application->automationable->data_array["active_salary_details"]["salary_total"])." ریال"}}</div>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <p>
                27 - وظیفه کارگر در این قرارداد عبارتست از انجام امور مربوط به وظایف شغل مندرج در بند 9 این قرارداد که بر اساس شرح وظایف مصوب شغل مندرج در طرح هماهنگ طبقه بندی مشاغل بوده این وظایف را در واحد محل خدمت تعیین شده طبق نظر کارفرما، تحت نظارت مافوق انجام دهد . نوبت کاری طبق ضوابط پرداخت می گردد . مزد و مزایای مندرج در این قرارداد بر مبنای مصوبات قانونی سال 1403 تنظیم شده است و از تاریخ عقد قرارداد مصوبات قانونی مزد و مزایای بر روی آنها لحاظ خواهد شد . در صورت اضافه کاری و نوبت کاری و شب کاری مواد 56 و 58 و 59 قانون کار اعمال خواهد شد.
            </p>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <p>28 - شرایط فسخ قرارداد</p>
            <p>- فسخ قرارداد 3 روز قبل به طرف مقابل کتباً اعلام می شود.</p>
            <p>- غیبت متوالی به مدت 3 روز درهفته یا 7 روز متناوب درماه و همچنین تأخیردر ورود و یا تعجیل در خروج از کارگاه جمعاً به مدت 10 ساعت در ماه موجب فسخ قرارداد می باشد.</p>
            <p>- رد صلاحیت از طریق مراجع ذی صلاح و حراست سازمان.</p>
            <p>- ارتکاب اعمال خلاف قانون که موجب تعقیب از طریق مراجع قضایی و غیر قضائی گردد.</p>
            <p>- اعتیاد به هرگونه مواد مخدر گیاهی، صنعتی و ... در صورت ارایه گزارش بازرسین یا با تایید مراجع ذی صلاح.</p>
            <p>- تعدیل نیرو یا عدم نیاز به ادامه خدمت کارگر ناشی از تقلیل فعالیت کارگاه و تغییر ساختار و همچنین عدم وجود تفاهم بین کارگر و کارفرما و لزوم حفظ نظم محل کار به تشخیص کارفرما.</p>
        </td>
    </tr>
    </tbody>
</table>
<table style="width: 100%;">
    <tr>
        <td style="height: 150px;text-align: center;vertical-align: middle;width: 20%">
            <div>شماره</div>
            <div>{{$number}}</div>
        </td>
        <td style="height: 150px;text-align: center;vertical-align: top;width: 40%">
            <span>امضاء و اثر انگشت کارگر</span>
        </td>
        <td style="height: {{count($application->automationable->data_array["active_salary_details"]["advantages"]) == 0 ? "200px" : (strval(200-(count($application->automationable->data_array["active_salary_details"]["advantages"]) * 5))) ."px"}};text-align: center;vertical-align: top;width: 40%">
            <span>نام، امضاء و مهر شرکت پیمانکار</span>
            @if($sign)
                @if($company_information->ceo->GetSign())
                    <br/>
                    <br/>
                    <div>
                        <img class="sign" src="{{$company_information->ceo->GetSign()}}" alt="همیاران شمال شرق">
                    </div>
                @endif
            @endif
        </td>
    </tr>
</table>
</body>
