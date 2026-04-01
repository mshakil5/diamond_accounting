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
            
            // Status filter
            if ($request->has('status_filter') && $request->status_filter != '') {
                $query->where('status', $request->status_filter);
            }
            
            $invoices = $query->get();
            
            return DataTables::of($invoices)
                ->addIndexColumn()
                ->addColumn('date', fn($row) => date('d-m-Y', strtotime($row->invoice_date)))
                ->addColumn('bill_to', function($row) {
                    $billTo = strip_tags($row->bill_to, '<br>');
                    return nl2br($billTo);
                })
                ->addColumn('status', function($row) {
                    $statusBadge = '';
                    if ($row->status == 1) {
                        $statusBadge = '<span class="badge badge-warning">Unpaid</span>';
                    } elseif ($row->status == 2) {
                        $statusBadge = '<span class="badge badge-success">Paid</span>';
                    } elseif ($row->status == 3) {
                        $statusBadge = '<span class="badge badge-danger">Cancelled</span>';
                    }
                    return $statusBadge;
                })
                ->addColumn('action', function($row) {
                    $statusOptions = '';
                    if ($row->status == 1) {
                        $statusOptions = '
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0)" class="dropdown-item change-status" data-id="'.$row->id.'" data-status="2">
                                <i class="fa fa-check text-success"></i> Mark as Paid
                            </a>
                            <a href="javascript:void(0)" class="dropdown-item change-status" data-id="'.$row->id.'" data-status="3">
                                <i class="fa fa-ban text-danger"></i> Cancel
                            </a>';
                    } elseif ($row->status == 2) {
                        $statusOptions = '
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0)" class="dropdown-item change-status" data-id="'.$row->id.'" data-status="1">
                                <i class="fa fa-undo text-warning"></i> Mark as Unpaid
                            </a>
                            <a href="javascript:void(0)" class="dropdown-item change-status" data-id="'.$row->id.'" data-status="3">
                                <i class="fa fa-ban text-danger"></i> Cancel
                            </a>';
                    } elseif ($row->status == 3) {
                        $statusOptions = '
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0)" class="dropdown-item change-status" data-id="'.$row->id.'" data-status="1">
                                <i class="fa fa-undo text-warning"></i> Mark as Unpaid
                            </a>';
                    }

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
                                        '.$statusOptions.'
                                        <div class="dropdown-divider"></div>
                                        <a href="javascript:void(0)" class="dropdown-item delete-invoice" data-id="'.$row->id.'">
                                            <i class="fa fa-trash text-danger"></i> Delete
                                        </a>
                                    </div>
                                </div>';
                    return $dropdown;
                })
                ->rawColumns(['action', 'bill_to', 'status'])
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

    // Add this new method for status update
    public function updateStatus(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:1,2,3'
        ]);
        
        $invoice->status = $request->status;
        $invoice->save();
        
        $statusText = [
            1 => 'Unpaid',
            2 => 'Paid',
            3 => 'Cancelled'
        ];
        
        return response()->json([
            'success' => true,
            'message' => "Invoice status updated to {$statusText[$request->status]}"
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_date' => 'required|date',
            'invoice_for' => 'nullable|string|max:255',
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

        // Encode Logo
        $logoPath = public_path('dlogo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }



        return view('invoices.show', compact('invoice','paidImageBase64','logoBase64'));
    }

    // invoice download as pdf
    public function invoiceDownload2($id)
    {
        $invoice = Invoice::with(['details'])->findOrFail($id);

        $pdf = \PDF::loadView('invoices.show', compact('invoice'));
        $filename = 'Invoice_' . $invoice->invoice_number . '.pdf';
        return $pdf->download($filename);
    }

    public function invoiceDownload($id)
    {
        $invoice = Invoice::with(['details'])->findOrFail($id);

        // Encode Logo
        $logoPath = public_path('dlogo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        // Encode Paid Stamp
        $paidImagePath = public_path('paidbg.png');
        $paidImageBase64 = null;
        if (file_exists($paidImagePath)) {
            $paidImageBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($paidImagePath));
        }

        // Pass everything to the view
        $pdf = \PDF::loadView('invoices.show', compact('invoice', 'paidImageBase64', 'logoBase64'));
        return $pdf->download('Invoice_' . $invoice->invoice_number . '.pdf');
    }

    public function destroy($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->details()->delete();
            $invoice->delete();

            return response()->json(['success' => true, 'message' => 'Invoice deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.'], 500);
        }
    }


}
