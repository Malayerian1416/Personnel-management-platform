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
    }
    .payslip-header{
        width: 100%;
        border: 1px solid #8e8e8e;
        padding: 10px;
        table-layout: fixed;
    }
    .payslip-header td{
        text-align: center;
        font-weight: 700;
    }
    .payslip-body{
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }
    .payslip-body td, .payslip-body th{
        border: 1px solid #8e8e8e;
        padding: 5px;
    }
    .text-muted{
        color: #5d5d5d;
    }
    .payslip-body tbody tr td{
        vertical-align: top;
    }
    .payslip-body thead th{
        font-family: Titr,'sans-serif';
        font-weight: normal;
    }
    .content-table{
        width: 100%;
        border: none;
        text-align: center;
    }
    .content-table tr td{
        border: none;
        font-family: Nazanin,'sans-serif';
    }
    .content-table tr:nth-child(odd){
        background-color: #ecedee;
    }
    .big-font{
        font-size: 16px;
    }
    .logo{
        width: 25px;
        height: auto;
    }
    .barcode-tag{
        position: absolute;
        left: 70px;
        top: 70px;
    }
</style>
<body>
<table class="payslip-header">
    <tr>
        <td colspan="5" class="titr">
            <img alt="logo" class="logo" src="{{ "data:image/png;base64,$logo" }}">
        </td>
    </tr>
    <tr>
        <td colspan="5" class="titr"><span style="font-weight: normal;font-size: 19px">شرکت خدمات بازرگانی ، حمل و نقل و توسعه اقتصادی همیاران شمال شرق</span></td>
    </tr>
    <tr>
        <td colspan="5" class="titr" style="font-weight: normal;font-size: 16px">{{ "صورتحساب حقوق و مزایای ".$payslip["employee"]["month"]." ماه ".$payslip["employee"]["year"]. " پرسنل" }}</td>
    </tr>
    <tr>
        <td colspan="5" style="padding-bottom: 15px">
            <div class="mitra">
                <span class="text-muted">تاریخ گزارش : </span>
                <span>{{$payslip["employee"]["report_date"]}}</span>
            </div>
        </td>
    </tr>
    <tr>
        <td style="width: 20%">
            <div class="mitra">
                <span class="text-muted">نام پرسنل : </span>
                <span>{{$payslip["employee"]["name"]}}</span>
            </div>
        </td>
        <td style="width: 15%">
            <div class="mitra">
                <span class="text-muted">کد ملی : </span>
                <span>{{$payslip["employee"]["national_code"]}}</span>
            </div>
        </td>
        <td style="width: 40%">
            <div class="mitra">
                <span class="text-muted">سازمان و قرارداد : </span>
                <span>{{$payslip["employee"]["contract"]}}</span>
            </div>
        </td>
        <td style="width: 25%">
            <div class="mitra">
                <span class="text-muted">شناسه یکتا : </span>
                <span>{{$payslip["employee"]["i_number"]}}</span>
            </div>
        </td>
    </tr>
</table>
<table class="payslip-body">
    <thead>
    <tr style="background-color: #cccdce">
        <th colspan="2" class="big-font" style="width: 33%">کــارکــرد</th>
        <th colspan="2" class="big-font" style="width: 33%">مـــزایــا</th>
        <th colspan="2" class="big-font" style="width: 33%">کـســـورات</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td colspan="2" style="width: 33%">
            <table class="content-table">
                <tbody>
                @forelse($payslip["functions"] as $function)
                    <tr>
                        <td>{{ $function["title"] }}</td>
                        <td>{{ $function["value"] }}</td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>
        </td>
        <td colspan="2" style="width: 33%">
            <table class="content-table">
                <tbody>
                @forelse($payslip["advantages"] as $advantage)
                    <tr>
                        <td>{{ $advantage["title"] }}</td>
                        <td>{{ $advantage["value"]." ریال" }}</td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>
        </td>
        <td colspan="2" style="width: 33%">
            <table class="content-table">
                <tbody>
                @forelse($payslip["deductions"] as $deduction)
                    <tr>
                        <td>{{ $deduction["title"] }}</td>
                        <td>{{ $deduction["value"]." ریال" }}</td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="2" class="titr" style="font-size: 16px;text-align: center">{{"جمع کل حقوق و مزایا : ".$payslip["total_advantages"]." ریال"}}</td>
        <td colspan="2" class="titr" style="font-size: 16px;text-align: center">{{"جمع کل کسورات : ".$payslip["total_deductions"]." ریال"}}</td>
        <td colspan="2" class="titr" style="font-size: 18px;text-align: center">{{"خالص پرداختی : ".$payslip["total_net"]." ریال"}}</td>
    </tr>
    </tfoot>
</table>
<p class="iranyekan" style="font-size: 9px;text-align: center">(جهت آگاهی از اصالت این برگه می توانید شناسه یکتای آن را در سامانه به آدرس https://www.hamyaranshomalshargh.com/Validation/index وارد نموده و یا با اسکن کد QR توسط دوربین گوشی خود و مراجعه با آدرس مشخص شده از اصالت آن اطمینان حاصل فرمایید)</p>
<div class="barcode-tag">
    <img alt="qrcode" src="{{ "data:image/png;base64,{$payslip["employee"]["qrcode"]}" }}">
</div>
</body>
