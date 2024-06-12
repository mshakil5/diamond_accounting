{{--<h1>Employee Ledger Called</h1>--}}
@extends('layouts.master')



@section('content')

    <div id="contentContainer">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                         <form  action="{{route('daycashbook_search')}}" method ="POST">
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

                    <!--get last balance -->
                    <?php
                    // $tbalance = 0;
                    if ($prebalance) {
                        $tbalance = $prebalance;
                    } else {
                        $tbalance = 0;
                    }

                    ?>
                    {{-- total balance calculation start  --}}
            @forelse ($data as $tamount)

                @if((($tamount->table_type == 'Income') && ($tamount->transaction_type == 'Advance' || $tamount->transaction_type == 'Current')) || (($tamount->table_type == 'Asset') && ($tamount->transaction_type == 'Receive' || $tamount->transaction_type == 'Sold')) ||(($tamount->table_type == 'Liabilities') && ($tamount->transaction_type == 'Receive')) || (($tamount->table_type == 'OwnerEquity') && ($tamount->transaction_type == 'Receive')))

                    @if ((($tamount->table_type == 'Liabilities') && ( $tamount->transaction_type == 'Receive')) || (($tamount->table_type == 'Asset')  && ( $tamount->transaction_type == 'Sold' || $tamount->transaction_type == 'Receive')) || (($tamount->table_type == 'OwnerEquity') && ( $tamount->transaction_type == 'Receive')))

                    <?php $tbalance = $tbalance + $tamount->at_amount;?>

                    @else

                    <?php $tbalance = $tbalance + $tamount->amount;?>

                    @endif

                @elseif(($tamount->table_type == 'Expense' && ($tamount->transaction_type == 'Current' || $tamount->transaction_type == 'Due Adjust'|| $tamount->transaction_type == 'Prepaid'|| $tamount->transaction_type == 'Payment')) || (($tamount->table_type == 'Asset') && ($tamount->transaction_type == 'Purchase' || $tamount->transaction_type == 'Payment')) || (($tamount->table_type == 'OwnerEquity') && ($tamount->transaction_type == 'Payment')) || (($tamount->table_type == 'Liabilities') && ($tamount->transaction_type == 'Payment')))

                    @if ($tamount->table_type == 'Expense' && ($tamount->transaction_type == 'Current'|| $tamount->transaction_type == 'Due Adjust'|| $tamount->transaction_type == 'Prepaid'|| $tamount->transaction_type == 'Payment'))

                        @if (($tamount->account->account_name == 'Salary') || ($tamount->account->account_name == 'Wages'))
                        <?php $tbalance = $tbalance - $tamount->at_amount;?>
                        @else
                        <?php $tbalance = $tbalance - $tamount->amount;?>
                        @endif

                    @else
                    <?php $tbalance = $tbalance - $tamount->at_amount;?>
                    @endif



                @else


                @endif
            </tr>
        @empty
            <h3>No post found from you.</h3>
        @endforelse

        {{-- total balance calculation end  --}}
                    <div class="card-body">
                        <div class="row">
                            <!--<div class="container">-->
                                <div class="col-md-1"></div>
                                <div class="col-md-10">
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


                            <table class="table table-bordered table-hover"  id="example">
            <thead>
            <tr>
                <th>Date</th>
                <th>Table Type</th>
                <th>Description</th>
                <th>Ref</th>
                <th>Receipts</th>
                <th>Payments</th>
                <th>Balance</th>

            </tr>
            </thead>
            <tbody>
            <?php
            $balance = 0;
            ?>
            {{-- new code start --}}

            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Previous Balance</th>
                <th> @if ($prebalance) {{$prebalance}}@endif </th>
            </tr>

            {{-- new code end  --}}
            @forelse ($data as $data)

                <tr>
                    <td>{{$data->t_date}}</td>
                    <td>{{$data->table_type}}</td>
                    <td>{{$data->description}}</th>
                    <td>{{$data->ref}}</td>

                    @if((($data->table_type == 'Income') && ($data->transaction_type == 'Advance' || $data->transaction_type == 'Current')) || (($data->table_type == 'Asset') && ($data->transaction_type == 'Receive' || $data->transaction_type == 'Sold')) ||(($data->table_type == 'Liabilities') && ($data->transaction_type == 'Receive')) || (($data->table_type == 'OwnerEquity') && ($data->transaction_type == 'Receive')))

                        @if ((($data->table_type == 'Liabilities') && ( $data->transaction_type == 'Receive')) || (($data->table_type == 'Asset')  && ( $data->transaction_type == 'Sold' || $data->transaction_type == 'Receive')) || (($data->table_type == 'OwnerEquity') && ( $data->transaction_type == 'Receive')))

                        <td> {{$data->at_amount}}</td>{{-- receipts  --}}
                        <td></td>{{-- payments  --}}
                        <td>{{ number_format($tbalance, 2) }} </td>{{-- Total Balance  --}}
                        <?php $tbalance = $tbalance - $data->at_amount;?>

                        @else

                        <td> {{$data->amount}}</td>{{-- receipts  --}}
                        <td></td>{{-- payments  --}}
                        <td>{{ number_format($tbalance, 2) }} </td>{{-- Total Balance  --}}
                        <?php $tbalance = $tbalance - $data->amount;?>

                        @endif

                    @elseif(($data->table_type == 'Expense' && ($data->transaction_type == 'Current' || $data->transaction_type == 'Due Adjust'|| $data->transaction_type == 'Prepaid'|| $data->transaction_type == 'Payment')) || (($data->table_type == 'Asset') && ($data->transaction_type == 'Purchase' || $data->transaction_type == 'Payment')) || ($data->table_type == 'OwnerEquity' && $data->transaction_type == 'Payment') || ($data->table_type == 'Liabilities' && $data->transaction_type == 'Payment'))

                        @if (($data->table_type == 'Expense' && $data->transaction_type == 'Current'|| $data->transaction_type == 'Due Adjust'|| $data->transaction_type == 'Prepaid'||$data->table_type == 'Expense' && $data->transaction_type == 'Payment') || ($data->table_type == 'Liabilities' && $data->transaction_type == 'Payment'))


                            @if ((($data->account->account_name == 'Salary') || ($data->account->account_name == 'Wages'))|| ($data->table_type == 'Liabilities' && $data->transaction_type == 'Payment'))
                                <td></td>{{-- receipts  --}}
                                <td>{{$data->at_amount}}</td>{{-- payments  --}}
                                <td>{{ number_format($tbalance, 2) }} </td>{{-- Total Balance  --}}
                                <?php $tbalance = $tbalance + $data->at_amount;?>
                            @else

                                <td></td>{{-- receipts  --}}
                                <td>{{$data->amount}}</td>{{-- payments  --}}
                                <td>{{ number_format($tbalance, 2) }} </td>{{-- Total Balance  --}}
                                <?php $tbalance = $tbalance + $data->amount;?>
                            @endif

                        @else
                            <td></td>{{-- receipts  --}}
                            <td>{{$data->at_amount}}</td>{{-- payments  --}}
                            <td>{{ number_format($tbalance, 2) }} </td>{{-- Total Balance  --}}
                            <?php $tbalance = $tbalance + $data->at_amount;?>
                        @endif

                    @else

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
            $("#daybook").addClass('active');
            $("#daybook").addClass('is-expanded');
            $("#daycashbook").addClass('active');
        });
    </script>
@endsection
