<?php

namespace App\Http\Controllers;


use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashFlowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function getCashFlow(Request $request){

        $branch_id = auth()->user()->branch_id;

//        liabilities adjust

        // previous balance calculation start

        $pretotalincome = 0;
        $pretotalsoldasset = 0;
        $pretotalpaymentreceivables = 0;
        $pretotalreceiptreceivables = 0;
        $pretotalreceiptliabilities = 0;
        $pretotalpurchaseasset = 0;
        $pretotaloutgoing = 0;
        $pretotalsalaryexpense = 0;
        $pretotalpaymentliabilities = 0;
        // previous balance calculation end


        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $paymentliabilities = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Liabilities'],
                ['transaction_type','=', 'Payment'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])
                ->groupBy('account_id')->get();


            $prepaymentliabilities = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '<', $fromDate],
                ['table_type','=', 'Liabilities'],
                ['transaction_type','=', 'Payment'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])
                ->groupBy('account_id')->get();


            foreach ($prepaymentliabilities as $prepaymentliabilitie){
                $prepaymentliabilitie->sumamount;
                $pretotalpaymentliabilities = $prepaymentliabilitie->sumamount + $pretotalpaymentliabilities;
            }

        }else{

            $paymentliabilities = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['table_type','=', 'Liabilities'],
                ['transaction_type','=', 'Payment'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])
                ->groupBy('account_id')->get();
        }


        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $receiptliabilities = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Liabilities'],
                ['transaction_type','=', 'Receive'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();

            $prereceiptliabilities = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '<', $fromDate],
                ['table_type','=', 'Liabilities'],
                ['transaction_type','=', 'Receive'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();

            foreach ($prereceiptliabilities as $prereceiptliabilitie){
                $prereceiptliabilitie->sumamount;
                $pretotalreceiptliabilities = $prereceiptliabilitie->sumamount + $pretotalreceiptliabilities;
            }

        }else{

            $receiptliabilities = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['table_type','=', 'Liabilities'],
                ['transaction_type','=', 'Receive'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();

        }

//        liabilities adjust


//        receivables cash transaction

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $paymentreceivables = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Asset'],
                ['transaction_type','=', 'Payment'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();

            $prepaymentreceivables = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '<', $fromDate],
                ['table_type','=', 'Asset'],
                ['transaction_type','=', 'Payment'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();

            foreach ($prepaymentreceivables as $prepaymentreceivable){
                $prepaymentreceivable->sumamount;
                $pretotalpaymentreceivables = $prepaymentreceivable->sumamount + $pretotalpaymentreceivables;
            }

        }else{

            $paymentreceivables = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['table_type','=', 'Asset'],
                ['transaction_type','=', 'Payment'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();
        }


        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            $receiptreceivables = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Asset'],
                ['transaction_type','=', 'Receive'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();

            $prereceiptreceivables = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '<', $fromDate],
                ['table_type','=', 'Asset'],
                ['transaction_type','=', 'Receive'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();

            foreach ($prereceiptreceivables as $prereceiptreceivable){
                $prereceiptreceivable->sumamount;
                $pretotalreceiptreceivables = $prereceiptreceivable->sumamount + $pretotalreceiptreceivables;
            }

        }else{

            $receiptreceivables = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['table_type','=', 'Asset'],
                ['transaction_type','=', 'Receive'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();
        }

//        receivables cash transaction end



        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $purchaseassets = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Asset'],
                ['transaction_type','=', 'Purchase'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();

            $prepurchaseassets = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '<', $fromDate],
                ['table_type','=', 'Asset'],
                ['transaction_type','=', 'Purchase'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();

            foreach ($prepurchaseassets as $prepurchaseasset){
                $prepurchaseasset->sumamount;
                $pretotalpurchaseasset = $prepurchaseasset->sumamount + $pretotalpurchaseasset;
            }

        }else{

            $purchaseassets = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['table_type','=', 'Asset'],
                ['transaction_type','=', 'Purchase'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();
        }


        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $soldassets = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Asset'],
                ['transaction_type','=', 'Sold'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();

            $presoldassets = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '<', $fromDate],
                ['table_type','=', 'Asset'],
                ['transaction_type','=', 'Sold'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();

            foreach ($presoldassets as $presoldasset){
                $presoldasset->sumamount;
                $pretotalsoldasset = $presoldasset->sumamount + $pretotalsoldasset;
            }

        }else{
            $soldassets = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['table_type','=', 'Asset'],
                ['transaction_type','=', 'Sold'],
                ['branch_id','=', $branch_id]
            ])->whereIn('payment_type', [ 'Cash','Bank'])->groupBy('account_id')->get();


        }




        // if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
        //     $fromDate = $request->input('fromDate');
        //     $toDate   = $request->input('toDate');

        //     $incomes = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
        //         ['t_date', '>=', $fromDate],
        //         ['t_date', '<=', $toDate],
        //         ['table_type','=', 'Income'],
        //         ['branch_id','=', $branch_id]
        //     ])->whereIn('payment_type', ['Cash','Bank'])->where('transaction_type','!=','Refund')
        //         ->groupBy('account_id')->get();

        //     $preincomes = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
        //         ['t_date', '<', $fromDate],
        //         ['table_type','=', 'Income'],
        //         ['branch_id','=', $branch_id]
        //     ])->whereIn('payment_type', [ 'Cash','Bank'])
        //         ->groupBy('account_id')->get();

        //     foreach ($preincomes as $preincome){
        //             $preincome->sumamount;
        //             $pretotalincome = $preincome->sumamount + $pretotalincome;
        //         }
        // }else{
        //     $incomes = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
        //         ['table_type','=', 'Income'],
        //         ['branch_id','=', $branch_id]
        //     ])->whereIn('payment_type', [ 'Cash','Bank'])->where('transaction_type','!=','Refund')
        //         ->groupBy('account_id')->get();
        // }

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            // $incomes = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
            //     ['t_date', '>=', $fromDate],
            //     ['t_date', '<=', $toDate],
            //     ['table_type','=', 'Income'],
            //     ['branch_id','=', $branch_id]
            // ])->whereIn('transaction_type', [ 'Due','Current','Advance Adjust'])->groupBy('account_id')->get();

            $incomes = Transaction::select('account_id',
                    DB::raw('SUM(CASE WHEN transaction_type = "Due" THEN amount ELSE 0 END) as total_due'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Current" THEN amount ELSE 0 END) as total_current'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Advance Adjust" THEN amount ELSE 0 END) as total_adv'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Refund" THEN amount ELSE 0 END) as total_refund'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Due" THEN amount ELSE 0 END) + SUM(CASE WHEN transaction_type = "Current" THEN amount ELSE 0 END) + SUM(CASE WHEN transaction_type = "Advance Adjust" THEN amount ELSE 0 END) - SUM(CASE WHEN transaction_type = "Refund" THEN amount ELSE 0 END) as sumamount, account_id')
                )->where([
                    ['t_date', '>=', $fromDate],
                    ['t_date', '<=', $toDate],
                    ['table_type','=', 'Income'],
                    ['branch_id','=', $branch_id]
                ])->groupBy('account_id')->get();

        }else{
            // $incomes = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
            //     ['table_type','=', 'Income'],
            //     ['branch_id','=', $branch_id]
            // ])->whereIn('transaction_type', [ 'Due','Current','Advance Adjust'])->groupBy('account_id')->get();

            $incomes = Transaction::select('account_id',
                    DB::raw('SUM(CASE WHEN transaction_type = "Due" THEN amount ELSE 0 END) as total_due'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Current" THEN amount ELSE 0 END) as total_current'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Advance Adjust" THEN amount ELSE 0 END) as total_adv'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Refund" THEN amount ELSE 0 END) as total_refund'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Due" THEN amount ELSE 0 END) + SUM(CASE WHEN transaction_type = "Current" THEN amount ELSE 0 END) + SUM(CASE WHEN transaction_type = "Advance Adjust" THEN amount ELSE 0 END) - SUM(CASE WHEN transaction_type = "Refund" THEN amount ELSE 0 END) as sumamount, account_id')
                )->where([
                    ['table_type','=', 'Income'],
                    ['branch_id','=', $branch_id]
                ])->groupBy('account_id')->get();
        }




        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            $expenses = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereNull('employee_id')->whereIn('payment_type', [ 'Cash','Bank'])
                ->groupBy('account_id')->get();

            $preexpenses = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
                ['t_date', '<', $fromDate],
                ['table_type','=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereNull('employee_id')->whereIn('payment_type', [ 'Cash','Bank'])
                ->groupBy('account_id')->get();

            foreach ($preexpenses as $preexpense){
                    $preexpense->sumamount;
                    $pretotaloutgoing = $preexpense->sumamount + $pretotaloutgoing;
                }

        }else{
            $expenses = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
                ['table_type','=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereNull('employee_id')->whereIn('payment_type', [ 'Cash','Bank'])
                ->groupBy('account_id')->get();
        }

         if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            $salaryexpenses = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereNotNull('employee_id')->whereIn('payment_type', [ 'Cash','Bank'])
                ->groupBy('account_id')->get();

            $presalaryexpenses = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '<', $fromDate],
                ['table_type','=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereNotNull('employee_id')->whereIn('payment_type', [ 'Cash','Bank'])
                ->groupBy('account_id')->get();

            foreach ($presalaryexpenses as $presalaryexpense){
                    $presalaryexpense->sumamount;
                    $pretotalsalaryexpense = $presalaryexpense->sumamount + $pretotalsalaryexpense;
                }

        }else{
            $salaryexpenses = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['table_type','=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereNotNull('employee_id')->whereIn('payment_type', [ 'Cash','Bank'])
                ->groupBy('account_id')->get();
                $presalaryexpenses = "";
        }





        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');


            $assetbalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '>=', $fromDate],
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.payment_type', '=', 'Owner Equity'],
                ['transactions.branch_id', '=', $branch_id]
            ])->sum('at_amount');

            $preassetbalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '<', $fromDate],
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.payment_type', '=', 'Owner Equity'],
                ['transactions.branch_id', '=', $branch_id]
            ])->sum('at_amount');

        }else{
             $assetbalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'Asset'],
                ['transactions.payment_type', '=', 'Owner Equity'],
                ['transactions.branch_id', '=', $branch_id]
            ])->sum('at_amount');
        }









        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $liabilitybalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '>=', $fromDate],
                ['transactions.t_date', '<=', $toDate],
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.payment_type', '=', 'Owner Equity'],
                ['transactions.branch_id', '=', $branch_id]
            ])->sum('at_amount');

            $preliabilitybalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.t_date', '<', $fromDate],
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.payment_type', '=', 'Owner Equity'],
                ['transactions.branch_id', '=', $branch_id]
            ])->sum('at_amount');


        }else{
            $liabilitybalance = DB::table('transactions')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('accounts','accounts.id','=','transactions.account_id')
            ->where([
                ['transactions.table_type', '=', 'Liabilities'],
                ['transactions.payment_type', '=', 'Owner Equity'],
                ['transactions.branch_id', '=', $branch_id]
            ])->sum('at_amount');
        }



        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $sharepremiums = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.t_date', '>=', $fromDate],
                    ['transactions.t_date', '<=', $toDate],
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.branch_id', '=', $branch_id],
                    ['accounts.account_name', '=', 'Share Premium']
                ])->sum('at_amount');

            $presharepremiums = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.t_date', '<', $fromDate],
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.branch_id', '=', $branch_id],
                    ['accounts.account_name', '=', 'Share Premium']
                ])->sum('at_amount');
        }else{
            $sharepremiums = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.branch_id', '=', $branch_id],
                    ['accounts.account_name', '=', 'Share Premium']
                ])->sum('at_amount');
        }






        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $ownerequities = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.t_date', '>=', $fromDate],
                    ['transactions.t_date', '<=', $toDate],
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.branch_id', '=', $branch_id],
                    ['accounts.account_name', '=', 'Capital']
                ])->sum('at_amount');

            $preownerequities = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.t_date', '<', $fromDate],
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.branch_id', '=', $branch_id],
                    ['accounts.account_name', '=', 'Capital']
                ])->sum('at_amount');
        }else{
            $ownerequities = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.branch_id', '=', $branch_id],
                    ['accounts.account_name', '=', 'Capital']
                ])->sum('at_amount');
        }



        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $withdrow = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.t_date', '>=', $fromDate],
                    ['transactions.t_date', '<=', $toDate],
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.branch_id','=', $branch_id],
                    ['accounts.account_name', '=', 'Withdraw']
                ])->sum('at_amount');

            $prewithdrow = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.t_date', '<', $fromDate],
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.branch_id','=', $branch_id],
                    ['accounts.account_name', '=', 'Withdraw']
                ])->sum('at_amount');
        }else{
            $withdrow = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.branch_id','=', $branch_id],
                    ['accounts.account_name', '=', 'Withdraw']
                ])->sum('at_amount');
        }


         if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $dividend = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.t_date', '>=', $fromDate],
                    ['transactions.t_date', '<=', $toDate],
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.transaction_type', '=', 'Payment'],
                    ['transactions.branch_id','=', $branch_id],
                    ['accounts.account_type', '=', 'Dividend']
                ])->sum('at_amount');

            $predividend = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.t_date', '<', $fromDate],
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.transaction_type', '=', 'Payment'],
                    ['transactions.branch_id','=', $branch_id],
                    ['accounts.account_type', '=', 'Dividend']
                ])->sum('at_amount');
        }else{
            $dividend = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.transaction_type', '=', 'Payment'],
                    ['transactions.branch_id','=', $branch_id],
                    ['accounts.account_type', '=', 'Dividend']
                ])->sum('at_amount');
        }


        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){

            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            }else{

            $fromDate = "";
            $toDate = "";

            }


            // previous balance calculation start
            if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){

                $fromDate = $request->input('fromDate');
                $toDate   = $request->input('toDate');

                $preopeningbalance = $preownerequities + $preliabilitybalance + $preassetbalance;

                $pretotalincoming = $pretotalincome + $pretotalsoldasset + $pretotalreceiptreceivables + $pretotalreceiptliabilities + $presharepremiums;
                // dd($pretotalincome);
                $prenetoutgoing =   $prewithdrow + $predividend + $pretotalpurchaseasset + $pretotalpaymentliabilities + $pretotaloutgoing + $pretotalsalaryexpense + $pretotalpaymentreceivables;

                $prenetbalance = $pretotalincoming +  $preopeningbalance - $prenetoutgoing;

                // dd($prenetoutgoing);

                }else{

                $fromDate = "";
                $toDate = "";
                $prenetbalance = "";

                }
            // previous balance calculation end

        $accounts = Account::All();

        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Cash Flow Statement');

        return view('financial_statement.cashflow')
            ->with('incomes', $incomes) // loop
            ->with('sharepremiums', $sharepremiums)
            ->with('ownerequities', $ownerequities)
            ->with('assetbalance', $assetbalance)
            ->with('liabilitybalance', $liabilitybalance)
            ->with('expenses', $expenses) // loop
            ->with('salaryexpenses', $salaryexpenses) // loop
            ->with('withdrow', $withdrow)
            ->with('dividend', $dividend)
            ->with('purchaseassets', $purchaseassets) // loop
            ->with('soldassets', $soldassets) // loop
            ->with('paymentreceivables', $paymentreceivables) // loop
            ->with('receiptreceivables', $receiptreceivables) // loop
            ->with('paymentliabilities', $paymentliabilities) // loop
            ->with('receiptliabilities', $receiptliabilities) // loop
            ->with('accounts', $accounts)
            ->with('pdfhead',$pdfhead)
            ->with('prenetbalance',$prenetbalance);

    }






    public function getProfitLoss(Request $request){
        $branch_id = auth()->user()->branch_id;

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            // $incomes = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
            //     ['t_date', '>=', $fromDate],
            //     ['t_date', '<=', $toDate],
            //     ['table_type','=', 'Income'],
            //     ['branch_id','=', $branch_id]
            // ])->whereIn('transaction_type', [ 'Due','Current','Advance Adjust'])->groupBy('account_id')->get();

            $incomes = Transaction::select('account_id',
                    DB::raw('SUM(CASE WHEN transaction_type = "Due" THEN amount ELSE 0 END) as total_due'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Current" THEN amount ELSE 0 END) as total_current'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Advance Adjust" THEN amount ELSE 0 END) as total_adv'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Refund" THEN amount ELSE 0 END) as total_refund'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Due" THEN amount ELSE 0 END) + SUM(CASE WHEN transaction_type = "Current" THEN amount ELSE 0 END) + SUM(CASE WHEN transaction_type = "Advance Adjust" THEN amount ELSE 0 END) - SUM(CASE WHEN transaction_type = "Refund" THEN amount ELSE 0 END) as sumamount, account_id')
                )->where([
                    ['t_date', '>=', $fromDate],
                    ['t_date', '<=', $toDate],
                    ['table_type','=', 'Income'],
                    ['branch_id','=', $branch_id]
                ])->groupBy('account_id')->get();

        }else{
            // $incomes = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
            //     ['table_type','=', 'Income'],
            //     ['branch_id','=', $branch_id]
            // ])->whereIn('transaction_type', [ 'Due','Current','Advance Adjust'])->groupBy('account_id')->get();

            $incomes = Transaction::select('account_id',
                    DB::raw('SUM(CASE WHEN transaction_type = "Due" THEN amount ELSE 0 END) as total_due'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Current" THEN amount ELSE 0 END) as total_current'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Advance Adjust" THEN amount ELSE 0 END) as total_adv'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Refund" THEN amount ELSE 0 END) as total_refund'),
                    DB::raw('SUM(CASE WHEN transaction_type = "Due" THEN amount ELSE 0 END) + SUM(CASE WHEN transaction_type = "Current" THEN amount ELSE 0 END) + SUM(CASE WHEN transaction_type = "Advance Adjust" THEN amount ELSE 0 END) - SUM(CASE WHEN transaction_type = "Refund" THEN amount ELSE 0 END) as sumamount, account_id')
                )->where([
                    ['table_type','=', 'Income'],
                    ['branch_id','=', $branch_id]
                ])->groupBy('account_id')->get();
        }




        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $expenses = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereNull('employee_id')->whereIn('transaction_type', [ 'Due','Current','Prepaid Adjust'])
                ->groupBy('account_id')->get();

        }else{
            $expenses = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
                ['table_type','=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereNull('employee_id')->whereIn('transaction_type', [ 'Due','Current','Prepaid Adjust'])
                ->groupBy('account_id')->get();
        }
        
        //        salary

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $salaryexp = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereNotNull('employee_id')->whereIn('transaction_type', [ 'Due','Current','Prepaid Adjust'])
                ->groupBy('account_id')->get();

        }else{
            $salaryexp = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['table_type','=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereNotNull('employee_id')->whereIn('transaction_type', [ 'Due','Current','Prepaid Adjust'])
                ->groupBy('account_id')->get();
        }


//        salary
        
        

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $totalprovision = 0;
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $provision = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', [ 'Account Payable'])
                ->groupBy('account_id')->get();

            foreach ($provision as $provision){
                $provision->sumamount;
                $totalprovision = $provision->sumamount + $totalprovision;
            }


        }else{
            $totalprovision = 0;
            $provision = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
                ['table_type','=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', ['Account Payable'])
                ->groupBy('account_id')->get();
            foreach ($provision as $provision){
                $provision->sumamount;
                $totalprovision = $provision->sumamount + $totalprovision;
            }
        }
        
        
        //      salary  tax calculation for PL

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){

            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            $totalsalarytax = 0;

            $salarytaxes = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
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
        }else{
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
        }

//      salary  tax calculation for PL end




        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $totalinterest = 0;
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $interest = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')
                ->where([
                    ['t_date', '>=', $fromDate],
                    ['t_date', '<=', $toDate],
                    ['table_type','=', 'Liabilities'],
                    ['transaction_type','=', 'Interest'],
                    ['branch_id','=', $branch_id]
                ])->groupBy('account_id')->get();
            foreach ($interest as $interest){
                $interest->sumamount;
                $totalinterest = $interest->sumamount + $totalinterest;
            }

        }else{
            $totalinterest = 0;
            $interest = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')
                ->where([
                    ['table_type','=', 'Liabilities'],
                    ['transaction_type','=', 'Interest'],
                    ['branch_id','=', $branch_id]
                ])->groupBy('account_id')->get();
            foreach ($interest as $interest){
                $interest->sumamount;
                $totalinterest = $interest->sumamount + $totalinterest;
            }
        }


        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $adjustexpenses = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'Asset'],
                ['branch_id','=', $branch_id]
            ])->whereNotNull('expense_id')->groupBy('account_id')->get();

        }else{
            $adjustexpenses = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['table_type', '=', 'Asset'],
                ['branch_id','=', $branch_id]
            ])->whereNotNull('expense_id')->groupBy('account_id')->get();
        }
        
        
        
        
        
        
        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

             $totaladdnewvat = 0;
            $addvats = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'Liabilities'],
                ['transaction_type','=', 'Add'],
                ['branch_id','=', $branch_id]
            ])->groupBy('account_id')->get();
            foreach ($addvats as $addvat){
                $addvat->sumamount;
                $totaladdnewvat = $addvat->sumamount + $totaladdnewvat;
            }

        }else{
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
        }
        
        
        
        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

             $totaladdassetvat = 0;
            $addassetvats = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'Asset'],
                ['transaction_type','=', 'Add'],
                ['branch_id','=', $branch_id]
            ])->groupBy('account_id')->get();
            foreach ($addassetvats as $addassetvat){
                $addassetvat->sumamount;
                $totaladdassetvat = $addassetvat->sumamount + $totaladdassetvat;
            }

        }else{
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
        }
        
        
        
        


        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

             $totalexpvat = 0;
            $expvats = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereNull('employee_id')->whereIn('transaction_type', [ 'Current','Due','Prepaid Adjust'])->groupBy('account_id')->get();
            foreach ($expvats as $expvat){
                $expvat->sumamount;
                $totalexpvat = $expvat->sumamount + $totalexpvat;
            }

        }else{
            $totalexpvat = 0;
            $expvats = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
                ['table_type', '=', 'Expense'],
                ['branch_id','=', $branch_id]
            ])->whereNull('employee_id')->whereIn('transaction_type', [ 'Current','Due','Prepaid Adjust'])->groupBy('account_id')->get();
            foreach ($expvats as $expvat){
                $expvat->sumamount;
                $totalexpvat = $expvat->sumamount + $totalexpvat;
            }
        }





        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            
            $totalincomevat = 0;
            $rcvevats = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'Income'],
                ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', [ 'Current','Due','Prepaid Adjust'])->groupBy('account_id')->get();
            
            foreach ($rcvevats as $vat){
                $vat->sumamount;
                $totalincomevat = $vat->sumamount + $totalincomevat;
            }

        }else{
            $totalincomevat = 0;
            $rcvevats = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
                ['table_type', '=', 'Income'],
                ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', [ 'Current','Due','Prepaid Adjust'])->groupBy('account_id')->get();
            
            foreach ($rcvevats as $vat){
                $vat->sumamount;
                $totalincomevat = $vat->sumamount + $totalincomevat;
            }
            
        }


        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $totaldep = 0;
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            $dep = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['table_type', '=', 'Asset'],
                ['branch_id','=', $branch_id],
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['transaction_type','=', 'Depreciation']
            ])->groupBy('account_id')->get();

            foreach ($dep as $dep){
                $dep->sumamount;
                $totaldep = $dep->sumamount + $totaldep;
            }
        }else{
            $totaldep = 0;
            $dep = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['table_type', '=', 'Asset'],
                ['branch_id','=', $branch_id],
                ['transaction_type','=', 'Depreciation']
            ])->groupBy('account_id')->get();

            foreach ($dep as $dep){
                $dep->sumamount;
                $totaldep = $dep->sumamount + $totaldep;
            }
        }
        
        //        for Revenue reserve

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $revenuereserve = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.t_date', '>=', $fromDate],
                    ['transactions.t_date', '<=', $toDate],
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.branch_id', '=', $branch_id],
                    ['accounts.account_name', '=', 'Revenue Reserve']
                ])->sum('at_amount');
        }else{
            $revenuereserve = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.branch_id', '=', $branch_id],
                    ['accounts.account_name', '=', 'Revenue Reserve']
                ])->sum('at_amount');
        }
        
        
        
        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            
                $vatreceive = Transaction::where([
                    ['t_date', '>=', $fromDate],
                     ['t_date', '<=', $toDate],
                    ['table_type', '=', 'Asset'],
                    ['branch_id','=', $branch_id]
                ])->whereNull('account_id')->sum('at_amount');

        }else{
            
                $vatreceive = Transaction::where([
                    ['table_type', '=', 'Asset'],
                    ['branch_id','=', $branch_id]
                ])->whereNull('account_id')->sum('at_amount');

        }
        
        
//        for Revenue reserve end
        
        
        
        //        for dividend payable calculation

        // if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
        //     $totaldividend = 0;
        //     $fromDate = $request->input('fromDate');
        //     $toDate   = $request->input('toDate');

        //     $dividend = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
        //         ['t_date', '>=', $fromDate],
        //         ['t_date', '<=', $toDate],
        //         ['table_type','=', 'OwnerEquity'],
        //         ['branch_id','=', $branch_id]
        //     ])->whereIn('transaction_type', ['Payable'])
        //         ->groupBy('account_id')->get();
        //     foreach ($dividend as $dividend){
        //         $dividend->sumamount;
        //         $totaldividend = $dividend->sumamount + $totaldividend;
        //     }


        // }else{
        //     $totaldividend = 0;
        //     $dividend = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
        //         ['table_type','=', 'OwnerEquity'],
        //         ['branch_id','=', $branch_id]
        //     ])->whereIn('transaction_type', ['Payable'])
        //         ->groupBy('account_id')->get();
        //     foreach ($dividend as $dividend){
        //         $dividend->sumamount;
        //         $totaldividend = $dividend->sumamount + $totaldividend;
        //     }
        // }

//        for dividend payable calculation end

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            
            }else{
                
            $fromDate = "";
            $toDate = "";                
                
            }
            
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Profit And Lose Account');

        return view('financial_statement.profitloss')
            ->with('incomes', $incomes)
            ->with('expenses', $expenses)
            ->with('salaryexp', $salaryexp)
            ->with('totalprovision', $totalprovision)
            ->with('totalsalarytax', $totalsalarytax)
            ->with('totalexpvat', $totalexpvat)
            ->with('totaladdnewvat', $totaladdnewvat)
            ->with('vatreceive', $vatreceive)
            ->with('totaldep', $totaldep)
            ->with('adjustexpenses', $adjustexpenses)
            ->with('revenuereserve', $revenuereserve)
            ->with('totalinterest', $totalinterest)
             ->with('totaladdassetvat', $totaladdassetvat)
            ->with('totalincomevat', $totalincomevat)
            ->with('pdfhead',$pdfhead);

    }
    
    
    
    
    
    

    public function getRetainedEarnings(Request $request)
    {
        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){

            $totalincome = 0;
            $totalexpense = 0;
            $totalsalaryexp = 0;
            $rcvvattotal = 0;
            $incomevattotal = 0;
            $totaladjustexpense = 0;
            $totaldepreciation = 0;
            $totaladjustliability = 0;

            $branch_id = auth()->user()->branch_id;

            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            $revenuereserve = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.t_date', '>=', $fromDate],
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
                    ['transactions.t_date', '>=', $fromDate],
                    ['transactions.t_date', '<=', $toDate],
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.transaction_type', '=', 'Reverse'],
                    ['transactions.branch_id', '=', $branch_id],
                    ['accounts.account_name', '=', 'Revenue Reserve']
                ])->sum('at_amount');

            $alldividends = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'OwnerEquity'],
                ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', [ 'Payable'])->groupBy('account_id')->get();


            $totaldividendexp = 0;
            $dividendexp = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'OwnerEquity'],
                ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', ['Payable'])
                ->groupBy('account_id')->get();
            foreach ($dividendexp as $dividendexp){
                $dividendexp->sumamount;
                $totaldividendexp = $dividendexp->sumamount + $totaldividendexp;
            }

            $totaldividendexp = 0;
            $dividendexp = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'OwnerEquity'],
                ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', ['Payable'])
                ->groupBy('account_id')->get();
            foreach ($dividendexp as $dividendexp){
                $dividendexp->sumamount;
                $totaldividendexp = $dividendexp->sumamount + $totaldividendexp;
            }







            $incomes = Transaction::selectRaw('SUM(amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Income'],
                ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', [ 'Due','Current','Advance Adjust'])->groupBy('account_id')->get();


            foreach ($incomes as $income){
                $income->sumamount;
                $totalincome = $income->sumamount + $totalincome;
            }

            
            $refundincomes = Transaction::where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Income'],
                ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', [ 'Refund'])->sum('amount');

            $adjustexpenses = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'Asset'],
                ['branch_id','=', $branch_id]
            ])->whereNotNull('expense_id')->groupBy('account_id')->get();

            foreach ($adjustexpenses as $adjustexpense){
                $adjustexpense->sumamount;
                $totaladjustexpense = $adjustexpense->sumamount + $totaladjustexpense;
            }




            $depreciations = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
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
                ['t_date', '>=', $fromDate],
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
                ['t_date', '>=', $fromDate],
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
                ['t_date', '>=', $fromDate],
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
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'Income'],
                ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', [ 'Current','Due','Prepaid Adjust'])
                ->groupBy('account_id')->get();
            foreach ($incomevats as $incomevat){
                $incomevat->sumamount;
                $incomevattotal = $incomevat->sumamount + $incomevattotal;
            }


            $rcvvats = Transaction::selectRaw('SUM(t_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
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
                ['t_date', '>=', $fromDate],
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
                ['t_date', '>=', $fromDate],
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
            
       

                $vatreceive = Transaction::where([
                    ['t_date', '>=', $fromDate],
                     ['t_date', '<=', $toDate],
                    ['table_type', '=', 'Asset'],
                    ['branch_id','=', $branch_id]
                ])->whereNull('account_id')->sum('at_amount');



            $totaladdnewvat = 0;
            $addvats = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['t_date', '>=', $fromDate],
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
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type', '=', 'Asset'],
                ['transaction_type','=', 'Add'],
                ['branch_id','=', $branch_id]
            ])->groupBy('account_id')->get();
            foreach ($addassetvats as $addassetvat){
                $addassetvat->sumamount;
                $totaladdassetvat = $addassetvat->sumamount + $totaladdassetvat;
            }

            $profitbeforetax = $totalincome - $totalexpense - $totalsalaryexp - $totaladjustexpense - $totaldepreciation - $totaladjustliability - $refundincomes;
            $vatprovision =   $totaladdnewvat + $incomevattotal - $rcvvattotal - $totaladdassetvat;

            $profitaftertax = $profitbeforetax - $taxprovision - $totalsalarytax - $vatprovision ;

        }else{


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

            $refundincomes = Transaction::where([
                ['table_type','=', 'Income'],
                ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', [ 'Refund'])->sum('amount');
                
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
            ])->whereIn('transaction_type', [ 'Current','Due','Prepaid Adjust'])
                ->groupBy('account_id')->get();
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
            
            
            $vatreceive = Transaction::where([
                    ['table_type', '=', 'Asset'],
                    ['branch_id','=', $branch_id]
                ])->whereNull('account_id')->sum('at_amount');

                // dd($refundincomes);
            $profitbeforetax = $totalincome - $totalexpense - $totalsalaryexp - $totaladjustexpense - $totaldepreciation - $totaladjustliability - $refundincomes;
            $vatprovision =   $totaladdnewvat + $incomevattotal - $rcvvattotal - $totaladdassetvat;

            $profitaftertax = $profitbeforetax - $taxprovision - $totalsalarytax - $vatprovision ;

            $alldividends = Transaction::selectRaw('SUM(at_amount) as sumamount, account_id')->where([
                ['table_type','=', 'OwnerEquity'],
                ['branch_id','=', $branch_id]
            ])->whereIn('transaction_type', [ 'Payable'])->groupBy('account_id')->get();



            $reverserevenue = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.transaction_type', '=', 'Reverse'],
                    ['transactions.branch_id', '=', $branch_id],
                    ['accounts.account_name', '=', 'Revenue Reserve']
                ])->sum('at_amount');
            $revenuereserve = DB::table('transactions')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('accounts','accounts.id','=','transactions.account_id')
                ->where([
                    ['transactions.table_type', '=', 'OwnerEquity'],
                    ['transactions.transaction_type', '=', 'Add'],
                    ['transactions.branch_id', '=', $branch_id],
                    ['accounts.account_name', '=', 'Revenue Reserve']
                ])->sum('at_amount');
        }

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            
            }else{
                
            $fromDate = "";
            $toDate = "";                
                
            }
            
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Profit And Lose Account');



        return view('financial_statement.retainedearnings')
            ->with('reverserevenue', $reverserevenue)
            ->with('revenuereserve', $revenuereserve)
            ->with('alldividends', $alldividends)
            ->with('profitaftertax', $profitaftertax)
            ->with('pdfhead',$pdfhead);
    }


    

    
    
}
