@extends("staff.staff_dashboard")
@section('variables')
    <script>
        const applications_data = @json($applications);
        const page_data = @json(json_decode($form_template->page_data,true));
        const background_data = @json($background);
        const form_id = @json($form_template->id);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i v-if="sidebar_toggle" class="sidebar-toggle fa fa-bars fa-1-6x me-4" v-cloak
               v-on:click="toggle_sidebar('open')"></i>
            <h5 class="iransans d-inline-block m-0 d-flex align-items-center">
                قالب فرم درخواست های پرسنل
                <span class="vertical-middle ms-2">(ویرایش)</span>
            </h5>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content w-100">
        <div style="background-color: #c2c2c3">
            <form-template method="put"></form-template>
        </div>
        <div class="form-group mb-3 col-12 text-center pt-4 pb-2">
            <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
                <span class="iransans">ارسال و ویرایش</span>
            </button>
            <a role="button" href="{{ route("FormTemplates.index") }}"
               class="btn btn-outline-secondary iransans">
                <i class="fa fa-arrow-turn-right fa-1-2x me-1"></i>
                <span class="iransans">بازگشت به لیست</span>
            </a>
        </div>
    </div>
@endsection
