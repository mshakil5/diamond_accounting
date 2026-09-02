@extends('layouts.master')

@section('content')
    <div id="addThisFormContainer">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>New Address</h3>
                        <div class="ermsg"></div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="container">

                                {!! Form::open(['url' => 'address/create','id'=>'createThisForm']) !!}
                                {!! Form::hidden('codeid','', ['id' => 'codeid']) !!}

                                {!! Form::label('title', 'Title', ['class' => 'awesome']) !!}
                                {!! Form::text('title','',['id'=>'title','class'=>'form-control','placeholder'=>'Title (e.g. Home, Office)']) !!}

                                {!! Form::label('address_first_line', 'Address First Line', ['class' => 'awesome']) !!}
                                {!! Form::text('address_first_line','',['id'=>'address_first_line','class'=>'form-control','placeholder'=>'Address First Line']) !!}

                                {!! Form::label('address_second_line', 'Address Second Line', ['class' => 'awesome']) !!}
                                {!! Form::text('address_second_line','',['id'=>'address_second_line','class'=>'form-control','placeholder'=>'Address Second Line']) !!}

                                {!! Form::label('address_third_line', 'Address Third Line', ['class' => 'awesome']) !!}
                                {!! Form::text('address_third_line','',['id'=>'address_third_line','class'=>'form-control','placeholder'=>'Address Third Line']) !!}

                                {!! Form::label('town', 'Town', ['class' => 'awesome']) !!}
                                {!! Form::text('town','',['id'=>'town','class'=>'form-control','placeholder'=>'Town']) !!}

                                {!! Form::label('postcode', 'Postcode', ['class' => 'awesome']) !!}
                                {!! Form::text('postcode','',['id'=>'postcode','class'=>'form-control','placeholder'=>'Postcode']) !!}

                                <div class="row">
                                    <div class="col-md-4">
                                        {!! Form::label('latitude', 'Latitude', ['class' => 'awesome']) !!}
                                        {!! Form::text('latitude','',['id'=>'latitude','class'=>'form-control','placeholder'=>'Lat']) !!}
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::label('longitude', 'Longitude', ['class' => 'awesome']) !!}
                                        {!! Form::text('longitude','',['id'=>'longitude','class'=>'form-control','placeholder'=>'Lng']) !!}
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::label('allowed_radius', 'Allowed Radius (m)', ['class' => 'awesome']) !!}
                                        {!! Form::number('allowed_radius', '300', ['id'=>'allowed_radius','class'=>'form-control','placeholder'=>'300']) !!}
                                    </div>
                                </div>

                                {!! Form::label('status', 'Status', ['class' => 'awesome']) !!}
                                {!! Form::select('status', ['1' => 'Active', '0' => 'Inactive'], '1', ['id'=>'status','class'=>'form-control']) !!}

                                <hr>
                                <input type="button" id="addBtn" value="Create" class="btn btn-primary">
                                <input type="button" id="FormCloseBtn" value="Close" class="btn btn-warning">
                                {!! Form::close() !!}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button id="newBtn" type="button" class="btn btn-info">Add New</button>
    <hr>

    <div id="contentContainer">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Address Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="container">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Title</th>
                                        <th>Address</th>
                                        <th>Town</th>
                                        <th>Postcode</th>
                                        <th>Lat / Lng</th>
                                        <th>Radius</th>
                                        <th>Status</th>
                                        @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $n = 1; ?>
                                    @forelse ($data as $row)
                                        <tr>
                                            <td>{{ $n++ }}</td>
                                            <td>{{ $row->title }}</td>
                                            <td>
                                                {{ $row->address_first_line }}
                                                @if($row->address_second_line)<br>{{ $row->address_second_line }}@endif
                                                @if($row->address_third_line)<br>{{ $row->address_third_line }}@endif
                                            </td>
                                            <td>{{ $row->town }}</td>
                                            <td>{{ $row->postcode }}</td>
                                            <td>
                                                @if($row->latitude && $row->longitude)
                                                    {{ $row->latitude }}, {{ $row->longitude }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $row->allowed_radius ? $row->allowed_radius . ' m' : '' }}</td>
                                            <td>
                                                @if($row->status == 1)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                            <td>
                                                <a id="EditBtn" rid="{{ $row->id }}" style="cursor:pointer;">
                                                    <i class="fa fa-edit" style="color:#2196f3;font-size:16px;"></i>
                                                </a>
                                                <a id="deleteBtn" rid="{{ $row->id }}" style="cursor:pointer;">
                                                    <i class="fa fa-trash-o" style="color:red;font-size:16px;"></i>
                                                </a>
                                            </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center"><h3>No record found.</h3></td></tr>
                                    @endforelse
                                    </tbody>
                                </table>

                                {{ $data->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $("#addThisFormContainer").hide();

            $("#newBtn").click(function () {
                clearform();
                $("#newBtn").hide(100);
                $("#addThisFormContainer").show(300);
            });

            $("#FormCloseBtn").click(function () {
                $("#addThisFormContainer").hide(200);
                $("#newBtn").show(100);
                clearform();
            });

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            var url   = "{{ URL::to('/address') }}";
            var upurl = "{{ URL::to('/address-update') }}";

            $("#addBtn").click(function () {
                if ($(this).val() == 'Create') {
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {
                            title: $("#title").val(),
                            address_first_line: $("#address_first_line").val(),
                            address_second_line: $("#address_second_line").val(),
                            address_third_line: $("#address_third_line").val(),
                            town: $("#town").val(),
                            postcode: $("#postcode").val(),
                            latitude: $("#latitude").val(),
                            longitude: $("#longitude").val(),
                            allowed_radius: $("#allowed_radius").val(),
                            status: $("#status").val()
                        },
                        success: function (d) {
                            handleResponse(d);
                        },
                        error: function (d) { console.log(d); }
                    });
                }

                if ($(this).val() == 'Update') {
                    $.ajax({
                        url: upurl,
                        method: "POST",
                        data: {
                            title: $("#title").val(),
                            address_first_line: $("#address_first_line").val(),
                            address_second_line: $("#address_second_line").val(),
                            address_third_line: $("#address_third_line").val(),
                            town: $("#town").val(),
                            postcode: $("#postcode").val(),
                            latitude: $("#latitude").val(),
                            longitude: $("#longitude").val(),
                            allowed_radius: $("#allowed_radius").val(),
                            status: $("#status").val(),
                            codeid: $("#codeid").val()
                        },
                        success: function (d) {
                            handleResponse(d);
                        },
                        error: function (d) { console.log(d); }
                    });
                }
            });

            $("#contentContainer").on('click', '#EditBtn', function () {
                $sid = $(this).attr('rid');
                $info_url = url + '/' + $sid + '/edit';
                $.get($info_url, {}, function (d) {
                    populateForm(d);
                    if (typeof pagetop === 'function') pagetop();
                });
            });

            $("#contentContainer").on('click', '#deleteBtn', function () {
                if (!confirm('Are you sure you want to delete this address?')) return;
                $shid = $(this).attr('rid');
                $info_url = url + '/' + $shid;
                $.ajax({
                    url: $info_url,
                    method: "GET",
                    data: {},
                    success: function (d) {
                        if (d.success) {
                            alert(d.message);
                            location.reload();
                        } else {
                            alert(d.message);
                        }
                    },
                    error: function (d) { console.log(d); }
                });
            });

            function handleResponse(d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                } else if (d.status == 300) {
                    $(".ermsg").html(d.message);
                    window.setTimeout(function () { location.reload() }, 2000);
                }
            }

            function populateForm(data) {
                $("#title").val(data.title);
                $("#address_first_line").val(data.address_first_line);
                $("#address_second_line").val(data.address_second_line);
                $("#address_third_line").val(data.address_third_line);
                $("#town").val(data.town);
                $("#postcode").val(data.postcode);
                $("#latitude").val(data.latitude);
                $("#longitude").val(data.longitude);
                $("#allowed_radius").val(data.allowed_radius);
                $("#status").val(data.status);
                $("#codeid").val(data.id);
                $("#addBtn").val('Update');
                $("#addThisFormContainer").show(300);
                $("#newBtn").hide(100);
            }

            function clearform() {
                $('#createThisForm')[0].reset();
                $("#addBtn").val('Create');
                $("#codeid").val('');
                $(".ermsg").html('');
                // Reset allowed_radius to default 300 on clear
                $("#allowed_radius").val(300);
            }
        });
    </script>


    <script type="text/javascript">
        $(document).ready(function() {
            $("#address_active").addClass('active');
        });
    </script>
@endsection