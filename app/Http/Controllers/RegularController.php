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
        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');

        // Base query
        $query = Regular::where('branch_id', $branch_id);

        if (!empty($fromDate) && !empty($toDate)) {
            $query->whereBetween('date', [$fromDate, $toDate]);
        }

        // ✅ Single query to get ALL sums at once
        $sums = (clone $query)->selectRaw("
            COALESCE(SUM(cash),0)          AS cash,
            COALESCE(SUM(bank),0)          AS bank,
            COALESCE(SUM(eviivo),0)        AS eviivo,
            COALESCE(SUM(parking_cash),0)  AS parking_cash,
            COALESCE(SUM(parking_card),0)  AS parking_card,
            COALESCE(SUM(other_sales),0)   AS other_sales,
            COALESCE(SUM(returnamount),0)  AS returnamount,
            COALESCE(SUM(advance_sales),0) AS advance_sales
        ")->first();

        $cash           = $sums->cash;
        $bank           = $sums->bank;
        $eviivo         = $sums->eviivo;
        $parking_cash   = $sums->parking_cash;
        $parking_card   = $sums->parking_card;
        $other_sales    = $sums->other_sales;
        $returnamount   = $sums->returnamount;
        $advance_sales  = $sums->advance_sales;

        // ✅ Use pagination instead of get() — much faster for large datasets
        $data = (clone $query)
                    ->orderBy('date', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->paginate(50);

        $pdfhead = [
            'fromDate' => $fromDate ?? '',
            'toDate'   => $toDate ?? '',
            'title'    => 'Daily Sales',
        ];

        return view('regular.create')
            ->with(compact(
                'advance_sales','returnamount','other_sales','parking_card',
                'parking_cash','eviivo','bank','cash','data','pdfhead'
            ));
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
