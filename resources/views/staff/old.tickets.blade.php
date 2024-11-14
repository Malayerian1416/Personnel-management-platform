@extends("staff.staff_dashboard")
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
        <div class="input-group mb-2">
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با موضوع و نام خانوادگی و کد ملی">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div class="accordion px-2 py-3" id="subjects">
            @forelse($tickets as $ticket)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="subjects{{$ticket["id"]}}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#subject-detail{{$ticket["id"]}}" aria-expanded="false" aria-controls="subject-detail{{$ticket["id"]}}">
                            <span class="iransans">{{ "موضوع : ".$ticket["subject"]." - ".$ticket["timestamp"] }}</span>
                        </button>
                    </h2>
                    <div id="subject-detail{{$ticket["id"]}}" class="accordion-collapse collapse" aria-labelledby="subjects{{$ticket["id"]}}" data-bs-parent="#subjects">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <form class="d-inline mb-3" id="delete_all_form_{{$ticket["id"]}}" action="{{route("Tickets.destroyAll",$ticket["id"])}}" method="post" v-on:submit="submit_form">
                                    @csrf
                                    @method("delete")
                                    <button form="delete_all_form_{{$ticket["id"]}}" class="btn btn-outline-secondary">
                                        <i class="fa fa-trash-can fa-1-2x"></i>
                                    </button>
                                </form>
                            </div>
                            @forelse($ticket["employees"] as $employee)
                                <div class="accordion" id="details">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="details{{$employee["id"]}}">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#detail{{$employee["id"]}}" aria-expanded="false" aria-controls="detail{{$employee["id"]}}">
                                                <span class="iransans">{{ "{$employee["employee"]} - {$employee["organization"]}" }}</span>
                                            </button>
                                        </h2>
                                        <div id="detail{{$employee["id"]}}" class="accordion-collapse collapse" aria-labelledby="details{{$employee["id"]}}" data-bs-parent="#details">
                                            <div class="accordion-body">
                                                @forelse($employee["messages"] as $message)
                                                    @if($message["sender"] == "expert")
                                                        <div class="p-3 d-flex flex-row align-items-center justify-content-start gap-4">
                                                            <img src="{{asset("/images/ticket_support.png")}}" alt="همیاران">
                                                            <div class="chat-bubble gap-1 gap-lg-2">
                                                                <p class="iransans text-muted mb-0">{{ $employee["expert"] }}</p>
                                                                <p class="iransans bold-font mb-0" contenteditable="true" v-on:input="EditMessage($event,{{$message["id"]}})" style="cursor: text;text-align: justify;word-break: break-word;">{{ $message["message"] }}</p>
                                                                @if($message["attachment"])
                                                                    <a download href="{{"/storage/ticket_attachments/{$ticket["id"]}/{$message["attachment"]}"}}" class="iransans">
                                                                        <span>دانلود فایل ضمیمه</span>
                                                                        <i class="fa fa-download fa-1-2x ms-1"></i>
                                                                    </a>
                                                                @endif
                                                                <div class="w-100 d-flex flex-row align-items-center justify-content-between">
                                                                    <div>
                                                                        <form class="d-inline" id="delete_form_{{$message["id"]}}" action="{{route("Tickets.destroy",$message["id"])}}" method="post" v-on:submit="submit_form">
                                                                            @csrf
                                                                            @method("delete")
                                                                            <button form="delete_form_{{$message["id"]}}" class="btn btn-sm btn-outline-secondary">
                                                                                <i class="fa fa-trash-can"></i>
                                                                            </button>
                                                                        </form>
                                                                        <form class="d-inline" id="update_form_{{$message["id"]}}" action="{{route("Tickets.update",$message["id"])}}" method="post" v-on:submit="submit_form">
                                                                            @csrf
                                                                            @method("put")
                                                                            <input id="message{{$message["id"]}}" type="hidden" name="message" value="{{ $message["message"] }}">
                                                                            <button form="update_form_{{$message["id"]}}" class="btn btn-sm btn-outline-secondary">
                                                                                <i class="fa fa-pen"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                    <span class="iranyekan text-muted">{{$message["timestamp"]}}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="p-3 d-flex flex-row align-items-center justify-content-end gap-4">
                                                            <div class="chat-bubble-invert gap-1 gap-lg-2">
                                                                <p class="iransans text-muted mb-0">{{ $employee["employee"] }}</p>
                                                                <p class="iransans bold-font mb-0" style="text-align: justify;word-break: break-word;">{{$message["message"]}}</p>
                                                                @if($message["attachment"])
                                                                    <a download href="{{"/storage/ticket_attachments/{$ticket["id"]}/{$message["attachment"]}"}}" class="iransans">
                                                                        <span>دانلود فایل ضمیمه</span>
                                                                        <i class="fa fa-download fa-1-2x ms-1"></i>
                                                                    </a>
                                                                @endif
                                                                <div class="w-100 d-flex flex-row align-items-center justify-content-end">
                                                                    <span class="iranyekan text-muted">{{$message["timestamp"]}}</span>
                                                                </div>
                                                            </div>
                                                            <img src="{{asset("/images/ticket_user.png")}}" alt="همیاران">
                                                        </div>
                                                    @endif
                                                @empty
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
    </div>
@endsection
