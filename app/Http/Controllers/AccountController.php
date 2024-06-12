<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
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

    


    public function index()
    {
        $branch_id = auth()->user()->branch_id;
        $accounts = Account::where('branch_id', $branch_id)->get();
        return view('account.create')->with('accounts',$accounts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('account.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(empty($request->account_type)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Account Type\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->account_name)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Account Name\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->account_desc)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Account Description\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        
        try{
            $account = new Account();
            $account->account_type = $request->account_type;
            $account->account_name = $request->account_name;
            $account->account_desc = $request->account_desc;
            $account->branch_id = auth()->user()->branch_id;
            $account->user_type = auth()->user()->user_type;
            $account->created_by = auth()->user()->name;
            $account->updated_ip = "";
            $account->created_ip = request()->ip();
            $account->save();
            
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Account Created Successfully.</b></div>"; 

           return response()->json(['status'=> 300,'message'=>$message]);
        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $branch_id = auth()->user()->branch_id;
        $where = [
            'id'=>$id,
            'branch_id'=>$branch_id
        ];
        $info = Account::where($where)->get()->first();
        return response()->json($info);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        if(empty($request->account_type)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Account Type\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->account_name)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Account Name\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->account_desc)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Account Description\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        
        $branch_id = auth()->user()->branch_id;
        $where = [
            'id'=>$account->id,
            'branch_id'=>$branch_id
        ];
        $accounttoupdate = Account::where($where)->get()->first();
        $accounttoupdate->account_type = $request->account_type;
        $accounttoupdate->account_name = $request->account_name;
        $accounttoupdate->account_desc = $request->account_desc;
        $accounttoupdate->updated_by = auth()->user()->name;
        $accounttoupdate->updated_ip = request()->ip();
        if ($accounttoupdate->save()) {
           $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Account Updated Successfully.</b></div>";
        
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
public function deleteaccount($id)
    {

        $branch_id = auth()->user()->branch_id;
        $data = Transaction::where([
            ['account_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->count();

        if($data>0){
            return response()->json(['success'=>true,'message'=>'Sorry! Already exits other transaction under this account delete first then try']);
        }
        elseif(Account::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Account has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }

    }
}
