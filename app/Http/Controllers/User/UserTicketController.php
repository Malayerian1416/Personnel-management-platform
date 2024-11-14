<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Ticket;
use App\Models\TicketRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UserTicketController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $tickets = TicketRoom::query()->whereHas("tickets",function($query){
                $query->where("employee_id","=",Auth::user()->employee->id);
            })->with([ "tickets.employee","tickets.expert.role","tickets.room","user"])->orderBy("updated_at")->get();
            return view("user.tickets",["tickets" => $tickets]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            return view("user.new_ticket");
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function send(Request $request): array
    {
        try {
            DB::beginTransaction();
            $response = [];
            $request->validate([
                "room_id" => "required",
                "message" => "required",
                "attachment" => "sometimes|nullable|mimes:png,jpg,bmp,tiff,pdf,jpeg|max:365000"
            ], [
                "room_id.required" => "موضوعی انتخاب نشده است",
                "message.required" => "پیامی درج نشده است",
                "attachment.mimes" => "فرمت فایل ضمیمه مورد تایید نمی باشد",
                "attachment.max" => "حجم فایل ضمیمه مورد تایید نمی باشد",
            ]);
            $room_id = $request->input("room_id");
            $expert_id = TicketRoom::query()->with("tickets")->findOrFail($room_id);
            $expert_id = $expert_id->tickets->first()->expert_id;
            Ticket::query()->create([
                "employee_id" => Auth::user()->employee->id,
                "expert_id" => $expert_id,
                "room_id" => $room_id,
                "sender" => "employee",
                "message" => $request->input("message"),
                "attachment" => $request->hasFile("attachment") ? $request->file("attachment")->hashName() : null
            ]);
            $filename = $request->hasFile("attachment") ? $request->file("attachment")->hashName() : null;
            if($filename)
                Storage::disk("ticket_attachments")->put($room_id,$request->file("attachment"));
            DB::commit();
            $notifications = Ticket::TicketMessaging(Auth::user()->employee->contract_id,$expert_id);
            $this->SendNotification($notifications["message"]["users"],$notifications["message"]["data"]);
            $response["result"] = "success";
            $response["tickets"] = TicketRoom::query()->with(["tickets" => function($query){
                $query->where("employee_id","=",Auth::user()->employee->id);
            },"tickets.employee","tickets.expert.role","tickets.room","user"])->get();
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["error"] = $error->getMessage();
            return $response;
        }
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $request->validate([
                "subject" => "required",
                "message" => "required",
                "attachment" => "sometimes|nullable|mimes:png,jpg,bmp,tiff,pdf,jpeg|max:365000"
            ], [
                "subject.required" => "درج عنوان الزامی می باشد",
                "message.required" => "درج شرح تیکت الزامی می باشد",
                "attachment.mimes" => "فرمت فایل ضمیمه مورد تایید نمی باشد",
                "attachment.max" => "حجم فایل ضمیمه مورد تایید نمی باشد",
            ]);
            $expert_id = User::RecipientTicketing(Auth::user()->employee->contract_id);
            if (count($expert_id) == 0)
                throw new \Exception("در حال حاضر کارشناسی جهت پاسخگویی وجود ندارد");
            else {
                $subject = $request->input("subject");
                $message = $request->input("message");
                $room_id = TicketRoom::query()->create(["subject" => $subject, "user_id" => Auth::id()]);
                $filename = $request->hasFile("attachment") ? $request->file("attachment")->hashName() : null;
                if ($filename)
                    Storage::disk("ticket_attachments")->put($room_id->id, $request->file("attachment"));
                Ticket::query()->create([
                    "employee_id" => Auth::user()->employee->id,
                    "expert_id" => $expert_id[0]->id,
                    "room_id" => $room_id->id,
                    "sender" => "employee",
                    "message" => $message,
                    "attachment" => $filename
                ]);
                DB::commit();
                $notifications = Ticket::TicketMessaging(Auth::user()->employee->contract_id,$expert_id[0]->id);
                $this->SendNotification($notifications["message"]["users"],$notifications["message"]["data"]);
                return redirect()->back()->with(["result" => "success","message" => "saved"]);
            }
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }


    public function update(Request $request,$id): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $request->validate(["message" => "required"],["message.required" => "درج متن پیام الزامی می باشد"]);
            $ticket = Ticket::query()->findOrFail($id);
            $ticket->update(["message" => $request->input("message")]);
            DB::commit();
            $notifications = Ticket::TicketMessaging(Auth::user()->employee->contract_id,$ticket->expert_id);
            $this->SendNotification($notifications["message"]["users"],$notifications["message"]["data"]);
            return redirect()->back()->with(["result" => "success","message" => "updated"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $ticket = Ticket::query()->findOrFail($id);
            if ($ticket->attachment)
                Storage::disk("ticket_attachments")->delete($ticket->room_id,$ticket->attachment);
            $ticket->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
