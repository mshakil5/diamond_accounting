<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Employee;
use App\Models\Liability;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class LiabilityController extends Controller
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
        
        $accounts = Account::where('branch_id', $branch_id)
        ->whereIn('account_type',['Long-term Liabilities','Short-term Liabilities','Liabilities-AP','Vat Payable'])->get();
        
        $liabilities = Transaction::where('branch_id', $branch_id)->whereIn('table_type',['Liabilities'])->get();
        
        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            $liabilities = Transaction::where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'Liabilities'],
                ['branch_id', '=', $branch_id],
            ])->get();

        }else{
            $fromDate = "";
            $toDate = "";
            $liabilities = Transaction::where('branch_id', $branch_id)->whereIn('table_type',['Liabilities'])->get();
        }
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Liability Report');
        
        return view('liability.create')
        ->with('liabilities',$liabilities)
        ->with('accounts',$accounts)
        ->with('pdfhead',$pdfhead);

    }
    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('liability.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        if(empty($request->liability_date)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Date\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->account_type)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \"Account \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->ref)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Ref \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->transaction_type)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Transaction Type \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->amount)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Amount \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if((($request->transaction_type != 'Interest') && ($request->transaction_type != 'Add')) && (empty($request->payment_type))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Payment Type \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->description)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Description  \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        try{
  
                $liability = new Transaction();
                $liability->account_id = $request->account_id;
                $liability->table_type = "Liabilities";
                $liability->ref = $request->ref;
                $liability->description = $request->description;
                $liability->t_date = $request->liability_date;
                $liability->amount;
                $liability->t_rate;
                $liability->t_amount;
                $liability->at_amount = $request->amount;
                $liability->transaction_type = $request->transaction_type;
                $liability->payment_type = $request->payment_type;
                $liability->employee_id;
                $liability->asset_id = $request->asset_id;
                $liability->liability_id;
                $liability->expense_id = $request->expense_id;
                $liability->branch_id = auth()->user()->branch_id;
                $liability->user_type = auth()->user()->user_type;
                $liability->updated_by;
                $liability->created_by = auth()->user()->name;
                $liability->updated_ip;
                $liability->created_ip = request()->ip();
                $liability->created_id = auth()->user()->id;
                $liability->save();

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Liability Created Successfully.</b></div>"; 

            return response()->json(['status'=> 300,'message'=>$message]);
        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $liability
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $liability)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $liability
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];

        $info = DB::table('transactions')
        ->select('transactions.*','accounts.account_type','accounts.account_name')
        ->join('accounts','accounts.id','=','transactions.account_id')
        ->where(['transactions.id' => $id])
        ->get()->first();
        return response()->json($info);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $liability
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $liability)
    {
        
       if(empty($request->liability_date)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Date\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->account_type)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \"Account \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->ref)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Ref \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->transaction_type)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Transaction Type \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->amount)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Amount \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if((($request->transaction_type != 'Interest')&& ($request->transaction_type != 'Add')) && (empty($request->payment_type))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Payment Type \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->description)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Description  \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        
        $where = [
            'id'=>$liability->id
        ];
        $liabilitytoupdate = Transaction::where($where)->get()->first();
        $liabilitytoupdate->t_date = $request->liability_date;
        $liabilitytoupdate->account_id = $request->account_id;
        $liabilitytoupdate->ref = $request->ref;
        $liabilitytoupdate->amount = $request->amount;
        $liabilitytoupdate->at_amount = $request->amount;
        $liabilitytoupdate->transaction_type = $request->transaction_type;
        $liabilitytoupdate->payment_type = $request->payment_type;
        $liabilitytoupdate->description = $request->description;
        $liabilitytoupdate->updated_by = auth()->user()->name;
        $liabilitytoupdate->updated_ip = request()->ip();
        if ($liabilitytoupdate->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Liability Updated Successfully.</b></div>"; 
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $liability
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $liability)
    {
        if(Transaction::destroy($liability->id)){
            return response()->json(['success'=>true,'message'=>'Listing Deleted']);
        }
        else{
            return response()->json(['success'=>false,'message'=>'Update Failed']);
        }
    }
}
