
@extends('layouts.master')



@section('content')
    <div id="contentContainer">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">

                        {{-- search strt  --}}
                        <div class="no-print">
                        <form  action="{{route('retained_earnings_search')}}" method ="POST">
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
                        {{-- search end  --}}
                        
                        
                                <!-- this row will not appear when printing -->
                                 <div class="row no-print">
                                    <div class="col-12">
                                        <button onclick="window.print()" class="fa fa-print btn btn-default float-right">Print</button>
                                    </div>
                                 </div>

                        <div class="text-center">
                            <h1>Branch: {{(auth()->user()->branch->branch_name) }}</h1>
                            <h5>Phone: {{(auth()->user()->branch->branch_phone) }}</h5>
                            <h5 style="text-align: center">Retained Earning Statement</h5>
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

                         <h3>Data: {{$fromDate.' '.$toDate}} </h3>                              
                        </div>

                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-2"></div>
                              <div class="col-md-8">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Particulars</th>
                                        <th style="text-align: center">Amount</th>
                                        <th style="text-align: center">Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th> Balance</th>
                                        <th></th>
                                        <th></th>
                                    </tr>

                                    <tr>
                                        <th style="padding-left: 50px;">Net Profit</th>
                                        <th style="text-align: right">{{ number_format($profitaftertax, 2) }}</th>
                                        <th></th>
                                    </tr>

                                    <tr>
                                        <th style="padding-left: 50px;">Less:</th>
                                        <th style="text-align: right"></th>
                                        <th></th>
                                    </tr>


                                    <?php
                                    $totaldividend = 0;
                                    ?>

                                    @forelse ($alldividends as $alldividend)
                                        <tr>
                                            <td style="padding-left: 70px;">{{$alldividend->account->account_name}}</td>
                                            <td style="text-align: right">{{$alldividend->sumamount}}</td>
                                            <td></td>

                                            <?php $totaldividend = $alldividend->sumamount + $totaldividend;?>
                                        </tr>
                                    @empty
                                    @endforelse

                                    <tr>
                                        <td  style="padding-left: 70px;">Revenue Reserve</td>
                                        <td style="text-align: right">{{ $revenuereserve - $reverserevenue}}</td>
                                        <td></td>
                                    </tr>

                                    <tr>
                                        <th>Closing Balance</th>
                                        <th></th>
                                        <th style="text-align: right">{{ $profitaftertax - $totaldividend - $revenuereserve + $reverserevenue}}</th>

                                    </tr>

                                    <tr>
                                        <th style="padding-left: 50px;"></th>
                                        <th style="text-align: right"></th>
                                        <th></th>
                                    </tr>



                                    </tbody>
                                </table>
                            </div>
                           <div class="col-md-2"></div>
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
                    $("#financial_statement").addClass('active');
                    $("#financial_statement").addClass('is-expanded');
                    $("#retained_earnings").addClass('active');
                });
            </script>
@endsection
