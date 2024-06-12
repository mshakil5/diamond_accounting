<?php

namespace App\Http\Controllers\Users\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
Use Redirect;

class AdminController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:admin');
    }

    public function index()
    {
        if (Auth::check()) {
            return Redirect::route('dashboard');
        }
           
        return Redirect::route('admin.login');
        return view('admin.dashboard');
    }
}