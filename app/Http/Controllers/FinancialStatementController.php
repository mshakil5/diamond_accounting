<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Asset;
use App\Models\Depreciation;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Liability;
use App\Models\Ownerequity;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialStatementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function getFinancialStatement(Request $request){

        if(!empty($request->input('toDate'))){
            $toDate   = $request->input('toDate');
			//query with date
        $totalincome = 0;
        $totalexpense = 0;
        $totalsalaryexp = 0;
        $rcvvattotal = 0;
        $incomevattotal = 0;
        $totaladjustexpense = 0;
        $totaldepreciation = 0;
        $totaladjustliability = 0;

        $branch_id = auth()->user()->branch_id;
        $incomes = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type','=', 'Income'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', [ 'Due','Current','Advance Adjust'])->groupBy('account_id')->get();
        foreach ($incomes as $income){
            $income->sumamount;
            $totalincome = $income->sumamount + $totalincome;
        }
        $adjustexpenses = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Asset'],
            ['branch_id','=', $branch_id]
        ])->whereNotNull('expense_id')->groupBy('account_id')->get();
        foreach ($adjustexpenses as $adjustexpense){
            $adjustexpense->sumamount;
            $totaladjustexpense = $adjustexpense->sumamount + $totaladjustexpense;
        }
        $depreciations = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Asset'],
            ['branch_id','=', $branch_id],
            ['transaction_type','=', 'Depreciation']
        ])->groupBy('account_id')->get();
        foreach ($depreciations as $depreciation){
            $depreciation->sumamount;
            $totaldepreciation = $depreciation->sumamount + $totaldepreciation;
        }
        $adjustliabilities = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Liabilities'],
            ['branch_id','=', $branch_id],
            ['transaction_type','=', 'Interest']
        ])->groupBy('account_id')->get();

        foreach ($adjustliabilities as $adjustliability){
            $adjustliability->sumamount;
            $totaladjustliability = $adjustliability->sumamount + $totaladjustliability;
        }
        $expenses = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type','=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', [ 'Due','Current','Prepaid Adjust'])
            ->whereNull('employee_id')
            ->groupBy('account_id')->get();
        foreach ($expenses as $expense){
            $expense->sumamount;
            $totalexpense = $expense->sumamount + $totalexpense;
        }
        
        $salaryexp = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type','=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNotNull('employee_id')->whereIn('transaction_type', [ 'Due','Current','Prepaid Adjust'])
            ->groupBy('account_id')->get();
        foreach ($salaryexp as $salaryexp){
            $salaryexp->sumamount;
            $totalsalaryexp = $salaryexp->sumamount + $totalsalaryexp;
        }
        $incomevats = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Income'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', [ 'Current','Due','Prepaid Adjust'])->whereNotNull('t_amount')->groupBy('account_id')->get();
        foreach ($incomevats as $incomevat){
            $incomevat->sumamount;
            $incomevattotal = $incomevat->sumamount + $incomevattotal;
        }
        $rcvvats = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNull('employee_id')->whereIn('transaction_type', [ 'Current','Due','Prepaid Adjust'])->groupBy('account_id')->get();
        foreach ($rcvvats as $rcvvat){
            $rcvvat->sumamount;
            $rcvvattotal = $rcvvat->sumamount + $rcvvattotal;
        }
        $taxprovision = 0;
        $provision = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type','=', 'Expense'],
            ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', ['Account Payable'])
                ->groupBy('account_id')->get();
        foreach ($provision as $provision){
            $provision->sumamount;
            $taxprovision = $provision->sumamount + $taxprovision;
        }
        $totalsalarytax = 0;
        $salarytaxes = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNotNull('employee_id')
            ->whereIn('payment_type', [ 'Account Payable'])
            ->groupBy('account_id')->get();

        foreach ($salarytaxes as $salarytax){
            $salarytax->sumamount;
            $totalsalarytax = $salarytax->sumamount + $totalsalarytax;
        }
        $totaladdnewvat = 0;
            $addvats = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'Liabilities'],
                ['transaction_type','=', 'Add'],
                ['branch_id','=', $branch_id]
            ])->groupBy('account_id')->get();
            foreach ($addvats as $addvat){
                $addvat->sumamount;
                $totaladdnewvat = $addvat->sumamount + $totaladdnewvat;
            }
        $totaladdassetvat = 0;
        $addassetvats = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'Asset'],
                ['transaction_type','=', 'Add'],
                ['branch_id','=', $branch_id]
            ])->groupBy('account_id')->get();
            foreach ($addassetvats as $addassetvat){
                $addassetvat->sumamount;
                $totaladdassetvat = $addassetvat->sumamount + $totaladdassetvat;
            }
        $revenuereserve = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'OwnerEquity'],
            ['transactions.transaction_type', '=', 'Add'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_name', '=', 'Revenue Reserve']
            ])->sum('at_amount');
        $reverserevenue = DB::table('transactions')
        ->select('transactions.*','accounts.account_type','accounts.account_name')
        ->join('accounts','accounts.id','=','transactions.account_id')
        ->where([
            ['transactions.t_date', '<=', $toDate],
            ['transactions.table_type', '=', 'OwnerEquity'],
            ['transactions.transaction_type', '=', 'Reverse'],
            ['transactions.branch_id', '=', $branch_id],
            ['accounts.account_name', '=', 'Revenue Reserve']
        ])->sum('at_amount');
        $totaldividendexp = 0;
        $dividendexp = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type','=', 'OwnerEquity'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', ['Payable'])
            ->groupBy('account_id')->get();
        foreach ($dividendexp as $dividendexp){
            $dividendexp->sumamount;
            $totaldividendexp = $dividendexp->sumamount + $totaldividendexp;
        }
        $totaldividend = 0;
        $alldividends = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type','=', 'OwnerEquity'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', [ 'Payable'])->groupBy('account_id')->get();

        foreach ($alldividends as $alldividend){
            $alldividend->sumamount;
            $totaldividend = $alldividend->sumamount + $totaldividend;
        }
        // $vatreceive = Transaction::where([
        //     ['table_type', '=', 'Asset'],
        //     ['branch_id','=', $branch_id]
        // ])->whereNull('account_id')->sum('at_amount');
        $profitbeforetax = $totalincome - $totalexpense - $totalsalaryexp - $totaladjustexpense - $totaldepreciation - $totaladjustliability;
        $vatprovision =   $totaladdnewvat + $incomevattotal - $rcvvattotal - $totaladdassetvat;
//        $profitaftertax = $profitbeforetax - $taxprovision - $vatprovision-$totaldividend-$revenuereserve;
        $retainedearning = $profitbeforetax - $taxprovision - $totalsalarytax - $vatprovision-$totaldividend-$revenuereserve + $reverserevenue;
        $vatreceive = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.transaction_type','=', 'Receive'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Vat Receivable'])->sum('at_amount');
        $addnewassetvat = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.transaction_type','=', 'Add'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Vat Receivable'])->sum('at_amount');
        $eexpvattotal = 0;
        $eexpvats = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNull('employee_id')->groupBy('account_id')->get();
        foreach ($eexpvats as $eexpvat){
            $eexpvat->sumamount;
            $eexpvattotal = $eexpvat->sumamount + $eexpvattotal;
        }
        $totalvatreceivable =$addnewassetvat + $eexpvattotal - $vatreceive;
//        $totalvatreceivable = $vatreceive - $rcvvattotal;
        $vatpaid = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.transaction_type','=', 'Payment'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Vat Payable'])->sum('at_amount');
         $addnewvat = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.transaction_type','=', 'Add'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Vat Payable'])->sum('at_amount');
        $incvattotal = 0;
        $incvats = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Income'],
            ['branch_id','=', $branch_id]
        ])->groupBy('account_id')->get();
        foreach ($incvats as $incvat){
            $incvat->sumamount;
            $incvattotal = $incvat->sumamount + $incvattotal;
        }
        $totalvatpayable =  $incvattotal  + $addnewvat - $vatpaid;
//        $totalvatreceivable =  $incomevattotal - $vatpaid;
//        {{ TAX PROVISION CALCULATION}}
        $totaltaxprovision  = 0;
        $taxprovisions= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Payment' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Expense'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transaction_type', [  'Payment','Account Payable'])->groupBy('transactions.account_id')->get();
        foreach ($taxprovisions as $taxprovision){
            $taxprovision->amount;
            $totaltaxprovision = $taxprovision->amount + $totaltaxprovision;
        }
//        {{ TAX PROVISION CALCULATION}}
//       {{$initialcurrentasset calculation}}
        $adjusttotal = 0;
        $initialtotal = 0;
        $initialcurrentassets = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Asset'],
            ['payment_type', '=', 'Owner Equity'],
            ['branch_id','=', $branch_id]
        ])->groupBy('account_id')->get();
        foreach ($initialcurrentassets as $initialcurrentasset){
            $initialcurrentasset->sumamount;
            $initialtotal = $initialcurrentasset->sumamount + $initialtotal;
        }
        $adjustcurrentassets = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Asset'],
            ['payment_type', '=', 'Adjust'],
            ['branch_id','=', $branch_id]
        ])->groupBy('account_id')->get();
        foreach ($adjustcurrentassets as $adjustcurrentasset){
            $adjustcurrentasset->sumamount;
            $adjusttotal = $adjustcurrentasset->sumamount + $adjusttotal;
        }
        $totalprepaidexpense  = 0;
        $prepaidexpenses= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Prepaid Adjust' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Expense'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transaction_type', [  'Prepaid Adjust','Prepaid'])->groupBy('transactions.account_id')->get();
        foreach ($prepaidexpenses as $prepaidexpense){
            $prepaidexpense->amount;
            $totalprepaidexpense = $prepaidexpense->amount + $totalprepaidexpense;
        }
        $totaldueexpense = 0;
        $dueexpenses= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Due Adjust' THEN -transactions.amount ELSE transactions.amount END) as amount"))
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Expense'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transaction_type', [  'Due Adjust','Due'])
            ->whereNull('employee_id')
            ->groupBy('transactions.account_id')->get();
        foreach ($dueexpenses as $dueexpense){
            $dueexpense->amount;
            $totaldueexpense = $dueexpense->amount + $totaldueexpense;
        }
        $totaladvanceincome = 0;
        $advanceincomes= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Advance Adjust' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Income'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transaction_type', [ 'Advance Adjust','Advance'])->groupBy('transactions.account_id')->get();
        foreach ($advanceincomes as $advanceincome){
            $advanceincome->amount;
            $totaladvanceincome = $advanceincome->amount + $totaladvanceincome;
        }
        $totaldueincome = 0;
        $dueincomes= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Due Adjust' THEN -transactions.amount ELSE transactions.amount END) as amount"))
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Income'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transaction_type', [ 'Due Adjust','Due'])->groupBy('transactions.account_id')->get();
        foreach ($dueincomes as $dueincome){
            $dueincome->amount;
            $totaldueincome = $dueincome->amount + $totaldueincome;
        }
//            ADVANCED DUE END

//        for cash calculation
        $receivecurrentcash = Transaction::whereIn('transaction_type', [ 'Sold','Receive'])->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Asset'],
            ['payment_type', '=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $paymentcurrentcash = Transaction::whereIn('transaction_type', [ 'Purchase','Payment'])->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Asset'],
            ['payment_type', '=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $totalassetscash = $receivecurrentcash  - $paymentcurrentcash;
        $receiveliabilitycash = Transaction::whereIn('transaction_type', ['Receive'])->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Liabilities'],
            ['payment_type', '=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $paymentliabilitycash = Transaction::whereIn('transaction_type', [ 'Payment'])->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Liabilities'],
            ['payment_type', '=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $totalliabilitycash = $receiveliabilitycash - $paymentliabilitycash;
        $receiveequitycash = Transaction::whereIn('transaction_type', [ 'Receive'])->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'OwnerEquity'],
            ['payment_type', '=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $paymentequitycash = Transaction::whereIn('transaction_type', [ 'Payment'])->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'OwnerEquity'],
            ['payment_type', '=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $totalequitycash = $receiveequitycash - $paymentequitycash;
        $expensecash = Transaction::where([
            ['t_date', '<=', $toDate],
            ['payment_type', '=', 'Cash'],
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNull('employee_id')->sum('amount');
        $expensesalarycash = Transaction::where([
            ['t_date', '<=', $toDate],
            ['payment_type', '=', 'Cash'],
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNotNull('employee_id')->sum('at_amount');
        $incomecash = Transaction::where([
            ['t_date', '<=', $toDate],
            ['payment_type', '=', 'Cash'],
            ['table_type', '=', 'Income'],
            ['branch_id','=', $branch_id]
        ])->sum('amount');
        $totalcash = $totalassetscash + $totalliabilitycash + $totalequitycash + $incomecash - $expensecash - $expensesalarycash;
//        for cash calculation end

//        for bank calculation
        $receivecurrentbank = Transaction::whereIn('transaction_type', [ 'Sold','Receive'])->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Asset'],
            ['payment_type', '=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $paymentcurrentbank = Transaction::whereIn('transaction_type', [ 'Purchase','Payment'])->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Asset'],
            ['payment_type', '=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $totalassetsbank = $receivecurrentbank - $paymentcurrentbank;
        $receiveliabilitybank = Transaction::whereIn('transaction_type', [ 'Receive'])->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Liabilities'],
            ['payment_type', '=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $paymentliabilitybank = Transaction::whereIn('transaction_type', [ 'Payment'])->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'Liabilities'],
            ['payment_type', '=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $totalliabilitybank = $receiveliabilitybank - $paymentliabilitybank;
        $receiveequitybank = Transaction::whereIn('transaction_type', [ 'Receive'])->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'OwnerEquity'],
            ['payment_type', '=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $paymentequitybank = Transaction::whereIn('transaction_type', [ 'Payment'])->where([
            ['t_date', '<=', $toDate],
            ['table_type', '=', 'OwnerEquity'],
            ['payment_type', '=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->sum('at_amount');
        $totalequitybank = $receiveequitybank - $paymentequitybank;
        $expensebank = Transaction::where([
            ['t_date', '<=', $toDate],
            ['payment_type', '=', 'Bank'],
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNull('employee_id')->sum('amount');
        $expensesalarybank = Transaction::where([
            ['t_date', '<=', $toDate],
            ['payment_type', '=', 'Bank'],
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNotNull('employee_id')->sum('at_amount');
        $incomebank = Transaction::where([
            ['t_date', '<=', $toDate],
            ['payment_type', '=', 'Bank'],
            ['table_type', '=', 'Income'],
            ['branch_id','=', $branch_id]
        ])->sum('amount');
        $totalbank = $totalassetsbank + $totalliabilitybank + $totalequitybank + $incomebank - $expensebank - $expensesalarybank;
//        for bank calculation end
//        withdraw calculation
        $withdrawbalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'OwnerEquity'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_name', '=', 'Withdraw']
            ])->sum('at_amount');
//        withdraw calculation end
 //      Account  receivables calculation
        $receivablereceipttotal = 0;
        $receivablepaymentstotal = 0;
        $receivables = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Current Asset-AR'],
                ['transactions.transaction_type', '=', 'Receive']
            ])->get();
        foreach ($receivables as $receivable){
            $receivable->at_amount;
            $receivablereceipttotal = $receivable->at_amount + $receivablereceipttotal;
        }
        $receivablepayments = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Current Asset-AR'],
                ['transactions.transaction_type', '=', 'Payment']
            ])->get();
        foreach ($receivablepayments as $receivablepayment){
            $receivablepayment->at_amount;
            $receivablepaymentstotal = $receivablepayment->at_amount + $receivablepaymentstotal;
        }
        $totalcurrentassetar = $receivablepaymentstotal - $receivablereceipttotal + $totalprepaidexpense + $totaldueincome;
 //      Account  receivables calculation end


//       fixed assets calculations
        $assets= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Sold' OR transactions.transaction_type ='Depreciation' THEN -transactions.at_amount ELSE transactions.at_amount END) as sumamount"))
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transaction_type', [ 'Initial','Purchase','Sold','Depreciation'])->groupBy('transactions.account_id')->get();
//       fixed assets calculations end
        $longtermliabilities= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Payment' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Long-term Liabilities'],
            ])->groupBy('transactions.account_id')->get();
        $shorttermliabilities= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Payment' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Short-term Liabilities'],
            ])->groupBy('transactions.account_id')->get();
        $totalapliabilities = 0;
        $apliabilities= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Payment' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Liabilities-AP'],
            ])->groupBy('transactions.account_id')->get();

        foreach ($apliabilities as $apliability){
            $apliability->amount;
            $totalapliabilities = $apliability->amount + $totalapliabilities;
        }
// asset purchase liabilities
        $totalassetpayable = 0;
        $assetpayables= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.liability_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.liability_id','transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Purchase' THEN transactions.at_amount ELSE 0 END) as amount"))
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.branch_id', '=', $branch_id],
                ['transactions.payment_type', '=', 'Account Payable'],
            ])->groupBy('transactions.liability_id')->get();
        foreach ($assetpayables as $assetpayable){
            $assetpayable->amount;
            $totalassetpayable = $assetpayable->amount + $totalassetpayable;
        }
        $netaccpayable = $totalapliabilities + $totaladvanceincome + $totaldueexpense + $totalassetpayable;
        $totalemployeepayable = 0;
        $employeepayables= DB::table('transactions')
            ->leftJoin('employees', 'transactions.account_id', '=', 'employees.id')
            ->select('employees.id','employees.employee_name', 'transactions.transaction_type','transactions.payment_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Due Adjust' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Expense'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transactions.transaction_type', [ 'Due Adjust','Due'])
            ->whereNotNull('transactions.employee_id')
            ->groupBy('transactions.employee_id')->get();

        foreach ($employeepayables as $employeepayable){
            $employeepayable->amount;
            $totalemployeepayable = $employeepayable->amount + $totalemployeepayable;
        }
//                CURRENT ASSETS
        $currentassets= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Receive' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Current Asset'],
            ])->groupBy('transactions.account_id')->get();
//                CURRENT ASSETS

        $assetbalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.payment_type', '=', 'Owner Equity'],
                ['transactions.branch_id', '=', $branch_id]
            ])->sum('at_amount');
        $liabilitybalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.payment_type', '=', 'Owner Equity'],
                ['transactions.branch_id', '=', $branch_id]
            ])->sum('at_amount');
        $equitybalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'OwnerEquity'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_name', '=', 'Capital']
            ])->sum('at_amount');
         $capitalreserve = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'OwnerEquity'],
                ['transactions.transaction_type', '=', 'Add'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_name', '=', 'Capital Reserve']
            ])->sum('at_amount');
        $reversecapital = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'OwnerEquity'],
                ['transactions.transaction_type', '=', 'Reverse'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_name', '=', 'Capital Reserve']
            ])->sum('at_amount'); 
        $sharepremium = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'OwnerEquity'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_name', '=', 'Share premium']
            ])->sum('at_amount');  
            // dividend calculation
        $totaldividendpayable = 0;
        $dividendpayables= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Payment' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'OwnerEquity'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Dividend']
            ])->whereIn('transaction_type', ['Payment','Payable'])->groupBy('transactions.account_id')->get();
        foreach ($dividendpayables as $dividendpayable){
            $dividendpayable->amount;
            $totaldividendpayable = $dividendpayable->amount + $totaldividendpayable;
        }
        // dividend calculation	
	
		//query with date end
        $pdfhead = Array('toDate'=> $toDate,'title'=>'Cash Flow Statement');
        return view('financial_statement.financialstatement')
            ->with('incomes', $incomes)
            ->with('expenses', $expenses)
            ->with('assets', $assets)
            ->with('totaldividendpayable', $totaldividendpayable)
            ->with('currentassets', $currentassets)
            ->with('assetbalance', $assetbalance)
            ->with('liabilitybalance', $liabilitybalance)
            ->with('equitybalance', $equitybalance)
            ->with('totaltaxprovision', $totaltaxprovision)
            ->with('totalsalarytax', $totalsalarytax)
            ->with('apliabilities', $apliabilities)
            ->with('netaccpayable', $netaccpayable)
            ->with('longtermliabilities', $longtermliabilities)
            ->with('shorttermliabilities', $shorttermliabilities)
            ->with('assetpayables', $assetpayables)
            ->with('totalvatreceivable', $totalvatreceivable)
            ->with('totalvatpayable', $totalvatpayable)
            ->with('totalcash', $totalcash)
            ->with('totalbank', $totalbank)
            ->with('withdrawbalance', $withdrawbalance)
            ->with('totalcurrentassetar', $totalcurrentassetar)
            ->with('depreciations', $depreciations)
            ->with('totalprepaidexpense', $totalprepaidexpense)
            ->with('dueexpenses', $dueexpenses)
            ->with('advanceincomes', $advanceincomes)
            ->with('totaldueincome', $totaldueincome)
            ->with('capitalreserve', $capitalreserve)
            ->with('reversecapital', $reversecapital)
            ->with('revenuereserve', $revenuereserve)
            ->with('reverserevenue', $reverserevenue)
            ->with('totaldividendexp', $totaldividendexp)
            ->with('totalemployeepayable', $totalemployeepayable)
            ->with('sharepremium', $sharepremium)
            ->with('retainedearning', $retainedearning)
            ->with('pdfhead',$pdfhead);

        }else{
			//query without date
			
			


        $totalincome = 0;
        $totalexpense = 0;
        $totalsalaryexp = 0;
        $rcvvattotal = 0;
        $incomevattotal = 0;
        $totaladjustexpense = 0;
        $totaldepreciation = 0;
        $totaladjustliability = 0;

        $branch_id = auth()->user()->branch_id;
        $incomes = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
            ['table_type','=', 'Income'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', [ 'Due','Current','Advance Adjust'])->groupBy('account_id')->get();
        foreach ($incomes as $income){
            $income->sumamount;
            $totalincome = $income->sumamount + $totalincome;
        }
        $adjustexpenses = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
            ['table_type', '=', 'Asset'],
            ['branch_id','=', $branch_id]
        ])->whereNotNull('expense_id')->groupBy('account_id')->get();
        foreach ($adjustexpenses as $adjustexpense){
            $adjustexpense->sumamount;
            $totaladjustexpense = $adjustexpense->sumamount + $totaladjustexpense;
        }
        $depreciations = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
            ['table_type', '=', 'Asset'],
            ['branch_id','=', $branch_id],
            ['transaction_type','=', 'Depreciation']
        ])->groupBy('account_id')->get();
        foreach ($depreciations as $depreciation){
            $depreciation->sumamount;
            $totaldepreciation = $depreciation->sumamount + $totaldepreciation;
        }
        $adjustliabilities = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
            ['table_type', '=', 'Liabilities'],
            ['branch_id','=', $branch_id],
            ['transaction_type','=', 'Interest']
        ])->groupBy('account_id')->get();

        foreach ($adjustliabilities as $adjustliability){
            $adjustliability->sumamount;
            $totaladjustliability = $adjustliability->sumamount + $totaladjustliability;
        }
        $expenses = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
            ['table_type','=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', [ 'Due','Current','Prepaid Adjust'])
            ->whereNull('employee_id')
            ->groupBy('account_id')->get();
        foreach ($expenses as $expense){
            $expense->sumamount;
            $totalexpense = $expense->sumamount + $totalexpense;
        }
        
        $salaryexp = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
            ['table_type','=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNotNull('employee_id')->whereIn('transaction_type', [ 'Due','Current','Prepaid Adjust'])
            ->groupBy('account_id')->get();
        foreach ($salaryexp as $salaryexp){
            $salaryexp->sumamount;
            $totalsalaryexp = $salaryexp->sumamount + $totalsalaryexp;
        }
        $incomevats = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['table_type', '=', 'Income'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', [ 'Current','Due','Prepaid Adjust'])->whereNotNull('t_amount')->groupBy('account_id')->get();
        foreach ($incomevats as $incomevat){
            $incomevat->sumamount;
            $incomevattotal = $incomevat->sumamount + $incomevattotal;
        }
        $rcvvats = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNull('employee_id')->whereIn('transaction_type', [ 'Current','Due','Prepaid Adjust'])->groupBy('account_id')->get();
        foreach ($rcvvats as $rcvvat){
            $rcvvat->sumamount;
            $rcvvattotal = $rcvvat->sumamount + $rcvvattotal;
        }
        $taxprovision = 0;
        $provision = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
                ['table_type','=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', ['Account Payable'])
                ->groupBy('account_id')->get();
        foreach ($provision as $provision){
            $provision->sumamount;
            $taxprovision = $provision->sumamount + $taxprovision;
        }
        $totalsalarytax = 0;
        $salarytaxes = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNotNull('employee_id')
            ->whereIn('payment_type', [ 'Account Payable'])
            ->groupBy('account_id')->get();

        foreach ($salarytaxes as $salarytax){
            $salarytax->sumamount;
            $totalsalarytax = $salarytax->sumamount + $totalsalarytax;
        }
        $totaladdnewvat = 0;
            $addvats = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['table_type', '=', 'Liabilities'],
                ['transaction_type','=', 'Add'],
                ['branch_id','=', $branch_id]
            ])->groupBy('account_id')->get();
            foreach ($addvats as $addvat){
                $addvat->sumamount;
                $totaladdnewvat = $addvat->sumamount + $totaladdnewvat;
            }
        $totaladdassetvat = 0;
        $addassetvats = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                
                ['table_type', '=', 'Asset'],
                ['transaction_type','=', 'Add'],
                ['branch_id','=', $branch_id]
            ])->groupBy('account_id')->get();
            foreach ($addassetvats as $addassetvat){
                $addassetvat->sumamount;
                $totaladdassetvat = $addassetvat->sumamount + $totaladdassetvat;
            }
        $revenuereserve = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'OwnerEquity'],
            ['transactions.transaction_type', '=', 'Add'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_name', '=', 'Revenue Reserve']
            ])->sum('at_amount');
        $reverserevenue = DB::table('transactions')
        ->select('transactions.*','accounts.account_type','accounts.account_name')
        ->join('accounts','accounts.id','=','transactions.account_id')
        ->where([
            ['transactions.table_type', '=', 'OwnerEquity'],
            ['transactions.transaction_type', '=', 'Reverse'],
            ['transactions.branch_id', '=', $branch_id],
            ['accounts.account_name', '=', 'Revenue Reserve']
        ])->sum('at_amount');
        $totaldividendexp = 0;
        $dividendexp = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
            ['table_type','=', 'OwnerEquity'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', ['Payable'])
            ->groupBy('account_id')->get();
        foreach ($dividendexp as $dividendexp){
            $dividendexp->sumamount;
            $totaldividendexp = $dividendexp->sumamount + $totaldividendexp;
        }
        $totaldividend = 0;
        $alldividends = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
            ['table_type','=', 'OwnerEquity'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', [ 'Payable'])->groupBy('account_id')->get();

        foreach ($alldividends as $alldividend){
            $alldividend->sumamount;
            $totaldividend = $alldividend->sumamount + $totaldividend;
        }
        // $vatreceive = Transaction::where([
        //     ['table_type', '=', 'Asset'],
        //     ['branch_id','=', $branch_id]
        // ])->whereNull('account_id')->sum('at_amount');
        $profitbeforetax = $totalincome - $totalexpense - $totalsalaryexp - $totaladjustexpense - $totaldepreciation - $totaladjustliability;
        $vatprovision =   $totaladdnewvat + $incomevattotal - $rcvvattotal - $totaladdassetvat;
//        $profitaftertax = $profitbeforetax - $taxprovision - $vatprovision-$totaldividend-$revenuereserve;
        $retainedearning = $profitbeforetax - $taxprovision - $totalsalarytax - $vatprovision-$totaldividend-$revenuereserve + $reverserevenue;
        $vatreceive = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.transaction_type','=', 'Receive'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Vat Receivable'])->sum('at_amount');
        $addnewassetvat = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.transaction_type','=', 'Add'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Vat Receivable'])->sum('at_amount');
        $eexpvattotal = 0;
        $eexpvats = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['table_type', '=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->whereNull('employee_id')->groupBy('account_id')->get();
        foreach ($eexpvats as $eexpvat){
            $eexpvat->sumamount;
            $eexpvattotal = $eexpvat->sumamount + $eexpvattotal;
        }
        $totalvatreceivable =$addnewassetvat + $eexpvattotal - $vatreceive;
//        $totalvatreceivable = $vatreceive - $rcvvattotal;
        $vatpaid = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.transaction_type','=', 'Payment'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Vat Payable'])->sum('at_amount');
         $addnewvat = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.transaction_type','=', 'Add'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Vat Payable'])->sum('at_amount');
        $incvattotal = 0;
        $incvats = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['table_type', '=', 'Income'],
            ['branch_id','=', $branch_id]
        ])->groupBy('account_id')->get();
        foreach ($incvats as $incvat){
            $incvat->sumamount;
            $incvattotal = $incvat->sumamount + $incvattotal;
        }
        $totalvatpayable =  $incvattotal  + $addnewvat - $vatpaid;
//        $totalvatreceivable =  $incomevattotal - $vatpaid;
//        {{ TAX PROVISION CALCULATION}}
        $totaltaxprovision  = 0;
        $taxprovisions= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Payment' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.table_type', '=', 'Expense'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transaction_type', [  'Payment','Account Payable'])->groupBy('transactions.account_id')->get();
        foreach ($taxprovisions as $taxprovision){
            $taxprovision->amount;
            $totaltaxprovision = $taxprovision->amount + $totaltaxprovision;
        }
//        {{ TAX PROVISION CALCULATION}}
//       {{$initialcurrentasset calculation}}
        $adjusttotal = 0;
        $initialtotal = 0;
        $initialcurrentassets = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['table_type', '=', 'Asset'],
            ['payment_type', '=', 'Owner Equity'],
            ['branch_id','=', $branch_id]
        ])->groupBy('account_id')->get();
        foreach ($initialcurrentassets as $initialcurrentasset){
            $initialcurrentasset->sumamount;
            $initialtotal = $initialcurrentasset->sumamount + $initialtotal;
        }
        $adjustcurrentassets = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
            ['table_type', '=', 'Asset'],
            ['payment_type', '=', 'Adjust'],
            ['branch_id','=', $branch_id]
        ])->groupBy('account_id')->get();
        foreach ($adjustcurrentassets as $adjustcurrentasset){
            $adjustcurrentasset->sumamount;
            $adjusttotal = $adjustcurrentasset->sumamount + $adjusttotal;
        }
        $totalprepaidexpense  = 0;
        $prepaidexpenses= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Prepaid Adjust' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.table_type', '=', 'Expense'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transaction_type', [  'Prepaid Adjust','Prepaid'])->groupBy('transactions.account_id')->get();
        foreach ($prepaidexpenses as $prepaidexpense){
            $prepaidexpense->amount;
            $totalprepaidexpense = $prepaidexpense->amount + $totalprepaidexpense;
        }
        $totaldueexpense = 0;
        $dueexpenses= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Due Adjust' THEN -transactions.amount ELSE transactions.amount END) as amount"))
            ->where([
                ['transactions.table_type', '=', 'Expense'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transaction_type', [  'Due Adjust','Due'])
            ->whereNull('employee_id')
            ->groupBy('transactions.account_id')->get();
        foreach ($dueexpenses as $dueexpense){
            $dueexpense->amount;
            $totaldueexpense = $dueexpense->amount + $totaldueexpense;
        }
        $totaladvanceincome = 0;
        $advanceincomes= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Advance Adjust' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.table_type', '=', 'Income'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transaction_type', [ 'Advance Adjust','Advance'])->groupBy('transactions.account_id')->get();
        foreach ($advanceincomes as $advanceincome){
            $advanceincome->amount;
            $totaladvanceincome = $advanceincome->amount + $totaladvanceincome;
        }
        $totaldueincome = 0;
        $dueincomes= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Due Adjust' THEN -transactions.amount ELSE transactions.amount END) as amount"))
            ->where([
                ['transactions.table_type', '=', 'Income'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transaction_type', [ 'Due Adjust','Due'])->groupBy('transactions.account_id')->get();
        foreach ($dueincomes as $dueincome){
            $dueincome->amount;
            $totaldueincome = $dueincome->amount + $totaldueincome;
        }
//            ADVANCED DUE END

//        for cash calculation
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

        $incomecashWithoutRefund = Transaction::where([
            ['payment_type', '=', 'Cash'],
            ['table_type', '=', 'Income'],
            ['transaction_type', '!=', 'Refund'],
            ['branch_id','=', $branch_id]
        ])->sum('amount');

        $incomecashWithRefund = Transaction::where([
            ['payment_type', '=', 'Cash'],
            ['table_type', '=', 'Income'],
            ['transaction_type', '=', 'Refund'],
            ['branch_id','=', $branch_id]
        ])->sum('amount');

        $incomecash = $incomecashWithoutRefund - $incomecashWithRefund;

        $totalcash = $totalassetscash + $totalliabilitycash + $totalequitycash + $incomecash - $expensecash - $expensesalarycash;
//        for cash calculation end

//        for bank calculation
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
        $incomebank = Transaction::where([
            ['payment_type', '=', 'Bank'],
            ['table_type', '=', 'Income'],
            ['branch_id','=', $branch_id]
        ])->sum('amount');
        $totalbank = $totalassetsbank + $totalliabilitybank + $totalequitybank + $incomebank - $expensebank - $expensesalarybank;
//        for bank calculation end
//        withdraw calculation
        $withdrawbalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'OwnerEquity'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_name', '=', 'Withdraw']
            ])->sum('at_amount');
//        withdraw calculation end
 //      Account  receivables calculation
        $receivablereceipttotal = 0;
        $receivablepaymentstotal = 0;
        $receivables = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Current Asset-AR'],
                ['transactions.transaction_type', '=', 'Receive']
            ])->get();
        foreach ($receivables as $receivable){
            $receivable->at_amount;
            $receivablereceipttotal = $receivable->at_amount + $receivablereceipttotal;
        }
        $receivablepayments = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Current Asset-AR'],
                ['transactions.transaction_type', '=', 'Payment']
            ])->get();
        foreach ($receivablepayments as $receivablepayment){
            $receivablepayment->at_amount;
            $receivablepaymentstotal = $receivablepayment->at_amount + $receivablepaymentstotal;
        }
        $totalcurrentassetar = $receivablepaymentstotal - $receivablereceipttotal + $totalprepaidexpense + $totaldueincome;
 //      Account  receivables calculation end


//       fixed assets calculations
        $assets= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Sold' OR transactions.transaction_type ='Depreciation' THEN -transactions.at_amount ELSE transactions.at_amount END) as sumamount"))
            ->where([
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transaction_type', [ 'Initial','Purchase','Sold','Depreciation'])->groupBy('transactions.account_id')->get();
//       fixed assets calculations end
        $longtermliabilities= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Payment' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Long-term Liabilities'],
            ])->groupBy('transactions.account_id')->get();
        $shorttermliabilities= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Payment' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Short-term Liabilities'],
            ])->groupBy('transactions.account_id')->get();
        $totalapliabilities = 0;
        $apliabilities= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Payment' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Liabilities-AP'],
            ])->groupBy('transactions.account_id')->get();

        foreach ($apliabilities as $apliability){
            $apliability->amount;
            $totalapliabilities = $apliability->amount + $totalapliabilities;
        }
// asset purchase liabilities
        $totalassetpayable = 0;
        $assetpayables= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.liability_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.liability_id','transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Purchase' THEN transactions.at_amount ELSE 0 END) as amount"))
            ->where([
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.branch_id', '=', $branch_id],
                ['transactions.payment_type', '=', 'Account Payable'],
            ])->groupBy('transactions.liability_id')->get();
        foreach ($assetpayables as $assetpayable){
            $assetpayable->amount;
            $totalassetpayable = $assetpayable->amount + $totalassetpayable;
        }
        $netaccpayable = $totalapliabilities + $totaladvanceincome + $totaldueexpense + $totalassetpayable;
        $totalemployeepayable = 0;
        $employeepayables= DB::table('transactions')
            ->leftJoin('employees', 'transactions.account_id', '=', 'employees.id')
            ->select('employees.id','employees.employee_name', 'transactions.transaction_type','transactions.payment_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Due Adjust' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.table_type', '=', 'Expense'],
                ['transactions.branch_id', '=', $branch_id],
            ])->whereIn('transactions.transaction_type', [ 'Due Adjust','Due'])
            ->whereNotNull('transactions.employee_id')
            ->groupBy('transactions.employee_id')->get();

        foreach ($employeepayables as $employeepayable){
            $employeepayable->amount;
            $totalemployeepayable = $employeepayable->amount + $totalemployeepayable;
        }
//                CURRENT ASSETS
        $currentassets= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Receive' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Current Asset'],
            ])->groupBy('transactions.account_id')->get();
//                CURRENT ASSETS

        $assetbalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.payment_type', '=', 'Owner Equity'],
                ['transactions.branch_id', '=', $branch_id]
            ])->sum('at_amount');
        $liabilitybalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.payment_type', '=', 'Owner Equity'],
                ['transactions.branch_id', '=', $branch_id]
            ])->sum('at_amount');
        $equitybalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'OwnerEquity'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_name', '=', 'Capital']
            ])->sum('at_amount');
         $capitalreserve = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'OwnerEquity'],
                ['transactions.transaction_type', '=', 'Add'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_name', '=', 'Capital Reserve']
            ])->sum('at_amount');
        $reversecapital = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'OwnerEquity'],
                ['transactions.transaction_type', '=', 'Reverse'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_name', '=', 'Capital Reserve']
            ])->sum('at_amount'); 
        $sharepremium = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'OwnerEquity'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_name', '=', 'Share premium']
            ])->sum('at_amount');  
            // dividend calculation
        $totaldividendpayable = 0;
        $dividendpayables= DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select('accounts.id','accounts.account_type','accounts.account_name', 'transactions.transaction_type', DB::raw("SUM(CASE  WHEN transactions.transaction_type ='Payment' THEN -transactions.at_amount ELSE transactions.at_amount END) as amount"))
            ->where([
                ['transactions.table_type', '=', 'OwnerEquity'],
                ['transactions.branch_id', '=', $branch_id],
                ['accounts.account_type', '=', 'Dividend']
            ])->whereIn('transaction_type', ['Payment','Payable'])->groupBy('transactions.account_id')->get();
        foreach ($dividendpayables as $dividendpayable){
            $dividendpayable->amount;
            $totaldividendpayable = $dividendpayable->amount + $totaldividendpayable;
        }
        // dividend calculation

        //query without date end

        return view('financial_statement.financialstatement')
            ->with('incomes', $incomes)
            ->with('expenses', $expenses)
            ->with('assets', $assets)
            ->with('totaldividendpayable', $totaldividendpayable)
            ->with('currentassets', $currentassets)
            ->with('assetbalance', $assetbalance)
            ->with('liabilitybalance', $liabilitybalance)
            ->with('equitybalance', $equitybalance)
            ->with('totaltaxprovision', $totaltaxprovision)
            ->with('totalsalarytax', $totalsalarytax)
            ->with('apliabilities', $apliabilities)
            ->with('netaccpayable', $netaccpayable)
            ->with('longtermliabilities', $longtermliabilities)
            ->with('shorttermliabilities', $shorttermliabilities)
            ->with('assetpayables', $assetpayables)
            ->with('totalvatreceivable', $totalvatreceivable)
            ->with('totalvatpayable', $totalvatpayable)
            ->with('totalcash', $totalcash)
            ->with('totalbank', $totalbank)
            ->with('withdrawbalance', $withdrawbalance)
            ->with('totalcurrentassetar', $totalcurrentassetar)
            ->with('depreciations', $depreciations)
            ->with('totalprepaidexpense', $totalprepaidexpense)
            ->with('dueexpenses', $dueexpenses)
            ->with('advanceincomes', $advanceincomes)
            ->with('totaldueincome', $totaldueincome)
            ->with('capitalreserve', $capitalreserve)
            ->with('reversecapital', $reversecapital)
            ->with('revenuereserve', $revenuereserve)
            ->with('reverserevenue', $reverserevenue)
            ->with('totaldividendexp', $totaldividendexp)
            ->with('totalemployeepayable', $totalemployeepayable)
            ->with('sharepremium', $sharepremium)
            ->with('retainedearning', $retainedearning);

        }


    }


}
