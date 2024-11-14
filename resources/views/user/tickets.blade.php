@extends("user.user_dashboard")
@section("variables")
    <script>
        const user_tickets_data = @json($tickets);
    </script>
@endsection
@section('contents')
    <div class="modal fade rtl" id="tickets_history_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="requests_history_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-md-down modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">سوابق تیکت ها</h5>
                </div>
                <div class="modal-body">
                    <div id="table-scroll-container">
                        <div id="table-scroll" class="table-scroll">
                            <table class="table table-striped sortArrowWhite" data-filter="[1,2,3]" style="min-width: auto">
                                <thead class="bg-menu-dark white-color">
                                <tr class="iransans">
                                    <th scope="col" style="width: 200px"><span>موضوع</span></th>
                                    <th scope="col" style="width: 150px"><span>توسط</span></th>
                                    <th scope="col" style="width: 150px"><span>تاریخ ثبت</span></th>
                                    <th scope="col" style="width: 150px"><span>آخرین ویرایش</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($tickets as $ticket)
                                    <tr class="pointer-cursor" v-on:click="OpenUserTicket($event,{{$ticket->id}})" data-bs-toggle="modal" data-bs-target="#ticket_details_modal">
                                        <td><span class="iransans">{{ $ticket->subject }}</span></td>
                                        <td><span class="iransans">{{ $ticket->user->name }}</span></td>
                                        <td><span class="iransans">{{ verta($ticket->created_at)->format("Y/m/d") }}</span></td>
                                        <td><span class="iransans">{{ verta($ticket->updated_at)->format("Y/m/d") }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">سابقه ای وجود ندارد</td>
                                    </tr>
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
    <div class="modal fade rtl" id="ticket_details_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="requests_history_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-md-down modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">@{{ UserTicketDetails.subject }}</h5>
                </div>
                <div class="modal-body">
                    <div class="fieldset">
                        <span class="legend">
                            <label class="iransans">جزئیات تیکت</label>
                        </span>
                        <div class="fieldset-body" style="max-height:50vh;overflow-y:auto">
                            <div class="w-100" v-for="chat in UserTicketDetails.tickets" :key="chat.id">
                                <div v-if="chat.sender === 'employee'" class="p-3 d-flex flex-row align-items-center justify-content-start gap-4">
                                    <img src="{{asset("images/ticket_user.png")}}" alt="همیاران">
                                    <div class="chat-bubble gap-1 gap-lg-2">
                                        <p class="iransans text-muted mb-0">@{{`${chat.employee.name} (${chat.employee.national_code})`}}</p>
                                        <p class="iransans bold-font mb-0" contenteditable="true" v-on:input="EditMessage($event,chat.id)" style="text-align: justify;word-break: break-word;">@{{chat.message}}</p>
                                        <a v-if="chat.attachment" download :href="`/storage/ticket_attachments/${chat.room_id}/${chat.attachment}`" class="iransans">
                                            <i class="fa fa-paperclip fa-1-2x vertical-middle"></i>
                                            <span>دانلود فایل ضمیمه</span>
                                        </a>
                                        <div class="w-100 d-flex flex-row align-items-center justify-content-between">
                                            <div>
                                                <form class="d-inline" :id="`delete_form_${chat.id}`" :action="GetRoute('UserTickets.destroy',[chat.id])" method="post" v-on:submit="submit_form">
                                                    @csrf
                                                    @method("delete")
                                                    <button :form="`delete_form_${chat.id}`" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fa fa-trash-can"></i>
                                                    </button>
                                                </form>
                                                <form class="d-inline" :id="`update_form_${chat.id}`" :action="GetRoute('UserTickets.update',[chat.id])" method="post" v-on:submit="submit_form">
                                                    @csrf
                                                    @method("put")
                                                    <input :id="`message${chat.id}`" type="hidden" name="message" :value="chat.message">
                                                    <button :form="`update_form_${chat.id}`" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fa fa-pen"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="w-100 d-flex flex-row align-items-center justify-content-end">
                                            <span class="iranyekan text-muted" v-text="PersianDateString(chat.updated_at,true)"></span>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="p-3 d-flex flex-row align-items-center justify-content-end gap-4">
                                    <div class="chat-bubble-invert gap-1 gap-lg-2">
                                        <p class="iransans text-muted mb-0">@{{`${chat.expert.name} (${chat.expert.role.name})`}}</p>
                                        <p class="iransans bold-font mb-0" style="text-align: justify;word-break: break-word;">@{{chat.message}}</p>
                                        <a v-if="chat.attachment" download :href="`/storage/ticket_attachments/${chat.room_id}/${chat.attachment}`" class="iransans">
                                            <i class="fa fa-paperclip fa-1-2x vertical-middle"></i>
                                            <span>دانلود فایل ضمیمه</span>
                                        </a>
                                        <div class="w-100 d-flex flex-row align-items-center justify-content-end">
                                            <span class="iranyekan text-muted" v-text="PersianDateString(chat.updated_at,true)"></span>
                                        </div>
                                    </div>
                                    <img src="{{asset("images/ticket_support.png")}}" alt="همیاران">
                                </div>
                            </div>
                        </div>
                    </div>
                    <textarea class="form-control iransans mt-3 mb-2" placeholder="پیام جدید خود را اینجا بنویسید" v-model="UserMessage"></textarea>
                    <label class="form-label iransans">فایل ضمیمه (در صورت نیاز)</label>
                    <s-file-browser :accept='["png","jpg","tiff","bmp","jpeg","pdf"]' :size="365000"></s-file-browser>
                    <button class="w-100 btn btn-success mt-3" v-on:click="SendUserTicket">
                        <i class="fa fa-send fa-1-2x me-2"></i>
                        <span class="iransans">ارسال</span>
                    </button>
                </div>
                <div class="modal-footer bg-menu">
                    <button class="btn btn-outline-secondary iransans" data-bs-toggle="modal" data-bs-target="#tickets_history_modal">
                        بازگشت
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const modal = new bootstrap.Modal(document.getElementById("tickets_history_modal"), {});
        modal.show();
    </script>
@endsection
