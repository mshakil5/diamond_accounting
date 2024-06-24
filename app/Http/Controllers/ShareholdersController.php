<?php

namespace App\Http\Controllers;

use App\Models\Shareholder;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ShareholdersController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $branch_id = auth()->user()->branch_id;
        $data = Shareholder::where('branch_id', $branch_id)->paginate(10);
        return view('shareholder.create')->with('data', $data);
    }


    public function create()
    {
        return view('shareholder.create');
    }

    public function store(Request $request)
    {
        if(empty($request->employee_name)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Employee Name\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->employee_id)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Employee Id\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->email)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Employee Email\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->employee_phone)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Employee Phone\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->employee_address)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Employee Address\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        
        
        try{
            $staff = new Shareholder();
            $staff->employee_name = $request->employee_name;
            $staff->employee_id = $request->employee_id;
            $staff->email = $request->email;
            $staff->employee_phone = $request->employee_phone;
            $staff->employee_address = $request->employee_address;
            $staff->branch_id = auth()->user()->branch_id;
            $staff->user_type = auth()->user()->user_type;
            $staff->updated_by;
            $staff->created_by = auth()->user()->name;
            $staff->updated_ip;
            $staff->created_ip = request()->ip();
            $staff->save();

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Employee Created Successfully.</b></div>"; 

            return response()->json(['status'=> 300,'message'=>$message]);
        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function edit($id)
    {
        $branch_id = auth()->user()->branch_id;
        $where = [
            'id'=>$id,
            'branch_id'=>$branch_id
        ];
        $info = Shareholder::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request, $id)
    {
        
        if(empty($request->employee_name)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Employee Name\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->employee_id)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Employee Id\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->email)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Employee Email\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->employee_phone)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Employee Phone\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->employee_address)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Employee Address\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        
        $branch_id = auth()->user()->branch_id;
        $where = [
            'id'=>$id,
            'branch_id'=>$branch_id
        ];
        $employeetoupdate = Shareholder::where($where)->get()->first();
        $employeetoupdate->employee_name = $request->employee_name;
        $employeetoupdate->employee_id = $request->employee_id;
        $employeetoupdate->email = $request->email;
        $employeetoupdate->employee_phone = $request->employee_phone;
        $employeetoupdate->employee_address = $request->employee_address;
        $employeetoupdate->updated_by = auth()->user()->name;
        $employeetoupdate->updated_ip = request()->ip();
        if ($employeetoupdate->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Employee Updated Successfully.</b></div>"; 
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function deleteaccount($id)
    {

        $branch_id = auth()->user()->branch_id;
        $data = Transaction::where([
            ['employee_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->count();

        if($data>0){
            return response()->json(['success'=>true,'message'=>'Sorry! Already exits other transaction delete first then try']);
        }
        elseif(Shareholder::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Employee has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }

    }




}
