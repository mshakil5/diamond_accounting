
@extends('layouts.master')



@section('content')
    <div id="contentContainer">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div>
                        
                    {{-- search strt  --}}
                        <div class="no-print">
                            <form  action="{{route('financial_statement_search')}}" method ="POST">
                                @csrf
                                <br>
                                <div class="container">
                                    <div class="row">
                                        <div class="container-fluid">
                                            <div class="form-group row">
                                                
                                                <label for="date" class="col-form-label col-md-2">Date</label>
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
                                    <h5 style="text-align: center">Balance Sheet</h5>
                                    <p>{{(auth()->user()->branch->branch_address) }}</p>
                    
                    
                                    <?php         
                                    if(isset($pdfhead["toDate"])){
                                        $toDate = $pdfhead["toDate"];
                                    ?>     
                                   <h3 style='margin-top:-15px;'>As at  {{$toDate}} </h3>
                                   <?php
                                    }else{
                                    $date = date('Y-m-d');  
                                    echo "<h3 style='margin-top:-15px;'>As at  $date </h3>";
                                    }  
                                    ?>     
                                </div>
                        
                    </div>
                    <div class="card-body" style="padding-left: 80px;padding-right: 80px;">
                        <div class="row">
                        <div class="col-md-2"></div>
                          <div class="col-md-8">
                            <table class="table table-hover table-bordered">
                                <thead>
                                <tr>
                                    <th>Particulars</th>
                                    <th>Amount</th>
                                    <th>Amount</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th>(1) Fixed Asset</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
<!--                                --><?php
                                $totalfixedasset = 0;
//                                ?>

                                @forelse ($assets as $asset)
                                    <tr>
                                        @if($asset->account_type == "Fixed Asset")
                                            <td style="padding-left: 70px;">{{$asset->account_name}}</td>
                                            <td></td>
                                            <td style="text-align: right">{{$asset->sumamount}}</td>
                                            <?php $totalfixedasset = $asset->sumamount + $totalfixedasset;?>
                                            <td></td>
                                        @endif
                                    </tr>

                                @empty
                                @endforelse


                                <tr>
                                    <th>Net Fixed Asset</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: right">{{ $totalfixedasset}} </th>
                                </tr>

                                <?php
                                $totalcurrentasset = 0;
                                ?>

                                <tr>
                                    <th>(2) Current Asset</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>

                                <tr>
                                    <td style="padding-left: 70px;">Cash in Hand</td>
                                    <td style="text-align: right">{{ number_format($totalcash, 2) }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td style="padding-left: 70px;">Cash at Bank</td>
                                    <td style="text-align: right">{{ number_format($totalbank, 2) }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>

                                <?php
                                $totalcurrentasset = 0;
                                ?>
                                @forelse ($currentassets as $currentasset)
                                    <tr>
                                        <td style="padding-left: 70px;">{{$currentasset->account_name}}</td>
                                        <td style="text-align: right">{{$currentasset->amount}}</td>
                                        <?php $totalcurrentasset = $currentasset->amount + $totalcurrentasset;?>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @empty
                                @endforelse


                                <tr>
                                    <td style="padding-left: 50px;">Account Receivables</td>
                                    <td style="text-align: right">{{$totalcurrentassetar}}</td>
                                    <td></td>
                                    <td></td>
                                </tr>




                                

                                <?php
                                $currentassetwithvat = $totalcurrentasset + $totalcurrentassetar + $totalcash + $totalbank;
                                $currentassetwithvat = round($currentassetwithvat, 2);
                                
                                ?>

                                <tr>
                                    <th>Total Current Asset</th>
                                    <th></th>
                                    <th style="text-align: right">{{ $currentassetwithvat }}</th>
                                    <th></th>
                                </tr>

                                <tr>
                                    <th>(3) Short Term Liabilities</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>

                                <?php
                                $totalshorttermliability = 0;
                                ?>
                                
                                @forelse ($shorttermliabilities as $shorttermliabilitie)
                                    <?php $totalshorttermliability = $shorttermliabilitie->amount + $totalshorttermliability;?>
                                @empty
                                @endforelse
                                
                                

                                <tr>
                                    <td style="padding-left: 50px;">Accounts Payable</td>
                                    <td style="text-align: right">{{ $netaccpayable + $totalshorttermliability }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                
                                <tr>
                                    <td style="padding-left: 50px;">Dividend Payable</td>
                                    <td style="text-align: right">{{ $totaldividendpayable }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>


                                <tr>
                                    <td style="padding-left: 50px;">Employee Payable</td>
                                    <td style="text-align: right">{{ $totalemployeepayable }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td style="padding-left: 50px;">Vat Payable</td>
                                    <td style="text-align: right">{{ $totalvatpayable - $totalvatreceivable }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                
                               
                                
                                 <tr>
                                    <td style="padding-left: 50px;">Tax Payable</td>
                                    <td style="text-align: right">{{ $totaltaxprovision + $totalsalarytax }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>




                                <?php
                                $totalcurrentliability = $totalshorttermliability+$totalemployeepayable+$netaccpayable + $totalvatpayable + $totaltaxprovision + $totalsalarytax + $totaldividendpayable - $totalvatreceivable;
                                 $totalcurrentliability = round($totalcurrentliability, 2);
                                ?>



                                <tr>
                                    <th>Total Current Liabilities</th>
                                    <th></th>
                                    <th style="text-align: right">{{$totalcurrentliability}}</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>(4) Net Current Assets:(2 less3 =)</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: right">{{$currentassetwithvat - $totalcurrentliability}}</th>
                                </tr>
                                <?php
                                $grossasset = $totalfixedasset + $currentassetwithvat - $totalcurrentliability;
                                ?>
                                <tr>
                                    <th>(5) Gross Assets- Sub Total( 1 + 4 =)</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: right">{{ $grossasset }}</th>
                                </tr>

                                <tr>
                                    <th>(6) Long Term Liabilities</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <?php
                                $totallongtermliability = 0;
                                ?>
                                @forelse ($longtermliabilities as $longtermliabilitie)
                                    <tr>
                                        <td style="padding-left: 70px;">{{$longtermliabilitie->account_name}}</td>
                                        <td style="text-align: right">{{$longtermliabilitie->amount}}</td>
                                        <?php $totallongtermliability = $longtermliabilitie->amount + $totallongtermliability;?>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @empty
                                @endforelse

                                <tr>
                                    <td>Total Long-term Liabilities</td>
                                    <td></td>
                                    <td style="text-align: right">{{ $totallongtermliability }}</td>
                                    <td></td>
                                </tr>


                                <tr>
                                    <th>Net Assets (5 LESS 6)</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: right">{{$grossasset - $totallongtermliability}}</th>
                                </tr>



                                <tr>
                                    <th>Stockholders Equity</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <?php
                                $totalcapitalbalance = $assetbalance + $equitybalance - $liabilitybalance;
                                $totalcapitalbalance = round($totalcapitalbalance, 2);
                                ?>

                                <tr>
                                    <td style="padding-left: 70px;">Equity Capital</td>
                                    <td></td>
                                    <td style="text-align: right"> {{$totalcapitalbalance}} </td>
                                    <td></td>
                                </tr>
                                <?php 
                                $totalsharepremium =  $sharepremium  - $capitalreserve + $reversecapital;
                                $totalsharepremium = round($totalsharepremium, 2);
                                
                                ?>
                                <tr>
                                    <td style="padding-left: 50px;">Add: Share Premium</td>
                                    <td></td>
                                    <td style="text-align: right">{{ $totalsharepremium }}</td>
                                    <td></td>
                                </tr>
                                

                                <tr>
                                    <td style="padding-left: 50px;">Retained Earning</td>
                                    <td style="text-align: right">{{ number_format($retainedearning, 2) }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                
                                <?php 
                                $totalreservefiund =  $revenuereserve + $capitalreserve - $reverserevenue - $reversecapital;
                                $totalreservefiund = round($totalreservefiund, 2);
                                $totalreservefiund2 = number_format($totalreservefiund, 2);
                                $retainedearning2 = round($retainedearning, 2);
                                
                                ?>


                                <tr>
                                    <td  style="padding-left: 70px;">Reserve Fund </td>
                                    <td style="text-align: right">{{ number_format($totalreservefiund, 2) }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                

                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: right">{{ $retainedearning2 + $totalreservefiund2}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 70px;">Withdraw</td>
                                    <td></td>
                                    <td style="text-align: right">{{ $withdrawbalance }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Total Stockholders Equity</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: right">{{$totalcapitalbalance + $totalsharepremium + $retainedearning  - $withdrawbalance + $totalreservefiund}}</th>
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
            $("#balancesheet").addClass('active');
        });
    </script>
@endsection
