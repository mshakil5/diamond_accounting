
@extends('layouts.master')



@section('content')

    <div id="contentContainer">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>
                            Transaction
                        </h3>

                        <form  action="{{route('analysis_transaction_search')}}" method ="POST">
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
                        
                        
                        <form  action="{{route('analysis_transaction_user_search')}}" method ="POST">
                            @csrf
                            <br>
                            <div class="container">
                                <div class="row">
                                    <div class="container-fluid">
                                        <div class="form-group row">

                                            <label for="username" class="col-form-label col-md-2">User Name</label>
                                            <div class="col-md-3">
                                                <select name="username" class="form-control" id="username">
                                                    <option value="" selected>Please Select</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
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
                    <div class="toppad">
                        <div class="row">
                            <div class="container">
                                <div class="text-center">
                                    <h1>Branch: {{(auth()->user()->branch->branch_name) }}</h1>
                                    <h5>Phone: {{(auth()->user()->branch->branch_phone) }}</h5>
                                    <p>{{(auth()->user()->branch->branch_address) }}</p>
                                    <h5>All Transaction Report</h5>
                                </div>

                                <table class="table table-bordered table-hover" id="example">
                                    <thead>
                                    <tr>
                                        <th>Account Name</th>
                                        <th>Transaction Type</th>
                                        <th>Payment Type</th>
                                        <th>Description</th>
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

                                    @forelse ($transactions as $transaction)

                                        <tr>
                                            <td>{{$transaction->account_name}}</td>
                                            <td>{{$transaction->transaction_type}}</td>
                                            <td>{{$transaction->payment_type}}</td>
                                            <td>{{$transaction->description}}</td>
                                            <td>{{$transaction->updated_by}}</td>
                                            <td>{{$transaction->created_by}}</td>
                                            <td>{{$transaction->updated_ip}}</td>
                                            <td>{{$transaction->created_ip}}</td>
                                            <td>{{$transaction->created_at}}</td>
                                            <td>{{$transaction->updated_at}}</td>
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
            $("#transaction").addClass('active');
        });
    </script>
@endsection
