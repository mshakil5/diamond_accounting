@extends('layouts.master')

@section('title')
    Accounts
@endsection

@section('sidebar')

    @include('inc.sidebar')

@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="text-center">
                    <h1> <strong>Welcome to  {{(auth()->user()->branch->branch_name) }}</strong></h1>
                    <h5>Phone: {{(auth()->user()->branch->branch_phone) }}</h5>
                    <p>{{(auth()->user()->branch->branch_address) }}</p>
                </div>
            </div>
        </div>
    </div>


        {{-- // sales entry not access --}}
        @if (auth()->user()->user_type != 3)

        <div class="row">
            <div class="col-md-3">
                <div class="tile">
                    <h3 class="tile-title text-center">Cash Balance</h3>
                    <h5  class="tile-title text-center" >{{ number_format($totalcash, 2) }}</h5>
                    <div class="embed-responsive embed-responsive-16by9">
                        <canvas class="embed-responsive-item" id="lineChartDemo"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="tile">
                    <h3 class="tile-title text-center">Bank Balance</h3>
                    <h5  class="tile-title text-center">{{ number_format($totalbank, 2) }}</h5>
                    <div class="embed-responsive embed-responsive-16by9">
                        <canvas class="embed-responsive-item" id="pieChartDemo"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="tile">
                    <h3 class="tile-title text-center">Room Sales</h3>
                    <h5  class="tile-title text-center">{{ number_format($roomsales, 2) }}</h5>
                    <div class="embed-responsive embed-responsive-16by9">
                        <canvas class="embed-responsive-item" id="lineChartDemo"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="tile">
                    <h3 class="tile-title text-center">Total Expense</h3>
                    <h5  class="tile-title text-center">{{ number_format($totalexpense, 2) }}</h5>
                    <div class="embed-responsive embed-responsive-16by9">
                        <canvas class="embed-responsive-item" id="pieChartDemo"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif



@endsection

@section('scripts')
    <script>

        var data = {
            labels: ["January", "February", "March", "April", "May"],
            datasets: [
                {
                    label: "My First dataset",
                    fillColor: "rgba(220,220,220,0.2)",
                    strokeColor: "rgba(220,220,220,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: [65, 59, 80, 81, 56]
                },
                {
                    label: "My Second dataset",
                    fillColor: "rgba(151,187,205,0.2)",
                    strokeColor: "rgba(151,187,205,1)",
                    pointColor: "rgba(151,187,205,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(151,187,205,1)",
                    data: [28, 48, 40, 19, 86]
                }
            ]
        };
        var pdata = [
            {
                value: 300,
                color: "#46BFBD",
                highlight: "#5AD3D1",
                label: "Complete"
            },
            {
                value: 50,
                color:"#F7464A",
                highlight: "#FF5A5E",
                label: "In-Progress"
            }
        ]

        var ctxl = $("#lineChartDemo").get(0).getContext("2d");
        var lineChart = new Chart(ctxl).Line(data);

        var ctxp = $("#pieChartDemo").get(0).getContext("2d");
        var pieChart = new Chart(ctxp).Pie(pdata);


    </script>
@endsection
