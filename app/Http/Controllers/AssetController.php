<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AssetController extends Controller
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

        $expenses = Account::where([
            ['account_type','=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->get();

        $payables = Account::where([
            ['account_type','=', 'Liabilities-AP'],
            ['branch_id','=', $branch_id]
        ])->get();


        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            $transaction = Transaction::where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'Asset'],
                ['branch_id', '=', $branch_id],
            ])->get();

        }else{
            $fromDate = "";
            $toDate = "";
            $transaction = Transaction::where('branch_id', $branch_id)->whereIn('table_type',['Asset'])->get();
        }

        $accounts = Account::where('branch_id', $branch_id)->whereIn('account_type',['Fixed Asset','Current Asset','Current Asset-AR','Vat Receivable'])->get();
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Asset Report');
        
        return view('asset.create')
            ->with('accounts',$accounts)
            ->with('payables',$payables)
            ->with('transaction',$transaction)
            ->with('expenses',$expenses)->with('pdfhead',$pdfhead);
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('asset.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(empty($request->asset_date)){            
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
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Transaction Type \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(($request->transaction_type == 'Adjust') && (empty($request->expense_id))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Adjust For \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(empty($request->amount)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Amount \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if((($request->transaction_type != 'Depreciation') && ($request->transaction_type != 'Adjust') && ($request->transaction_type != 'Add')) && (empty($request->payment_type))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Payment Type \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(($request->payment_type == 'Account Payable') && (empty($request->liability_id))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Payable Holder \" field..!</b></div>"; 
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
            $account->table_type = "Asset";
            $account->ref = $request->ref;
            $account->description = $request->description;
            $account->t_date = $request->asset_date;
            $account->amount;
            $account->t_rate;
            $account->t_amount;
            $account->at_amount = $request->amount;
            $account->transaction_type = $request->transaction_type;
            $account->payment_type = $request->payment_type;
            $account->employee_id;
            $account->asset_id;
            $account->liability_id = $request->liability_id;
            $account->expense_id = $request->expense_id;
            $account->branch_id = auth()->user()->branch_id;
            $account->user_type = auth()->user()->user_type;
            $account->updated_by;
            $account->created_by = auth()->user()->name;
            $account->updated_ip;
            $account->created_ip = request()->ip();
            $account->created_id = auth()->user()->id;
            $account->save();

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Asset Created Successfully.</b></div>"; 

            return response()->json(['status'=> 300,'message'=>$message]);
        }catch (\Exception $e){
             return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $asset
     * @return \Illuminate\Http\Response
     */
    public function show(Asset $asset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $asset
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
     * @param  \App\Models\Transaction  $asset
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $asset)
    {
       if(empty($request->asset_date)){            
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
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Transaction Type \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(($request->transaction_type == 'Adjust') && (empty($request->expense_id))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Adjust For \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(empty($request->amount)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Amount \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if((($request->transaction_type != 'Depreciation') && ($request->transaction_type != 'Adjust') && ($request->transaction_type != 'Add')) && (empty($request->payment_type))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Payment Type \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(($request->payment_type == 'Account Payable') && (empty($request->liability_id))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Payable Holder \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(empty($request->description)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Description  \" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $where = [
            'id'=>$asset->id
        ];
        $assettoupdate = Transaction::where($where)->get()->first();
        $assettoupdate->account_id = $request->account_id;
        $assettoupdate->table_type = "Asset";
        $assettoupdate->ref = $request->ref;
        $assettoupdate->description = $request->description;
        $assettoupdate->t_date = $request->asset_date;
        $assettoupdate->amount;
        $assettoupdate->t_rate;
        $assettoupdate->t_amount;
        $assettoupdate->at_amount = $request->amount;
        $assettoupdate->transaction_type = $request->transaction_type;
        $assettoupdate->payment_type = $request->payment_type;
        $assettoupdate->employee_id;
        $assettoupdate->asset_id;
        $assettoupdate->liability_id= $request->liability_id;
        $assettoupdate->expense_id= $request->expense_id;
        $assettoupdate->branch_id = auth()->user()->branch_id;
        $assettoupdate->user_type = auth()->user()->user_type;
        $assettoupdate->updated_by = auth()->user()->name;
        $assettoupdate->updated_ip = request()->ip();

        if ($assettoupdate->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Asset Updated Successfully.</b></div>"; 
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
             return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $asset
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $asset)
    {
        if(Transaction::destroy($asset->id)){
            return response()->json(['success'=>true,'message'=>'Listing Deleted']);
        }
        else{
            return response()->json(['success'=>false,'message'=>'Update Failed']);
        }
    }
}
