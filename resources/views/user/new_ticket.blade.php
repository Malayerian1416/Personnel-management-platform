@extends("user.user_dashboard")
@section('contents')
    <div class="modal fade rtl" id="new_ticket_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="new_request_modal" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ایجاد تیکت پشتیبانی جدید</h5>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("UserTickets.store") }}" method="post" enctype="multipart/form-data" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label iransans">
                                    موضوع
                                    <strong class="red-color">*</strong>
                                </label>
                                <input type="text" class="form-control iransans @error('subject') is-invalid is-invalid-fake @enderror" name="subject">
                                @error('subject')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label iransans">
                                    شرح تیکت
                                    <span class="iransans red-color">*</span>
                                </label>
                                <textarea class="form-control text-center iransans @error('message') is-invalid @enderror" name="message"></textarea>
                                @error('message')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label iransans">
                                    فایل ضمیمه
                                </label>
                                <s-file-browser @error('avatar') :error_class="'is-invalid'" :error_message="'{{ $message }}'"
                                                @enderror :accept='["png","jpg","jpeg","bmp","tiff","pdf"]' :size="365000"
                                                :filename_box_id="'attachment_filename'" :file_box_id="'attachment'"
                                                :file_box_name="'attachment'">
                                </s-file-browser>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
                        <span class="iransans">ارسال و ذخیره</span>
                    </button>
                    <a href="{{route("user_idle")}}" class="btn btn-outline-secondary iransans">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const modal = new bootstrap.Modal(document.getElementById("new_ticket_modal"), {});
        modal.show();
    </script>
@endsection
