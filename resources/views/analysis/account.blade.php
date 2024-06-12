
@extends('layouts.master')



@section('content')

    <div id="contentContainer">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>
                            Account
                        </h3>

                    </div>
                    <div class="toppad">
                        <div class="row">
                            <div class="container">

                                <div class="text-center">
                                    <h1>Branch: {{(auth()->user()->branch->branch_name) }}</h1>
                                    <h5>Phone: {{(auth()->user()->branch->branch_phone) }}</h5>
                                    <p>{{(auth()->user()->branch->branch_address) }}</p>
                                    <h5>Account Report</h5>
                                </div>

                                <table class="table table-bordered table-hover" id="example">
                                    <thead>
                                    <tr>
                                        <th>Account Name</th>
                                        <th>Account Type</th>
                                        <th>Updated By</th>
                                        <th>Created By</th>
                                        <th>Updated Ip</th>
                                        <th>Created Ip</th>
                                        <th>Updated At</th>
                                        <th>Created At</th>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    $balance = 0;
                                    ?>

                                    @forelse ($accounts as $account)

                                        <tr>
                                            <th>{{$account->account_name}}</th>
                                            <th>{{$account->account_type}}</th>
                                            <th>{{$account->updated_by}}</th>
                                            <th>{{$account->created_by}}</th>
                                            <th>{{$account->updated_ip}}</th>
                                            <th>{{$account->created_ip}}</th>
                                            <th>{{$account->created_at}}</th>
                                            <th>{{$account->updated_at}}</th>
                                        </tr>
                                    @empty
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
            $("#analysis").addClass('active');
            $("#analysis").addClass('is-expanded');
            $("#accounts").addClass('active');
        });
        
    </script>
@endsection
