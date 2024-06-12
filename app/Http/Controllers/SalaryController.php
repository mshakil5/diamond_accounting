<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
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
        ])->whereIn('account_name',['Salary','Wages'])->get();

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
                ])->whereIn('account_name',['Salary','Wages'])->whereIn('transactions.table_type',['Expense'])
                ->get();
        }else{

            $expenses = DB::table('accounts')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('transactions','transactions.account_id','=','accounts.id')
                ->where([
                    ['transactions.branch_id', '=', $branch_id]
                ])->whereIn('account_name',['Salary','Wages'])->whereIn('transactions.table_type',['Expense'])
                ->get();
        }
        return view('employee.salary')
            ->with('expenses',$expenses)
            ->with('accounts',$accounts)
            ->with('employees',$employees)
            ->with('report_title','Expense Report');
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


        if(($request->transaction_type != 'Prepaid Adjust') && (empty($request->payment_type))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Payment Type \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if((($request->account_name == 'Salary') || ($request->account_name == 'Wages')) && (empty($request->employee_id))){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Employee \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->description)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Description  \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        try{

            if ($request->tax_amount){

                $account = new Transaction();
                $account->account_id = $request->account_id;
                $account->table_type = "Expense";
                $account->ref = $request->ref;
                $account->description = $request->description;
                $account->t_date = $request->expense_date;
                $account->amount = $request->amount;
                $account->t_rate;
                $account->t_amount;
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

                $account2 = new Transaction();
                $account2->account_id = $request->account_id;
                $account2->table_type = "Expense";
                $account2->ref = $request->ref;
                $account2->description = $request->description;
                $account2->t_date = $request->expense_date;
                $account2->at_amount;
                $account2->t_rate;
                $account2->amount;
                $account2->t_amount = $request->tax_amount;
                $account2->transaction_type;
                $account2->payment_type = "Account Payable";
                $account2->employee_id = $request->employee_id;
                $account2->asset_id;
                $account2->liability_id = $request->modelable_id;
                $account2->expense_id;
                $account2->branch_id = auth()->user()->branch_id;
                $account2->user_type = auth()->user()->user_type;
                $account2->updated_by;
                $account2->created_by = auth()->user()->name;
                $account2->updated_ip;
                $account2->created_ip = request()->ip();
                $account2->created_id = auth()->user()->id;
                $account2->save();


            }else{

                $account3 = new Transaction();
                $account3->account_id = $request->account_id;
                $account3->table_type = "Expense";
                $account3->ref = $request->ref;
                $account3->description = $request->description;
                $account3->t_date = $request->expense_date;
                $account3->amount = $request->amount;
                $account3->t_rate = $request->tax_rate;
                $account3->t_amount = $request->tax_amount;
                $account3->at_amount = $request->after_tax_amount;
                $account3->transaction_type = $request->transaction_type;
                $account3->payment_type = $request->payment_type;
                $account3->employee_id = $request->employee_id;
                $account3->asset_id;
                $account3->liability_id = $request->modelable_id;
                $account3->expense_id;
                $account3->branch_id = auth()->user()->branch_id;
                $account3->user_type = auth()->user()->user_type;
                $account3->updated_by;
                $account3->created_by = auth()->user()->name;
                $account3->updated_ip;
                $account3->created_ip = request()->ip();
                $account3->created_id = auth()->user()->id;
                $account3->save();


            }




            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Salary Created Successfully.</b></div>";

            return response()->json(['status'=> 300,'message'=>$message]);

        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $salary
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $salary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $salary
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
     * @param  \App\Models\Transaction  $salary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $salary)
    {
        if ((empty($request->amount)) && ($request->payment_type == "Account Payable") && (!empty($request->tax_amount))){

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
            if(empty($request->tax_amount)){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Tax amount \" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
            if(empty($request->employee_id)){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Employee \" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }

            if(empty($request->description)){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Description  \" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }

            $where = [
                'id'=>$salary->id
            ];
            $expensetoupdate = Transaction::where($where)->get()->first();
            $expensetoupdate->t_date = $request->expense_date;
            $expensetoupdate->account_id = $request->account_id;
            $expensetoupdate->ref = $request->ref;
            $expensetoupdate->t_amount = $request->tax_amount;
            $expensetoupdate->payment_type = $request->payment_type;
            $expensetoupdate->employee_id = $request->employee_id;
            $expensetoupdate->description = $request->description;
            $expensetoupdate->updated_by = auth()->user()->name;
            $expensetoupdate->updated_ip = request()->ip();

            if ($expensetoupdate->save()) {
                $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Salary Updated Successfully.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
            }else{
                return response()->json(['status'=> 303,'message'=>'Server Error!!']);
            }




        }else{





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
            if(($request->transaction_type != 'Prepaid Adjust') && (empty($request->payment_type))){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Payment Type \" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
            if((($request->account_name == 'Salary') || ($request->account_name == 'Wages')) && (empty($request->employee_id))){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \" Employee \" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
            if(empty($request->description)){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Description  \" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }



            $where = [
                'id'=>$salary->id
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
            $expensetoupdate->employee_id = $request->employee_id;
            $expensetoupdate->description = $request->description;
            $expensetoupdate->updated_by = auth()->user()->name;
            $expensetoupdate->updated_ip = request()->ip();

            if ($expensetoupdate->save()) {
                $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Salary Updated Successfully.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
            }else{
                return response()->json(['status'=> 303,'message'=>'Server Error!!']);
            }

        }




    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $salary
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $salary)
    {
        if(Transaction::destroy($salary->id)){
            return response()->json(['success'=>true,'message'=>'Listing Deleted']);
        }
        else{
            return response()->json(['success'=>false,'message'=>'Update Failed']);
        }
    }

}
