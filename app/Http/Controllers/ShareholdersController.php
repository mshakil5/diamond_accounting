<?php

namespace App\Http\Controllers;

use App\Models\Shareholder;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ShareholdersController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $branch_id = auth()->user()->branch_id;
        $data = Shareholder::where('branch_id', $branch_id)->paginate(10);
        return view('shareholder.create')->with('data', $data);
    }


    public function create()
    {
        return view('shareholder.create');
    }

    public function store(Request $request)
    {
        if(empty($request->name)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Name\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->email)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Email\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->phone)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Phone\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        
        
        try{
            $staff = new Shareholder();
            $staff->name = $request->name;
            $staff->email = $request->email;
            $staff->phone = $request->phone;
            $staff->branch_id = auth()->user()->branch_id;
            $staff->created_by = auth()->user()->name;
            $staff->save();

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Shareholder Created Successfully.</b></div>"; 

            return response()->json(['status'=> 300,'message'=>$message]);
        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function edit($id)
    {
        $branch_id = auth()->user()->branch_id;
        $where = [
            'id'=>$id,
            'branch_id'=>$branch_id
        ];
        $info = Shareholder::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        if(empty($request->name)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Name\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->email)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Email\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 
        if(empty($request->phone)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Phone\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        } 

        $branch_id = auth()->user()->branch_id;
        $where = [
            'id'=>$request->codeid,
            'branch_id'=>$branch_id
        ];
        $data = Shareholder::where($where)->get()->first();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->updated_by = auth()->user()->name;
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>"; 
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function delete($id)
    {

        $branch_id = auth()->user()->branch_id;
        $data = Transaction::where([
            ['shareholder_id','=', $id],
            ['branch_id','=', $branch_id]
        ])->count();


        if($data>0){
            return response()->json(['success'=>true,'message'=>'Sorry! Already exits other transaction delete first then try']);
        }
        elseif(Shareholder::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Data has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }

    }


    public function getShareholderList()
    {
        $branch_id = auth()->user()->branch_id;
        $data = Shareholder::where('branch_id', $branch_id)->get();
        return view('ledger.shareholder')->with('data', $data);
    }

    public function getShareholderCapitalLedger($id)
    {
        $id = $id;
            $fromDate = "";
            $toDate = "";        
        $branch_id = auth()->user()->branch_id;
        $shareholder = Shareholder::where('id', $id)->first();

        $data = Transaction::where([
            ['shareholder_id','=', $id],
            ['branch_id','=', $branch_id]
        ])
        ->whereHas('account', function ($query) {
            $query->whereIn('account_name',['Withdraw','Capital']);
        })
        ->whereIn('transaction_type',['Receive','Payment'])->orderBy('created_at','DESC')
        ->get();


        
        $title =  $shareholder->name.' Capital Ledger (Shareholder)';
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);           
        
        return view('ledger.shareholdercapitalledger')
        ->with('data', $data)
        ->with('id', $id)
        ->with('shareholder', $shareholder)->with('pdfhead',$pdfhead);
    }


    public function getShareholderCapitalLedgerSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;


        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('shareholderid');
        $shareholder = Shareholder::where('id', $id)->first();
        
        $data = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['shareholder_id','=', $id],
            ['branch_id','=', $branch_id]
        ])
        ->whereHas('account', function ($query) {
            $query->whereIn('account_name',['Withdraw','Capital']);
        })
        ->whereIn('transaction_type',['Receive','Payment'])->orderBy('created_at','DESC')
        ->get();

        
        $title =  $shareholder->name.' Capital Ledger (Shareholder)';
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);  

        return view('ledger.shareholdercapitalledger')
            ->with('shareholder', $shareholder)
            ->with('id', $id)
            ->with('data', $data)->with('pdfhead',$pdfhead)->with('title',$title);
    }
    

    public function getShareholderListDividendLedger($id)
    {
        $id = $id;     
        
            $fromDate = "";
            $toDate = "";        
        $branch_id = auth()->user()->branch_id;
        $shareholder = Shareholder::where('id', $id)->first();

        $data = Transaction::where([
                            ['shareholder_id','=', $id],
                            ['branch_id','=', $branch_id]
                        ])
                        ->whereHas('account', function ($query) {
                            $query->where('account_type', 'Dividend');
                        })
                        ->whereIn('transaction_type',['Payable','Payment'])->orderBy('created_at','DESC')
                        ->get();


        $title =  $shareholder->name.' Dividend Ledger (Shareholder)';
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);   
        return view('ledger.shareholderdividendledger')
        ->with('data', $data)
        ->with('id', $id)
        ->with('shareholder', $shareholder)->with('pdfhead',$pdfhead);
    }

    public function getShareholderListDividendLedgerSearch(Request $request)
    {
        $branch_id = auth()->user()->branch_id;
        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $id   = $request->input('shareholderid');
        $shareholder = Shareholder::where('id', $id)->first();
        
        $data = Transaction::where([
            ['t_date', '>=', $fromDate],
            ['t_date', '<=', $toDate],
            ['shareholder_id','=', $id],
            ['branch_id','=', $branch_id]
        ])
        ->whereHas('account', function ($query) {
            $query->where('account_type', 'Dividend');
        })
        ->whereIn('transaction_type',['Payable','Payment'])->orderBy('created_at','DESC')
        ->get();

        
        $title =  $shareholder->name.' Dividend Ledger (Shareholder)';
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>$title);  

        return view('ledger.shareholderdividendledger')
            ->with('shareholder', $shareholder)
            ->with('id', $id)
            ->with('data', $data)->with('pdfhead',$pdfhead)->with('title',$title);
    }



}
