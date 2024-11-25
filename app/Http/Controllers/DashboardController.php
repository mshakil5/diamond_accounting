<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){

        $branch_id = auth()->user()->branch_id;


        //  for cash calculation
        $receivecurrentcash = Transaction::whereIn('transaction_type', [ 'Sold','Receive'])->where([
            ['table_type', '=', 'Asset'],
            ['payment_type', '=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $paymentcurrentcash = Transaction::whereIn('transaction_type', [ 'Purchase','Payment'])->where([
            ['table_type', '=', 'Asset'],
            ['payment_type', '=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $totalassetscash = $receivecurrentcash  - $paymentcurrentcash;
        $receiveliabilitycash = Transaction::whereIn('transaction_type', ['Receive'])->where([
            ['table_type', '=', 'Liabilities'],
            ['payment_type', '=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $paymentliabilitycash = Transaction::whereIn('transaction_type', [ 'Payment'])->where([
            ['table_type', '=', 'Liabilities'],
            ['payment_type', '=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $totalliabilitycash = $receiveliabilitycash - $paymentliabilitycash;
        $receiveequitycash = Transaction::whereIn('transaction_type', [ 'Receive'])->where([
            ['table_type', '=', 'OwnerEquity'],
            ['payment_type', '=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $paymentequitycash = Transaction::whereIn('transaction_type', [ 'Payment'])->where([
            ['table_type', '=', 'OwnerEquity'],
            ['payment_type', '=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $totalequitycash = $receiveequitycash - $paymentequitycash;
        $expensecash = Transaction::where([
            ['payment_type', '=', 'Cash'],
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNull('employee_id')->sum('amount');

        $expensesalarycash = Transaction::where([
            ['payment_type', '=', 'Cash'],
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNotNull('employee_id')->sum('at_amount');

        $incomecashNotRefund = Transaction::where([
            ['payment_type', '=', 'Cash'],
            ['table_type', '=', 'Income'],
            ['branch_id','=', $branch_id]
        ])
        ->where('transaction_type','!=',  'Refund')
        ->sum('amount');

        $incomecashRefund = Transaction::where([
            ['payment_type', '=', 'Cash'],
            ['table_type', '=', 'Income'],
            ['branch_id','=', $branch_id]
        ])
        ->where('transaction_type', 'Refund')
        ->sum('amount');

        $incomecash = $incomecashNotRefund - $incomecashRefund;





        $totalcash = $totalassetscash + $totalliabilitycash + $totalequitycash + $incomecash - $expensecash-$expensesalarycash;
        //  for cash calculation end


        //  for bank calculation
        $receivecurrentbank = Transaction::whereIn('transaction_type', [ 'Sold','Receive'])->where([
            ['table_type', '=', 'Asset'],
            ['payment_type', '=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $paymentcurrentbank = Transaction::whereIn('transaction_type', [ 'Purchase','Payment'])->where([
            ['table_type', '=', 'Asset'],
            ['payment_type', '=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $totalassetsbank = $receivecurrentbank - $paymentcurrentbank;
        $receiveliabilitybank = Transaction::whereIn('transaction_type', [ 'Receive'])->where([
            ['table_type', '=', 'Liabilities'],
            ['payment_type', '=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $paymentliabilitybank = Transaction::whereIn('transaction_type', [ 'Payment'])->where([
            ['table_type', '=', 'Liabilities'],
            ['payment_type', '=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $totalliabilitybank = $receiveliabilitybank - $paymentliabilitybank;
        $receiveequitybank = Transaction::whereIn('transaction_type', [ 'Receive'])->where([
            ['table_type', '=', 'OwnerEquity'],
            ['payment_type', '=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $paymentequitybank = Transaction::whereIn('transaction_type', [ 'Payment'])->where([
            ['table_type', '=', 'OwnerEquity'],
            ['payment_type', '=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $totalequitybank = $receiveequitybank - $paymentequitybank;
        $expensebank = Transaction::where([
            ['payment_type', '=', 'Bank'],
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNull('employee_id')->sum('amount');

        $expensesalarybank = Transaction::where([
            ['payment_type', '=', 'Bank'],
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNotNull('employee_id')->sum('at_amount');

        
        $incomebankNotRefund = Transaction::where([
            ['payment_type', '=', 'Bank'],
            ['table_type', '=', 'Income'],
            ['branch_id','=', $branch_id]
        ])
        ->where('transaction_type','!=',  'Refund')
        ->sum('amount');

        $incomebankRefund = Transaction::where([
            ['payment_type', '=', 'Bank'],
            ['table_type', '=', 'Income'],
            ['branch_id','=', $branch_id]
        ])
        ->where('transaction_type', 'Refund')
        ->sum('amount');

        $incomebank = $incomebankNotRefund - $incomebankRefund;

        $totalbank = $totalassetsbank + $totalliabilitybank + $totalequitybank + $incomebank - $expensebank - $expensesalarybank;
        //    for bank calculation end






        //  for Room Sales calculation

        $roomsales = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'Income'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereMonth('t_date', Carbon::now()->month)
            ->whereIn('transaction_type', [ 'Due','Current','Advance Adjust'])
            ->sum('amount');
        //  for Room Sales calculation end




        //  for Expense calculation
        $interest = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.transaction_type','=', 'Interest'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereMonth('t_date', Carbon::now()->month)
            ->sum('at_amount');

        $adjustexpenses = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereNotNull('expense_id')
            ->whereMonth('t_date', Carbon::now()->month)
            ->sum('at_amount');

        $dep = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.transaction_type','=', 'Depreciation'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereMonth('t_date', Carbon::now()->month)
            ->sum('at_amount');

        $expense = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'Expense'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereNull('employee_id')
            ->whereMonth('t_date', Carbon::now()->month)
            ->whereIn('transaction_type', [ 'Due','Current','Prepaid Adjust'])
            ->sum('amount');

        $Salaryexpense = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'Expense'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereNotNull('employee_id')
            ->whereMonth('t_date', Carbon::now()->month)
            ->whereIn('transaction_type', [ 'Due','Current','Prepaid Adjust'])
            ->sum('at_amount');


        $totalexpense = $interest + $adjustexpenses + $dep + $expense + $Salaryexpense;

        // for Expense calculation end




        //    monthly sales transaction
        $monthlysales = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'Income'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereMonth('t_date', Carbon::now()->month)
            ->whereIn('transaction_type', [ 'Due','Current','Advance Adjust'])
            ->sum('amount');
        //   monthly sales transaction end




        return view('admin.dashboard')
            ->with('totalexpense',$totalexpense)
            ->with('monthlysales',$monthlysales)
            ->with('roomsales',$roomsales)
            ->with('totalcash',$totalcash)
            ->with('totalbank',$totalbank);
    }
}
