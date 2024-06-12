<?php

namespace App\Http\Controllers;


use App\Models\Account;
use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class DayBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function getDayBook()
    {
        $branch_id = auth()->user()->branch_id;
        $query = Transaction::where([
            ['payment_type','=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();

    $fromDate = "";
    $toDate = "";
    $prebalance = "";

    $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Day Cashbook');
    return view('daybook.cash')->with('data', $query)->with('pdfhead',$pdfhead)->with('prebalance',$prebalance);

    }

    public function getDayBookSearch(Request $request)
    {

        $branch_id = auth()->user()->branch_id;

        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');

        $query = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['payment_type','=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();

        // new code start

        $prevcashrcv = Transaction::where([
            ['t_date', '<', $fromDate],
            ['payment_type','=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', ['Receive','Advance','Sold'])->sum('at_amount');

        $prevcashpmt = Transaction::where([
            ['t_date', '<', $fromDate],
            ['payment_type','=', 'Cash'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', ['Payment','Purchase','Prepaid','Due Adjust'])->sum('at_amount');

        $currentpmt = Transaction::where([
            ['t_date', '<', $fromDate],
            ['payment_type','=', 'Cash'],
            ['transaction_type','=', 'Current'],
            ['table_type','=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->sum('amount'); // must get amount, not at_amount



        $currentrcv = Transaction::where([
            ['t_date', '<', $fromDate],
            ['payment_type','=', 'Cash'],
            ['transaction_type','=', 'Current'],
            ['table_type','=', 'Income'],
            ['branch_id','=', $branch_id]
        ])->sum('amount'); // must get amount, not at_amount

        // new code end

        $prebalance = $prevcashrcv + $currentrcv - $prevcashpmt - $currentpmt;

        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Day Cashbook');

    return view('daybook.cash')->with('data', $query)->with('pdfhead',$pdfhead)->with('prebalance',$prebalance);
    }





    public function getBankBook()
    {

        $branch_id = auth()->user()->branch_id;
        $query = Transaction::where([
            ['payment_type','=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();
        // dd($query);

            $fromDate = "";
            $toDate = "";
            $prebalance = "";
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Day Bankbook');

    return view('daybook.bank')->with('data', $query)->with('pdfhead',$pdfhead)->with('prebalance',$prebalance);


    }

    public function getBankBookSearch(Request $request)
    {

        $branch_id = auth()->user()->branch_id;


        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');

        $query = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['payment_type','=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();

        $prevbankrcv = Transaction::where([
            ['t_date', '<', $fromDate],
            ['payment_type','=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', ['Receive','Advance','Sold'])->sum('at_amount');

        $prevbankpmt = Transaction::where([
            ['t_date', '<', $fromDate],
            ['payment_type','=', 'Bank'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type', ['Payment','Purchase','Prepaid','Due Adjust'])->sum('at_amount');

        $currentpmt = Transaction::where([
            ['t_date', '<', $fromDate],
            ['payment_type','=', 'Bank'],
            ['transaction_type','=', 'Current'],
            ['table_type','=', 'Expense'],
            ['branch_id','=', $branch_id]
        ])->sum('amount'); // must get amount, not at_amount



        $currentrcv = Transaction::where([
            ['t_date', '<', $fromDate],
            ['payment_type','=', 'Bank'],
            ['transaction_type','=', 'Current'],
            ['table_type','=', 'Income'],
            ['branch_id','=', $branch_id]
        ])->sum('amount'); // must get amount, not at_amount


        $prebalance = $prevbankrcv + $currentrcv - $prevbankpmt - $currentpmt;

    $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Day Bankbook');
    return view('daybook.bank')->with('data', $query)->with('pdfhead',$pdfhead)->with('prebalance',$prebalance);
    }







}
