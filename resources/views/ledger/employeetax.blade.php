@extends('layouts.master')



@section('content')

    <div id="contentContainer">


        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Employee Tax Payable List</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="container">

                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Employee Name</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $n = 1;
                                    ?>
                                    @forelse ($employees as $employee)
                                        <tr>
                                            <td>{{$n++}}</td>
                                            <td><a href="{{url('ledger/employeetax/'.$employee->id) }}">{{$employee->employee_name}}</a></td>
                                            {{--                    <td><a href="{{ url('projects/display/'.$projects->id) }}">{{$employee->employee_name}}</a></td>--}}
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
            $("#employeetax-ledger").addClass('active');
        });
    </script>
@endsection
