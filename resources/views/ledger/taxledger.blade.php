
@extends('layouts.master')



@section('content')

    <div id="contentContainer">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>
                            Tax Provision + Salary Tax Ledger
                        </h3>
                        <form  action="{{route('tax_ledger_search')}}" method ="POST">
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
                  <!--get total balance -->
                    <?php
                    $tbalance = 0;
                    ?> 
          ` @forelse ($taxes as $taxamt)

                    @if( $taxamt->transaction_type == 'Payment' )
                    
         
                        <?php $tbalance = $tbalance -  $taxamt->at_amount ;?>

                    @elseif( $taxamt->transaction_type == 'Account Payable' ||  $taxamt->account_name == 'Salary' ||  $taxamt->account_name == 'Wages')

                        @if( $taxamt->account_name == 'Salary' ||  $taxamt->account_name == 'Wages')

                        <?php $tbalance =  $tbalance + $taxamt->t_amount;?>

                        @else

                            <?php $tbalance =  $tbalance + $taxamt->at_amount;?>

                        @endif

                    @endif

            @empty
            @endforelse                    
                    <div class="toppad">
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
										  <table class="table table-bordered table-hover" id="example">
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
                                    @forelse ($taxes as $tax)

                                        <tr>
                                            <td>{{$tax->t_date}}</td>
                                            <td>{{$tax->description}}</td>
                                            <td>{{$tax->transaction_type}}</td>
                                            <td>{{$tax->ref}}</td>
                                            @if( $tax->transaction_type == 'Payment' )
                                            
                                                <td>{{$tax->at_amount}}</td><!-- debit blance -->
                                                <td></td><!-- credit blance -->
                                                <td>{{ round($tbalance, 2) }} </td>
                                                <?php $tbalance = $tbalance +  $tax->at_amount ;?>
                                                
                                            @elseif( $tax->transaction_type == 'Account Payable' ||  $tax->account_name == 'Salary' ||  $tax->account_name == 'Wages')
                                               <td></td><!-- debit blance -->
                                               
                                                @if( $tax->account_name == 'Salary' ||  $tax->account_name == 'Wages')
                                                <td>{{$tax->t_amount}}</td><!-- Credit blance -->
                                                <td>{{ round($tbalance, 2) }} </td>
                                                <?php $tbalance =  $tbalance - $tax->t_amount;?>
                                                
                                                @else
                                                <td>{{$tax->at_amount}}</td><!-- Credit blance -->
                                                <td>{{ round($tbalance, 2) }} </td>
                                                <?php $tbalance =  $tbalance - $tax->at_amount;?>
                                                
                                                @endif
                                                
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
