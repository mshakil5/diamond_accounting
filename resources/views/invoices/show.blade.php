<!DOCTYPE html>
<html lang="en">
<head>
    @php
        use Carbon\Carbon;
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Diamonds Group - Invoice</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        * {
            -webkit-print-color-adjust: exact;
        }
        .invoice-body {
            margin: 0 auto;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .no-print { display: none; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <section class="invoice">
        <div class="invoice-body">

            <br><br><br><br>

            {{-- ===== HEADER: Logo + Invoice Title ===== --}}
            <table>
                <tbody>
                    <tr>
                        <td style="width:50%;">
                            <img src="{{ $logoBase64 }}" width="120px" style="display:inline-block;" />
                        </td>
                        <td style="width:50%; text-align: right;">
                            <h1 style="font-size: 30px; color:blue; margin: 0;">INVOICE</h1>
                        </td>
                    </tr>
                </tbody>
            </table>

            <br><br><br><br>

            {{-- ===== BILL TO + DATE ===== --}}
            <table>
                <tbody>
                    <tr>
                        <td style="width:40%; vertical-align: top;">
                            <h5 style="font-size: 12px; text-align: left; line-height: 10px; margin: 0;">
                                Branch: {{ $invoice->branch->branch_name }} <br><br>
                                Invoice No: {{ $invoice->invoice_number }} <br><br>
                                Bill To
                            </h5>
                            {!! $invoice->bill_to !!}
                        </td>
                        <td style="width:30%;"></td>
                        <td style="width:30%; text-align: right; vertical-align: top;">
                            <p style="font-size: 12px; margin: 5px; text-align: right; line-height: 10px;">
                                Date: {{ Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <br>

            {{-- ===== MAIN TABLE: Items + Empty Space + Summary ===== --}}
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #dee2e6;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #dee2e6; text-align:center; width:10%; padding: 8px;">Serial</th>
                        <th style="border: 1px solid #dee2e6; text-align:center; padding: 8px;">Description</th>
                        <th style="border: 1px solid #dee2e6; text-align:center; width:20%; padding: 8px;">Amount</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- ===== ITEM ROWS ===== --}}
                    @foreach($invoice->details as $index => $item)
                    <tr>
                        <td style="border: 1px solid #dee2e6; text-align:center; padding: 8px;">{{ $index + 1 }}</td>
                        <td style="border: 1px solid #dee2e6; text-align:center; padding: 8px;">{!! $item->description !!}</td>
                        <td style="border: 1px solid #dee2e6; text-align:right; padding: 8px;">£{{ number_format($item->unit_price, 2) }}</td>
                    </tr>
                    @endforeach

                    {{-- ===== EMPTY SPACE ROWS (vertical borders only, no horizontal lines) ===== --}}
                    @for($i = 0; $i < 10; $i++)
                    <tr style="height: 25px;">
                        <td style="border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6; padding: 0;"></td>
                        <td style="border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6; padding: 0;"></td>
                        <td style="border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6; padding: 0;"></td>
                    </tr>
                    @endfor

                    {{-- ===== SUMMARY ROWS (at bottom of table) ===== --}}
                    <tr>
                        <td colspan="2" style="text-align:right; padding: 8px; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6;">
                            <strong>Subtotal</strong>
                        </td>
                        <td style="text-align:right; padding: 8px; border-top: 1px solid #dee2e6; border: 1px solid #dee2e6;">
                            £{{ number_format($invoice->subtotal ?? 0, 2) }}
                        </td>
                    </tr>

                    @if($invoice->discount_percent)
                    <tr>
                        <td colspan="2" style="text-align:right; padding: 8px; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6;">
                            Discount ({{ $invoice->discount_percent }}%)
                        </td>
                        <td style="text-align:right; padding: 8px; border: 1px solid #dee2e6;">
                            £{{ number_format($invoice->discount_amount ?? 0, 2) }}
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td colspan="2" style="text-align:right; padding: 8px; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6;">
                            VAT
                        </td>
                        <td style="text-align:right; padding: 8px; border: 1px solid #dee2e6;">
                            £{{ number_format($invoice->vat_amount ?? 0, 2) }}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="text-align:right; padding: 8px; background-color: #f8f9fa; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                            <strong>Total</strong>
                        </td>
                        <td style="text-align:right; padding: 8px; background-color: #f8f9fa; border: 1px solid #dee2e6;">
                            <strong>£{{ number_format($invoice->net_amount ?? 0, 2) }}</strong>
                        </td>
                    </tr>

                </tbody>
            </table>

            <br><br>

            {{-- ===== PAID STAMP ===== --}}
            @if ($invoice->status == 2)
            <table>
                <tr>
                    <td style="width:60%;"></td>
                    <td style="width:40%; text-align:right; vertical-align: top;">
                        <img src="{{ $paidImageBase64 }}" width="120px" />
                    </td>
                </tr>
            </table>
            <br>
            @endif

            {{-- ===== SIGNATURE SECTION (fixed at page bottom, no boxes) ===== --}}
            <div style="position: absolute; bottom: 40px; width: 100%; padding: 0 40px; box-sizing: border-box;">
                <table style="width: 100%; border: none; border-collapse: collapse;">
                    <tr>
                        <td style="width: 45%; text-align: left; border: none; vertical-align: top; padding: 0;">
                            <p style="margin: 0 0 8px 0; font-weight: bold; font-size: 12px;">Authorised by</p>
                            <p style="margin: 0; font-size: 10px;">Date: ________________</p>
                        </td>
                        <td style="width: 10%; border: none;"></td>
                        <td style="width: 45%; text-align: right; border: none; vertical-align: top; padding: 0; padding-right: 40px;">
                            <p style="margin: 0 0 8px 0; font-weight: bold; font-size: 12px;">Receiver Sign</p>
                            <p style="margin: 0; font-size: 10px;">Date: ________________</p>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- ===== FOOTER (hidden) ===== --}}
            <div style="display: none; position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); max-width: 794px; width: 100%; padding: 10px 20px; border-top: 1px solid #ddd;">
                <table>
                    <tbody>
                        <tr>
                            <td style="width: 50%; text-align:left;">
                                39 Monkgate, York, YO31 7PB, Mob: 07340631122
                            </td>
                            <td style="width: 50%; text-align:right;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
    </script>
</body>
</html>