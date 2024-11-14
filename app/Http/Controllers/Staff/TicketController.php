<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Ticket;
use App\Models\TicketRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use SebastianBergmann\Diff\Exception;
use Throwable;

class TicketController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"Tickets");
        try {
            return view("staff.tickets",["subjects" => TicketRoom::query()->with(["user.employee.contract.organization","tickets.expert"])
                ->orderBy("updated_at","desc")->whereHas("user.employee", function ($query){
                    $query->whereIn("contract_id",Contract::GetPermitted());
                })->get()]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }

    }

    public function store(Request $request)
    {
        Gate::authorize("create","Tickets");
        try {
            DB::beginTransaction();
            $request->validate([
                "room_id" => "required",
                "message" => "required",
                "attachment" => "sometimes|nullable|mimes:jpg,jpeg,png,svg,gif,tiff,bmp,doc,docx,pdf|max:2048"
            ], [
                "room_id.required" => "اطلاعات تیکت معتبر نمی باشد",
                "message.required" => "درج متن پیام الزامی می باشد",
                "attachment.mimes" => "فرمت فایل ضمیمه قابل قبول نمی باشد",
                "attachment.max" => "حجم فایل ضمیمه قابل قبول نمی باشد"
            ]);
            $room_id = $request->input("room_id");
            $message = $request->input("message");
            $room = TicketRoom::query()->findOrFail($room_id);
            $employee = $room->tickets()->first()->employee_id;
            $filename = $request->hasFile("attachment") ? $request->file("attachment")->hashName() : null;
            $ticket = $room->tickets()->create([
                "employee_id" => $employee,
                "expert_id" => Auth::id(),
                "sender" => "expert",
                "message" => $message,
                "attachment" => $filename ?: null
            ]);
            if($filename)
                Storage::disk("ticket_attachments")->put($ticket->id,$request->file("attachment"));
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "successful", "subject_id" => $room_id]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"Tickets");
        try {
            DB::beginTransaction();
            $request->validate(["message" => "required"],["message.required" => "درج متن پیام الزامی می باشد"]);
            $ticket = Ticket::query()->findOrFail($id);
            $ticket->update(["message" => $request->input("message")]);
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "updated", "subject_id" => $ticket->room_id]);

        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"Tickets");
        try {
            DB::beginTransaction();
            $ticket = Ticket::query()->findOrFail($id);
            if ($ticket->attachment)
                Storage::disk("ticket_attachments")->delete($ticket->room_id,$ticket->attachment);
            $ticket->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted", "subject_id" => $ticket->room_id]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroyAll($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"Tickets");
        try {
            DB::beginTransaction();
            $room = TicketRoom::query()->with("tickets")->findOrFail($id);
            foreach ($room->tickets as $ticket)
                if ($ticket->attachment)
                    Storage::disk("ticket_attachments")->delete($ticket->room_id,$ticket->attachment);
            $room->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
