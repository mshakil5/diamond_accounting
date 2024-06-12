
@extends('layouts.master')



@section('content')

    <div id="contentContainer">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>
                            Employee
                        </h3>

                        

                    </div>
                    <div class="toppad">
                        <div class="row">
                            <div class="container">
                                        <div class="text-center">
                                            <h1>Branch: {{(auth()->user()->branch->branch_name) }}</h1>
                                            <h5>Phone: {{(auth()->user()->branch->branch_phone) }}</h5>
                                            <p>{{(auth()->user()->branch->branch_address) }}</p>
                                            <h5>Employee Report</h5>
                                        </div>

                                <table class="table table-bordered table-hover" id="example">
                                    <thead>
                                    <tr>
                                        <th>Employee Name</th>
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

                                    @forelse ($employees as $employee)

                                        <tr>
                                            <th>{{$employee->employee_name}}</th>
                                            <th>{{$employee->updated_by}}</th>
                                            <th>{{$employee->created_by}}</th>
                                            <th>{{$employee->updated_ip}}</th>
                                            <th>{{$employee->created_ip}}</th>
                                            <th>{{$employee->created_at}}</th>
                                            <th>{{$employee->updated_at}}</th>
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
            $("#employees").addClass('active');
        });
    </script>
@endsection
