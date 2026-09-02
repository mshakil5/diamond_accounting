<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * List addresses for current branch.
     */
    public function index()
    {
        $data = Address::forCurrentBranch()
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('address.create')->with('data', $data);
    }

    /**
     * Store a new address.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'               => 'nullable|string|max:255',
            'address_first_line'  => 'nullable|string|max:255',
            'address_second_line' => 'nullable|string|max:255',
            'address_third_line'  => 'nullable|string|max:255',
            'town'                => 'nullable|string|max:255',
            'postcode'            => 'nullable|string|max:50',
            'status'              => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $address = new Address();
            $address->fill($request->only([
                'title',
                'address_first_line',
                'address_second_line',
                'address_third_line',
                'town',
                'postcode',
                'latitude',       
                'longitude',      
                'allowed_radius', 
            ]));
            $address->status     = $request->status ?? 1;
            $address->branch_id   = auth()->user()->branch_id;
            $address->created_by  = auth()->user()->name;
            $address->save();

            return $this->successResponse('Address Created Successfully.');
        } catch (\Exception $e) {
            return response()->json(['status' => 303, 'message' => 'Server Error!!']);
        }
    }

    /**
     * Return single address as JSON for edit modal.
     */
    public function edit($id)
    {
        $info = Address::forCurrentBranch()->where('id', $id)->first();
        return response()->json($info);
    }

    /**
     * Update existing address.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'               => 'nullable|string|max:255',
            'address_first_line'  => 'nullable|string|max:255',
            'address_second_line' => 'nullable|string|max:255',
            'address_third_line'  => 'nullable|string|max:255',
            'town'                => 'nullable|string|max:255',
            'postcode'            => 'nullable|string|max:50',
            'status'              => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $address = Address::forCurrentBranch()->where('id', $request->codeid)->first();

        if (!$address) {
            return response()->json([
                'status'  => 303,
                'message' => $this->alert('warning', 'Address not found.'),
            ]);
        }

        $address->fill($request->only([
            'title',
            'address_first_line',
            'address_second_line',
            'address_third_line',
            'town',
            'postcode',
            'latitude',      
            'longitude',     
            'allowed_radius',
        ]));
        $address->status     = $request->status ?? $address->status;
        $address->updated_by  = auth()->user()->name;

        if ($address->save()) {
            return $this->successResponse('Data Updated Successfully.');
        }

        return response()->json(['status' => 303, 'message' => 'Server Error!!']);
    }

    /**
     * Delete an address.
     */
    public function delete($id)
    {
        $deleted = Address::forCurrentBranch()->where('id', $id)->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Data has been deleted successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Delete Failed',
        ]);
    }

    /* ------------------- Helpers ------------------- */

    private function alert(string $type, string $msg): string
    {
        return "<div class='alert alert-{$type}'>
                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    <b>{$msg}</b>
                </div>";
    }

    private function successResponse(string $msg)
    {
        return response()->json([
            'status'  => 300,
            'message' => $this->alert('success', $msg),
        ]);
    }

    private function validationErrorResponse($validator)
    {
        $errors = collect($validator->errors()->all())
            ->map(fn ($e) => "<b>{$e}</b>")
            ->implode('<br>');

        return response()->json([
            'status'  => 303,
            'message' => $this->alert('warning', $errors),
        ]);
    }
}