@extends('layouts.master')



@section('content')

    <div id="contentContainer">


        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>All Income & Expense Ledger</h3>
                        <a href="{{url('ledger/adv_income_expense')}}" class="btn btn-info" role="button" style="float: right;
    margin-top: -35px;">Prepaid Income & Expense Ledger</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="container">


										<div class="table-responsive">
										
										
                            <table class="table table-bordered table-hover" id="example">
            <thead>
            <tr>
                <th>Accounts Name</th>

            </tr>
            </thead>
            <tbody>
            <?php
            $n = 5;
            ?>


            <tr>
                <td>
                    <a href="{{url('ledger/interest')}}">Interest</a>
                </td>
            </tr>
            
            <tr>
                <td>
                    <a href="{{url('ledger/depreciation')}}">Depreciation</a>
                </td>
            </tr>
            
            <tr>
                <td>
                    <a href="{{url('ledger/tax')}}">Tax Provision + Salary Tax</a>
                </td>
            </tr>
            
            <tr>
                <td>
                    <a href="{{url('ledger/salary')}}">Salary/Wages</a>
                </td>
            </tr>


            @forelse ($ledgers as $ledger)
                <tr>
                    <td>
                        <a href="{{url('ledger/income_expense/'.$ledger->id) }}">{{$ledger->account_name}}</a>

                    </td>
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
            $("#income_expense").addClass('active');
        });
    </script>
@endsection
