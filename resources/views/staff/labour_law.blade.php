@extends("staff.staff_dashboard")
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                تعرفه دستمزد پرسنل - قانون کار
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
            <button class="btn btn-primary d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_labour_law_modal">
                <i class="fa fa-plus fa-1-6x"></i>
            </button>
            <input type="text" class="form-control text-center iransans" data-table="labour_law_table" placeholder="جستجو با نام و سال موثر" v-on:input="filter_table">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table id="labour_law_table" class="table table-striped table-hover pointer-cursor sortArrowWhite" data-filter="[1,2]">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col" style="width: 80px" data-sortas="numeric"><span>شماره</span></th>
                        <th scope="col"><span>عنوان</span></th>
                        <th scope="col" style="width: 120px"><span>سال مؤثر</span></th>
                        <th scope="col" style="width: 150px"><span>توسط</span></th>
                        <th scope="col" style="width: 150px"><span>تاریخ ثبت</span></th>
                        <th scope="col" style="width: 150px"><span>تاریخ ویرایش</span></th>
                        <th scope="col" style="width: 200px"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($labour_laws as $labour_law)
                        <tr>
                            <td class="iransans">{{ $labour_law->id }}</td>
                            <td><span class="iransans">{{ $labour_law->name }}</span></td>
                            <td><span class="iransans">{{ $labour_law->effective_year }}</span></td>
                            <td><span class="iransans">{{ $labour_law->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($labour_law->created_at)->format("Y/m/d") }}</span></td>
                            <td><span class="iransans">{{ verta($labour_law->updated_at)->format("Y/m/d") }}</span></td>
                            <td>
                                <div class="d-flex flex-row flex-wrap align-items-center justify-content-center gap-2 gap-lg-3">
                                    @can("edit", "LabourLaw")
                                        <a role="button" href="{{ route("LabourLaw.edit",$labour_law->id) }}" class="btn btn-sm btn-outline-dark">
                                            <i class="far fa-edit fa-1-2x vertical-middle"></i>
                                        </a>
                                    @endcan
                                    @can("delete","LabourLaw")
                                        <div>
                                            <form class="w-100" id="delete-form-{{ $labour_law->id }}" action="{{ route("LabourLaw.destroy",$labour_law->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                            </form>
                                            <button type="submit" form="delete-form-{{ $labour_law->id }}" class="btn btn-sm btn-outline-dark">
                                                <i class="far fa-trash fa-1-2x vertical-middle"></i>
                                            </button>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr >
                            <td colspan="7">
                                <span class="iransans">اطلاعاتی وجود ندارد</span>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot class="bg-dark">
                    <tr>
                        <td colspan="12">
                            <div class="d-flex align-items-center justify-content-start gap-2 gap-lg-4 my-1 px-2">
                                <p class="iransans white-color mb-0">
                                    مجموع :
                                    {{ count($labour_laws) }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_labour_law_modal" tabindex="-1" aria-labelledby="new_labour_law_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد تعرفه جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("LabourLaw.store") }}" method="POST" v-on:submit="submit_form">
                        @csrf
                        <div class="form-row">
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    عنوان
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iransans @error('name') is-invalid @enderror" type="text" name="name" value="{{ old("name") }}">
                                @error('name')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    سال
                                    <strong class="red-color">*</strong>
                                </label>
                                <select class="form-control iransans selectpicker-select @error('effective_year') is-invalid @enderror" data-live-search="true" data-size="10" name="effective_year">
                                    @for($i = 5; $i >= 0; $i--)
                                        <option @if(verta()->subYears($i)->format("Y") == verta()->format("Y")) selected @endif value="{{ verta()->subYears($i)->format("Y") }}">{{ verta()->subYears($i)->format("Y") }}</option>
                                    @endfor
                                </select>
                                @error('effective_year')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    دستمزد روزانه
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iransans @error('daily_wage') is-invalid @enderror thousand_separator" type="text" name="daily_wage" value="{{ old("daily_wage") }}" v-on:input="ChildAllowanceCalculate">
                                @error('daily_wage')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    کمک هزینه اقلام مصرفی خانوار
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iransans @error('household_consumables_allowance') is-invalid @enderror thousand_separator" type="text" name="household_consumables_allowance" value="{{ old("household_consumables_allowance") }}">
                                @error('household_consumables_allowance')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    کمک هزینه مسکن
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iransans @error('housing_purchase_allowance') is-invalid @enderror thousand_separator" type="text" name="housing_purchase_allowance" value="{{ old("housing_purchase_allowance") }}">
                                @error('housing_purchase_allowance')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    حق تاهل
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iransans @error('marital_allowance') is-invalid @enderror thousand_separator" type="text" name="marital_allowance" value="{{ old("marital_allowance") }}">
                                @error('marital_allowance')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    حق اولاد
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iransans @error('child_allowance') is-invalid @enderror thousand_separator" type="text" name="child_allowance" value="{{ old("child_allowance") }}">
                                @error('child_allowance')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-save fa-1-2x me-1"></i>
                        <span class="iransans">ارسال و ذخیره</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @if($errors->has('name'))
        <script>
            const modal = document.getElementById("new_sms_phrase_modal");
            modal.show();
        </script>
    @endif
@endsection
