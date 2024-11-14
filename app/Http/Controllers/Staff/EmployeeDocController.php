<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Throwable;

class EmployeeDocController extends Controller
{
    public function image_view($path): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return Storage::disk("employee_docs")->download(str_replace("@","/",$path));
    }
    public function download_application($path): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $file = Storage::disk("temporarily")->path(str_replace("@","/",$path));
        return Response::file($file,[
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline;'
        ]);
    }
    public function print_docs($path,$config = null){
        $pdf = PDF::loadView('layouts.pdf.doc_print', ["image" => base64_encode(Storage::disk("employee_docs")->get(str_replace("@","/",$path)))],[], [
            'format' => 'A4-P'
        ]);
        Session::put('page-config',collect(json_decode($config,true)));
        Session::put('print-url',$path);
        return Response::download($pdf->stream('doc.pdf'),'doc.pdf',[],'inline');
    }
    public function image_delete(Request $request){
        try {
            Storage::disk("employee_docs")->delete($request->path);
            $response["result"] = "success";
            $response["message"] = "تصویر با موفقیت حذف شد";
            $response["employee"] = Employee::query()->with(["contract.organization","registrant_user","user"])->findOrFail($request->id);
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "failed";
            $response["message"] = $error->getMessage();
            return $response;
        }

    }
}
