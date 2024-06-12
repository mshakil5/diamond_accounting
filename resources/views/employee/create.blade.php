@extends('layouts.master')



@section('content')
    <div id="addThisFormContainer">


        <div class="row">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>New Employee</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="ermsg">
                              
                                
                            </div>
                            <div class="container">


                            {!! Form::open(['url' => 'employee/create','id'=>'createThisForm']) !!}
                            {!! Form::hidden('employeeid','', ['id' => 'employeeid']) !!}
                            {!! Form::label('Title', 'Employee Name', ['class' => 'awesome']) !!}
                            {!! Form::text('title','',['id'=>'employee_name','class'=>'form-control','placeholder'=>'Employee Name']) !!}
                            {!! Form::label('Title', 'Employee Id', ['class' => 'awesome']) !!}
                            {!! Form::text('title','',['id'=>'employee_id','class'=>'form-control','placeholder'=>'Employee Id']) !!}
                            {!! Form::label('Title', 'Employee Email', ['class' => 'awesome']) !!}
                            {!! Form::text('title','',['id'=>'email','class'=>'form-control','placeholder'=>'Employee Email']) !!}
                            {!! Form::label('Title', 'Employee Phone', ['class' => 'awesome']) !!}
                            {!! Form::text('title','',['id'=>'employee_phone','class'=>'form-control','placeholder'=>'Employee Phone']) !!}
                            {!! Form::label('Description', 'Employee Address', ['class' => 'awesome']) !!}
                            {!! Form::textarea('description','',['id'=>'employee_address','class'=>'form-control','placeholder'=>'Employee Address']) !!}


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


        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>Sl</th>
                <th>Employee Name</th>
                <th>Employee Id</th>
                <th>Employee Email</th>
                <th>Employee Phone</th>
                <th>Employee Address</th>
                @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                <th>Action</th>
                @endif

            </tr>
            </thead>
            <tbody>
            <?php
            $n = 1;
            ?>
            @forelse ($employees as $employee)
                <tr>
                    <td>{{$n++}}</td>
                    {{--                    {{$room->writer->name}}--}}
                    <td>{{$employee->employee_name}}</td>
                    <td>{{$employee->employee_id}}</td>
                    <td>{{$employee->email}}</td>
                    <td>{{$employee->employee_phone}}</td>
                    <td>{{$employee->employee_address}}</td>
                    @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                        
                        <td><a id="EditBtn" rid="{{$employee->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                    <a id="deleteBtn" rid="{{$employee->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a></td>
                    @endif
                </tr>
            @empty
                <h3>No post found from you. Create a new Employee</h3>
            @endforelse
            </tbody>
        </table>

        {{$employees->links()}}







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

            var url = "{{URL::to('/employee')}}";
            // console.log(url);
            $("#addBtn").click(function(){
                // alert('form work');
                if($(this).val() == 'Create') {
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {
                            employee_name: $("#employee_name").val(),
                            employee_id: $("#employee_id").val(),
                            email: $("#email").val(),
                            employee_phone: $("#employee_phone").val(),
                            employee_address: $("#employee_address").val()
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
                        url:url+'/'+$("#employeeid").val(),
                        method: "PUT",
                        type: "PUT",
                        data:{
                            employee_name: $("#employee_name").val(),
                            employee_id: $("#employee_id").val(),
                            email: $("#email").val(),
                            employee_phone: $("#employee_phone").val(),
                            employee_address: $("#employee_address").val()
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
                $employeeid = $(this).attr('rid');
                //console.log($roomid);
                $info_url = url + '/'+$employeeid+'/edit';
                //console.log($info_url);
                $.get($info_url,{},function(d){
                    populateForm(d);
                    pagetop();
                });
            });
            //Edit Room end

        //Delete Employee
            url2 =  "{{URL::to('/employee_delete')}}";
            $("#contentContainer").on('click','#deleteBtn', function(){
                if(!confirm('Sure?')) return;
                $employeeid = $(this).attr('rid');
                $info_url = url2 + '/'+$employeeid;
                $.ajax({
                    url:$info_url,
                    method: "GET",
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
            //Delete Employee End




            function populateForm(data){
                $("#employee_name").val(data.employee_name);
                $("#employee_id").val(data.employee_id);
                $("#email").val(data.email);
                $("#employee_phone").val(data.employee_phone);
                $("#employee_address").val(data.employee_address);
                $("#employeeid").val(data.id);
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
            $("#addemployee").addClass('active');
        });
    </script>
@endsection
