@extends("staff.staff_dashboard")
@section('variables')
    @if(old("is_parent"))
        <script>
            let is_parent_sub = {{ old("is_parent") }};
        </script>
    @endif
    @if(old("children_subset_list"))
        <script>
            let children_list = @json(json_decode(old("children_subset_list"),true));
        </script>
    @endif
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                قرارداد ها
                <span class="vertical-middle ms-1 text-muted">ایجاد ، جستجو ، ویرایش</span>
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
    <div class="page-content w-100">
        <div class="input-group mb-2">
            <button class="btn btn-primary d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_contract_modal">
                <i class="fa fa-plus fa-1-6x"></i>
            </button>
            <input type="text" class="form-control text-center iransans" data-table="contracts_table" placeholder="جستجو با نام، سازمان و زیرمجموعه" v-on:input="filter_table">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table id="contracts_table" class="table table-striped table-hover pointer-cursor sortArrowWhite" data-filter="[1,2,3]">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col" data-sortas="numeric"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>سازمان</span></th>
                        <th scope="col" class="d-none"></th>
                        <th scope="col"><span>زیرمجموعه</span></th>
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>مستندات</span></th>
                        <th scope="col"><span>پیش ثبت نام</span></th>
                        <th scope="col"><span>پرسنل</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col" style="width: 150px"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($contracts as $contract)
                        <tr>
                            <td class="iransans">{{ $contract->id }}</td>
                            <td><span class="iransans text-wrap">{{ $contract->name }}</span></td>
                            <td><span class="iransans text-wrap">{{ $contract->organization->name }}</span></td>
                            <td class="d-none">
                                @forelse($contract->children as $child)
                                    <span>{{ $child->name }}</span>
                                @empty
                                @endforelse
                            </td>
                            <td>
                                @if($contract->children->isNotEmpty())
                                    <div class="dropdown table-functions iransans">
                                        <a class="table-functions-button dropdown-toggle border-0 iransans green-color" type="button" id="children" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-eye fa-1-4x"></i>
                                        </a>
                                        <div class="dropdown-menu pt-0 pb-0" aria-labelledby="children">
                                            <ul class="list-group list-group-flush">
                                                @foreach($contract->children as $children)
                                                    <li class="list-group-item px-3 py-2">
                                                        <span>{{ "$children->id - $children->name " . "(".count($children->employees).")" }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @else
                                    <span><i class="far fa-times-circle red-color fa-1-4x vertical-middle"></i></span>
                                @endif
                            </td>
                            <td>
                                <span class="iransans">
                                     @if($contract->inactive == 1)
                                        <i class="far fa-times-circle red-color fa-1-4x vertical-middle"></i>
                                    @elseif($contract->inactive == 0)
                                        <i class="far fa-check-circle green-color fa-1-4x vertical-middle"></i>
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if($contract->files)
                                    <a href="{{ route("Contracts.download_docs",$contract->id) }}"><i class="fa fa-download fa-1-4x"></i></a>
                                @else
                                    <span><i class="far fa-times-circle fa-1-4x red-color"></i></span>
                                @endif
                            </td>
                            <td>
                                @if($contract->pre_employees->isNotEmpty())
                                    <span class="iransans">{{ count($contract->pre_employees) }}</span>
                                @else
                                    <i class="far fa-times-circle red-color fa-1-4x vertical-middle"></i>
                                @endif
                            </td>
                            <td>
                                @if($contract->employees->isNotEmpty())
                                    <span class="iransans">{{ count($contract->employees) }}</span>
                                @else
                                    <i class="far fa-times-circle red-color fa-1-4x vertical-middle"></i>
                                @endif
                            </td>
                            <td><span class="iransans">{{ $contract->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($contract->created_at)->format("Y/m/d") }}</span></td>
                            <td><span class="iransans">{{ verta($contract->updated_at)->format("Y/m/d") }}</span></td>
                            <td>
                                <div class="d-flex flex-row flex-wrap align-items-center justify-content-center gap-2 gap-lg-3">
                                    @can("activation", "Contracts")
                                        <div>
                                            <form hidden id="activation-form-{{ $contract->id }}" action="{{ route("Contracts.activation",$contract->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                            </form>
                                            <button form="activation-form-{{ $contract->id }}" class="btn btn-sm btn-outline-dark">
                                                @if($contract->inactive == 0)
                                                    <i class="far fa-lock fa-1-2x vertical-middle"></i>
                                                @elseif($contract->inactive == 1)
                                                    <i class="far fa-lock-open fa-1-2x vertical-middle"></i>
                                                @endif
                                            </button>
                                        </div>
                                    @endcan
                                    @can("edit", "Contracts")
                                        <a role="button" class="btn btn-sm btn-outline-dark" href="{{route("Contracts.edit",$contract->id)}}">
                                            <i class="far fa-edit fa-1-2x vertical-middle"></i>
                                        </a>
                                    @endcan
                                    @can("delete","Contracts")
                                        <div>
                                            <form hidden id="delete-form-{{ $contract->id }}" action="{{ route("Contracts.destroy",$contract->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                                <button type="submit" form="delete-form-{{ $contract->id }}" class="dropdown-item">
                                                    <i class="far fa-trash"></i>
                                                    <span class="iransans">حذف</span>
                                                </button>
                                            </form>
                                            <button form="delete-form-{{ $contract->id }}" class="btn btn-sm btn-outline-dark">
                                                <i class="far fa-trash fa-1-2x vertical-middle"></i>
                                            </button>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="13"><span class="iransans">اطلاعاتی وجود ندارد</span></td></tr>
                    @endforelse
                    </tbody>
                    <tfoot class="bg-dark">
                    <tr>
                        <td colspan="13">
                            <div class="d-flex align-items-center justify-content-start gap-2 gap-lg-4 my-1 px-2">
                                <p class="iransans white-color mb-0">
                                    مجموع :
                                    {{ count($contracts) }}
                                </p>
                                <p class="iransans white-color mb-0">
                                    فعال :
                                    {{  count($contracts->where("inactive",0)) }}
                                </p>
                                <p class="iransans white-color mb-0">
                                    غیر فعال :
                                    {{ count($contracts->where("inactive",1)) }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <vue-context v-cloak ref="contextMenu">
            @can("activation", "Contracts")
                <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">

                </li>
            @endcan
            @can("edit", "Contracts")
                <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">

                </li>
            @endcan
            @can("delete","Contracts")
                <li class="py-2 px-3 pointer-cursor hover-blue hover-bg-light">

                </li>
            @endcan
        </vue-context>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_contract_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد قرارداد جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("Contracts.store") }}" method="POST" :data-json="is_parent === false ? 'employees' : 'children_subset_list'" enctype="multipart/form-data" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div class="form-group mb-3 col-12">
                                <input type="checkbox" class="vertical-middle" id="is_parent" name="is_parent" v-model="is_parent" @if(old("is_parent")) checked @endif value="1" v-on:change="refresh_selects">
                                <label class="form-label iransans fw-bold" for="is_parent">
                                    تعریف قرارداد به همراه زیرمجموعه
                                </label>
                                <div class="form-text iransans">
                                    با انتخاب این گزینه می توانید زیرمجموعه های دیگری را به عنوان فرزند به قرارداد فعلی اضافه کنید و از قرارداد فعلی فقط به عنوان والد قرارداد های دیگر استفاده نمایید.
                                </div>
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    سازمان
                                </label>
                                <select class="form-control text-center iransans @error('organization_id') is-invalid is-invalid-fake @enderror selectpicker-select" title="انتخاب کنید" data-size="20" data-live-search="true" name="organization_id">
                                    @forelse($organizations as $organization)
                                        <option @if($organization->id == old("organization_id")) selected @endif value="{{ $organization->id }}">{{ $organization->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('organization_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    نام
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iransans @error('name') is-invalid @enderror" type="text" name="name" value="{{ old("name") }}">
                                @error('name')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12 mb-3">
                                <label class="form-label iransans mb-2">شماره</label>
                                <input class="form-control text-center iransans @error('number') is-invalid @enderror" type="text" name="number" value="{{ old("number") }}">
                            </div>
                            <div class="form-group col-12 mb-3">
                                <label class="form-label iransans mb-2">تاریخ شروع</label>
                                <input class="form-control text-center iransans @error('start_date') is-invalid @enderror persian_datepicker_range_from" tabindex="-1" readonly type="text" name="start_date" value="{{ old("start_date") }}">
                                @error('start_date')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-12 mb-3">
                                <label class="form-label iransans mb-2">تاریخ پایان</label>
                                <input class="form-control text-center iransans @error('end_date') is-invalid @enderror persian_datepicker_range_to" tabindex="-1" readonly type="text" name="end_date" value="{{ old("end_date") }}">
                                @error('end_date')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">مستندات</label>
                                <m-file-browser :accept="['png','jpg','bmp','tiff','pdf','xlsx','txt']" :size="325000"></m-file-browser>
                            </div>
                        </div>
                        <div class="row" v-if="is_parent === false">
                            <div class="form-group mb-3 col-12 col-lg-9">
                                <label class="form-label iransans">
                                    پرسنل
                                    <a href="{{ route("Contracts.excel_download") }}" class="button">(دانلود فایل اکسل نمونه)</a>
                                </label>
                                <s-file-browser :accept="['xlsx']" :size="500000" :file_box_name="'excel_file'" :file_box_id="'excel_file'" :filename_box_id="'excel_absolute_browser_box'"></s-file-browser>
                            </div>
                            <div class="col-12 col-lg-3 align-self-center mb-3">
                                <live-transfer :target="'employees'" :elements="['#excel_file']" :required="['#excel_file']" :class="'btn btn-primary w-100 mt-1'" route="{{ route("Contracts.excel_upload") }}" :message="'آیا برای بارگذاری لیست پرسنل اطمینان دارید؟'">
                                    <i class="fa fa-upload fa-1-2x me-1"></i>
                                    <span class="iransans">بارگذاری فایل</span>
                                </live-transfer>
                            </div>
                            <div class="form-group col-12 mb-3">
                                <div class="mb-2">
                                    <div class="input-group-text w-100 d-flex flex-row align-items-center justify-content-between @error('employees') is-invalid @enderror">
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#employees"><i class="fa fa-magnifying-glass green-color fa-1-2x" v-on:click="return_modal='#new_contract_modal'"></i></button>
                                            <span class="iransans">
                                                پرسنل بارگذاری شده :
                                                @{{ employees.length }}
                                                نفر
                                            </span>
                                        </div>
                                        <button v-if="import_errors.length > 0" type="button" class="btn btn-sm btn-outline-secondary iransans" data-bs-toggle="modal" data-bs-target="#import_errors" v-on:click="return_modal='#new_contract_modal'">
                                            <i class="fa fa-triangle-exclamation red-color fa-1-2x me-1"></i>
                                            خطای بارگذاری
                                        </button>
                                    </div>
                                    @error('employees')
                                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row" v-else>
                            <div class="col-12">
                                <label class="form-label iransans mb-3">
                                    زیرمجموعه های قرارداد
                                </label>
                                <button type="button" class="btn btn-outline-dark d-block mb-3 w-100" data-bs-target="#new_subset_modal" data-bs-toggle="modal">
                                    <i class="far fa-plus fa-1-2x me-1 vertical-middle"></i>
                                    <span class="iransans">ایجاد زیرمجموعه جدید</span>
                                </button>
                                <ul class="list-group @error('children_subset_list') is-invalid is-invalid-fake @enderror">
                                    <li v-for="(child,index) in children_subset_list" class="list-group-item d-flex align-items-center justify-content-between">
                                        <span class="iransans">@{{ `${++index} - ${child.name}` }}</span>
                                        <i class="fa fa-trash-can fa-1-4x hover-red" v-on:click="children_subset_list.splice(index,1)"></i>
                                    </li>
                                    <li v-if="children_subset_list.length === 0" class="list-group-item d-flex align-items-center justify-content-center @error('children_subset_list') is-invalid @enderror">
                                        <span class="iransans text-muted">زیرمجموعه ای ایجاد نشده است</span>
                                    </li>
                                </ul>
                                @error('children_subset_list')
                                <div class="invalid-feedback iransans small_font">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-check fa-1-2x me-1 vertical-middle"></i>
                        <span class="iransans">ارسال و ذخیره</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1 vertical-middle"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rtl" id="new_subset_modal" tabindex="-1" role="dialog" aria-labelledby="new_subset_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد زیرمجموعه جدید</h6>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group mb-3 col-12">
                            <label class="form-label iransans">
                                عنوان
                            </label>
                            <input class="form-control text-center iransans" type="text" id="child_name">
                        </div>
                        <div class="form-group mb-3 col-12 col-lg-9">
                            <label class="form-label iransans">
                                بارگذاری مشخصات پرسنل مربوطه
                                <a href="{{ route("Contracts.excel_download") }}" class="button">(دانلود فایل اکسل نمونه)</a>
                            </label>
                            <s-file-browser :accept="['xlsx','xls']" :size="500000" :file_box_name="'excel_file'" :file_box_id="'excel_file'" :filename_box_id="'excel_browser_box'"></s-file-browser>
                        </div>
                        <div class="col-12 col-lg-3 align-self-center mb-3">
                            <live-transfer :target="'employees'" :elements="['#excel_file']" :required="['#excel_file']" :class="'btn btn-primary w-100 mt-1'" route="{{ route("Contracts.excel_upload") }}" :message="'آیا برای بارگذاری لیست پرسنل اطمینان دارید؟'">
                                <i class="fa fa-upload fa-1-2x me-1"></i>
                                <span class="iransans">بارگذاری فایل</span>
                            </live-transfer>
                        </div>
                        <div class="form-group col-12 mb-3">
                            <div class="mb-2">
                                <div class="input-group-text w-100 d-flex flex-row align-items-center justify-content-between @error('employees') is-invalid @enderror">
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#employees" v-on:click="return_modal='#new_subset_modal'"><i class="fa fa-magnifying-glass green-color fa-1-2x"></i></button>
                                        <span class="iransans">
                                                پرسنل بارگذاری شده :
                                                @{{ employees.length }}
                                                نفر
                                            </span>
                                    </div>
                                    <button v-if="import_errors.length > 0" type="button" class="btn btn-sm btn-outline-secondary iransans" data-bs-toggle="modal" data-bs-target="#import_errors" v-on:click="return_modal='#new_subset_modal'">
                                        <i class="fa fa-triangle-exclamation red-color fa-1-2x me-1"></i>
                                        خطای بارگذاری
                                    </button>
                                </div>
                                @error('employees')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success submit_button" v-on:click="add_subset">
                        <i class="fa fa-plus fa-1-2x me-1"></i>
                        <span class="iransans">ایجاد</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" data-bs-target="#new_contract_modal" data-bs-toggle="modal">
                        <i class="fa fa-times fa-1-2x ms-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="import_errors" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans" id="exampleModalLongTitle">مشکلات بارگذاری فایل اکسل</h6>
                </div>
                <div class="modal-body scroll-style">
                    <table class="table table-bordered text-center w-100 iransans">
                        <thead class="thead-dark">
                        <tr>
                            <th>ردیف فایل</th>
                            <th>مقدار</th>
                            <th>پیام خطا</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="error in import_errors" :key="error.row">
                            <td>@{{ error.row }}</td>
                            <td>@{{ error.national_code }}</td>
                            <td>@{{ error.message }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button v-if="return_modal" type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" data-bs-toggle="modal" :data-bs-target="return_modal" v-on:click="return_modal=''">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                    <button v-else type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="employees" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans" id="exampleModalLongTitle">پرسنل بارگذاری شده</h6>
                </div>
                <div class="modal-body scroll-style">
                    <table class="table table-bordered text-center w-100 iransans">
                        <thead class="thead-dark">
                        <tr>
                            <th>ردیف</th>
                            <th>نام</th>
                            <th>کد ملی</th>
                            <th>موبایل</th>
                            <th>حذف</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(employee,index) in employees" :key="employee.id">
                            <td>@{{ ++index }}</td>
                            <td>@{{ employee.name }}</td>
                            <td>@{{ employee.national_code }}</td>
                            <td>@{{ employee.mobile }}</td>
                            <td>
                                <i class="fa fa-trash hover-red fa-1-2x" v-on:click="employees.splice(index,1)"></i>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button v-if="return_modal" type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" data-bs-toggle="modal" :data-bs-target="return_modal" v-on:click="return_modal=''">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                    <button v-else type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @if($errors->has('name') || $errors->has('start_date') || $errors->has('end_date') || $errors->has('organization_id') || $errors->has('children_subset_list') || $errors->has('upload_file'))
        <script defer>
            $(document).ready(function (){
                let modal = new bootstrap.Modal(document.getElementById("new_contract_modal"), {});
                modal.show();
            });
        </script>
    @endif
@endsection
