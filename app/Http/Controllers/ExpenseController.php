<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
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
            ['account_type','=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->get();

        $payables = Account::where([
            ['account_type','=', 'Liabilities-AP'],
            ['branch_id','=', $branch_id]
        ])->get();


        $employees = Employee::where('branch_id', $branch_id)->get();
        
        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            
            
        $expenses = DB::table('accounts')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('transactions','transactions.account_id','=','accounts.id')
                ->where([
                    ['t_date', '>=', $fromDate],
                    ['t_date', '<=', $toDate],
                    ['transactions.branch_id', '=', $branch_id]
                ])->whereNull('employee_id')->whereIn('transactions.table_type',['Expense'])
                ->orderBy('t_date','DESC')->orderBy('id','DESC')->get();    
            
            

        }else{
            $fromDate = "";
            $toDate = "";
            
            $expenses = DB::table('accounts')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('transactions','transactions.account_id','=','accounts.id')
                ->where([
                    ['transactions.branch_id', '=', $branch_id]
                ])->whereNull('employee_id')->whereIn('transactions.table_type',['Expense'])
                ->orderBy('t_date','DESC')->orderBy('id','DESC')->get();
            
            // $expenses = Transaction::where('branch_id', $branch_id)->whereIn('table_type',['Expense'])->get();
        }
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Expense Report');
        
        return view('expense.create')
            ->with('expenses',$expenses)
            ->with('accounts',$accounts)
            ->with('payables',$payables)
            ->with('employees',$employees)
            ->with('pdfhead',$pdfhead);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('expense.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(empty($request->expense_date)){
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
        if((($request->transaction_type != 'Prepaid Adjust') && ($request->transaction_type != 'Account Payable') && ($request->transaction_type != 'Salary Tax')) && (empty($request->payment_type))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Payment Type \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(($request->payment_type == 'Account Payable') && (empty($request->modelable_id))){
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
                $account->table_type = "Expense";
                $account->ref = $request->ref;
                $account->description = $request->description;
                $account->t_date = $request->expense_date;
                $account->amount = $request->amount;
                $account->t_rate = $request->tax_rate;
                $account->t_amount = $request->tax_amount;
                $account->at_amount = $request->after_tax_amount;
                $account->transaction_type = $request->transaction_type;
                $account->payment_type = $request->payment_type;
                $account->employee_id = $request->employee_id;
                $account->asset_id;
                $account->liability_id = $request->modelable_id;
                $account->expense_id;
                $account->branch_id = auth()->user()->branch_id;
                $account->user_type = auth()->user()->user_type;
                $account->updated_by;
                $account->created_by = auth()->user()->name;
                $account->updated_ip;
                $account->created_ip = request()->ip();
                $account->created_id = auth()->user()->id;
                $account->save();           


            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Expense Created Successfully.</b></div>";

            return response()->json(['status'=> 300,'message'=>$message]);

        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $expense
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
     * @param  \App\Models\Transaction  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $expense)
    {
        if(empty($request->expense_date)){
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
        if((($request->transaction_type != 'Prepaid Adjust') && ($request->transaction_type != 'Account Payable') && ($request->transaction_type != 'Salary Tax')) && (empty($request->payment_type))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Payment Type \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(($request->payment_type == 'Account Payable') && (empty($request->modelable_id))){
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
            'id'=>$expense->id
        ];
        $expensetoupdate = Transaction::where($where)->get()->first();
        $expensetoupdate->t_date = $request->expense_date;
        $expensetoupdate->account_id = $request->account_id;
        $expensetoupdate->ref = $request->ref;
        $expensetoupdate->amount = $request->amount;
        $expensetoupdate->t_rate = $request->tax_rate;
        $expensetoupdate->t_amount = $request->tax_amount;
        $expensetoupdate->at_amount = $request->after_tax_amount;
        $expensetoupdate->transaction_type = $request->transaction_type;
        $expensetoupdate->payment_type = $request->payment_type;
        $expensetoupdate->liability_id = $request->modelable_id;
        $expensetoupdate->employee_id = $request->employee_id;
        $expensetoupdate->description = $request->description;
        $expensetoupdate->updated_by = auth()->user()->name;
        $expensetoupdate->updated_ip = request()->ip();
        if ($expensetoupdate->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Expense Updated Successfully.</b></div>"; 
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $expense)
    {
        if(Transaction::destroy($expense->id)){
            return response()->json(['success'=>true,'message'=>'Listing Deleted']);
        }
        else{
            return response()->json(['success'=>false,'message'=>'Update Failed']);
        }
    }

}