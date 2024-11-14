<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ContractSubset;
use App\Models\ContractSubsetEmployee;
use App\Models\Employee;
use App\Rules\NationalCodeChecker;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AxiosController extends Controller
{
    public function ContractSubsetEmployeeEdit(Request $request): array
    {
        try {
            $response = [];
            $id = $request->input("id");
            $employee = ContractSubsetEmployee::query()->findOrFail($id);
            $employee->update([
                "user_id" => Auth::id(),
                "name" => $request->input("name") != null ? $request->input("name") : $employee->name,
                "national_code" => $request->input("national_code") != null ? $request->input("national_code") : $employee->national_code,
                "mobile" => $request->input("mobile") != null ? $request->input("mobile") : $employee->mobile
            ]);
            $response["result"] = "success";
            $response["message"] = "عملیات ویرایش با موفقیت انجام شد";
            $response["data"] = ContractSubsetEmployee::query()->with(["user"])->where("contract_subset_id","=",$employee->contract_subset_id)->get()->toArray();
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }

    }
    public function ContractSubsetEmployeeDelete(Request $request): array
    {
        try {
            $response = [];
            $id = $request->input("id");
            $employee = ContractSubsetEmployee::query()->findOrFail($id);
            $contract_subset_id = $employee->contract_subset_id;
            $employee->delete();
            $response["result"] = "success";
            $response["message"] = "عملیات حذف با موفقیت انجام شد";
            $response["data"] = ContractSubsetEmployee::query()->with(["user"])->where("contract_subset_id","=",$contract_subset_id)->get()->toArray();
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }

    }
    public function ContractSubsetEmployeeDeleteAll(Request $request): array
    {
        try {
            $response = [];
            $id = $request->input("id");
            $contract_subset = ContractSubset::query()->findOrFail($id);
            $contract_subset->employees()->delete();
            $response["result"] = "success";
            $response["message"] = "عملیات حذف با موفقیت انجام شد";
            $response["data"] = ContractSubsetEmployee::query()->with(["user"])->where("contract_subset_id","=",$id)->get()->toArray();
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function ContractSubsetEmployeeAdd(Request $request): array
    {
        try {
            $response = [];
            $id = $request->input("id");
            $contract_subset = ContractSubset::query()->findOrFail($id);
            $contract_subset->register_employees()->create([
                "user_id" => Auth::id(),
                "name" => $request->input("new_name"),
                "national_code" => $request->input("new_national_code"),
                "mobile" => $request->input("new_mobile")
            ]);
            $response["result"] = "success";
            $response["message"] = "عملیات ذخیره سازی با موفقیت انجام شد";
            $response["data"] = ContractSubsetEmployee::query()->with(["user"])->where("contract_subset_id","=",$id)->get()->toArray();
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function EditEmployeeDatabaseInformation(Request $request): array
    {
        try {
            $response = [];
            $data = json_decode($request->input("data"),true);
            $employee = Employee::query()->findOrFail($data["id"]);
            $employee->update($data);
            $response["result"] = "success";
            $response["message"] = "عملیات ذخیره سازی با موفقیت انجام شد";
            $response["data"] = $data;
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = $request->toArray();
            return $response;
        }
    }
}
