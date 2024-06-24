<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Account;
use App\Models\Shareholder;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerequityController extends Controller
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

        $oe = Account::where([
            ['branch_id','=', $branch_id]
        ])->whereIn('account_type',['Owner Equity','Dividend'])->get();

        $shareholders = Shareholder::where([
            ['branch_id','=', $branch_id]
        ])->get();


        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            $ownerequities = Transaction::where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'OwnerEquity'],
                ['branch_id', '=', $branch_id],
            ])->get();

        }else{
            $fromDate = "";
            $toDate = "";            
        $ownerequities = Transaction::where('branch_id', $branch_id)->whereIn('table_type',['OwnerEquity'])->get();
        }

        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Owner Equity Report');
        
        return view('ownerequity.create')->with('ownerequities',$ownerequities)->with('oe',$oe)->with('pdfhead',$pdfhead)->with('shareholders',$shareholders);
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('ownerequity.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        if(empty($request->equity_date)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Date\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->account_id)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Account \" field..!</b></div>"; 
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
        if((($request->transaction_type != 'Add') && ($request->transaction_type != 'Reverse') && ($request->transaction_type != 'Payable')) && (empty($request->payment_type))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Payment Type \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        
        
        if(empty($request->description)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Description  \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        
        try{
            

            $account = new Transaction();
            $account->account_id = $request->account_id;
            $account->table_type = "OwnerEquity";
            $account->ref = $request->ref;
            $account->description = $request->description;
            $account->t_date = $request->equity_date;
            $account->amount;
            $account->t_rate;
            $account->t_amount;
            $account->at_amount = $request->amount;
            $account->transaction_type = $request->transaction_type;
            $account->payment_type = $request->payment_type;
            $account->employee_id;
            $account->asset_id;
            $account->liability_id;
            $account->expense_id;
            $account->shareholder_id = $request->shareholder_id;
            $account->branch_id = auth()->user()->branch_id;
            $account->user_type = auth()->user()->user_type;
            $account->updated_by;
            $account->created_by = auth()->user()->name;
            $account->updated_ip;
            $account->created_ip = request()->ip();
            $account->created_id = auth()->user()->id;
            $account->save();


            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Owner Equity Created Successfully.</b></div>"; 

            return response()->json(['status'=> 300,'message'=>$message]);
        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $ownerequity
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $ownerequity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $ownerequity
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
     * @param  \App\Models\Transaction  $ownerequity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $ownerequity)
    {
        
           if(empty($request->equity_date)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Date\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->account_id)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Account \" field..!</b></div>"; 
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
        
        
        
        if((($request->transaction_type != 'Add') && ($request->transaction_type != 'Reverse') && ($request->transaction_type != 'Payable')) && (empty($request->payment_type))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Payment Type \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->description)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Description  \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $where = [
            'id'=>$ownerequity->id
        ];
        $ownerequitytoupdate = Transaction::where($where)->get()->first();
        $ownerequitytoupdate->t_date = $request->equity_date;
        $ownerequitytoupdate->account_id = $request->account_id;
        $ownerequitytoupdate->ref = $request->ref;
        $ownerequitytoupdate->amount = $request->amount;
        $ownerequitytoupdate->transaction_type = $request->transaction_type;
        $ownerequitytoupdate->payment_type = $request->payment_type;
        $ownerequitytoupdate->description = $request->description;
        $ownerequitytoupdate->shareholder_id = $request->shareholder_id;
        $ownerequitytoupdate->updated_by = auth()->user()->name;
        $ownerequitytoupdate->updated_ip = request()->ip();
        if ($ownerequitytoupdate->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Owner Equity Updated Successfully.</b></div>"; 
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $ownerequity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $ownerequity)
    {
        if(Transaction::destroy($ownerequity->id)){
            return response()->json(['success'=>true,'message'=>'Listing Deleted']);
        }
        else{
            return response()->json(['success'=>false,'message'=>'Update Failed']);
        }
    }
}
