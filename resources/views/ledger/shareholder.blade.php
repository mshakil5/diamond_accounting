@extends('layouts.master')



@section('content')

    <div id="contentContainer">


        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3> Shareholder List</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="container">

                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Capital and withdraw</th>
                                    <th>Dividend Payable</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $n = 1;
                                ?>
                                @forelse ($data as $data)
                                    <tr>
                                        <td>{{$n++}}</td>
                                        <td><a href="{{url('ledger/shareholder-capital-ledger/'.$data->id) }}">{{$data->name}}</a></td>
                                        <td><a href="{{url('ledger/shareholder-dividend-ledger/'.$data->id) }}">{{$data->name}}</a></td>
                                    </tr>
                                @empty
                                    <h3>No data found.</h3>
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
            $("#shareholderLedger").addClass('active');
        });
    </script>
@endsection
