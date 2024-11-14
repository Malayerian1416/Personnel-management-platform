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
    @if($background)
    @page {
        background-image: url("data:image/jpg;base64,{{$background}}");
        background-position: center;
        background-repeat: no-repeat;
        background-image-resize:6
    }
    @endif
     body{
        direction: rtl;
    }
    .letter-information{
        position: absolute;
        top: 100px;
        left: 100px;
    }
    .letter-body{
        position: absolute;
        top: 250px;
        right: 80px;
        width: 162mm;
    }
    .payslip{
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 12px;
    }
    .payslip td{
        border: 1px solid #d3d4d5;
        text-align: center;
        vertical-align: middle;
        padding: 2px;
    }
    .sign-container{
        float: left;
        width: 150px;
        padding: 20px 60px;
        text-align: center;
    }
    .sign_role{
        padding-bottom: 5px;
        text-align: center;
    }
    .sign_user{
        padding-bottom: 10px;
        text-align: center;
    }
    .sign{
        max-width: 120px;
        height: auto;
    }
</style>
<body>
@if(isset($test) && $test)
    <div class="letter-body">
        <p class="mitra" style="font-size: 12px">سلام علیکم</p>
        <p class="nazanin" style="text-align: justify;line-height: 25px;font-size: 15px">
            احتراماً بدینوسیله گواهی می گردد بر اساس قرارداد تامین نیروی
            {{verta($active_contract_date["start"])->diffMonths(verta($active_contract_date["end"]))}}
            ماهه ،
            {{$employee->gender_refer}}
            <b>{{$employee->name}}</b>
            فرزند
            <b>{{$employee->father_name}}</b>
            به شماره شناسنامه
            <b>{{$employee->id_number}}</b>
            و کد ملی
            <b>{{$employee->national_code}}</b>
            که تقاضای وام به مبلغ
            <b> 0 </b>
            ریال می نماید، جزء پرسنل موقت این شرکت بوده که در
            <b>{{$active_contract}}</b>
            مشغول به فعالیت می باشد. بر اساس قرارداد با شرکت همیاران شمال شرق حقوق و مزایای ناخالص ایشان در
            <b>{{$payslip["year_month"]}}</b>
            مبلغ
            <b>{{number_format($payslip["total_advantages"])}}</b>
            ریال می باشد.ضمنا این گواهی به درخواست ایشان جهت ضمانت وام ، درآن بانک یا موسسه محترم می باشد و چنانچه وام گیرنده در پرداخت اقساط تاخیر نماید و تا زمانی که نامبرده با این شرکت همکاری داشته باشد به مجرد اعلام کتبی و در صورت داشتن مانده قابل پرداخت، اقساط معوقه را از مانده حقوقی وی برابر مقررات شرکت کسر و به حساب آن بانک محترم واریز می نماید. مزید امتنان خواهد بود به محض تسویه وام مراتب را به این شرکت اعلام نمایند.
        </p>
        @if(isset($payslip["advantages"]))
            <table class="payslip">
                <tr><td colspan="4" class="titr">{{"فیش حقوقی ".$payslip["year_month"]}}</td></tr>
                <tr>
                    <td colspan="2" class="nazanin" style="width: 50%">مزایا</td>
                    <td colspan="2" class="nazanin" style="width: 50%">کسورات</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;width: 30%">
                        @forelse($payslip["advantages"] as $advantage)
                            <div class="nazanin">{{$advantage["title"]}}</div>
                        @empty
                        @endforelse
                    </td>
                    <td style="vertical-align: top;width: 20%">
                        @forelse($payslip["advantages"] as $advantage)
                            <div class="nazanin">{{number_format($advantage["value"])." ریال"}}</div>
                        @empty
                        @endforelse
                    </td>
                    <td style="vertical-align: top;width: 30%">
                        @forelse($payslip["deductions"] as $deduction)
                            <div class="nazanin">{{$deduction["title"]}}</div>
                        @empty
                        @endforelse
                    </td>
                    <td style="vertical-align: top;width: 20%">
                        @forelse($payslip["deductions"] as $deduction)
                            <div class="nazanin">{{number_format($deduction["value"])." ریال"}}</div>
                        @empty
                        @endforelse
                    </td>
                </tr>
            </table>
        @endif
    </div>
@else
    <div class="letter-information">
        <div style="text-align: center">
            <p class="mitra" style="font-size: 14px">
                تاریخ صدور :
                {{verta($application->updated_at)->format("Y/m/d")}}
            </p>
            <p class="mitra" style="font-size: 14px">
                تاریخ انقضاء :
                {{verta($application->updated_at)->addDays(15)->format("Y/m/d")}}
            </p>
            <div>
                <img src="{{"data:image/png;base64,$qrCode"}}" alt="بارکد" style="margin: 0;padding: 0">
            </div>
            <span class="mitra" style="font-size: 14px;text-align: center">
            شناسه یکتا
        </span>
            <div></div>
            <span class="iransans" style="font-size: 13px;text-align: center">
            {{$application->automationable->i_number}}
        </span>
        </div>
    </div>
    <div class="letter-body">
        <p class="titr" style="font-size: 19px">{{$application->automationable->recipient}}</p>
        <p class="titr" style="font-size: 18px;padding-bottom: 15px">{{"موضوع : ".$application->application_name}}</p>
        <p class="mitra" style="font-size: 12px">سلام علیکم</p>
        <p class="nazanin" style="text-align: justify;line-height: 25px;font-size: 15px">
            احتراماً بدینوسیله گواهی می گردد بر اساس قرارداد تامین نیرو،
            {{$application->employee->gender_refer}}
            <b>{{$application->employee->name}}</b>
            فرزند
            <b>{{$application->employee->father_name}}</b>
            به شماره شناسنامه
            <b>{{$application->employee->id_number}}</b>
            و کد ملی
            <b>{{$application->employee->national_code}}</b>
            @if($application->automationable->borrower != null)
                که ضمانت وام آقای/خانم
                <b>{{$application->automationable->borrower}}</b>
                را به مبلغ
            @else
                که تقاضای وام به مبلغ
            @endif
            <b>{{number_format($application->automationable->loan_amount)}}</b>
            ریال می نماید، جزء پرسنل موقت این شرکت بوده که در
            <b>{{$application->automationable->data_array["active_contract"]["organization_name"]}}</b>
            مشغول به فعالیت می باشد. بر اساس قرارداد با شرکت همیاران شمال شرق حقوق و مزایای ناخالص ایشان در
            <b>{{$application->automationable->data_array["payslip"]["year_month"]}}</b>
            مبلغ
            <b>{{number_format($application->automationable->data_array["payslip"]["total_advantages"])}}</b>
            ریال می باشد.ضمنا این گواهی به درخواست ایشان جهت ضمانت وام ، درآن بانک یا موسسه محترم می باشد و چنانچه وام گیرنده در پرداخت اقساط تاخیر نماید و تا زمانی که نامبرده با این شرکت همکاری داشته باشد به مجرد اعلام کتبی و در صورت داشتن مانده قابل پرداخت، اقساط معوقه را از مانده حقوقی وی برابر مقررات شرکت کسر و به حساب آن بانک محترم واریز می نماید. مزید امتنان خواهد بود به محض تسویه وام مراتب را به این شرکت اعلام نمایند.
        </p>
        @if(json_decode($application->automationable->data,true)["payslip"]["advantages"] != [])
            <table class="payslip">
                <tr><td colspan="4" class="titr">{{"فیش حقوقی ".json_decode($application->automationable->data,true)["payslip"]["year_month"]}}</td></tr>
                <tr>
                    <td colspan="2" class="nazanin" style="width: 50%">مزایا</td>
                    <td colspan="2" class="nazanin" style="width: 50%">کسورات</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;width: 30%">
                        @forelse(json_decode($application->automationable->data,true)["payslip"]["advantages"] as $advantage)
                            @if($advantage["value"] > 0)
                                <div class="nazanin">{{$advantage["title"]}}</div>
                            @endif
                        @empty
                        @endforelse
                    </td>
                    <td style="vertical-align: top;width: 20%">
                        @forelse(json_decode($application->automationable->data,true)["payslip"]["advantages"] as $advantage)
                            @if($advantage["value"] > 0)
                                <div class="nazanin">{{number_format($advantage["value"])." ریال"}}</div>
                            @endif
                        @empty
                        @endforelse
                    </td>
                    <td style="vertical-align: top;width: 30%">
                        @forelse(json_decode($application->automationable->data,true)["payslip"]["deductions"] as $deduction)
                            @if($deduction["value"] > 0)
                                <div class="nazanin">{{$deduction["title"]}}</div>
                            @endif
                        @empty
                        @endforelse
                    </td>
                    <td style="vertical-align: top;width: 20%">
                        @forelse(json_decode($application->automationable->data,true)["payslip"]["deductions"] as $deduction)
                            @if($deduction["value"] > 0)
                                <div class="nazanin">{{number_format($deduction["value"])." ریال"}}</div>
                            @endif
                        @empty
                        @endforelse
                    </td>
                </tr>
            </table>
        @endif
        @if(isset($sign["sign"]))
            <div class="sign-container">
                <div class="sign_box">
                    <p class="sign_role mitra">{{$sign["role"]}}</p>
                    <p class="sign_user mitra"><b>{{$sign["name"]}}</b></p>
                    <img class="sign" alt="همیاران شمال شرق" src="url('{{$sign["sign"]}}')">
                </div>
            </div>
        @endif
    </div>
@endif
</body>
