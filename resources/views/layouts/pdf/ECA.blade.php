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
        top: 300px;
        right: 80px;
        width: 162mm;
    }
</style>
<body>
@if(isset($test) && $test)
    <div class="letter-body">
        <p class="mitra" style="font-size: 12px">سلام علیکم</p>
        <p class="nazanin" style="text-align: justify;line-height: 25px;font-size: 15px">
            احتراماً بدینوسیله گواهی می گردد
            {{$employee->gender_refer}}
            <b>{{$employee->name}}</b>
            فرزند
            <b>{{$employee->father_name}}</b>
            به شماره شناسنامه
            <b>{{$employee->id_number}}</b>
            صادره از
            <b>{{$employee->issue_city}}</b>
            و کد ملی
            <b>{{$employee->national_code}}</b>
            در حال حاضر در
            <b>{{$active_contract}}</b>
            مشغول بکار می باشد. این گواهی بنا به درخواست نامبرده جهت ارائه به آن مدیریت محترم صادر گردیده و فاقد هرگونه ارزش قانونی دیگری می باشد.
        </p>
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
            احتراماً بدینوسیله گواهی می گردد
            {{$application->employee->gender_refer}}
            <b>{{$application->employee->name}}</b>
            فرزند
            <b>{{$application->employee->father_name}}</b>
            به شماره شناسنامه
            <b>{{$application->employee->id_number}}</b>
            صادره از
            <b>{{$application->employee->issue_city}}</b>
            و کد ملی
            <b>{{$application->employee->national_code}}</b>
            در حال حاضر در
            <b>{{$application->automationable->data_array["active_contract"]["organization_name"]}}</b>
            مشغول بکار می باشد. این گواهی بنا به درخواست نامبرده جهت ارائه به آن مدیریت محترم صادر گردیده و فاقد هرگونه ارزش قانونی دیگری می باشد.
        </p>
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
