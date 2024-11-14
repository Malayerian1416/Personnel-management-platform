@extends("staff.staff_dashboard")
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">

            <h5 class="iransans d-inline-block m-0">متون پیامک</h5>
            <span>(ایجاد، جستجو و ویرایش)</span>
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
            <button class="btn btn-outline-info d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_sms_phrase_modal">
                <i class="fa fa-plus fa-1-4x me-1"></i>
                <span class="iransans create-button">متن جدید</span>
            </button>
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام سرفصل">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table>
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>دسته بندی</span></th>
                        <th scope="col"><span>عنوان</span></th>
                        <th scope="col"><span>متن</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($phrases as $phrase)
                        <tr>
                            <td><span class="iransans">{{ $phrase->id }}</span></td>
                            <td><span class="iransans">{{ $phrase->category->name }}</span></td>
                            <td><span class="iransans">{{ $phrase->name }}</span></td>
                            <td class="overflow-text"><span class="iransans">{{ $phrase->text }}</span></td>
                            <td><span class="iransans">{{ $phrase->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($phrase->created_at)->format("Y/m/d") }}</span></td>
                            <td><span class="iransans">{{ verta($phrase->updated_at)->format("Y/m/d") }}</span></td>
                            <td class="position-relative">
                                <div class="dropdown table-functions iransans">
                                    <a class="table-functions-button dropdown-toggle border-0 iransans info-color" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cog fa-1-2x"></i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <form class="w-100" id="test-form-{{ $phrase->id }}" action="{{ route("SmsPhrases.test",$phrase->id) }}" method="POST" v-on:submit="submit_form">
                                            @csrf
                                            <button type="submit" form="test-form-{{ $phrase->id }}" class="dropdown-item">
                                                <i class="fa fa-phone"></i>
                                                <span>ارسال تست</span>
                                            </button>
                                        </form>
                                        @can("edit", "SmsPhrases")
                                            <div class="dropdown-divider"></div>
                                            <a role="button" href="{{ route("SmsPhrases.edit",$phrase->id) }}" class="dropdown-item">
                                                <i class="fa fa-edit"></i>
                                                <span class="iransans">ویرایش</span>
                                            </a>
                                        @endcan
                                        @can("delete","SmsPhrases")
                                            <div class="dropdown-divider"></div>
                                            <form class="w-100" id="delete-form-{{ $phrase->id }}" action="{{ route("SmsPhrases.destroy",$phrase->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                                <button type="submit" form="delete-form-{{ $phrase->id }}" class="dropdown-item">
                                                    <i class="fa fa-trash"></i>
                                                    <span class="iransans">حذف</span>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
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
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_sms_phrase_modal" tabindex="-1" aria-labelledby="new_sms_phrase_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد متن جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("SmsPhrases.store") }}" method="POST" v-on:submit="submit_form">
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
                                    متن
                                    <strong class="red-color">*</strong>
                                </label>
                                <textarea class="form-control iransans @error('text') is-invalid @enderror" type="text" name="text">
                                    {{ old("text") }}
                                </textarea>
                                @error('text')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    دسته بندی
                                </label>
                                <select class="form-control text-center iransans @error('category_id') is-invalid @enderror selectpicker-select" data-container="body" title="انتخاب کنید" size="20" data-live-search="true" name="category_id">
                                    @forelse($categories as $category)
                                        <option @if($category->id == old("category_id")) selected @endif value="{{ $category->id }}">{{ $category->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('category_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
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
