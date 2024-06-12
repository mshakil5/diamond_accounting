{{--<h1>Employee Ledger Called</h1>--}}
@extends('layouts.master')



@section('content')

    <div id="contentContainer">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3> @foreach ($accountnames as $accountname)
                               Advance {{ $accountname->account_name }} Ledger
                            @endforeach</h3>

                        <form  action="{{route('advIncomeExpense_ledger_search')}}" method ="POST">
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
                                            <input type="text" class="form-control" value="{{ $id }}" id="hiddensearchid" name="hiddensearchid"  hidden/>
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
                    
              <!--get last balance       -->
                    <?php
                    $tbalance = 0;
                    ?>
                    @forelse ($ledgers as $ledgerbl)
                    
                            @if((($ledgerbl->table_type == 'Expense') && ($ledgerbl->transaction_type == 'Prepaid')) || (($ledgerbl->table_type == 'Income') && ($ledgerbl->transaction_type == 'Advance Adjust' )))
                                    
                                    <?php $tbalance = $tbalance + $ledgerbl->at_amount;?>

                            @elseif((($ledgerbl->table_type == 'Income') && ($ledgerbl->transaction_type == 'Advance')) || (($ledgerbl->table_type == 'Expense') && ($ledgerbl->transaction_type == 'Prepaid Adjust' )))
                                    
                                     <?php $tbalance =   $tbalance - $ledgerbl->at_amount;?>
                            @endif

                    @empty
                    @endforelse
                    
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="container">
                                <div class="text-center">
                                    <h1>Branch: {{(auth()->user()->branch->branch_name) }}</h1>
                                    <h5>Phone: {{(auth()->user()->branch->branch_phone) }}</h5>
                                    <p>{{(auth()->user()->branch->branch_address) }}</p>
                                 <?php 
                                    if(isset($pdfhead["title"])){
                                        $title = $pdfhead["title"];
                                    }else {
                                        $title = "Generated Report";
                                    }
                                    if(isset($pdfhead["fromDate"]) && $pdfhead["fromDate"]!="" ){
                                        $fromDate = $pdfhead["fromDate"]." to ";
                                    }else{
                                        $fromDate = "All Data";
                                    }
                                    if(isset($pdfhead["toDate"])){
                                        $toDate = $pdfhead["toDate"];
                                    }else{
                                        $toDate = "";
                                    }
                                    ?>
                         <h3>Report: {{$title}}</h3>
                        <h3>Data: {{$fromDate.' '.$toDate}} </h3>                                    
           
           
                                </div>
                                
                            <div class="table-responsive">
                            			
                                <table class="table table-bordered table-hover"   id="example">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Transaction Type</th>
                                        <th>Ref</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Total Balance</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($ledgers as $ledger)

                                        <tr>
                                            <td>{{$ledger->t_date}}</td>
                                            <td>{{$ledger->description}}</td>
                                            <td>{{$ledger->payment_type}}</td>
                                            <td>{{$ledger->ref}}</td>
                                            @if((($ledger->table_type == 'Expense') && ($ledger->transaction_type == 'Prepaid')) || (($ledger->table_type == 'Income') && ($ledger->transaction_type == 'Advance Adjust' )))
                                                <td>{{$ledger->at_amount}}</td> <!--debit-->
                                                <td></td> <!--credit-->
                                                <td>{{ round($tbalance, 2) }} </td>
                                            <?php $tbalance = $tbalance - $ledger->at_amount;?>
                                               
                                            @elseif(((($ledger->table_type == 'Income') && ($ledger->transaction_type == 'Advance')) || (($ledger->table_type == 'Expense') && ($ledger->transaction_type == 'Prepaid Adjust' ))))
                                                <td></td> <!--debit-->
                                                <td>{{$ledger->at_amount}}</td> <!--credit-->
                                                <td>{{ round($tbalance, 2) }} </td>
                                            <?php $tbalance = $tbalance + $ledger->at_amount;?>

                                            @endif
                       
                                        </tr>
                                    @empty
                                    @endforelse


                                    </tbody>
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
            $("#income_expense").addClass('active');
        });
    </script>
@endsection
