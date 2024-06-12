<?php

namespace App\Http\Controllers;


use App\Models\Account;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Liability;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function getEmployeeList()
    {
        $branch_id = auth()->user()->branch_id;
        $employees = Employee::where('branch_id', $branch_id)->get();
        return view('ledger.employee')->with('employees', $employees);
    }

        public function getEmployeeledger($id)
    {
        $id = $id;
            $fromDate = "";
            $toDate = "";        
        $branch_id = auth()->user()->branch_id;
        $allemployees = Employee::where('id', "$id")->get();

        $employees = Transaction::where([
            ['employee_id','=', $id],
            ['at_amount','!=', '0.00'],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type',['Current','Due','Prepaid Adjust'])->orderBy('t_date','DESC')
        ->get();
        
        foreach ($allemployees as $allemployee){
        $title =  $allemployee->employee_name.' Ledger (Employee)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);           
        
        return view('ledger.employeeledger')
        ->with('employees', $employees)
        ->with('id', $id)
        ->with('allemployees', $allemployees)->with('pdfhead',$pdfhead);
    }
    
    public function employeeSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;


        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('employeeledger');
        $allemployees = Employee::where('id', "$id")->get();
        
        $employees = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['employee_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type',['Current','Due'])->orderBy('t_date','DESC')->get();

        foreach ($allemployees as $allemployee){
        $title =  $allemployee->employee_name.' Ledger (Employee)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);  

        return view('ledger.employeeledger')
            ->with('employees', $employees)
            ->with('id', $id)
            ->with('allemployees', $allemployees)->with('pdfhead',$pdfhead);
    }
    
     public function getEmployeePayableList()
    {
            $branch_id = auth()->user()->branch_id;
            
            $employees = DB::table('employees')
            ->select('transactions.*','employees.employee_name')
            ->join('transactions','transactions.employee_id','=','employees.id')
            ->where([
                ['transactions.table_type', '=', 'Expense'],
                ['transactions.transaction_type', '=', 'Due'],
                ['transactions.branch_id', '=', $branch_id]
            ])->groupBy('employee_id')->get();


        return view('ledger.employeepayable')->with('employees', $employees);
    }

    public function getEmployeePayableledger($id)
    {
         $id = $id;
        $fromDate = "";
        $toDate = "";         
        $branch_id = auth()->user()->branch_id;
        $allemployees = Employee::where('id', "$id")->get();

        $employees = Transaction::where([
            ['employee_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type',['Due Adjust','Due'])->orderBy('t_date','DESC')
        ->get();
        
        foreach ($allemployees as $allemployee){
        $title =  $allemployee->employee_name.' - Employee Payable Ledger';
        }
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);  
        
        return view('ledger.employeepayableledger')
        ->with('employees', $employees)
        ->with('id', $id)
        ->with('allemployees', $allemployees)->with('pdfhead',$pdfhead);
    }
    
    
    public function getEmployeePayableledgerSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;


        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('employeeledger');
        $allemployees = Employee::where('id', "$id")->get();


        $employees = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['employee_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type',['Due Adjust','Due'])->orderBy('t_date','ASC')
            ->get();

        foreach ($allemployees as $allemployee){
        $title =  $allemployee->employee_name.' - Employee Payable Ledger)';
        }
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);  

        return view('ledger.employeepayableledger')
            ->with('employees', $employees)
            ->with('id', $id)
            ->with('allemployees', $allemployees)->with('pdfhead',$pdfhead);
    }
    
    
    
    
    public function getEmployeeTax()
    {
        // $employees = Employee::all();
        $branch_id = auth()->user()->branch_id;
        $employees = Employee::where('branch_id', $branch_id)->get();
        return view('ledger.employeetax')->with('employees', $employees);
    }

    public function getEmployeetaxledger($id)
    {
        $id = $id;
        $fromDate = "";
        $toDate = "";    
        $branch_id = auth()->user()->branch_id;
        $allemployees = Employee::where('id', "$id")->get();

        $employees = Transaction::where([
            ['employee_id','=', $id],
            ['t_amount','!=', '0.00'],
            ['branch_id','=', $branch_id]
        ])->whereIn('payment_type',['Account Payable'])->orderBy('t_date','DESC')
            ->get();

        foreach ($allemployees as $allemployee){
        $title =  $allemployee->employee_name.' Ledger (Employee)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);              
        foreach ($allemployees as $allemployee){
        $title =  $allemployee->employee_name.' - Employee Tax Ledger';
        }
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);            
        return view('ledger.employeetaxledger')
            ->with('employees', $employees)
            ->with('id', $id)
            ->with('allemployees', $allemployees)->with('pdfhead',$pdfhead);
    }
    
    public function getEmployeetaxledgerSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;
        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('employeeledger');
        $allemployees = Employee::where('id', "$id")->get();


        $employees = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['employee_id','=', $id],
            ['t_amount','!=', '0.00'],
            ['branch_id','=', $branch_id]
        ])->whereIn('payment_type',['Account Payable'])->orderBy('t_date','ASC')
            ->get();
        foreach ($allemployees as $allemployee){
        $title =  $allemployee->employee_name.' - Employee Tax Ledger)';
        }
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);             
        return view('ledger.employeetaxledger')
            ->with('employees', $employees)
            ->with('id', $id)
            ->with('allemployees', $allemployees)->with('pdfhead',$pdfhead);
    }
    
    
    
    public function getPrepaidEmployeeList()
    {
        $branch_id = auth()->user()->branch_id;

        $employees = DB::table('employees')
            ->select('transactions.*','employees.employee_name')
            ->join('transactions','transactions.employee_id','=','employees.id')
            ->where([
                ['transactions.table_type', '=', 'Expense'],
                ['transactions.transaction_type', '=', 'Prepaid'],
                ['transactions.branch_id', '=', $branch_id]
            ])->groupBy('employee_id')->get();

        return view('ledger.prepaidemployee')->with('employees', $employees);
    }

    public function getPrepaidEmployeeledger($id)
    {
        $id = $id;
            $fromDate = "";
            $toDate = "";        
        $branch_id = auth()->user()->branch_id;
        $allemployees = Employee::where('id', "$id")->get();

        $employees = Transaction::where([
            ['employee_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type',['Prepaid Adjust','Prepaid'])->orderBy('t_date','DESC')
            ->get();
            
        foreach ($allemployees as $allemployee){
        $title =  $allemployee->employee_name.' Ledger (Prepaid Salary)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);        
        return view('ledger.prepaidemployeeledger')
            ->with('employees', $employees)
            ->with('id', $id)
            ->with('allemployees', $allemployees)->with('pdfhead',$pdfhead);
    }
    
    public function getPrepaidEmployeeledgerSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;


        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('employeeledger');
        $allemployees = Employee::where('id', "$id")->get();

        $employees = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['employee_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->whereIn('transaction_type',['Prepaid Adjust','Prepaid'])->orderBy('t_date','ASC')
            ->get();
            
        foreach ($allemployees as $allemployee){
        $title =  $allemployee->employee_name.' Ledger (Prepaid Salary)';
        }
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);      
        return view('ledger.prepaidemployeeledger')
            ->with('employees', $employees)
            ->with('id', $id)
            ->with('allemployees', $allemployees)->with('pdfhead',$pdfhead);
    }


    
    
    








    public function getAssetList()
    {
        $branch_id = auth()->user()->branch_id;
        $assets = Account::where([
            ['account_type','=', 'Fixed Asset'],
            ['branch_id','=', $branch_id]
        ])->get();
        return view('ledger.asset')->with('assets', $assets);
    }

    public function getAssetledger($id)
    {
        $id = $id;
            $fromDate = "";
            $toDate = "";        
        $branch_id = auth()->user()->branch_id;
        $assets = Transaction::where([
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();
        $accountnames = Account::where('id', "$id")->get();
        $accounts = Account::All();

        foreach ($accountnames as $accountname){
        $title =  $accountname->account_name.' Ledger (Fixed Asset)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);   

        return view('ledger.assetledger')
            ->with('accountnames', $accountnames)
            ->with('assets', $assets)
            ->with('id', $id)
            ->with('accounts', $accounts)->with('pdfhead',$pdfhead);
    }
    
     public function fixedAssetSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;
        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('hiddensearchid');
        $accountnames = Account::where('id', "$id")->get();
        
        $assets = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();

        foreach ($accountnames as $accountname){
        $title =  $accountname->account_name.' Ledger (Fixed Asset)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);   
        
        return view('ledger.assetledger')
            ->with('accountnames', $accountnames)
            ->with('assets', $assets)
            ->with('id', $id)->with('pdfhead',$pdfhead);

    }
    
    


    public function getCurrentAsset()
    {
        $branch_id = auth()->user()->branch_id;
        $assets = Account::where([
            ['account_type','=', 'Current Asset'],
            ['branch_id','=', $branch_id]
        ])->get();
        return view('ledger.current_asset')->with('assets', $assets);
    }
    public function getCurrentAssetledger($id)
    {
        $id = $id;
            $fromDate = "";
            $toDate = "";        
        $branch_id = auth()->user()->branch_id;
        $assets = Transaction::where([
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();
        $accountnames = Account::where('id', "$id")->get();
        $accounts = Account::All();
        
         foreach ($accountnames as $accountname){
        $title =  $accountname->account_name.' Ledger (Current Asset)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);            
        
        return view('ledger.current_asset_ledger')
            ->with('assets', $assets)
            ->with('id', $id)
            ->with('accounts', $accounts)
            ->with('accountnames', $accountnames)->with('pdfhead',$pdfhead);
    }
    
     public function currentAssetSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;


        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('hiddensearchid');
        $accountnames = Account::where('id', "$id")->get();
        
        $assets = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();

        foreach ($accountnames as $accountname){
        $title =  $accountname->account_name.' Ledger (Current Asset)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);   
        
        return view('ledger.current_asset_ledger')
            ->with('accountnames', $accountnames)
            ->with('assets', $assets)
            ->with('id', $id)->with('pdfhead',$pdfhead);

    }
    
    
    

    public function getAccountReceivable()
    {

        $branch_id = auth()->user()->branch_id;
        $assets = Account::where([
            ['account_type','=', 'Current Asset-AR'],
            ['branch_id','=', $branch_id]
        ])->get();

        $accounts = Account::All();
        return view('ledger.receivable')->with('assets', $assets)->with('accounts', $accounts);
    }

    public function getAccReceivableledger($id)
    {
        $id = $id;
            $fromDate = "";
            $toDate = "";        
        $branch_id = auth()->user()->branch_id;

        $salesdue = DB::table('transactions')->where([
            ['table_type','=', 'Income'],
            ['asset_id','=', $id],
            ['payment_type','=', 'Account Receivable'],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->select('t_date', 'description', 'at_amount','ref','transaction_type','table_type','payment_type');
        
        $assets = Transaction::where([
            ['table_type','=', 'Asset'],
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->select('t_date','description','at_amount','ref','transaction_type','table_type','payment_type')
            ->union($salesdue)
            ->orderBy('t_date','DESC')->get();

        $accountnames = Account::where('id', "$id")->get();
        $accounts = Account::All();

        foreach ($accountnames as $accountname){
        $title =  $accountname->account_name.' Ledger (Account Receivable)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);           
        
        return view('ledger.receivableledger')
            ->with('assets', $assets)
            ->with('accounts', $accounts)
            ->with('id', $id)
            ->with('accountnames', $accountnames)->with('pdfhead',$pdfhead);
    }
    
    
    public function accReceivableSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;
        
        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('hiddensearchid');
        $accountnames = Account::where('id', "$id")->get();
        
        
        $salesdue = DB::table('transactions')->where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['table_type','=', 'Income'],
            ['payment_type','=', 'Account Receivable'],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->select('t_date', 'description', 'at_amount','ref','transaction_type','table_type','payment_type');
        
        $assets = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['table_type','=', 'Asset'],
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->select('t_date','description','at_amount','ref','transaction_type','table_type','payment_type')
            ->union($salesdue)
            ->orderBy('t_date','DESC')->get();
 

        foreach ($accountnames as $accountname){
        $title =  $accountname->account_name.' Ledger (Account Receivable)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title); 
        
        
        return view('ledger.receivableledger')
            ->with('assets', $assets)
            ->with('id', $id)
            ->with('accountnames', $accountnames)->with('pdfhead',$pdfhead);
    }
    
    
    
    public function getLiabilityList()
    {
        $branch_id = auth()->user()->branch_id;
        $liabilities = Account::where([
            ['account_type','=', 'Long-term Liabilities'],
            ['branch_id','=', $branch_id]
        ])->get();
        return view('ledger.liability')->with('liabilities', $liabilities);
    }

    public function getLiabilityledger($id)
    {
        $id = $id;
            $fromDate = "";
            $toDate = "";        
        $branch_id = auth()->user()->branch_id;
        $liabilities = Transaction::where([
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();
        $accountnames = Account::where('id', "$id")->get();
        
             foreach ($accountnames as $accountname){
            $title =  $accountname->account_name.' Ledger (Long-term Liability)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);        
        
        return view('ledger.liabilityledger')
            ->with('accountnames', $accountnames)
            ->with('liabilities', $liabilities)
            ->with('id', $id)->with('pdfhead',$pdfhead);
    }

    public function getLiabilitySearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;


        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('hiddensearchid');
        $accountnames = Account::where('id', "$id")->get();
        
        $liabilities = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();
        
        
         foreach ($accountnames as $accountname){
        $title =  $accountname->account_name.' Ledger (Long-term Liability)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);           
        
        return view('ledger.liabilityledger')
            ->with('accountnames', $accountnames)
            ->with('liabilities', $liabilities)
            ->with('id', $id)->with('pdfhead',$pdfhead);
    }
    
    
    
    
    
    
    
    
    public function getCurrentLiabilityList()
    {
        $branch_id = auth()->user()->branch_id;
        $liabilities = Account::where([
            ['account_type','=', 'Short-term Liabilities'],
            ['branch_id','=', $branch_id]
        ])->get();
        return view('ledger.currentliability')->with('liabilities', $liabilities);
    }

    public function getCurrentLiabilityledger($id)
    {
        $id = $id;
            $fromDate = "";
            $toDate = "";
        $branch_id = auth()->user()->branch_id;
        $liabilities = Transaction::where([
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();
        $accountnames = Account::where('id', "$id")->get();
        
             foreach ($accountnames as $accountname){
            $title =  $accountname->account_name.' Ledger (Short-term Liability)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);
        
        return view('ledger.currentliabilityledger')
            ->with('accountnames', $accountnames)
            ->with('liabilities', $liabilities)
            ->with('id', $id)->with('pdfhead',$pdfhead);
    }

    public function getCurrentLiabilitySearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;


        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('hiddensearchid');
        $accountnames = Account::where('id', "$id")->get();
        
        $liabilities = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();

             foreach ($accountnames as $accountname){
            $title =  $accountname->account_name.' Ledger (Short-term Liability)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);
        
        return view('ledger.currentliabilityledger')
            ->with('accountnames', $accountnames)
            ->with('liabilities', $liabilities)
            ->with('id', $id)->with('pdfhead',$pdfhead);
    }










    public function getAccountPayable()
    {
        $branch_id = auth()->user()->branch_id;
        $liabilities = Account::where([
            ['account_type','=', 'Liabilities-AP'],
            ['branch_id','=', $branch_id]
        ])->get();
        return view('ledger.payable')->with('liabilities', $liabilities);
    }
    
    

    public function getAccPayableledger($id)
    {
        $id = $id;
        $branch_id = auth()->user()->branch_id;
            $fromDate = "";
            $toDate = "";        
        $expense = DB::table('transactions')->where([
            ['table_type','=', 'Expense'],
            ['Liability_id','=', $id],
            ['payment_type','=', 'Account Payable'],
            ['branch_id','=', $branch_id]
        ])->select('t_date', 'description', 'at_amount','ref','transaction_type','table_type','payment_type');


        $purchasedue = DB::table('transactions')->where([
            ['table_type','=', 'Asset'],
            ['Liability_id','=', $id],
            ['payment_type','=', 'Account Payable'],
            ['branch_id','=', $branch_id]
        ])->select('t_date', 'description', 'at_amount','ref','transaction_type','table_type','payment_type');


        $liabilities = Transaction::where([
            ['table_type','=', 'Liabilities'],
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->select('t_date','description','at_amount','ref','transaction_type','table_type','payment_type')
            ->union($expense)
            ->union($purchasedue)
            ->orderBy('t_date','DESC')->get();

        $accountnames = Account::where('id', "$id")->get();
        
         foreach ($accountnames as $accountname){
            $title =  $accountname->account_name.' Ledger (Account Payable)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);

        return view('ledger.payableledger')
            ->with('accountnames', $accountnames)
            ->with('liabilities', $liabilities)->with('id', $id)->with('pdfhead',$pdfhead);
    }
    
    public function getAccPayableSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;


        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('hiddensearchid');
        $accountnames = Account::where('id', "$id")->get();
        
        
        $expense = DB::table('transactions')->where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['table_type','=', 'Expense'],
            ['Liability_id','=', $id],
            ['payment_type','=', 'Account Payable'],
            ['branch_id','=', $branch_id]
        ])->select('t_date', 'description', 'at_amount','ref','transaction_type','table_type','payment_type');


        $purchasedue = DB::table('transactions')->where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['table_type','=', 'Asset'],
            ['Liability_id','=', $id],
            ['payment_type','=', 'Account Payable'],
            ['branch_id','=', $branch_id]
        ])->select('t_date', 'description', 'at_amount','ref','transaction_type','table_type','payment_type');



        $liabilities = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['table_type','=', 'Liabilities'],
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->select('t_date','description','at_amount','ref','transaction_type','table_type','payment_type')
            ->union($purchasedue)
            ->orderBy('t_date','DESC')->get();

     foreach ($accountnames as $accountname){
            $title =  $accountname->account_name.' Ledger (Account Payable)';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);

        
        return view('ledger.payableledger')
            ->with('accountnames', $accountnames)
            ->with('liabilities', $liabilities)->with('id', $id)->with('pdfhead',$pdfhead);
    }
    
    
    
    
    
    
    
    
    public function getledger()
    {
        $branch_id = auth()->user()->branch_id;

        $ledgers = Account::where('branch_id', $branch_id)->whereIn('account_type',['Expense','Income'])->whereNotIn('account_name',['Salary','Wages','Tax Provision'])->get();
        return view('ledger.incomeexpense')->with('ledgers', $ledgers);
    }

   public function getIncomeorExpense($id)
    {
        $id = $id;

        $branch_id = auth()->user()->branch_id;

            $fromDate = "";
            $toDate = "";

        $assetadjust = DB::table('transactions')->where([
            ['transaction_type','=', 'Adjust'],
            ['table_type','=', 'Asset'],
            ['expense_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->select('t_date', 'description', 'at_amount as amount','ref','transaction_type','table_type','payment_type');


        $ledgers = Transaction::where([
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->whereIn('table_type',['Expense','Income'])->whereIn('transaction_type',['Prepaid Adjust','Current','Due','Adjust','Advance Adjust','Account Payable','Payment','Refund'])
            ->select('t_date','description','amount','ref','transaction_type','table_type','payment_type')
            ->union($assetadjust)
            ->orderBy('t_date','DESC')->get();

        $accountnames = Account::where('id', "$id")->get();
        $accounts = Account::All();
        
        foreach ($accountnames as $accountname){
            $title =  $accountname->account_name.' Ledger';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);
        return view('ledger.incomeexpenseledger')
            ->with('accountnames', $accountnames)
            ->with('accounts', $accounts)
            ->with('ledgers', $ledgers)
            ->with('id', $id)->with('pdfhead',$pdfhead);
    }


      public function getIncomeExpenseSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;


        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('hiddensearchid');
        $accountnames = Account::where('id', "$id")->get();

        $assetadjust = DB::table('transactions')->where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['transaction_type','=', 'Adjust'],
            ['table_type','=', 'Asset'],
            ['expense_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->select('t_date', 'description', 'at_amount as amount','ref','transaction_type','table_type','payment_type');


        $ledgers = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->whereIn('table_type',['Expense','Income'])->whereIn('transaction_type',['Prepaid Adjust','Current','Due','Adjust','Advance Adjust','Account Payable','Payment'])
            ->select('t_date','description','amount','ref','transaction_type','table_type','payment_type')
            ->union($assetadjust)
            ->orderBy('t_date','DESC')->get();

        foreach ($accountnames as $accountname){
            $title =  $accountname->account_name.' Ledger';
        }
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);


        return view('ledger.incomeexpenseledger')
            ->with('accountnames', $accountnames)
            ->with('ledgers', $ledgers)
            ->with('id', $id)->with('pdfhead',$pdfhead);
    }
    
    
    
    
    public function getadvanceledger()
    {
        $branch_id = auth()->user()->branch_id;


        $ledgers = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Expense','Income'])->whereNotIn('account_name',['Salary','Wages','Tax Provision'])
            ->whereIn('transaction_type',['Prepaid Adjust','Advance Adjust','Advance','Prepaid',])
            ->groupBy('account_id')->get();

        return view('ledger.advincomeexpense')->with('ledgers', $ledgers);
    }

    public function getAdvIncomeorExpense($id)
    {
        $id = $id;
        $branch_id = auth()->user()->branch_id;
            $fromDate = "";
            $toDate = "";
        $assetadjust = DB::table('transactions')->where([
            ['transaction_type','=', 'Adjust'],
            ['table_type','=', 'Asset'],
            ['expense_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->select('t_date', 'description', 'at_amount','ref','transaction_type','table_type','payment_type');

        $ledgers = Transaction::where([
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->whereIn('table_type',['Expense','Income'])->whereIn('transaction_type',['Prepaid Adjust','Advance Adjust','Advance','Prepaid',])
            ->select('t_date','description','at_amount','ref','transaction_type','table_type','payment_type')
            ->union($assetadjust)->orderBy('t_date','DESC')->get();

        $accountnames = Account::where('id', "$id")->get();
        $accounts = Account::All();
        
           foreach ($accountnames as $accountname){
            $title = 'Advance '.$accountname->account_name.' Ledger';
        }
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);
        
        return view('ledger.advincomeexpenseledger')
            ->with('accountnames', $accountnames)
            ->with('accounts', $accounts)
            ->with('ledgers', $ledgers)
            ->with('id', $id)->with('pdfhead',$pdfhead);
    }
    
    public function getAdvIncomeorExpenseSearch(Request $request)
    {
        
        $branch_id = auth()->user()->branch_id;
        
        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('hiddensearchid');

        $assetadjust = DB::table('transactions')->where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['transaction_type','=', 'Adjust'],
            ['table_type','=', 'Asset'],
            ['expense_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->select('t_date', 'description', 'at_amount','ref','transaction_type','table_type','payment_type');

        $ledgers = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->whereIn('table_type',['Expense','Income'])->whereIn('transaction_type',['Prepaid Adjust','Advance Adjust','Advance','Prepaid',])
            ->select('t_date','description','at_amount','ref','transaction_type','table_type','payment_type')
            ->union($assetadjust)->orderBy('t_date','DESC')->get();

        $accountnames = Account::where('id', "$id")->get();
        $accounts = Account::All();
        
         foreach ($accountnames as $accountname){
        $title = 'Advance '.$accountname->account_name.' Ledger';
        }
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);
        
        return view('ledger.advincomeexpenseledger')
            ->with('accountnames', $accountnames)
            ->with('accounts', $accounts)
            ->with('ledgers', $ledgers)
            ->with('id', $id)->with('pdfhead',$pdfhead);
    }


    
    
    
    
    
    
    
    
    public function interest()
    {

        $branch_id = auth()->user()->branch_id;
        $fromDate = "";
        $toDate = "";
        $interest = DB::table('transactions')->where([
            ['table_type','=', 'Liabilities'],
            ['transaction_type','=', 'Interest'],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Interest Ledger');
        
        return view('ledger.interestledger')
        ->with('interest', $interest)->with('pdfhead',$pdfhead);
    }
    
      public function getInterestSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;


        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        
        
        $interest = DB::table('transactions')->where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['table_type','=', 'Liabilities'],
            ['transaction_type','=', 'Interest'],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Interest Ledger');

        return view('ledger.interestledger')
        ->with('interest', $interest)->with('pdfhead',$pdfhead);
    }
    
    
    
    public function depreciation()
    {

        $branch_id = auth()->user()->branch_id;
        
        $fromDate = "";
        $toDate = "";

        $depreciation = DB::table('transactions')->where([
            ['table_type','=', 'Asset'],
            ['transaction_type','=', 'Depreciation'],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Depreciation Ledger');
        
        return view('ledger.depreciationledger')
        ->with('depreciation', $depreciation)->with('pdfhead',$pdfhead);
    }


    public function getDepSearch(Request $request)
        {
            $branch_id = auth()->user()->branch_id;
    
    
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            
            
            $depreciation = DB::table('transactions')->where([
                ['t_date', '>=', $fromDate],
                ['t_date', '<=', $toDate],
                ['table_type','=', 'Asset'],
                ['transaction_type','=', 'Depreciation'],
                ['branch_id','=', $branch_id]
            ])->orderBy('t_date','DESC')->get();
            
             $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Depreciation Ledger');
            return view('ledger.depreciationledger')->with('depreciation', $depreciation)->with('pdfhead',$pdfhead);
        }

    
    
    
    public function getTaxPayableLedger()
    {
        $branch_id = auth()->user()->branch_id;
        $fromDate = "";
        $toDate = "";
        $taxes = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.transaction_type', '!=', 'Due Adjust'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Expense'])
            ->whereIn('accounts.account_name',['Salary','Wages','Tax Provision'])
            ->whereIn('transactions.transaction_type',['Account Payable','Payment',''])
            ->orderBy('t_date','DESC')
            ->get();
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Tax Provision & Salary Tax Ledger');
        return view('ledger.taxledger')->with('taxes', $taxes)->with('pdfhead',$pdfhead);
    }


    public function getTaxSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;
        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');

        $taxes = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.t_date', '>=', $fromDate],
                ['transactions.t_date', '<=', $toDate],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Expense'])
            ->whereIn('accounts.account_name',['Salary','Wages','Tax Provision'])
            ->whereIn('transactions.transaction_type',['Account Payable','Payment',''])
            ->orderBy('t_date','DESC')
            ->get();
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Tax Provision & Salary Tax Ledger');
        return view('ledger.taxledger')->with('taxes', $taxes)->with('pdfhead',$pdfhead);
    }
    
    
    
    
//    Salary ledger start

    public function getSalaryLedger()
    {
        $branch_id = auth()->user()->branch_id;
            $fromDate = "";
            $toDate = "";
        $salary = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.amount','!=', 'Null'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Expense'])
            ->whereIn('accounts.account_name',['Salary','Wages'])->whereIn('transactions.transaction_type',['Current','Due','Prepaid Adjust'])->orderby('id','DESC')
            ->get();
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Salary Ledger');
        return view('ledger.salaryledger')->with('salary', $salary)->with('pdfhead',$pdfhead);
    }


    public function getSalaryLedgerSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;
        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');

        $salary = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.t_date', '>=', $fromDate],
                ['transactions.t_date', '<=', $toDate],
                ['transactions.amount','!=', 'Null'],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Expense'])
            ->whereIn('accounts.account_name',['Salary','Wages'])->whereIn('transactions.transaction_type',['Current','Due','Prepaid Adjust'])->orderby('id','DESC')
            ->get();
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Salary Ledger');
        return view('ledger.salaryledger')->with('salary', $salary)->with('pdfhead',$pdfhead);
    }


//    Salary ledger end


    
    
    
    

    public function vatReceive()
    {

        $branch_id = auth()->user()->branch_id;
        $fromDate = "";
        $toDate = "";
        $vatreceivable = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Vat Receivable'])
            ->select('t_date', 'description', 'at_amount as amount','ref','transaction_type','table_type','payment_type');

        $vatreceipt=DB::table('transactions')->where([
            ['table_type','=', 'Expense'],
            ['t_amount','!=', '0.00'],
            ['branch_id','=', $branch_id]
        ])->whereNull('employee_id')
            ->select('t_date','description','t_amount as amount','ref','transaction_type','table_type','payment_type')
            ->union($vatreceivable)
            ->orderBy('t_date','DESC')->get();
            
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Vat Receivable Ledger');
        return view('ledger.receivablevat')->with('taxes', $vatreceipt)->with('pdfhead',$pdfhead);
    }
    
    
     public function ReceivableVatSearch(Request $request)
        {
            $branch_id = auth()->user()->branch_id;
    
    
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            
            
          $vatreceivable = DB::table('accounts')
                ->select('transactions.*','accounts.account_type','accounts.account_name')
                ->join('transactions','transactions.account_id','=','accounts.id')
                ->where([
                    ['transactions.t_date', '>=', $fromDate],
                    ['transactions.t_date', '<=', $toDate],
                    ['transactions.branch_id', '=', $branch_id]
                ])->whereIn('accounts.account_type',['Vat Receivable'])
                ->select('t_date', 'description', 'at_amount as amount','ref','transaction_type','table_type','payment_type');


        $vatreceipt=DB::table('transactions')->where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['table_type','=', 'Expense'],
            ['t_amount','!=', '0.00'],
            ['branch_id','=', $branch_id]
        ])
            ->select('t_date','description','t_amount as amount','ref','transaction_type','table_type','payment_type')
            ->union($vatreceivable)
            ->orderBy('t_date','DESC')->get();
            
            $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Vat Receivable Ledger');
            return view('ledger.receivablevat')->with('taxes', $vatreceipt)->with('pdfhead',$pdfhead);
        }
        
        
        public function getOwnerEquity()
    {
        $branch_id = auth()->user()->branch_id;
        $oe = Account::where('branch_id', $branch_id)->whereIn('account_type',['Owner Equity','Dividend'])->get();

        return view('ledger.oeledger')
            ->with('oe', $oe);
    }


    public function getOwnerEquityLedger($id)
    {
        $id = $id;
            $fromDate = "";
            $toDate = "";
        $branch_id = auth()->user()->branch_id;

        $oe = Transaction::where([
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();


        $accountnames = Account::where('id', "$id")->get();
        
        foreach ($accountnames as $accountname){
                $accountname->account_name;
            
        }
        $title = $accountname->account_name.' Ledger';                   
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);        
        return view('ledger.ownerequityledger')
            ->with('accountnames', $accountnames)
            ->with('oe', $oe)
            ->with('id', $id)->with('pdfhead',$pdfhead);
    }

    public function getOwnerEquitySearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;

        $id   = $request->input('hiddensearchid');

        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');

        $oe = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->orderBy('t_date','DESC')->get();
        
        $accountnames = Account::where('id', "$id")->get();

        foreach ($accountnames as $accountname){
                $accountname->account_name;
            
        }
        $title = $accountname->account_name.' Ledger';   

        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);

        return view('ledger.ownerequityledger')
            ->with('oe', $oe)->with('id', $id)->with('accountnames', $accountnames)->with('pdfhead',$pdfhead);
    }
    
    
    public function getSharePremiumLedger()
    {
        $branch_id = auth()->user()->branch_id;
            $fromDate = "";
            $toDate = "";
        $share = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Owner Equity'])
            ->whereIn('accounts.account_name',['Capital Reserve','Share Premium'])
            ->orderBy('t_date','DESC')
            ->get();
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Share Premium and Capital Reserve Ledger');
        return view('ledger.sharepremiumledger')->with('share', $share)->with('pdfhead',$pdfhead);
    }


    public function getSharePremiumSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;
        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');

        $share = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.t_date', '>=', $fromDate],
                ['transactions.t_date', '<=', $toDate],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Owner Equity'])
            ->whereIn('accounts.account_name',['Capital Reserve','Share Premium'])
            ->orderBy('t_date','DESC')
            ->get();
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Share Premium and Capital Reserve Ledger');
        return view('ledger.sharepremiumledger')->with('share', $share)->with('pdfhead',$pdfhead);
    }
    
    
    
    
    
    
    
    

    public function vatpayable()
    {

        $branch_id = auth()->user()->branch_id;
        $fromDate = "";
        $toDate = "";
        
        $vatpayable = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Vat Payable'])
            ->select('t_date', 'description', 'at_amount as amount','ref','transaction_type','table_type','payment_type');

        $taxes=DB::table('transactions')->where([
            ['table_type','=', 'Income'],
            ['t_amount','!=', '0.00'],
            ['branch_id','=', $branch_id]
        ])
            ->select('t_date','description','t_amount as amount','ref','transaction_type','table_type','payment_type')
            ->union($vatpayable)
            ->orderBy('t_date','DESC')->get();
            $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Vat Payable Ledger');
            return view('ledger.payablevat')->with('taxes', $taxes)->with('pdfhead',$pdfhead);
    }

public function PayableVatSearch(Request $request)
        {
            $branch_id = auth()->user()->branch_id;
    
    
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
            
            
            $vatpayable = DB::table('accounts')
            ->select('transactions.*','accounts.account_type','accounts.account_name')
            ->join('transactions','transactions.account_id','=','accounts.id')
            ->where([
                ['transactions.t_date', '>=', $fromDate],
                ['transactions.t_date', '<=', $toDate],
                ['transactions.branch_id', '=', $branch_id]
            ])->whereIn('accounts.account_type',['Vat Payable'])
            ->select('t_date', 'description', 'at_amount as amount','ref','transaction_type','table_type','payment_type');
      


        $taxes=DB::table('transactions')->where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['table_type','=', 'Income'],
            ['t_amount','!=', '0.00'],
            ['branch_id','=', $branch_id]
        ])
            ->select('t_date','description','t_amount as amount','ref','transaction_type','table_type','payment_type')
            ->union($vatpayable)
            ->orderBy('t_date','DESC')->get();
            
            
            $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Vat Payable Ledger');
            return view('ledger.payablevat')->with('taxes', $taxes)->with('pdfhead',$pdfhead);
        }








}
