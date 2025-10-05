<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $branch_id = auth()->user()->branch_id;
        // dd($branch_id);

        $prefix = 'D' . date('Ym');

        $latest = Invoice::orderBy('id', 'desc')->first();

        if ($latest) {
            $lastNumber = (int) Str::after($latest->invoice_number, '-');
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $invoiceNumber = $prefix . '-' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

        return view('invoices.create', compact('invoiceNumber'));
    }
}
