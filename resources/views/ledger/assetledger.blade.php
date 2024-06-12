
@extends('layouts.master')

@section('content')

    <div id="contentContainer">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3> @foreach ($accountnames as $accountname)
                                {{ $accountname->account_name }} Ledger
                            @endforeach</h3>
                            
                            <form  action="{{route('asset_ledger_search')}}" method ="POST">
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
                    <!--get total balance -->
                    <?php
                    $tbalance = 0;
                    ?>
                    @forelse ($assets as $assetbl)

                            @if($assetbl->transaction_type =='Purchase' || $assetbl->transaction_type =='Initial')

                                <?php $tbalance = $tbalance + $assetbl->at_amount;?>


                            @elseif($assetbl->transaction_type =='Sold' || $assetbl->transaction_type =='Depreciation')

                                <?php $tbalance = $tbalance - $assetbl->at_amount;?>

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
                                    <th>Ref</th>
                                    <th>Transaction Type</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Total Balance</th>

                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($assets as $asset)

                                    <tr>
                                        <td>{{$asset->t_date}}</td>
                                        <td>{{$asset->description}}</td>
                                        <td>{{$asset->ref}}</td>
                                        <td>{{$asset->transaction_type}}</td>
                                        @if($asset->transaction_type =='Purchase' || $asset->transaction_type =='Initial')
                                            <td>{{$asset->at_amount}}</td>{{-- debit  --}}
                                            <td></td>{{-- credit  --}}
                                            <td>{{ round($tbalance, 2) }}</td>{{-- total  --}}
                                            <?php $tbalance = $tbalance - $asset->at_amount;?>


                                        @elseif($asset->transaction_type =='Sold' || $asset->transaction_type =='Depreciation')
                                             <td></td>{{-- debit  --}}
                                            <td>{{$asset->at_amount}}</td>{{-- credit  --}}
                                            <td>{{ round($tbalance, 2) }}</td>{{-- total  --}}
                                            <?php $tbalance = $tbalance + $asset->at_amount;?>

                                        @endif

                                    </tr>
                                @empty
                                    <h3>No post found from you.</h3>
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
            $("#allasset").addClass('active');
            $("#allasset").addClass('is-expanded');
            $("#asset_ledger").addClass('active');
        });
    </script>
@endsection
