{{--<h1>Employee Ledger Called</h1>--}}
@extends('layouts.master')



@section('content')

    <div id="contentContainer">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Vat Receivable Ledger</h3>
                        <form  action="{{route('receivable_vat_ledger_search')}}" method ="POST">
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
            @forelse ($taxes as $taxamt)

                    @if($taxamt->table_type =='Expense')

                        <?php $tbalance = $tbalance + $taxamt->amount;?>

                    @elseif($taxamt->table_type =='Asset')
                    
                        <?php $tbalance = $tbalance - $taxamt->amount;?>

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
                                </div>
        <table class="table table-bordered table-hover" id="example">
            <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Payment Type</th>
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
                    <td>{{$tax->payment_type}}</td>
                    <td>{{$tax->ref}}</td>

                    @if($tax->table_type =='Expense')
                        <td>{{$tax->amount}}</td>
                        <td></td>
                        <?php $tbalance = $tbalance - $tax->amount;?>
                        <td>{{ $tbalance }} </td>
    
                    @elseif($tax->table_type =='Asset')
                        <td></td>
                        <td>{{$tax->amount}}</td>
                        <?php $tbalance = $tbalance + $tax->amount;?>
                        <td>{{ $tbalance }} </td>

                    @endif
                </tr>
                
            @empty
                <h3>No post found from you.</h3>
            @endforelse
            </tbody>
        </table>

        {{--        {{$employees ?? ''->links()}}--}}

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
            $("#vat_ledger").addClass('active');
            $("#vat_ledger").addClass('is-expanded');
            $("#vat_receive").addClass('active');
        });
    </script>
@endsection
