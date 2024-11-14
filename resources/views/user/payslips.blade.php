@extends("user.user_dashboard")
@section("variables")
    <script>
        const user_payslips_data = @json($payslips);
    </script>
@endsection
@section('contents')
    <div class="modal fade rtl" id="payslips_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="requests_history_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-md-down modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">فیش حقوقی</h5>
                </div>
                <div class="modal-body">
                    <div id="table-scroll-container">
                        <div id="table-scroll" class="table-scroll">
                            <table id="contracts_table" class="table table-striped table-hover sortArrowWhite" data-filter="[1,2,3]" style="min-width: auto">
                                <thead class="bg-menu-dark white-color">
                                <tr class="iransans">
                                    <th scope="col" style="width: 150px"><span>شناسه یکتا</span></th>
                                    <th scope="col" style="width: 100px"><span>سال</span></th>
                                    <th scope="col" style="width: 100px"><span>ماه</span></th>
                                    <th scope="col" style="width: 150px"><span>ثبت</span></th>
                                    <th scope="col" style="width: 150px"><span>ویرایش</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($payslips as $payslip)
                                    <tr class="pointer-cursor" data-bs-toggle="modal" data-bs-target="#payslip_details_modal" v-on:click="OpenUserPayslip($event,{{$payslip->id}})">
                                        <td><span class="iransans">{{ $payslip->i_number }}</span></td>
                                        <td><span class="iransans">{{ $payslip->persian_year }}</span></td>
                                        <td><span class="iransans">{{ $payslip->persian_month_name }}</span></td>
                                        <td><span class="iransans">{{ verta($payslip->created_at)->format("Y/m/d") }}</span></td>
                                        <td><span class="iransans">{{ verta($payslip->updated_at)->format("Y/m/d") }}</span></td>
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
    <div class="modal fade rtl" id="payslip_details_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="requests_history_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-md-down modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans" v-text="UserPayslip.persian_month_name + ' ماه سال ' + UserPayslip.persian_year"></h5>
                </div>
                <div class="modal-body">
                    <table class="payslip">
                        <tr style="background: whitesmoke">
                            <td colspan="2" class="iransans text-center fw-bold" style="width: 30%">کارکرد</td>
                            <td colspan="2" class="iransans text-center fw-bold" style="width: 35%">مزایا</td>
                            <td colspan="2" class="iransans text-center fw-bold" style="width: 35%">کسورات</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;width: 20%">
                                <div style="text-align: right">
                                    <div class="iransans pb-2" v-for="(function_titles,index) in UserPayslipDetails.functions" :key="index" v-text="function_titles['title']"></div>
                                </div>
                            </td>
                            <td style="vertical-align: top;width: 15%">
                                <div>
                                    <div class="iransans pb-2" v-for="(function_values,index) in UserPayslipDetails.functions" :key="index" v-text="function_values['value']"></div>
                                </div>
                            </td>
                            <td style="vertical-align: top;width: 30%">
                                <div style="text-align: right">
                                    <div class="iransans pb-2" v-for="(advantage,index) in UserPayslipDetails.advantages" :key="index" v-text="advantage['title']"></div>
                                </div>
                            </td>
                            <td style="vertical-align: top;width: 20%">
                                <div>
                                    <div class="iransans pb-2" v-for="(advantage,index) in UserPayslipDetails.advantages" :key="index" v-text="advantage['value']"></div>
                                </div>
                            </td>
                            <td style="vertical-align: top;width: 30%">
                                <div style="text-align: right">
                                    <div class="iransans pb-2" v-for="(deduction,i) in UserPayslipDetails.deductions" :key="i" v-text="deduction['title']"></div>
                                </div>
                            </td>
                            <td style="vertical-align: top;width: 20%">
                                <div>
                                    <div class="iransans pb-2" v-for="(deduction,i) in UserPayslipDetails.deductions" :key="i" v-text="deduction['value']"></div>
                                </div>
                            </td>
                        </tr>
                        <tfoot>
                        <tr style="background: whitesmoke">
                            <td colspan="2"></td>
                            <td><span class="iransans">جمع کل مزایا </span></td>
                            <td><span class="iransans fw-bolder font-size-lg" v-text="UserPayslipDetails.total_advantages"></span></td>
                            <td><span class="iransans">جمع کل کسورات </span></td>
                            <td><span class="iransans fw-bolder font-size-lg" v-text="UserPayslipDetails.total_deductions"></span></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="border-0"></td>
                            <td colspan="2">
                                <span class="iransans font-size-lg fw-bold me-3">قابل پرداخت : </span>
                                <span class="iransans fw-bolder font-size-xl" v-text="UserPayslipDetails.total_net"></span>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer bg-menu">
                    <a class="btn btn-primary iransans" role="button" :href="GetRoute('UserPaySlips.download',[UserPayslip?.id ? UserPayslip?.id : 0])">
                        دانلود Pdf
                    </a>
                    <button class="btn btn-outline-secondary iransans" data-bs-toggle="modal" data-bs-target="#payslips_modal">
                        بازگشت
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const modal = new bootstrap.Modal(document.getElementById("payslips_modal"), {});
        modal.show();
    </script>
@endsection
