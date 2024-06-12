<?php

namespace App\Http\Controllers\Auth;
use App\Models\Branch;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm()
    {
        $branch = Branch::all();
        return view('auth.admin-login')->with('branch',$branch);
    }

    public function login(Request $request)
    {
        // Validate form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);

        // $check = Admin::where($where)->get()->first('user_type');
        $check = Admin::where('email', $request->email)->value('user_type');

        if($check == 11){

            if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember))
            {
                return redirect()->intended(route('admin.dashboard'));
            }

        }else{

        // Attempt to log the user in
        if(Auth::guard('admin')->attempt(['branch_id' => $request->branch_id,'email' => $request->email, 'password' => $request->password], $request->remember))
        {
            return redirect()->intended(route('admin.dashboard'));
        }
    }
        // if unsuccessful
        $errors = 'Provided credentials is not corect';
        return redirect()->back()->withErrors($errors)->withInput($request->only('email','remember'));
    }
}