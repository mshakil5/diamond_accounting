<?php

namespace App\Http\Controllers\Auth;

use App\Models\Branch;
use App\Models\Admin;
use App\Models\Address; 
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
        return view('auth.admin-login')->with('branch', $branch);
    }

    // Helper function to calculate distance between two lat/long coordinates in meters
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) 
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            
            // Return distance in meters (1 mile = 1609.34 meters)
            return ($miles * 1609.34); 
        }
    }

        public function login(Request $request)
    {
        // 1. Validate form data including lat/long and branch_id
        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required|min:5',
            'branch_id' => 'required', // Make sure branch is required for location check
            'latitude'  => 'required',
            'longitude' => 'required'
        ]);

        $userLat = $request->latitude;
        $userLng = $request->longitude;

        // --- LOCATION CHECK FOR ALL USERS ---
        
        // Find the Address for the selected Branch
        $address = Address::where('branch_id', $request->branch_id)->latest()->first();

        if ($address) {
            // Calculate distance between user's location and the Branch's address location
            $distance = $this->calculateDistance($userLat, $userLng, $address->latitude, $address->longitude);

            // Check if user is outside the allowed radius (allowed_radius is in meters)
            if ($distance > $address->allowed_radius) {
                $errors = 'Access Denied: You are not within the allowed location area to login.';
                return redirect()->back()->withErrors($errors)->withInput($request->only('email', 'remember'));
            }
        } else {
            // If no address is found for this branch in the database, deny access for safety
            $errors = 'No allowed location is set up for this branch.';
            return redirect()->back()->withErrors($errors)->withInput($request->only('email', 'remember'));
        }
        
        // --- END LOCATION CHECK ---

        // Get user type
        $check = Admin::where('email', $request->email)->value('user_type');

        // 2. Proceed to Authentication if location check passed
        if ($check == 11) {
            // Super Admin Login
            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
                return redirect()->intended(route('admin.dashboard'));
            }
        } else {
            // Regular Employee Login
            if (Auth::guard('admin')->attempt(['branch_id' => $request->branch_id, 'email' => $request->email, 'password' => $request->password], $request->remember)) {
                return redirect()->intended(route('admin.dashboard'));
            }
        }

        // if unsuccessful
        $errors = 'Provided credentials are not correct';
        return redirect()->back()->withErrors($errors)->withInput($request->only('email', 'remember'));
    }
}