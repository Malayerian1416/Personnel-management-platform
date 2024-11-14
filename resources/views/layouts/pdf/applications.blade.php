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
    @if($page_data["background"])
    @page {
        background-image: url("data:image/jpg;base64,{{$page_data['background']}}");
        background-position: center;
        background-repeat: no-repeat;
        background-image-resize:6
    }
    @endif
    body{
        direction: rtl;
    }
    p{
        position: absolute;
    }
</style>
<body>
@forelse($page_data["contents"] as $paragraph)
    <p class="{{$paragraph["fontFamily"]}}" style="width: {{$paragraph["width"]}};height: {{$paragraph["height"]}};top: {{$paragraph["top"]."px"}};left: {{$paragraph["left"]."px"}};font-size: {{$paragraph["fontSize"]}};text-align: {{$paragraph["textAlignment"]}};line-height: {{$paragraph["lineHeight"]}}">
        {!! $paragraph["text"] !!}
    </p>
@empty
@endforelse
</body>
