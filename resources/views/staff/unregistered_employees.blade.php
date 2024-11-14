@extends("staff.staff_dashboard")
@section('variables')
    <script>
        const allowed_organizations = @json($organizations);
    </script>
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                درخواست پیش ثبت نام
                <span class="vertical-middle ms-1 text-muted">مشاهده ، تایید ، عدم تایید</span>
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
            <input type="text" class="form-control text-center iransans" data-table="unregistered_employees_table" placeholder="جستجو با نام، کد ملی و سازمان" v-on:input="filter_table">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table id="unregistered_employees_table" class="table table-striped table-hover pointer-cursor sortArrowWhite" data-filter="[1,2,3]">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col" style="width: 70px" data-sortas="numeric"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col" style="width: 130px"><span>کد ملی</span></th>
                        <th scope="col"><span>سازمان</span></th>
                        <th scope="col" style="width: 90px"><span>توضیحات</span></th>
                        <th scope="col" style="width: 140px"><span>تاریخ ثبت</span></th>
                        <th scope="col" style="width: 150px"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td class="iransans">{{ $employee->id }}</td>
                            <td><span class="iransans">{{ $employee->name }}</span></td>
                            <td><span class="iransans">{{ $employee->national_code }}</span></td>
                            <td><span class="iransans">{{ $employee->organization->name }}</span></td>
                            <td>
                                @if($employee->description)
                                    <i class="fa fa-comment fa-1-4x blue-color hover-blue" data-bs-toggle="popover" data-bs-title="توضیحات ثبت شده" data-bs-content="{{$employee->description}}"></i>
                                @else
                                    <i class="fa fa-times-circle red-color fa-1-4x"></i>
                                @endif
                            </td>
                            <td><span class="iransans">{{ verta($employee->created_at)->format("Y/m/d") }}</span></td>
                            <td>
                                <div class="d-flex flex-row flex-wrap align-items-center justify-content-center gap-2 gap-lg-3">
                                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#confirm_modal" v-on:click="SetUnregisteredEmployee($event,{{$employee->id}},'confirm_form')">
                                        <i class="far fa-check-square fa-1-2x vertical-middle"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reject_modal" v-on:click="SetUnregisteredEmployee($event,{{$employee->id}},'reject_form')">
                                        <i class="far fa-times-square fa-1-2x vertical-middle"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><span class="iransans">اطلاعاتی وجود ندارد</span></td></tr>
                    @endforelse
                    </tbody>
                    <tfoot class="bg-dark">
                    <tr>
                        <td colspan="12">
                            <div class="d-flex align-items-center justify-content-start gap-2 gap-lg-4 my-1 px-2">
                                <p class="iransans white-color mb-0">
                                    مجموع :
                                    {{ count($employees) }}
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
    <div class="modal fade rtl" id="confirm_modal" tabindex="-1" aria-labelledby="confirm_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">تایید و ایجاد</h6>
                </div>
                <div class="modal-body">
                    <form id="confirm_form" class="p-3" action="" method="POST" v-on:submit="submit_form">
                        @csrf
                        @method("put")
                        <div class="form-row">
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    سازمان
                                    <strong class="red-color">*</strong>
                                </label>
                                <div class="mb-2">
                                    <tree-select :branch_node="true" dir="rtl" :is_multiple="false" :placeholder="'انتخاب کنید'" :database="organizations" @contract_selected="UnRegContractSelected"></tree-select>
                                    <input type="hidden" id="contract_id" name="contract_id">
                                </div>
                            </div>
                            <div class="form-group col-12 mb-3">
                                <input type="checkbox" name="send_sms_permission" v-model="sms_send_permission" value="true" class="form-check d-inline-block vertical-middle" tabindex="-1" v-on:change="refresh_selects">
                                <label class="form-label iransans mb-2" for="sms_send_permission">
                                    ارسال پیامک
                                </label>
                                <select :disabled="!sms_send_permission" class="form-control text-center iransans selectpicker-select mb-3 @error('sms_phrase_id') is-invalid @enderror" v-model="select_model" tabindex="-1" title="انتخاب کنید" data-container="body" data-size="10" data-live-search="true" data-place="confirm_sms_text" v-on:change="place_sms_text">
                                    @forelse($sms_phrase_categories as $category)
                                        <optgroup style="font-size: 18px" label="{{ $category->name }}">
                                            @forelse($category->phrases as $phrase)
                                                <option value="{{ $phrase->id }}">{{ $phrase->name }}</option>
                                            @empty
                                            @endforelse
                                        </optgroup>
                                        <option data-divider="true"></option>
                                    @empty
                                    @endforelse
                                </select>
                                <textarea :disabled="!sms_send_permission" name="sms_text" id="confirm_sms_text" class="form-control iransans" tabindex="-1"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="submit" form="confirm_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
                        <span class="iransans">ارسال و تایید</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rtl" id="reject_modal" tabindex="-1" aria-labelledby="reject_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">عدم تایید و حذف</h6>
                </div>
                <div class="modal-body">
                    <form id="reject_form" class="p-3" action="" method="POST" v-on:submit="submit_form">
                        @csrf
                        @method("delete")
                        <div class="form-row">
                            <div class="form-group col-12 mb-3">
                                <input type="checkbox" name="send_sms_permission" v-model="sms_send_permission" value="true" class="form-check d-inline-block vertical-middle" tabindex="-1" v-on:change="refresh_selects">
                                <label class="form-label iransans mb-2" for="sms_send_permission">
                                    ارسال پیامک
                                </label>
                                <select :disabled="!sms_send_permission" class="form-control text-center iransans selectpicker-select mb-3 @error('sms_phrase_id') is-invalid @enderror" v-model="select_model" tabindex="-1" title="انتخاب کنید" data-container="body" data-size="10" data-live-search="true" data-place="confirm_sms_text" v-on:change="place_sms_text">
                                    @forelse($sms_phrase_categories as $category)
                                        <optgroup style="font-size: 18px" label="{{ $category->name }}">
                                            @forelse($category->phrases as $phrase)
                                                <option value="{{ $phrase->id }}">{{ $phrase->name }}</option>
                                            @empty
                                            @endforelse
                                        </optgroup>
                                        <option data-divider="true"></option>
                                    @empty
                                    @endforelse
                                </select>
                                <textarea :disabled="!sms_send_permission" name="sms_text" id="confirm_sms_text" class="form-control iransans" tabindex="-1"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="submit" form="reject_form" class="btn btn-danger submit_button">
                        <i class="submit_button_icon fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">ارسال و عدم تایید</span>
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
