@extends("staff.staff_dashboard")
@section('variables')
    <script>
        const allowed_organizations = @json($organizations);
        const excel_columns_data = @json(json_decode($template->columns,true));
        const contract_id_data = @json($template->contract_id);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                قالب فیش حقوقی
                <span class="vertical-middle ms-1 text-muted">ویرایش</span>
            </h5>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-light">
                <i class="fa fa-circle-question fa-1-4x green-color"></i>
            </button>
            <a role="button" class="btn btn-sm btn-outline-light" href={{route("staff_idle")}}>
                <i class="fa fa-times fa-1-4x gray-color"></i>
            </a>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content edit w-100">
        <form id="update_form" class="p-3" action="{{ route("PaySlipTemplates.update",$template->id) }}" method="POST" data-json="excel_columns" v-on:submit="submit_form">
            @csrf
            @method('PUT')
            <div class="row">
                <div v-if="excel_columns.length > 0" class="col-12 mb-3">
                    <label class="form-label iransans">انتخاب قرارداد</label>
                    <tree-select :branch_node="true" @error('contract_id') :validation_error="true" @enderror :selected="{{$template->contract_id}}" @contract_selected="ContractSelected" dir="rtl" :is_multiple="false" :placeholder="'انتخاب کنید'" :database="organizations"></tree-select>
                    <input type="hidden" name="contract_id" v-model="contract_id">
                    @error('contract_id')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-12 mb-3">
                    <label v-if="excel_columns.length === 0" class="form-label iransans">تعداد کل ستون های مورد نیاز</label>
                    <label v-if="excel_columns.length > 0" class="form-label iransans">افزایش / کاهش (به انتها و از انتها)</label>
                    <div v-if="excel_columns.length === 0" class="input-group">
                        <input type="text" class="form-control text-center iransans" v-model="last_excel_column">
                        <button type="button" class="btn btn-sm btn-outline-primary input-group-append ps-5 pe-5" v-on:click="ExcelColumnsCreation">
                            <i class="fa fa-check fa-1-4x"></i>
                        </button>
                    </div>
                    <div v-if="excel_columns.length > 0" class="input-group">
                        <input type="text" class="form-control text-center iransans" v-model="add_remove_excel_column">
                        <button type="button" class="btn btn-sm btn-outline-primary input-group-append ps-3 pe-3" v-on:click="ExcelColumnsNumber('increase')">
                            <i class="fa fa-plus fa-1-4x"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger input-group-append ps-3 pe-3" v-on:click="ExcelColumnsNumber('decrease')">
                            <i class="fa fa-minus fa-1-4x"></i>
                        </button>
                    </div>
                </div>
                <div v-if="excel_columns.length > 0" class="col-12 mb-3">
                    <label class="form-label iransans">مشخص نمودن ستون مخصوص کد ملی پرسنل</label>
                    <select class="form-control iransans selectpicker-select" data-live-search="true" data-size="10" name="national_code_index">
                        <option v-for="(column,index) in excel_columns" :selected="index === {{$template->national_code_index}}" :key="index" :value="index">
                            ستون
                            @{{ column.column }}
                        </option>
                    </select>
                </div>
                <div v-if="excel_columns.length > 0" class="col-12">
                    <div id="table-scroll-container">
                        <div id="table-scroll" class="table-scroll fixed" style="max-height: 50vh">
                            <table>
                                <thead class="bg-dark white-color iransans">
                                <tr>
                                    <th scope="col">ستون</th>
                                    <th scope="col">عنوان</th>
                                    <th scope="col">نوع</th>
                                    <th scope="col">خصوصیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="iransans" v-for="(column,index) in excel_columns" :key="index">
                                    <td><span style="font-size:18px;font-weight: 700">@{{ column.column }}</span></td>
                                    <td>
                                        <input :disabled="column.ignore" type="text" class="form-control iransans text-center" placeholder="عنوان" v-model="column.title">
                                    </td>
                                    <td>
                                        <select :disabled="column.ignore" class="form-control iransans" v-model="column.type">
                                            <option value="information">اطلاعات پرسنل(نام و یا کد ملی)</option>
                                            <option value="function">اطلاعات کارکرد(تعداد روزهای کارکرد...)</option>
                                            <option value="advantage">مزایـــا</option>
                                            <option value="deduction">کـسـورات</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input :disabled="column.ignore" class="form-check-input" type="checkbox" :id="`isNumber${index}`" :value="true" v-model="column.isNumber">
                                            <label class="form-check-label iransans" :for="`isNumber${index}`">مقدار عددی</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" :id="`isIgnored${index}`" :value="false" v-model="column.ignore">
                                            <label class="form-check-label iransans" :for="`isIgnored${index}`">نادیده گرفتن</label>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section("footer")
    <div class="content-footer-container d-flex align-items-center justify-content-center gap-3 flex-wrap">
        <button type="submit" form="update_form" class="btn btn-success submit_button">
            <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
            <span class="iransans">ارسال و ویرایش</span>
        </button>
        <a role="button" href="{{ route("PaySlipTemplates.index") }}"
           class="btn btn-outline-secondary iransans">
            <i class="fa fa-arrow-turn-right fa-1-2x me-1"></i>
            <span class="iransans">بازگشت به لیست</span>
        </a>
    </div>
@endsection
