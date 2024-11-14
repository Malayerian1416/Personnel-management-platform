@extends("user.user_dashboard")
@section('contents')
    <div class="modal fade rtl" id="requests_history_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="requests_history_modal" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">سوابق درخواست ها</h5>
                </div>
                <div class="modal-body">
                    <div class="fieldset mb-3 mt-2">
                        <span class="legend iransans">نکات قابل توجه</span>
                        <div class="fieldset-body">
                            <p class="iranyekan text-muted text-justify">
                                جهت دانلود فایل PDF درخواست تایید شده، در انتهای ردیف  جدول برروی دکمه عملیات کلیک و گزینه دانلود را انتخاب نمایید. مدت زمان امکان دانلود فایل PDF درخواست های تایید شده از زمان تایید نهایی توسط سازمان، 15 روز متوالی می باشد.
                            </p>
                        </div>
                    </div>
                    <div id="table-scroll-container">
                        <div id="table-scroll" class="table-scroll">
                            <table id="contracts_table" class="table table-striped sortArrowWhite" data-filter="[1,2,3]">
                                <thead class="bg-menu-dark white-color">
                                <tr class="iransans">
                                    <th scope="col" style="width: 150px"><span>شناسه یکتا</span></th>
                                    <th scope="col"><span>درخواست</span></th>
                                    <th scope="col" style="width: 200px"><span>موقعیت</span></th>
                                    <th scope="col" style="width: 100px"><span>وضعیت</span></th>
                                    <th scope="col" style="width: 150px"><span>توسط</span></th>
                                    <th scope="col" style="width: 150px"><span>تاریخ انقضا</span></th>
                                    <th scope="col" style="width: 150px"><span>تاریخ ثبت</span></th>
                                    <th scope="col" style="width: 150px"><span>آخرین ویرایش</span></th>
                                    <th scope="col" style="width: 250px"><span>عملیات</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($requests as $request)
                                    <tr>
                                        <td><span class="iransans">{{ $request->automationable->i_number }}</span></td>
                                        <td><span class="iransans">{{ $request->application_name }}</span></td>
                                        <td>
                                            @if($request->current_role)
                                                <span class="iransans">{{ "کارتابل ".$request->current_role->name }}</span>
                                            @else
                                                <span class="iransans">{{ "کارتابل " }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->automationable->is_accepted == 1)
                                                <i class="fad fa-check-circle fa-1-4x vertical-middle green-color" data-bs-toggle="tooltip" title="درخواست تایید شده است"></i>
                                            @elseif($request->automationable->is_refused == 1)
                                                <i class="fad fa-times-circle fa-1-4x red-color vertical-middle" data-bs-toggle="tooltip" title="درخواست رد شده است"></i>
                                            @else
                                                <i class="fad fa-bars-progress fa-fade fa-1-4x vertical-middle" data-bs-toggle="tooltip" title="در جریان..."></i>
                                            @endif
                                        </td>
                                        <td><span class="iransans">{{ $request->user->name }}</span></td>
                                        <td>
                                            <span class="iransans">
                                                @switch($request->expiration_date)
                                                    @case("remain")
                                                        {{ verta($request->updated_at)->addDays(15)->format("Y/m/d") }}
                                                    @break
                                                    @case("refused")
                                                        <i class="fad fa-diamond-exclamation fa-1-4x vertical-middle red-color" data-bs-toggle="tooltip" title="درخواست رد شده است"></i>
                                                    @break
                                                    @case("expired")
                                                        {{ "منقضی شده" }}
                                                    @break
                                                    @case("waiting")
                                                        <i class="fad fa-bars-progress fa-fade fa-1-4x vertical-middle" data-bs-toggle="tooltip" title="پس از تایید مشخص می گردد"></i>
                                                    @break
                                                    @default
                                                        {{"نامشخص"}}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td><span class="iransans">{{ verta($request->created_at)->format("Y/m/d") }}</span></td>
                                        <td><span class="iransans">{{ verta($request->updated_at)->format("Y/m/d") }}</span></td>
                                        <td class="position-relative">
                                            <div class="d-flex flex-row align-items-center justify-content-center gap-4">
                                                @if($request->message != null)
                                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="popover" data-bs-title="پیام کارشناس" data-bs-content="{{$request->message}}">
                                                        <i class="fa fa-message vertical-middle fa-1-4x"></i>
                                                    </button>
                                                @endif
                                                @if($request->automationable->is_accepted && $request->expiration_date == "remain")
                                                    <a role="button" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="دانلود PDF" href="{{ route("ApplicationForms.download_pdf",$request->id) }}">
                                                        <i class="fa fa-download vertical-middle fa-1-4x"></i>
                                                    </a>
                                                @endif
                                                @if($request->expiration_date == "expired")
                                                    <div class="form-text iransans">درخواست منقضی شده است</div>
                                                @elseif($request->editable)
                                                    <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="ویرایش درخواست" href="{{ route("ApplicationForms.edit",$request->id) }}">
                                                        <i class="fa fa-edit vertical-middle fa-1-4x"></i>
                                                    </a>
                                                    <form id="delete-form-{{ $request->id }}" action="{{ route("ApplicationForms.destroy",$request->id) }}" method="POST" v-on:submit="submit_form">
                                                        @csrf
                                                        @method("Delete")
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="حذف درخواست" form="delete-form-{{ $request->id }}">
                                                            <i class="fa fa-trash vertical-middle fa-1-4x"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-menu">
                    <a href="{{route("user_idle")}}" class="btn btn-outline-secondary iransans">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">بازگشت</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const modal = new bootstrap.Modal(document.getElementById("requests_history_modal"), {});
        modal.show();
    </script>
@endsection
