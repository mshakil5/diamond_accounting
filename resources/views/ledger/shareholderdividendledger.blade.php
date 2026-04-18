@extends('layouts.master')

@section('content')

    <div id="contentContainer">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>{{ $shareholder->name }} Dividend Ledger </h3>

                    <form action="{{route('shareholderDividendLedgerSearch', $id)}}" method="POST">
                        @csrf
                        <br>
                        <div class="container">
                            <div class="row">
                                <div class="container-fluid">
                                    <div class="form-group row">
                                        <label for="date" class="col-form-label col-md-2">From Date</label>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" id="fromDate" name="fromDate" required/>
                                        </div>
                                        <input type="text" class="form-control" value="{{ $id }}" id="shareholderid" name="shareholderid" hidden/>
                                        <label for="date" class="col-form-label col-md-2">To Date</label>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" id="toDate" name="toDate" required/>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn" name="search" title="Search"><img src="https://img.icons8.com/android/24/000000/search.png"/></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                    </form>
                </div>

                <?php
                // Step 1: Calculate final balance (current balance)
                $finalBalance = 0;
                foreach($data as $item) {
                    if($item->transaction_type == 'Payable') {
                        $finalBalance += $item->at_amount;
                    } elseif($item->transaction_type == 'Payment') {
                        $finalBalance -= $item->at_amount;
                    }
                }
                
                // Step 2: Pre-calculate balance for each row
                $balanceList = [];
                $tempBalance = $finalBalance;
                foreach($data as $item) {
                    // Store current balance for this row
                    $balanceList[$item->id] = $tempBalance;
                    
                    // Reverse calculate to get previous balance
                    if($item->transaction_type == 'Payable') {
                        $tempBalance -= $item->at_amount;
                    } elseif($item->transaction_type == 'Payment') {
                        $tempBalance += $item->at_amount;
                    }
                }
                ?>
                
                <div class="card-body">
                    <div class="row">
                        <div class="container">

                            <div class="text-center">
                                <h1>Branch: {{ auth()->user()->branch->branch_name }}</h1>
                                <h5>Phone: {{ auth()->user()->branch->branch_phone }}</h5>
                                <p>{{ auth()->user()->branch->branch_address }}</p>
                                <?php 
                                if(isset($pdfhead["title"])){
                                    $title = $pdfhead["title"];
                                } else {
                                    $title = "Generated Report";
                                }
                                if(isset($pdfhead["fromDate"]) && $pdfhead["fromDate"] != ""){
                                    $fromDate = $pdfhead["fromDate"] . " to ";
                                } else {
                                    $fromDate = "All Data";
                                }
                                if(isset($pdfhead["toDate"])){
                                    $toDate = $pdfhead["toDate"];
                                } else {
                                    $toDate = "";
                                }
                                ?>
                                <h3>Report: {{ $title }}</h3>
                                <h3>Data: {{ $fromDate . ' ' . $toDate }}</h3>    
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="example2">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Payment Type</th>
                                            <th>Ref</th>
                                            <th>Debit</th>
                                            <th>Credit</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($data as $item)
                                        <tr>
                                            <td>{{ $item->t_date }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->payment_type }}</td>
                                            <td>{{ $item->ref }}</td>
                                            
                                            @if($item->transaction_type == 'Payment')
                                                <td>{{ number_format($item->at_amount, 2) }}</td>
                                                <td>-</td>
                                                <td><strong>{{ number_format($balanceList[$item->id], 2) }}</strong></td>
                                            @elseif($item->transaction_type == 'Payable')
                                                <td>-</td>
                                                <td>{{ number_format($item->at_amount, 2) }}</td>
                                                <td><strong>{{ number_format($balanceList[$item->id], 2) }}</strong></td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center"><h3>No data found.</h3></td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color: #f0f0f0; font-weight: bold;">
                                            <td colspan="6" class="text-right">Current Balance:</td>
                                            <td>{{ number_format($finalBalance, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#allownerequity").addClass('active');
            $("#allownerequity").addClass('is-expanded');
            $("#shareholderLedger").addClass('active');

            // Initialize DataTable WITHOUT auto-sorting
                $('#example2').DataTable({
                    pageLength: 25,
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    responsive: true,
                    columnDefs: [ { type: 'date', 'targets': [0] } ],
                    order: false,
                    dom: '<"html5buttons"B>lTfgitp',
                    buttons: [
                        {extend: 'copy'},
                        {extend: 'excel', title: title},
                        {extend: 'pdfHtml5',
                        title: 'Report',
                        orientation : 'landscape',
                            header:true,
                            customize: function ( doc ) {
                                doc.content.splice(0, 1, {
                                        text: [
                                                { text: branch_name+'\n',bold:true,fontSize:15 },
                                                { text: branch_phone+'\n',italics:true,fontSize:12 },
                                                { text: branch_address+'\n'+'\n',italics:true,fontSize:12 },
                                                { text: data+'\n',bold:true,fontSize:12 },
                                                { text: title+'\n',bold:true,fontSize:15 }

                                        ],
                                        margin: [0, 0, 0, 12],
                                        alignment: 'center'
                                    });
                                doc.defaultStyle.alignment = 'center'
                            } 
                        },
                        {extend: 'print',
                        title: "<p style='text-align:center;'>"+branch_name+"<br>"+branch_phone+"<br>"+branch_address+"<br>"+data+"<br>"+title+"</p>",
                        header:true,
                            customize: function (win){
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');
                            $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                        }
                        }
                    ]
                });
        });
    </script>
@endsection