@extends('layouts.master')



@section('content')
    <h3>New Staff</h3>
    <div id="addThisFormContainer">
        {!! Form::open(['url' => 'staff/create','id'=>'createThisForm']) !!}
        {!! Form::hidden('staffid','', ['id' => 'staffid']) !!}
        <div>
            <label for="branch" class="awesome">Choose a Branch</label>
        <select name="branch_id" class="form-control" id="branch_id" form="branchform">
            <option value="" disabled selected>Please Select Branch</option>
            @foreach($branch as $branch)
                <option value="{{$branch->id}}">{{ $branch->branch_name }}</option>
            @endforeach
        </select>
        </div>
        {!! Form::label('Title', 'Staff Name', ['class' => 'awesome']) !!}
        {!! Form::text('title','',['id'=>'staff_name','class'=>'form-control','placeholder'=>'Staff Name']) !!}
        {!! Form::label('Title', 'Staff Phone', ['class' => 'awesome']) !!}
        {!! Form::text('title','',['id'=>'staff_phone','class'=>'form-control','placeholder'=>'Staff Phone']) !!}
        <div>
            <label for="role" class="awesome">Choose a Role</label>
            <select name="role_id" class="form-control" id="role_id" form="roleform">
                <option value="" disabled selected>Please Select Role</option>
                @foreach($roles as $role)
                    <option value="{{$role->id}}">{{ $role->role_name }}</option>
                @endforeach
            </select>
        </div>
        {!! Form::label('Description', 'Staff Address', ['class' => 'awesome']) !!}
        {!! Form::textarea('description','',['id'=>'staff_address','class'=>'form-control','placeholder'=>'Staff Address']) !!}
        <div>
            <label for="password" class="awesome">Password</label>
            <input type="password" name="password" class="form-control" id="password">
        </div>
        <div>
            <label for="c_password" class="awesome">Password</label>
            <input type="password" name="c_password" class="form-control" id="c_password">
        </div>
        <hr>
        <input type="button" id="addBtn" value="Create" class="btn btn-primary">
        <input type="button" id="FormCloseBtn" value="Close" class="btn btn-warning">
        {!! Form::close() !!}
    </div>

    <button id="newBtn" type="button" class="btn btn-info">Add New Staff</button>
    <hr>
    <div id="contentContainer">

        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>Sl</th>
                <th>Branch Name</th>
                <th>Staff Name</th>
                <th>Staff Phone</th>
                <th>Role</th>
                <th>Staff Address</th>
                <th>Action</th>

            </tr>
            </thead>
            <tbody>
            <?php
            $n = 1;
            ?>
            @forelse ($staff as $staff)
                <tr>
                    <td>{{$n++}}</td>
{{--                    {{$room->writer->name}}--}}
                    <td>{{$staff->branch->branch_name}}</td>
                    <td>{{$staff->staff_name}}</td>
                    <td>{{$staff->staff_phone}}</td>
                    <td>{{$staff->role->role_name}}</td>
{{--                    <td>staff</td>--}}
                    <td>{{$staff->staff_address}}</td>
                    <td><button id="EditBtn" rid="{{$staff->id}}" type="button" class="btn btn-info"><i class="fa fa-edit"></i></button>
                        <button id="deleteBtn" rid="{{$staff->id}}" type="button" class="btn btn-info"><i class="fa fa-trash-o"></i></button></td>
                </tr>
            @empty
                <h3>No post found from you. Create a new Staff</h3>
            @endforelse
            </tbody>
        </table>

        {{--        {{$branches ?? ''->links()}}--}}
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

            var url = "{{URL::to('/staff')}}";
           // console.log(url);
            $("#addBtn").click(function(){
               // alert('form work');
                if($(this).val() == 'Create') {
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {
                            branch_id: $("#branch_id").val(),
                            staff_name: $("#staff_name").val(),
                            staff_phone: $("#staff_phone").val(),
                            role_id: $("#role_id").val(),
                            staff_address: $("#staff_address").val()
                        },
                        success: function (d) {
                            if (d.success) {
                                alert(d.message);
                                location.reload();
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
                        url:url+'/'+$("#staffid").val(),
                        method: "PUT",
                        type: "PUT",
                        data:{
                            branch_id: $("#branch_id").val(),
                            staff_name: $("#staff_name").val(),
                            staff_phone: $("#staff_phone").val(),
                            role_id: $("#role_id").val(),
                            staff_address: $("#staff_address").val()
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
                }
                //Update Branch
            });
            //Edit
            $("#contentContainer").on('click','#EditBtn', function(){
                // alert("btn work");
                $staffid = $(this).attr('rid');
                //console.log($roomid);
                $info_url = url + '/'+$staffid+'/edit';
                console.log($info_url);
                $.get($info_url,{},function(d){
                    populateForm(d);
                });
            });
            //Edit Room end

            //Delete Branch
            $("#contentContainer").on('click','#deleteBtn', function(){
                //alert()
                if(!confirm('Sure?')) return;
                $staffid = $(this).attr('rid');
                //console.log($roomid);
                $info_url = url + '/'+$staffid;
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
                $("#branch_id").val(data.branch_id);
                $("#branch_name").val(data.branch_name);
                $("#branch_phone").val(data.branch_phone);
                $("#role_id").val(data.role_id);
                $("#branch_address").val(data.branch_address);
                $("#staffid").val(data.id);
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
@endsection
