
@extends("user.user_dashboard")
@section("variables")
    <script>
        const applications_data = @json($applications);
    </script>
    @if(old("application_form_type"))
        <script>
            const select_model_data = '{{old("application_form_type")}}';
        </script>
    @endif
@endsection
@section('contents')
    <div class="modal fade rtl" id="new_request_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="new_request_modal" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ایجاد درخواست جدید</h5>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("ApplicationForms.store") }}" method="post" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label iransans">
                                    انتخاب نوع درخواست
                                </label>
                                <select class="form-control selectpicker-select iransans @error('application_form_type') is-invalid is-invalid-fake @enderror" data-max-size="10" name="application_form_type" title="انتخاب کنید" v-model="select_model">
                                    <option v-for="application in applications" :key="application.id" :value="application.application_form_type">@{{ application.name }}</option>
                                </select>
                                @error('application_form_type')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div v-cloak v-show="select_model === 'LoanPaymentConfirmationApplication' || select_model === 'EmploymentCertificateApplication'" class="col-12 mb-3">
                                <label class="form-label iransans">
                                    نهاد درخواست کننده
                                    <span class="iransans red-color">*</span>
                                </label>
                                <input class="form-control text-center iransans @error('recipient') is-invalid @enderror" name="recipient" type="text" placeholder="نام و شعبه بانک، موسسه، سازمان و ..." value="{{old("recipient")}}">
                                @error('recipient')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div v-cloak v-show="select_model === 'LoanPaymentConfirmationApplication'" class="col-12 mb-3">
                                <label class="form-label iransans">
                                    نام وام گیرنده (جهت ضمانت)
                                </label>
                                <input class="form-control text-center iransans" name="borrower" type="text" placeholder="در صورتی که وام گیرنده خود پرسنل نباشد" value="{{old("borrower")}}">
                            </div>
                            <div v-cloak v-show="select_model === 'LoanPaymentConfirmationApplication'" class="col-12 mb-3">
                                <label class="form-label iransans">
                                    مبلغ وام (ریال)
                                    <span class="iransans red-color">*</span>
                                </label>
                                <input class="form-control text-center iransans thousand_separator @error('loan_amount') is-invalid @enderror" autocomplete="off" name="loan_amount" type="text" value="{{old("loan_amount")}}">
                                @error('loan_amount')
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
        const modal = new bootstrap.Modal(document.getElementById("new_request_modal"), {});
        modal.show();
    </script>
@endsection
