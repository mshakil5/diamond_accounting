<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Income;
use App\Models\Transaction;
use Illuminate\Http\Request;

class IncomeController extends Controller
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

    public function index(Request $request)
    {
        $branch_id = auth()->user()->branch_id;

        $accounts = Account::where([
            ['account_type','=', 'Income'],
            ['branch_id','=', $branch_id]
        ])->get();

        $receivables = Account::where([
            ['account_type','=', 'Current Asset-AR'],
            ['branch_id','=', $branch_id]
        ])->get();
        
        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            $trans = Transaction::where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'Income'],
                ['branch_id', '=', $branch_id],
            ])->orderBy('t_date','DESC')->orderBy('id','DESC')->get();

        }else{
            $fromDate = "";
            $toDate = "";
             $trans = Transaction::where('branch_id', $branch_id)->whereIn('table_type',['Income'])->orderBy('t_date','DESC')->orderBy('id','DESC')->get();
        }
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Income Report');
       
        return view('income.create')
            ->with('receivables',$receivables)
            ->with('accounts',$accounts)
            ->with('trans',$trans)->with('pdfhead',$pdfhead);
    }
    
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('income.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        if(empty($request->income_date)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Date\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->account_id)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Account \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->ref)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Ref \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        
        if(empty($request->amount)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Amount \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(empty($request->transaction_type)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Transaction Type \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(($request->transaction_type != 'Advance Adjust') && (empty($request->payment_type))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Payment Type \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(($request->transaction_type == 'Due') && (empty($request->asset_id))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Account receiable holder \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }      
        if(empty($request->description)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Description  \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        try{

            $income = new Transaction();
            $income->account_id = $request->account_id;
            $income->table_type = "Income";
            $income->ref = $request->ref;
            $income->description = $request->description;
            $income->t_date = $request->income_date;
            $income->amount = $request->amount;
            $income->t_rate = $request->tax_rate;
            $income->t_amount = $request->tax_amount;
            $income->at_amount = $request->after_tax_amount;
            $income->transaction_type = $request->transaction_type;
            $income->payment_type = $request->payment_type;
            $income->employee_id = $request->employee_id;
            $income->asset_id = $request->asset_id;
            $income->liability_id;
            $income->expense_id;
            $income->branch_id = auth()->user()->branch_id;
            $income->user_type = auth()->user()->user_type;
            $income->updated_by;
            $income->created_by = auth()->user()->name;
            $income->updated_ip;
            $income->created_ip = request()->ip();
            $income->created_id = auth()->user()->id;
            $income->save();

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Income Created Successfully.</b></div>"; 

            return response()->json(['status'=> 300,'message'=>$message]);
        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $income
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $income)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $income
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Transaction::where($where)->get()->first();
        return response()->json($info);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $income
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $income)
    {
            if(empty($request->income_date)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Date\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->account_id)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Account \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->ref)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Ref \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        
        if(empty($request->amount)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Amount \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(empty($request->transaction_type)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Transaction Type \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(($request->transaction_type != 'Advance Adjust') && (empty($request->payment_type))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Payment Type \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(($request->transaction_type == 'Due') && (empty($request->asset_id))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Account receiable holder \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }      
        if(empty($request->description)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Description  \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $where = [
            'id'=>$income->id
        ];
        $incomeup = Transaction::where($where)->get()->first();
        $incomeup->t_date = $request->income_date;
        $incomeup->account_id = $request->account_id;
        $incomeup->ref = $request->ref;
        $incomeup->amount = $request->amount;
        $incomeup->t_rate = $request->tax_rate;
        $incomeup->t_amount = $request->tax_amount;
        $incomeup->at_amount = $request->after_tax_amount;
        $incomeup->transaction_type = $request->transaction_type;
        $incomeup->payment_type = $request->payment_type;
        $incomeup->liability_id = $request->modelable_id;
        $incomeup->employee_id = $request->employee_id;
        $incomeup->description = $request->description;
        $incomeup->updated_by = auth()->user()->name;
        $incomeup->updated_ip = request()->ip();
        
        if ($incomeup->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Income Updated Successfully.</b></div>"; 
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $income
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $income)
    {
        if(Transaction::destroy($income->id)){
            return response()->json(['success'=>true,'message'=>'Listing Deleted']);
        }
        else{
            return response()->json(['success'=>false,'message'=>'Update Failed']);
        }
    }
}
