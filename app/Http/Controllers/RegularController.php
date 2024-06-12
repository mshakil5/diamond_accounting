<?php

namespace App\Http\Controllers;

use App\Models\Regular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegularController extends Controller
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



        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $branch_id = auth()->user()->branch_id;
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $cash = DB::table('regulars')->where([
                ['branch_id','=', $branch_id],
                ['date', '>=', $fromDate],
                ['date', '<=', $toDate],
            ])->sum('regulars.cash');

            $bank = DB::table('regulars')->where([
                ['branch_id','=', $branch_id],
                ['date', '>=', $fromDate],
                ['date', '<=', $toDate],
            ])->sum('regulars.bank');
    
            $eviivo = DB::table('regulars')->where([
                ['branch_id','=', $branch_id],
                ['date', '>=', $fromDate],
                ['date', '<=', $toDate],
            ])->sum('regulars.eviivo');
                
            $parking_cash = DB::table('regulars')->where([
                ['branch_id','=', $branch_id],
                ['date', '>=', $fromDate],
                ['date', '<=', $toDate],
            ])->sum('regulars.parking_cash');
                
            $parking_card = DB::table('regulars')->where([
                ['branch_id','=', $branch_id],
                ['date', '>=', $fromDate],
                ['date', '<=', $toDate],
            ])->sum('regulars.parking_card');
                
            $other_sales = DB::table('regulars')->where([
                ['branch_id','=', $branch_id],
                ['date', '>=', $fromDate],
                ['date', '<=', $toDate],
            ])->sum('regulars.other_sales');
                
            $returnamount = DB::table('regulars')->where([
                ['branch_id','=', $branch_id],
                ['date', '>=', $fromDate],
                ['date', '<=', $toDate],
            ])->sum('regulars.returnamount');
                
            $advance_sales = DB::table('regulars')->where([
                ['branch_id','=', $branch_id],
                ['date', '>=', $fromDate],
                ['date', '<=', $toDate],
            ])->sum('regulars.advance_sales');
                
            $data = Regular::where([
                ['date', '>=', $fromDate],
                ['date', '<=', $toDate],
                ['branch_id', '=', $branch_id],
            ])->orderBy('date','DESC')->orderBy('id','DESC')->get();
            
            
        }else{
            
             $branch_id = auth()->user()->branch_id;
            
            $fromDate = "";
            $toDate = "";
            
            $cash = DB::table('regulars')->where('branch_id', $branch_id)->sum('regulars.cash');
                
            $bank = DB::table('regulars')->where('branch_id', $branch_id)->sum('regulars.bank');
                
            $eviivo = DB::table('regulars')->where('branch_id', $branch_id)->sum('regulars.eviivo');
                
            $parking_cash = DB::table('regulars')->where('branch_id', $branch_id)->sum('regulars.parking_cash');
                
            $parking_card = DB::table('regulars')->where('branch_id', $branch_id)->sum('regulars.parking_card');
                
            $other_sales = DB::table('regulars')->where('branch_id', $branch_id)->sum('regulars.other_sales');
                
            $returnamount = DB::table('regulars')->where('branch_id', $branch_id)->sum('regulars.returnamount');
                
            $advance_sales = DB::table('regulars')->where('branch_id', $branch_id)->sum('regulars.advance_sales');
                
            $data = Regular::where('branch_id', $branch_id)->orderBy('date','DESC')->orderBy('id','DESC')->get();
        }
        
        $pdfhead = Array('fromDate'=> $fromDate,'toDate'=> $toDate,'title'=>'Regular Sales');
        
        return view('regular.create')
            ->with('advance_sales',$advance_sales)
            ->with('returnamount',$returnamount)
            ->with('other_sales',$other_sales)
            ->with('parking_card',$parking_card)
            ->with('parking_cash',$parking_cash)
            ->with('eviivo',$eviivo)
            ->with('bank',$bank)
            ->with('cash',$cash)
            ->with('data',$data)
            ->with('pdfhead',$pdfhead);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('regular.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        try{
            $account = new Regular();
            $account->name = $request->name;
            $account->agt = $request->agt;
            $account->ref = $request->ref;
            $account->orderno = $request->orderno;
            $account->cash = $request->cash;
            $account->bank = $request->bank;
            $account->eviivo = $request->eviivo;
            $account->parking_cash = $request->parking_cash;
            $account->parking_card = $request->parking_card;
            $account->other_sales = $request->other_sales;
            $account->returnamount = $request->returnamount;
            $account->advance_sales = $request->advance_sales;
            $account->remark = $request->remark;
            $account->date = $request->date;
            $account->branch_id = auth()->user()->branch_id;
            $account->user_type = auth()->user()->user_type;
            $account->updated_by = "";
            $account->created_by = auth()->user()->name;
            $account->save();


            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Regular Created Successfully.</b></div>";

            return response()->json(['status'=> 300,'message'=>$message]);
        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Regular  $regular
     * @return \Illuminate\Http\Response
     */
    public function show(Regular $regular)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Regular  $regular
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $branch_id = auth()->user()->branch_id;
        $where = [
            'id'=>$id,
            'branch_id'=>$branch_id
        ];
        $info = Regular::where($where)->get()->first();
//        dd($info);
        return response()->json($info);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Regular  $regular
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Regular $regular)
    {
       

        $branch_id = auth()->user()->branch_id;
        $where = [
            'id'=>$regular->id,
            'branch_id'=>$branch_id
        ];
        $regulartoupdate = Regular::where($where)->get()->first();
        $regulartoupdate->name = $request->name;
        $regulartoupdate->agt = $request->agt;
        $regulartoupdate->ref = $request->ref;
        $regulartoupdate->orderno = $request->orderno;
        $regulartoupdate->cash = $request->cash;
        $regulartoupdate->bank = $request->bank;
        $regulartoupdate->eviivo = $request->eviivo;
        $regulartoupdate->parking_cash = $request->parking_cash;
        $regulartoupdate->parking_card = $request->parking_card;
        $regulartoupdate->other_sales = $request->other_sales;
        $regulartoupdate->returnamount = $request->returnamount;
        $regulartoupdate->advance_sales = $request->advance_sales;
        $regulartoupdate->remark = $request->remark;
        $regulartoupdate->date = $request->date;
        if ($regulartoupdate->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Regular Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Regular  $regular
     * @return \Illuminate\Http\Response
     */
    public function destroy(Regular $regular)
    {
        if(Regular::destroy($regular->id)){
            return response()->json(['success'=>true,'message'=>'Listing Deleted']);
        }
        else{
            return response()->json(['success'=>false,'message'=>'Update Failed']);
        }
    }
}
