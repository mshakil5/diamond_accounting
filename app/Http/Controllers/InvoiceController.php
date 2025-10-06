<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\InvoiceDetail;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Invoice::with(['details'])->latest();
            $filter = $request->status_filter;
            $invoices = $query->get();
            return DataTables::of($invoices)
                ->addIndexColumn()
                ->addColumn('date', fn($row) => date('d-m-Y', strtotime($row->invoice_date)))
                ->addColumn('bill_to', function($row) {
                    $billTo = strip_tags($row->bill_to, '<br>');
                    return nl2br($billTo);
                })
                ->addColumn('action', function($row) {
                    $dropdown = '<div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="'.route('invoices.show', $row->id).'" class="dropdown-item view" target="_blank">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        <a href="'.route('invoices.download', $row->id).'" class="dropdown-item download" target="_blank">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>';
                    return $dropdown;
                })
                ->rawColumns(['action', 'bill_to']) 
                ->make(true);

        }

        $branch_id = auth()->user()->branch_id;

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

    public function store(Request $request)
    {
        $request->validate([
            'invoice_date' => 'required|date',
            'invoice_for' => 'required|string|max:255',
            'bank_information' => 'nullable|string',
            'vat_percent' => 'nullable|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'subtotal' => 'nullable|numeric|min:0',
            'net_amount' => 'nullable|numeric|min:0',
            'description.*' => 'nullable|string',
            'period.*' => 'nullable|string',
            'price.*' => 'nullable|numeric|min:0',
        ]);


        try {
            // 🔹 Auto-generate Invoice Number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            // 🔹 Create main invoice record
            $invoice = Invoice::create([
                'invoice_number'   => $request->invoice_number,
                'invoice_date'     => $request->invoice_date,
                'invoice_for'      => $request->invoice_for,
                'branch_id'        => auth()->user()->branch_id,
                'bill_to'          => $request->bill_to ?? 0,
                'vat_amount'       => $request->vat_amount ?? 0,
                'subtotal'         => $request->subtotal ?? 0,
                'discount_percent' => $request->discount_percent ?? 0,
                'discount_amount'  => $request->discount_amount ?? 0,
                'net_amount'       => $request->net_amount ?? 0,
                'description'      => $request->bank_information,
                'created_by'       => auth()->id(),
                'status'           => 1, // Pending by default
            ]);

            // 🔹 Create invoice details
            if ($request->has('description')) {
                foreach ($request->description as $index => $desc) {
                    $period = $request->period[$index] ?? '';
                    $unitPrice = $request->price[$index] ?? 0;
                    $vatPercent = $invoice->vat_percent ?? 0;
                    $vatAmount = ($unitPrice * $vatPercent) / 100;
                    $totalWithVat = $unitPrice + $vatAmount;

                    InvoiceDetail::create([
                        'invoice_id'    => $invoice->id,
                        'description'   => $desc,
                        'period'        => $period,
                        'unit_price'    => $request->price[$index] ?? 0,
                        'total_inc_vat' => $request->price[$index] ?? 0,
                        'status'        => 1,
                        'created_by'    => auth()->id(),
                    ]);
                }
            }


            // 🔹 Return success with PDF link (optional)
            return response()->json([
                'message'  => '<div class="alert alert-success">Invoice created successfully!</div>',
                'redirect' => route('invoices.show', $invoice->id) // or PDF view link
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => '<div class="alert alert-danger">Something went wrong: ' . $e->getMessage() . '</div>'
            ], 500);
        }

    }

    public function show($id)
    {
        $invoice = Invoice::with(['details'])->findOrFail($id);

        $paidImagePath = public_path('paidbg.png');
        if (file_exists($paidImagePath)) {
            $paidImageData = base64_encode(file_get_contents($paidImagePath));
            $paidImageBase64 = 'data:image/png;base64,' . $paidImageData;
        } else {
            $paidImageBase64 = null;
        }


        return view('invoices.show', compact('invoice','paidImageBase64'));
    }

    // invoice download as pdf
    public function invoiceDownload($id)
    {
        $invoice = Invoice::with(['details'])->findOrFail($id);

        $pdf = \PDF::loadView('invoices.show', compact('invoice'));
        $filename = 'Invoice_' . $invoice->invoice_number . '.pdf';
        return $pdf->download($filename);
    }


}
