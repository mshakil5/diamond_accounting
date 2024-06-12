{{--<h1>Employee Ledger Called</h1>--}}
@extends('layouts.master')



@section('content')
    <div id="contentContainer">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="no-print">
                         {{-- search strt  --}}
                        <form  action="{{route('cashflow_search')}}" method ="POST">
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
                                    <h5 style="text-align: center">Cash Flow Statement</h5>
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
                    </div>
                    <div class="card-body" style="padding-left: 80px;padding-right: 80px;">
                        <div class="row">
                        <div class="col-md-2"></div>
                          <div class="col-md-8">
                            <table class="table table-hover table-bordered" >
                                <thead>
                                <tr>
                                    <th>Particulars</th>
                                    <th style="text-align: right">Amounts</th>
                                    <th style="text-align: right">Amounts</th>
                                </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    if ($prenetbalance) {
                                        $openingbalance = $ownerequities + $liabilitybalance + $assetbalance + $prenetbalance;
                                    } else {
                                        $openingbalance = $ownerequities + $liabilitybalance + $assetbalance;
                                    }


                                    ?>
                                <tr>
                                    <th>(1) Opening Balance</th>
                                    <th></th>
                                    <th style="text-align: right">{{ $openingbalance }}</th>
                                </tr>

                                <tr>
                                    <th>(2) Cash Incoming </th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <?php
                                $totalincome = 0;
                                $totalsoldasset = 0;
                                $totalpaymentreceivables = 0;
                                $totalreceiptreceivables = 0;
                                $totalreceiptliabilities = 0;
                                ?>

                                @forelse ($incomes as $income)
                                    <tr>
                                        <td style="padding-left: 70px;">{{$income->account->account_name}}</td>
                                        <td style="text-align: right">{{$income->sumamount}}</td>
                                        <?php $totalincome = $income->sumamount + $totalincome;?>
                                        <td></td>
                                    </tr>
                                @empty
                                @endforelse

                                @forelse ($soldassets as $soldasset)
                                    <?php $totalsoldasset = $soldasset->sumamount + $totalsoldasset;?>
                                @empty
                                @endforelse

                                <tr>
                                    @if($totalsoldasset > 0)
                                    <td style="padding-left: 70px;">Asset Sold</td>
                                    <td style="text-align: right">{{ $totalsoldasset}}</td>
                                    <td></td>
                                    @endif
                                </tr>



                                @forelse ($receiptreceivables as $receiptreceivable)
                                    <?php  $totalreceiptreceivables = $receiptreceivable->sumamount + $totalreceiptreceivables;?>
                                @empty
                                @endforelse
                                <?php
                                $ar = $totalreceiptreceivables;
                                ?>
                                <tr>
                                    @if($ar > 0)
                                    <td style="padding-left: 70px;">Account Receive</td>
                                    <td style="text-align: right">{{ $ar }}</td>
                                    <td></td>
                                    @endif
                                </tr>

                                @forelse ($receiptliabilities as $receiptliabilitie)
                                    <?php $totalreceiptliabilities = $receiptliabilitie->sumamount + $totalreceiptliabilities;?>
                                @empty
                                @endforelse

                                <tr>
                                    <td style="padding-left: 70px;">Liabilities Receive</td>
                                    <td style="text-align: right">{{$totalreceiptliabilities}}</td>
                                    <td></td>
                                </tr>

                                <tr>
                                    @if($sharepremiums > 0)
                                    <td style="padding-left: 70px;">Share Premium Receive</td>
                                    <td style="text-align: right">{{$sharepremiums}}</td>
                                    <td></td>
                                    @endif
                                </tr>



                                <?php
                                $totalincoming = $totalincome + $totalsoldasset + $ar + $totalreceiptliabilities + $sharepremiums;
                                ?>

                                <tr>
                                    <td>Total Cash Incoming</td>
                                    <td></td>
                                    <td style="text-align: right"> {{ $totalincoming }}</td>
                                </tr>
                                <tr>
                                    <th>(3) Gross Cash Flow (1+2) =</th>
                                    <th></th>
                                    <th style="text-align: right"> {{ $totalincoming +  $openingbalance}}</th>
                                </tr>

                                <tr>
                                    <th>Cash Outgoing</th>
                                    <th></th>
                                    <th></th>
                                </tr>

                                    <tr>
                                        @if($withdrow > 0)
                                        <td style="padding-left: 70px;">Withdraw</td>
                                        <td style="text-align: right">{{$withdrow}}</td>
                                        <td></td>
                                        @endif
                                    </tr>

                                    <tr>
                                        @if($dividend > 0)
                                        <td style="padding-left: 70px;">Dividend</td>
                                        <td style="text-align: right">{{$dividend}}</td>
                                        <td></td>
                                        @endif
                                    </tr>


                                <?php
                                $totalpurchaseasset = 0;
                                ?>

                                @forelse ($purchaseassets as $purchaseasset)
                                        <?php $totalpurchaseasset = $purchaseasset->sumamount + $totalpurchaseasset;?>
                                @empty
                                @endforelse



                                <tr>
                                    @if($totalpurchaseasset > 0)
                                    <td style="padding-left: 70px;"> Asset Purchase</td>
                                    <td style="text-align: right">{{ $totalpurchaseasset}}</td>
                                    <td></td>
                                    @endif
                                </tr>


                                     @forelse ($paymentreceivables as $paymentreceivable)
                                      <?php $totalpaymentreceivables = $paymentreceivable->sumamount + $totalpaymentreceivables;?>
                                     @empty
                                   @endforelse

                                    <tr>
                                        @if($totalpaymentreceivables > 0)
                                            <td style="padding-left: 70px;"> Current Asset Payment</td>
                                            <td style="text-align: right">{{ $totalpaymentreceivables}}</td>
                                            <td></td>
                                        @endif
                                    </tr>




                                <?php
                                $totaloutgoing = 0;
                                $totalsalaryexpense = 0;
                                $totalpaymentliabilities = 0;
                                ?>




                                @forelse ($paymentliabilities as $paymentliabilitie)
                                    <?php $totalpaymentliabilities = $paymentliabilitie->sumamount + $totalpaymentliabilities;?>
                                @empty
                                @endforelse



                                <tr>
                                    @if($totalpaymentliabilities > 0)
                                    <td style="padding-left: 70px;">Liabilities Payment</td>
                                    <td style="text-align: right">{{$totalpaymentliabilities}}</td>
                                    <td></td>
                                    @endif
                                </tr>

                                 @forelse ($salaryexpenses as $salaryexpense)
                                    <tr>
                                        <td style="padding-left: 70px;">{{$salaryexpense->account->account_name}}</td>
                                        <td style="text-align: right">{{$salaryexpense->sumamount}}</td>
                                        <td></td>
                                        <?php $totalsalaryexpense = $salaryexpense->sumamount + $totalsalaryexpense;?>
                                    </tr>
                                @empty
                                @endforelse



                                @forelse ($expenses as $expense)
                                    <tr>
                                        <td style="padding-left: 70px;">{{$expense->account->account_name}}</td>
                                        <td style="text-align: right">{{$expense->sumamount}}</td>
                                        <td></td>
                                        <?php $totaloutgoing = $expense->sumamount + $totaloutgoing;?>
                                    </tr>
                                @empty
                                @endforelse

                                <?php
                                $netoutgoing =   $withdrow + $dividend + $totalpurchaseasset + $totalpaymentliabilities + $totaloutgoing + $totalsalaryexpense + $totalpaymentreceivables;
                                ?>


                                <tr>
                                    <th>(4) Total Cash Outgoing</th>
                                    <th style="text-align: right"></th>
                                    <th style="text-align: right">{{ $netoutgoing }} </th>
                                </tr>




                                <tr>
                                    <th> Closing Balance (3  - 4)</th>
                                    <th></th>
                                    <th style="text-align: right">{{$totalincoming +  $openingbalance - $netoutgoing}}</th>
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
            $("#cashflow").addClass('active');
        });
    </script>
@endsection
