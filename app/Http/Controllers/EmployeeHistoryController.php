<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeHistory;
use Illuminate\Http\Request;

class EmployeeHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index()
    {
        $branch_id = auth()->user()->branch_id;
        $employees = Employee::where('branch_id', $branch_id)->get();
        $histries = EmployeeHistory::where('branch_id', $branch_id)->paginate(10);
        return view('employee.employee_history')->with('histries',$histries)->with('employees',$employees);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('employee.employee_history');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request);
        
        if(empty($request->employee_id)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Employee \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->start_datetime)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Start Date and Time\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->end_datetime)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"End Date and Time\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->history_desc)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Description\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        try{
            $histries = new EmployeeHistory();
            $histries->employee_id = $request->employee_id;
            $histries->start_datetime = $request->start_datetime;
            $histries->end_datetime = $request->end_datetime;
            $histries->history_desc = $request->history_desc;
            $histries->branch_id = auth()->user()->branch_id;
            $histries->user_type = auth()->user()->user_type;
            $histries->updated_by = "";
            $histries->created_by = auth()->user()->name;

            $histries->save();
            //dd($histries);
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Employee Histry Created Successfully.</b></div>"; 

            return response()->json(['status'=> 300,'message'=>$message]);
        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmployeeHistory  $employeeHistory
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeHistory $employeeHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmployeeHistory  $employeeHistory
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $branch_id = auth()->user()->branch_id;
        $where = [
            'id'=>$id,
            'branch_id'=>$branch_id
        ];
        $info = EmployeeHistory::where($where)->get()->first();
        return response()->json($info);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EmployeeHistory  $employeeHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeHistory $employeeHistory)
    {
        
        if(empty($request->employee_id)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Employee \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->start_datetime)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Start Date and Time\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->end_datetime)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"End Date and Time\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->history_desc)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Description\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        
        $branch_id = auth()->user()->branch_id;
        $where = [
            'id'=>$employeeHistory->id,
            'branch_id'=>$branch_id
        ];
        $histriestoupdate = EmployeeHistory::where($where)->get()->first();
        $histriestoupdate->employee_id = $request->employee_id;
        $histriestoupdate->start_datetime = $request->start_datetime;
        $histriestoupdate->end_datetime = $request->end_datetime;
        $histriestoupdate->history_desc = $request->history_desc;
        $histriestoupdate->updated_by = auth()->user()->name;
        if ($histriestoupdate->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Employee Histry Updated Successfully.</b></div>"; 
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmployeeHistory  $employeeHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeHistory $employeeHistory)
    {
        if(EmployeeHistory::destroy($employeeHistory->id)){
            return response()->json(['success'=>true,'message'=>'Listing Deleted']);
        }
        else{
            return response()->json(['success'=>false,'message'=>'Update Failed']);
        }
    }
}
