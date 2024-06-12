@extends('layouts.master')



@section('content')

    <div id="contentContainer">


        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Owner Equity Ledger</h3></div>
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
                                    $n = 2;
                                    ?>
                                    
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <a href="{{url('ledger/sharepremium')}}">Share Premium + Capital Reserve</a>
                                        </td>
                                    </tr>

                                    @forelse ($oe as $oe)
                                        <tr>
                                            <td>{{$n++}}</td>
                                            <td>
                                                <a href="{{url('ledger/ownerequity/'.$oe->id) }}">{{$oe->account_name}}</a>

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
            $("#allownerequity").addClass('active');
            $("#allownerequity").addClass('is-expanded');
            $("#oeledger").addClass('active');
        });
    </script>
@endsection
