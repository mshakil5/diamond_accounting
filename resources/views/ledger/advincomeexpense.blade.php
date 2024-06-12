@extends('layouts.master')



@section('content')

    <div id="contentContainer">


        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3>Advance Income & Expense List</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="container">


                                <table class="table table-bordered table-hover" id="example">
                                    <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Accounts Name</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $n = 1;
                                    ?>


                                    @forelse ($ledgers as $ledger)
                                        <tr>
                                            <td>{{$n++}}</td>
                                            <td>
                                                <a href="{{url('ledger/adv_income_expense/'.$ledger->account_id) }}"> {{$ledger->account_name}}</a>

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
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#income_expense").addClass('active');
        });
    </script>
@endsection
