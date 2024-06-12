<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiagnosisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function getTransaction()
    {
        $branch_id = auth()->user()->branch_id;
        // $transactions = Transaction::where('branch_id', $branch_id)->orderBy('t_date','DESC')->get();
        
        
        $users = Admin::where('branch_id', $branch_id)->get();
        $transactions = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.branch_id', '=', $branch_id]
            ])->orderBy('t_date','DESC')->get();
        
        
        
        return view('analysis.transaction')->with('transactions', $transactions)->with('users', $users);
    }
    
    
    
    public function getTransactionSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;

        $users = Admin::where('branch_id', $branch_id)->get();
        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        
        $transactions = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['transactions.branch_id', '=', $branch_id]
            ])->orderBy('t_date','DESC')->get();
            
        
    return view('analysis.transaction')->with('transactions', $transactions)->with('users', $users);
    }
    
    
    
    public function getTransactionUserSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;

        $users = Admin::where('branch_id', $branch_id)->get();
        $username = $request->input('username');

        $transactions = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.created_id', '=', $username],
                ['transactions.branch_id', '=', $branch_id]
            ])->orderBy('t_date','DESC')->get();


        return view('analysis.transaction')->with('transactions', $transactions)->with('users', $users);
    }


    
    
    

    public function getAccount()
    {
        $branch_id = auth()->user()->branch_id;
        $accounts = Account::where('branch_id', $branch_id)->orderBy('id','DESC')->get();
        return view('analysis.account')->with('accounts', $accounts);
    }
    
  
    

    public function getEmployee()
    {
        $branch_id = auth()->user()->branch_id;
        $employees = Employee::where('branch_id', $branch_id)->get();
        return view('analysis.employee')->with('employees', $employees);
    }
    
    
    public function getUpdatedData()
    {
        $branch_id = auth()->user()->branch_id;

        $users = Admin::where('branch_id', $branch_id)->get();
        $data = Transaction::where('branch_id', $branch_id)->whereNotNull('updated_by')->get();
        return view('analysis.updatedata')->with('data', $data)->with('users', $users);
    }


    public function getUpdatedTransactionSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;

        $users = Admin::where('branch_id', $branch_id)->get();
        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');

        $data = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['branch_id','=', $branch_id]
        ])->whereNotNull('updated_by')->orderBy('t_date','DESC')->get();

        return view('analysis.updatedata')->with('data', $data)->with('users', $users);
    }

    public function getUpdatedTransactionUserSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;

        $users = Admin::where('branch_id', $branch_id)->get();
        $username = $request->input('username');

        $data = Transaction::where([
            ['updated_by', '=', $username],
            ['branch_id','=', $branch_id]
        ])->whereNotNull('updated_by')->orderBy('t_date','DESC')->get();

        return view('analysis.updatedata')->with('data', $data)->with('users', $users);
    }


    
    

   public function getAccDiagonosis($id)
    {
        $ar = [];
        $branch_id = auth()->user()->branch_id;
        $accounts = Account::where([
            ['id','=', $id],
            ['branch_id','=', $branch_id]
        ])->get();


        foreach ($accounts as $account) {
          $ar[] = $account;
        }
        
    return ['status'=> 202, 'report'=> $ar];
    }
   
public function getTranDiagonosis($id)
    {
        $ar = [];
        $branch_id = auth()->user()->branch_id;
        $accounts = Transaction::where([
            ['id','=', $id],
            ['branch_id','=', $branch_id]
        ])->get();


        foreach ($accounts as $account) {
          $ar[] = $account;
        }
        
    return ['status'=> 202, 'report'=> $ar];
    }
     


}
