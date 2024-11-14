@extends("staff.staff_dashboard")
@section('variables')
    <script>
        const user_tickets_data = @json($subjects);
    </script>
    @if(session()->has("subject_id"))
        <script>
            const subject_data = @json(array_values(array_filter($subjects->toArray(), function ($subject){
                return $subject["id"] === intval(session("subject_id"));
            })))[0];
        </script>
    @endif
@endsection
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">

            <h5 class="iransans d-inline-block m-0">تیکت پشتیبانی</h5>
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
        <div class="d-flex flex-column flex-lg-row gap-1 align-items-start justify-content-start py-4 px-3 flex-wrap">
            <div class="ticket-box-information">
                <input id="searchBox" type="text" list="subject_list" class="form-control text-right iransans mb-3 position-sticky top-0" placeholder="جستجو" v-on:input="ticketSearch">
                @forelse($subjects as $subject)
                    <div id="{{"ticket_$subject->id"}}" class="subjects pointer-cursor d-flex flex-column align-items-start justify-content-between gap-2 w-100 p-3 border border-opacity-25 border-secondary rounded-3 bg-light mb-3 ticket-box @if(session()->has("subject_id") && $subject->id === intval(session("subject_id"))) active @endif" v-on:click="openTickets($event,{{$subject->id}})">
                        <div class="d-flex align-items-center justify-content-between gap-2 w-100 mb-3">
                            <div class="d-flex flex-row align-items-center justify-content-start gap-3">
                                @if($subject->user->avatar && Storage::disk("avatars")->exists("$user->id/$user->avatar"))
                                    <img alt="avatar" src="{{"data:image/png;base64,".base64_encode(Storage::disk("avatars")->get("$user->id/$user->avatar"))}}">
                                @else
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 6.4em;height: 6.4em">
                                        <span class="text-white iranyekan" style="font-size: 2rem">{{mb_substr($subject->user->employee->first_name,0,1). " " .mb_substr($subject->user->employee->last_name,0,1)}}</span>
                                    </div>
                                @endif
                                <div class="d-flex flex-column align-items-start justify-content-start gap-1">
                                    <span class="iransans bold-font text-success font-size-xl">{{$subject->user->name}}<label class="font-size-sm ms-1">{{"(".$subject->user->employee->national_code.")"}}</label></span>
                                    <span class="iransans font-size-lg">{{$subject->user->employee->contract->organization->name}}</span>
                                    <span class="iransans text-muted font-size-sm">{{$subject->user->employee->contract->name}}</span>
                                </div>
                            </div>
                        </div>
                        <span class="iransans font-size-xl text-muted ps-2">{{$subject->subject}}</span>
                        <div class="w-100">
                            <p class="iransans text-justify text-nowrap w-100 ps-2" style="text-overflow: ellipsis;overflow: hidden;font-style: italic">
                                {{$subject->tickets()->orderBy("id","desc")->limit(1)->first() ? $subject->tickets()->where("sender","=","employee")->orderBy("id","desc")->limit(1)->first()->message : "پیامی ارسال نشده است"}}
                            </p>
                        </div>
                        <div class="w-100 d-flex flex-row align-items-center justify-content-between gap-1">
                            <div>
                                @if($subject->tickets()->orderBy("id","desc")->limit(1)->first())
                                    @if($subject->tickets()->orderBy("id","desc")->limit(1)->first()->is_read)
                                        <i class="fa fa-check-double vertical-middle text-muted fa-1-2x"></i>
                                    @else
                                        <i class="fa fa-check vertical-middle text-muted fa-1-2x"></i>
                                    @endif
                                @else
                                    <i class="fa fa-check-double vertical-middle text-muted fa-1-2x"></i>
                                @endif
                                <span class="iransans text-muted">
                                    @if($subject->tickets()->orderBy("id","desc")->limit(1)->first())
                                        @if($subject->tickets()->orderBy("id","desc")->limit(1)->first()->is_read)
                                            بازدید شده
                                        @else
                                            جدید
                                        @endif
                                    @else
                                        بازدید شده
                                    @endif
                                </span>
                            </div>
                            <span class="iransans text-muted">{{verta($subject->updated_at)->format("H:i:s Y/m/d")}}</span>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
            <div class="ticket-chats ps-2" v-cloak>
                <h4 v-if="subject?.user?.name" class="iransans text-muted mb-4 ps-4" v-text="subject?.user?.name + ' - ' + subject.subject"></h4>
                <div class="px-3 mb-3" v-for="(ticket,index) in subject.tickets" :key="index">
                    <div v-if="ticket.sender === 'expert'" class="d-flex flex-row align-items-center justify-content-start gap-4">
                        <div class="rounded-circle bg-info d-flex align-items-center justify-content-center" style="width: 3.75em;height: 3.75em">
                            <span class="text-white iranyekan" style="font-size: 1.2rem" v-text="Array.from(ticket.expert.name)[0]"></span>
                        </div>
                        <div class="chat-bubble gap-1 gap-lg-2">
                            <p class="iransans text-muted mb-0">@{{ ticket.expert.name }}</p>
                            <p class="iransans bold-font mb-0" contenteditable="true" v-on:input="EditMessage($event,ticket.id)" style="cursor: text;text-align: justify;word-break: break-word;">@{{ ticket.message }}</p>
                            <a v-if="ticket.attachment" download :href="'/storage/ticket_attachments/'+ticket.id+'/'+ticket.attachment" class="iransans">
                                <span>دانلود فایل ضمیمه</span>
                                <i class="fa fa-download fa-1-2x ms-1"></i>
                            </a>
                            <div class="w-100 d-flex flex-row align-items-center justify-content-between">
                                <div>
                                    <form class="d-inline" :id="`delete_form_${ticket.id}`" :action="GetRoute('Tickets.destroy',ticket.id)" method="post" v-on:submit="submit_form">
                                        @csrf
                                        @method("delete")
                                        <button :form="`delete_form_${ticket.id}`" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa fa-trash-can"></i>
                                        </button>
                                    </form>
                                    <form class="d-inline" :id="`update_form_${ticket.id}`" :action="GetRoute('Tickets.update',ticket.id)" method="post" v-on:submit="submit_form">
                                        @csrf
                                        @method("put")
                                        <input :id="`message_${ticket.id}`" type="hidden" name="message" :value="ticket.message">
                                        <button :form="`update_form_${ticket.id}`" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa fa-pen"></i>
                                        </button>
                                    </form>
                                </div>
                                <span class="iranyekan text-muted" v-text="to_persian_date(ticket.updated_at)"></span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="d-flex flex-row align-items-center justify-content-start gap-4">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 3.75em;height: 3.75em">
                            <span class="text-white iranyekan" style="font-size: 1.2rem" v-text="Array.from(subject.user.employee.first_name)[0] +' '+ Array.from(subject.user.employee.last_name)[0]"></span>
                        </div>
                        <div class="chat-bubble invert gap-1 gap-lg-2">
                            <p class="iransans text-muted mb-0">@{{ subject.user.name }}</p>
                            <p class="iransans bold-font mb-0" style="text-align: justify;word-break: break-word;">@{{ ticket.message }}</p>
                            <a v-if="ticket.attachment" download :href="'/storage/ticket_attachments/'+ticket.id+'/'+ticket.attachment" class="iransans">
                                <span>دانلود فایل ضمیمه</span>
                                <i class="fa fa-download fa-1-2x ms-1"></i>
                            </a>
                            <div class="w-100 d-flex flex-row align-items-center justify-content-end">
                                <span class="iranyekan text-muted" v-text="to_persian_date(ticket.updated_at)"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-show="subject?.user" class="px-3 pb-4 mt-5 position-sticky bottom-0">
                    <div class="d-flex flex-row align-items-center justify-content-start gap-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 3.75em;height: 3.75em">
                            <img src="{{asset("/images/ticket_support.png")}}" style="width: 3.75em;height: 3.75em" alt="همیاران">
                        </div>
                        <div class="chat-bubble gap-1 gap-lg-2 p-0">
                            <form class="w-100" action="{{route("Tickets.store")}}" method="post" v-on:submit="submit_form" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" :value="subject?.id" name="room_id">
                                <textarea class="form-control iransans m-0" name="message" style="min-height: 150px!important;" placeholder="پیام خود را اینجا وارد نمایید"></textarea>
                                <div class="w-100 p-1">
                                    <s-file-browser :accept='["doc","docx","pdf","jpg","jpeg","png","svg","gif","tiff","bmp"]' :file_box_name="'attachment'" :size="2097152" @error('national_card') class="is-invalid is-invalid-fake" :error_class="'is-invalid'" @enderror :input_class="'registration-input-text'"></s-file-browser>
                                </div>
                                <div class="p-1 w-100">
                                    <button class="btn btn-secondary w-100 iransans">
                                        <i class="fab fa-telegram fa-1-6x align-middle pe-2"></i>
                                        ارســال پــیــام
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
