
@extends('layouts.master')



@section('content')
    <div id="contentContainer">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">

                        {{-- search strt  --}}
                        <div class="no-print">
                        <form  action="{{route('profit_loss_search')}}" method ="POST">
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
                            <h5 style="text-align: center">PROFIT AND LOSE ACCOUNT</h5>
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
                                    <th>TURN OVER SALES</th>
                                    <th></th>
                                    <th></th>
                                </tr>

                                <?php
                                $totalincome = 0;
                                ?>

                                @forelse ($incomes as $income)
                                    <tr>
                                        <td style="padding-left: 70px;">{{$income->account->account_name}}</td>
                                        <td style="text-align: right">{{$income->sumamount}}</td>
                                        <td></td>

                                        <?php $totalincome = $income->sumamount + $totalincome;?>
                                    </tr>
                                @empty
                                @endforelse

                                <tr>
                                    <th>Total Income</th>
                                    <th style="text-align: right">{{ $totalincome }} </th>
                                    <th style="text-align: right"></th>

                                </tr>

                                <tr>
                                    <th>Gross Profit</th>
                                    <th style="text-align: right"></th>
                                    <th style="text-align: right">{{ $totalincome }}</th>
                                </tr>
                                <tr>
                                    <th>EXPENDITURE</th>
                                    <th></th>
                                    <th></th>
                                </tr>

                                <?php
                                $totalexpense = 0;
                                $totalsalaryexp = 0;
                                ?>
                                
                                @forelse ($salaryexp as $salaryexp)
                                    <tr>
                                        <td style="padding-left: 70px;">{{$salaryexp->account->account_name}}</td>
                                        <td style="text-align: right">{{$salaryexp->sumamount}}</td>
                                        <td style="text-align: right"></td>
                                        <?php $totalsalaryexp = $salaryexp->sumamount + $totalsalaryexp;?>
                                    </tr>
                                @empty
                                @endforelse

                                @forelse ($expenses as $expense)
                                    <tr>
                                        <td style="padding-left: 70px;">{{$expense->account->account_name}}</td>
                                        <td style="text-align: right">{{$expense->sumamount}}</td>
                                        <td style="text-align: right"></td>
                                        <?php $totalexpense = $expense->sumamount + $totalexpense;?>
                                    </tr>
                                @empty
                                @endforelse
                                <!--<tr>-->
                                <!--    <th>Adjusted Expense</th>-->
                                <!--    <th></th>-->
                                <!--    <th style="text-align: right"></th>-->
                                <!--</tr>-->
                                <?php
                                $totaladjustexpense = 0;
                                ?>
                                @forelse ($adjustexpenses as $adjustexpense)
                                    <tr>
                                        <td style="padding-left: 70px;">{{$adjustexpense->account->account_name}}</td>
                                        <td style="text-align: right">{{$adjustexpense->sumamount}}</td>
                                        <?php $totaladjustexpense = $adjustexpense->sumamount + $totaladjustexpense;?>
                                        <td></td>
                                    </tr>
                                @empty
                                @endforelse

                                <tr>
                                    
                                    @if($totaldep>0)
                                        <td style="padding-left: 70px;">Depreciation</td>
                                        <td style="text-align: right">{{$totaldep}}</td>
                                        <td></td>
                                        @endif
                                    </tr>

                                <tr>
                                     @if($totalinterest>0)
                                    <td style="padding-left: 70px;"> Interest Payable</td>
                                    <td style="text-align: right">{{ $totalinterest }}</td>
                                    <td></td>
                                    @endif
                                </tr>
                                
                               


                                <tr>
                                    <th>Total Expense</th>
                                    <th></th>
                                    <th style="text-align: right">{{ $totalexpense + $totalsalaryexp + $totaladjustexpense + $totaldep + $totalinterest}}</th>
                                </tr>
                                <?php
                                $profitbeforetax =  $totalincome - $totalexpense - $totalsalaryexp - $totaladjustexpense - $totaldep - $totalinterest;
                                ?>

                                <tr>
                                    <th>Profit/Loss Before Vat</th>
                                    <th></th>
                                    <th style="text-align: right">{{ number_format($profitbeforetax, 2) }}</th>
                                </tr>



                           
                                <?php
                                // $vatprovision = $totalexpvat - $totaladdnewvat - $totalincomevat;
                                $vatprovision =   $totaladdnewvat + $totalincomevat - $totalexpvat-$totaladdassetvat;
                                ?>

                                <tr>
                                     @if($vatprovision>0 || $vatprovision<0 )
                                    <th style="padding-left: 70px;">Vat Provision</th>
                                    <th style="text-align: right">{{ $vatprovision }}</th>
                                    <th></th>
                                    @endif
                                </tr>
                                <?php
                                $profitaftertax =  $profitbeforetax - $vatprovision;
                                ?>

              
                                <tr>
                                     @if($totalprovision>0 || $totalsalarytax > 0 || $totalprovision < 0 || $totalsalarytax < 0)
                                    <th style="padding-left: 70px;">Tax Provision</th>
                                    <th style="text-align: right">{{ $totalprovision + $totalsalarytax }}</th>
                                    <th></th>
                                    @endif
                                </tr>
                                
                                
                               
                                
                                <?php
                                $netprofit =  $profitaftertax - $totalprovision - $totalsalarytax;
                                ?>

                                <tr>
                                    <th>Net Profit</th>
                                    <th></th>
                                    <th style="text-align: right">{{ number_format($netprofit, 2) }}</th>
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
                    $("#profit_loss").addClass('active');
                });
            </script>
@endsection
