@extends("staff.staff_dashboard")
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h5 class="iransans d-inline-block m-0 fw-bolder">
                سازمان ها
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
            <button class="btn btn-primary d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_organization_modal">
                <i class="fa fa-plus fa-1-6x"></i>
            </button>
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام" data-table="organizations_table" v-on:input="filter_table">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table id="organizations_table" class="table table-hover table-striped pointer-cursor sortArrowWhite" data-filter="[1]">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col" data-sortas="numeric"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col" style="width: 150px"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($organizations as $organization)
                        <tr>
                            <td class="iransans">{{ $organization->id }}</td>
                            <td><span class="iransans">{{ $organization->name }}</span></td>
                            <td>
                                <span class="iransans">
                                     @if($organization->inactive == 1)
                                        <i class="far fa-times-circle red-color fa-1-4x vertical-middle"></i>
                                    @elseif($organization->inactive == 0)
                                        <i class="far fa-check-circle green-color fa-1-4x vertical-middle"></i>
                                    @endif
                                </span>
                            </td>
                            <td><span class="iransans">{{ $organization->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($organization->created_at)->format("Y/m/d") }}</span></td>
                            <td><span class="iransans">{{ verta($organization->updated_at)->format("Y/m/d") }}</span></td>
                            <td>
                                <div class="d-flex flex-row flex-wrap align-items-center justify-content-center gap-2 gap-lg-3">
                                    @can("activation", "Organizations")
                                        <div>
                                            <form hidden id="activation-form-{{ $organization->id }}" action="{{ route("Organizations.activation",$organization->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                            </form>
                                            <button form="activation-form-{{ $organization->id }}" class="btn btn-sm btn-outline-dark">
                                                @if($organization->inactive == 0)
                                                    <i class="far fa-lock fa-1-2x vertical-middle"></i>
                                                @elseif($organization->inactive == 1)
                                                    <i class="far fa-lock-open fa-1-2x vertical-middle"></i>
                                                @endif
                                            </button>
                                        </div>
                                    @endcan
                                    @can("edit", "Organizations")
                                        <a role="button" class="btn btn-sm btn-outline-dark" href="{{route("Organizations.edit",$organization->id)}}">
                                            <i class="far fa-edit fa-1-2x vertical-middle"></i>
                                        </a>
                                    @endcan
                                    @can("delete","Organizations")
                                        <div>
                                            <form hidden id="delete-form-{{ $organization->id }}" action="{{ route("Organizations.destroy",$organization->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                            </form>
                                            <button form="delete-form-{{ $organization->id }}" class="btn btn-sm btn-outline-dark">
                                                <i class="far fa-trash fa-1-2x vertical-middle"></i>
                                            </button>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                    <tfoot class="bg-dark">
                    <tr>
                       <td colspan="7" class="py-2 px-3">
                           <div class="d-flex align-items-center justify-content-start gap-2 gap-lg-4 my-1 px-2">
                               <p class="iransans white-color mb-0">
                                   مجموع :
                                   {{ count($organizations) }}
                               </p>
                               <p class="iransans white-color mb-0">
                                   فعال :
                                   {{  count($organizations->where("inactive",0)) }}
                               </p>
                               <p class="iransans white-color mb-0">
                                   غیر فعال :
                                   {{ count($organizations->where("inactive",1)) }}
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
    <div class="modal fade rtl" id="new_organization_modal" tabindex="-1" aria-labelledby="new_organization_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد سازمان جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("Organizations.store") }}" method="POST" enctype="multipart/form-data" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label iransans">
                                    نام
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iransans @error('name') is-invalid @enderror" type="text" name="name" value="{{ old("name") }}">
                                @error('name')
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
        <script defer>
            $(document).ready(function (){
                let modal = new bootstrap.Modal(document.getElementById("new_organization_modal"), {});
                modal.show();
            });
        </script>
    @endif
@endsection
