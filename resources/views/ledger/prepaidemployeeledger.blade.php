{{--<h1>Employee Ledger Called</h1>--}}
@extends('layouts.master')



@section('content')

    <div id="contentContainer">


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3> @foreach ($allemployees as $employee)
                                {{ $employee->employee_name }} Ledger Data
                            @endforeach</h3>

                        <form  action="{{route('prepaidEmployee_ledger_search')}}" method ="POST">
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
                                            <input type="text" class="form-control" value="{{ $id }}" id="employeeledger" name="employeeledger"  hidden/>
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
                                    @forelse ($employees as $employeeamt)
 

                                            @if($employeeamt->transaction_type =='Prepaid')

                                                <?php $tbalance = $tbalance + $employeeamt->at_amount;?>


                                            @elseif($employeeamt->transaction_type =='Prepaid Adjust')

                                                <?php $tbalance = $tbalance - $employeeamt->at_amount;?>

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
                                <table class="table table-bordered table-hover"   id="example">
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
                                    @forelse ($employees as $employee)
                                        <tr>
                                            <td>{{$employee->t_date}}</td>
                                            <td>{{$employee->description}}</td>
                                            <td>{{$employee->payment_type}}</td>
                                            <td>{{$employee->ref}}</td>

                                            @if($employee->transaction_type =='Prepaid')

                                                <td>{{$employee->at_amount}}</td>
                                                <td></td>
                                                <td>{{round($tbalance, 2)}}</td>
                                                <?php $tbalance = $tbalance - $employee->at_amount;?>
          

                                            @elseif($employee->transaction_type =='Prepaid Adjust')
                                                <td></td>
                                                <td>{{$employee->at_amount}}</td>
                                                <td>{{round($tbalance, 2)}}</td>
                                                <?php $tbalance = $tbalance + $employee->at_amount;?>
                                                

                                            @endif
                                            
                                        </tr>
                                    @empty
                                        <h3>No post found from you. Create a new Employee</h3>
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
            $("#employee").addClass('active');
            $("#employee").addClass('is-expanded');
            $("#employee-ledger").addClass('active');
        });
    </script>
@endsection
