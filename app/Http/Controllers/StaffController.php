<?php

namespace App\Http\Controllers;


use App\Models\Branch;
use App\Models\UserRole;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $roles = UserRole::all();
        $branch = Branch::all();
        $staff = Staff::All();
        return view('staff.create', compact('branch',$branch))->with('staff',$staff)->with('roles',$roles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('staff.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

//        dd($request);
//        exit;

        try{
            $staff = new Staff();
            $staff->branch_id = $request->branch_id;
            $staff->staff_name = $request->staff_name;
            $staff->staff_phone = $request->staff_phone;
            $staff->role_id = $request->role_id;
            $staff->staff_address = $request->staff_address;
            $staff->updated_by = "ABC";
            $staff->created_by = "ABC";
            $staff->save();

            return response()->json(['success'=>true,'message'=>'Staff Created!!'], 200);
        }catch (\Exception $e){
            return response()->json(['success'=>false,'message'=>'Error!!']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Staff $staff)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $where = [
//            'writer_id'=>Auth::id(),
            'id'=>$id
        ];
        $info = Branch::where($where)->get()->first();
//        dd($info);
        return response()->json($info);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Staff $staff)
    {
        $where = [
            'id'=>$staff->id
        ];
        $stafftoupdate = Staff::where($where)->get()->first();
        $stafftoupdate->branch_id = $request->branch_id;
        $stafftoupdate->staff_name = $request->staff_name;
        $stafftoupdate->staff_phone = $request->staff_phone;
        $stafftoupdate->role_id = $request->role_id;
        $stafftoupdate->staff_address = $request->staff_address;
        if ($stafftoupdate->save()) {
            return response()->json(['success'=>true,'message'=>'Listing Updated']);
        }
        else{
            return response()->json(['success'=>false,'message'=>'Update Failed']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function destroy(Staff $staff)
    {
        if(Staff::destroy($staff->id)){
            return response()->json(['success'=>true,'message'=>'Listing Deleted']);
        }
        else{
            return response()->json(['success'=>false,'message'=>'Update Failed']);
        }
    }
}
