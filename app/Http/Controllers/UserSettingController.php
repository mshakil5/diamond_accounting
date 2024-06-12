<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }


    public function index()
    {
        $branch = Branch::all();
        return view('setting.create')->with('branch',$branch);
    }

    public function update(Request $request){

        if(empty($request->branch_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select branch</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $where = [
            'id'=>auth()->user()->id
        ];
        $branchchange = Admin::where($where)->get()->first();
        $branchchange->branch_id = $request->branch_id;
        if ($branchchange->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Branch Change Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function changePassword(Request $request){

        if(empty($request->password)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Password\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->password === $request->confirmpassword)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $where = [
                'id'=>auth()->user()->id
            ];
            $passwordchange = Admin::where($where)->get()->first();
            $passwordchange->password =Hash::make($request->input('password'));

            if ($passwordchange->save()) {
                $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password Change Successfully.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
            }else{
                return response()->json(['status'=> 303,'message'=>'Server Error!!']);
            }
        }


}
