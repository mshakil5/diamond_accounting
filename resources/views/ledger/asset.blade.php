@extends('layouts.master')



@section('content')


    <div id="contentContainer">

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3> Asset List</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="container">


                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Asset Name</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $n = 1;
                                    ?>
                                    @forelse ($assets as $asset)
                                        <tr>
                                            <td>{{$n++}}</td>
                                            <td><a href="{{url('ledger/asset_ledger/'.$asset->id) }}">{{$asset->account_name}}</a></td>
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
            $("#allasset").addClass('active');
            $("#allasset").addClass('is-expanded');
            $("#asset_ledger").addClass('active');
        });
    </script>
@endsection
