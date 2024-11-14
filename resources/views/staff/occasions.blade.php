@extends("staff.staff_dashboard")
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                مناسبت ها
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
            <button class="btn btn-primary d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_occasion_modal">
                <i class="fa fa-plus fa-1-6x"></i>
            </button>
            <input type="text" class="form-control text-center iransans" data-table="occasions_table" placeholder="جستجو با عنوان مناسبت" v-on:input="filter_table">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table id="occasions_table" class="table table-striped table-hover pointer-cursor sortArrowWhite" data-filter="[1]">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col" style="width: 70px" data-sortas="numeric"><span>شماره</span></th>
                        <th scope="col" style="width: 300px"><span>عنوان</span></th>
                        <th scope="col" style="width: 80px"><span>وضعیت انتشار</span></th>
                        <th scope="col" style="width: 120px"><span>توسط</span></th>
                        <th scope="col" style="width: 120px"><span>تاریخ ثبت</span></th>
                        <th scope="col" style="width: 120px"><span>تاریخ ویرایش</span></th>
                        <th scope="col" style="width: 150px"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($occasions as $occasion)
                        <tr class="iransans">
                            <td>{{ $occasion->id }}</td>
                            <td style="max-width:300px;overflow: hidden;text-overflow: ellipsis">{{ $occasion->title }}</td>
                            <td>
                                @if($occasion->publish == 1)
                                    <i class="fa fa-check-circle green-color fa-1-4x"></i>
                                @elseif($occasion->publish == 0)
                                    <i class="fa fa-times-circle red-color fa-1-4x"></i>
                                @else
                                    <i class="fa fa-question-circle blue-color fa-1-4x"></i>
                                @endif
                            </td>
                            <td>{{ $occasion->user->name }}</td>
                            <td>{{ verta($occasion->created_at)->format("Y/m/d") }}</td>
                            <td>{{ verta($occasion->updated_at)->format("Y/m/d") }}</td>
                            <td>
                                <div class="d-flex flex-row flex-wrap align-items-center justify-content-center gap-2 gap-lg-3">
                                    @can("activation", "Occasions")
                                        <div>
                                            <form hidden id="activation-form-{{ $occasion->id }}" action="{{ route("Occasions.activation",$occasion->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                            </form>
                                            <button type="submit" form="activation-form-{{ $occasion->id }}" class="btn btn-sm btn-outline-dark">
                                                @if($occasion->publish == 1)
                                                    <i class="far fa-eye-slash fa-1-2x vertical-middle"></i>
                                                @elseif($occasion->publish == 0)
                                                    <i class="far fa-eye fa-1-2x vertical-middle"></i>
                                                @endif
                                            </button>
                                        </div>
                                    @endcan
                                    @can("edit", "Occasions")
                                        <a role="button" href="{{ route("Occasions.edit",$occasion->id) }}" class="btn btn-sm btn-outline-dark">
                                            <i class="far fa-edit fa-1-2x vertical-middle"></i>
                                        </a>
                                    @endcan
                                    @can("delete","Occasions")
                                        <div>
                                            <form hidden id="delete-form-{{ $occasion->id }}" action="{{ route("Occasions.destroy",$occasion->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                            </form>
                                            <button type="submit" form="delete-form-{{ $occasion->id }}" class="btn btn-sm btn-outline-dark">
                                                <i class="far fa-trash fa-1-2x vertical-middle"></i>
                                            </button>
                                        </div>
                                    @endcan
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
                                    {{ count($occasions) }}
                                </p>
                                <p class="iransans white-color mb-0">
                                    منتشر شده :
                                    {{  count($occasions->where("published",1)) }}
                                </p>
                                <p class="iransans white-color mb-0">
                                    منتشر نشده :
                                    {{ count($occasions->where("published",0)) }}
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
    <div class="modal fade rtl" id="new_occasion_modal" tabindex="-1" aria-labelledby="new_occasion_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ایجاد مناسبت جدید</h5>
                </div>
                <div class="modal-body" style="max-height: 80vh;overflow-y: auto">
                    <form id="main_submit_form" class="p-3" action="{{ route("Occasions.store") }}" method="post" v-on:submit="submit_form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    عنوان
                                </label>
                                <input class="form-control text-center iransans @error('title') is-invalid @enderror" type="text" name="title" value="{{ old("title") }}">
                                @error('title')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    شرح اسلاید
                                </label>
                                <input class="form-control text-center iransans @error('description') is-invalid @enderror" type="text" name="description" value="{{ old("description") }}">
                                @error('description')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    تصویر
                                    <strong class="red-color">*</strong>
                                </label>
                                <s-file-browser @error('image') class="is-invalid is-invalid-fake" :error_class="'is-invalid'" @enderror file_box_id="image" file_box_name="image" filename_box_id="image_box" :accept="['jpg','jpeg','png','tiff','bmp','svg']" size="3072000"></s-file-browser>
                                @error('image')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
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
    @if($errors->has('images'))
        <script>
            const modal = new bootstrap.Modal(document.getElementById("new_occasion_modal"));
            modal.show();
        </script>
    @endif
@endsection
