@extends('layouts.master')



@section('content')
    <div id="addThisFormContainer">

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>New Employee History</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="ermsg">
                              
                                
                            </div>
                    <div class="col-md-3">
                    </div>
                    <div class="col-md-6">


                            {!! Form::open(['url' => 'employee/employee_history','id'=>'createThisForm']) !!}
                            {!! Form::hidden('historyid','', ['id' => 'historyid']) !!}
                            <div>
                                <label for="employee_id" class="awesome">Employee</label>
                                <select name="employee_id" class="form-control" id="employee_id" form="employeeform">
                                    <option value="" disabled selected>Please Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{$employee->id}}">{{ $employee->employee_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {!! Form::label('Title', 'Start Date & Time', ['class' => 'awesome']) !!}
                            {!! Form::date('title','',['id'=>'start_datetime','class'=>'form-control','placeholder'=>'Start Date & Time']) !!}
                            {!! Form::label('Title', 'End Date & Time', ['class' => 'awesome']) !!}
                            {!! Form::date('title','',['id'=>'end_datetime','class'=>'form-control','placeholder'=>'End Date & Time']) !!}
                            {!! Form::label('Description', 'Description', ['class' => 'awesome']) !!}
                            {!! Form::textarea('description','',['id'=>'history_desc','class'=>'form-control','rows'=>'5','placeholder'=>'Description']) !!}

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

    <button id="newBtn" type="button" class="btn btn-info">Add New Employee</button>
    <hr>
    <div id="contentContainer">



        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3> Employee Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="container">


                            <table class="table table-bordered table-hover" id="example">
                                <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Employee</th>
                                    <th>Start Date & Time</th>
                                    <th>End Date & Time</th>
                                    <th>Description</th>
                                    @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                    <th>Action</th>
                                     @endif

                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $n = 1;
                                ?>
                                @forelse ($histries as $history)
                                    <tr>
                                        <td>{{$n++}}</td>
                                        <td>{{$history->employee->employee_name}}</td>
                                        <td>{{$history->start_datetime}}</td>
                                        <td>{{$history->end_datetime}}</td>
                                        <td>{{$history->history_desc}}</td>
                                            
                                            @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                            <td><a id="EditBtn" rid="{{$history->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                            <a id="deleteBtn" rid="{{$history->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a></td>
                                             @endif
                                    </tr>
                                @empty
                                    <h3>No post found from you. Create a new One.</h3>
                                @endforelse
                                </tbody>
                            </table>

                            {{$histries->links()}}




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
            $("#newBtn").click(function(){
                clearform();
                $("#newBtn").hide(100);
                $("#addThisFormContainer").show(300);

            });
            $("#FormCloseBtn").click(function(){
                $("#addThisFormContainer").hide(200);
                $("#newBtn").show(100);
                clearform();
            });


            //header for csrf-token is must in laravel
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            //

            var url = "{{URL::to('/employee_history')}}";
            // console.log(url);
            $("#addBtn").click(function(){
                // alert('form work');
                if($(this).val() == 'Create') {
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {
                            employee_id: $("#employee_id").val(),
                            start_datetime: $("#start_datetime").val(),
                            end_datetime: $("#end_datetime").val(),
                            history_desc: $("#history_desc").val()
                        },
                        success: function (d) {
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            }else if(d.status == 300){
                                $(".ermsg").html(d.message);
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        error: function (d) {
                            console.log(d);
                        }
                    });
                }

                //create  end
                //Update Branch
                if($(this).val() == 'Update'){
                    $.ajax({
                        url:url+'/'+$("#historyid").val(),
                        method: "PUT",
                        type: "PUT",
                        data:{
                            employee_id: $("#employee_id").val(),
                            start_datetime: $("#start_datetime").val(),
                            end_datetime: $("#end_datetime").val(),
                            history_desc: $("#history_desc").val()
                        },
                        success: function(d){
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            }else if(d.status == 300){
                                $(".ermsg").html(d.message);
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        error:function(d){
                            console.log(d);
                        }
                    });
                }
                //Update Branch
            });
            //Edit
            $("#contentContainer").on('click','#EditBtn', function(){
                // alert("btn work");
                $historyid = $(this).attr('rid');
                //console.log($roomid);
                $info_url = url + '/'+$historyid+'/edit';
                console.log($info_url);
                $.get($info_url,{},function(d){
                    populateForm(d);
                    pagetop();
                });
            });
            //Edit Room end

            //Delete Branch
            $("#contentContainer").on('click','#deleteBtn', function(){
                //alert()
                if(!confirm('Sure?')) return;
                $historyid = $(this).attr('rid');
                //console.log($roomid);
                $info_url = url + '/'+$historyid;
                $.ajax({
                    url:$info_url,
                    method: "DELETE",
                    type: "DELETE",
                    data:{
                    },
                    success: function(d){
                        if(d.success) {
                            alert(d.message);
                            location.reload();
                        }
                    },
                    error:function(d){
                        console.log(d);
                    }
                });
            });
            //Delete Branch




            function populateForm(data){
                $("#employee_id").val(data.employee_id);
                $("#start_datetime").val(data.start_datetime);
                $("#end_datetime").val(data.end_datetime);
                $("#history_desc").val(data.history_desc);
                $("#historyid").val(data.id);
                $("#addBtn").val('Update');
                $("#addThisFormContainer").show(300);
                $("#newBtn").hide(100);
            }
            function clearform(){
                $('#createThisForm')[0].reset();
                $("#addBtn").val('Create');
            }


        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#employee").addClass('active');
            $("#employee").addClass('is-expanded');
            $("#employee_history").addClass('active');
        });
    </script>
@endsection

