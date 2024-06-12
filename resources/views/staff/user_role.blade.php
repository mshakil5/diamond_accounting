@extends('layouts.master')



@section('content')
    <h3>New Staff</h3>
    <div id="addThisFormContainer">
        {!! Form::open(['url' => 'staff/user_role','id'=>'createThisForm']) !!}
        {!! Form::hidden('roleid','', ['id' => 'roleid']) !!}

        {!! Form::label('Title', 'Role Name', ['class' => 'awesome']) !!}
        {!! Form::text('title','',['id'=>'role_name','class'=>'form-control','placeholder'=>'Role Name']) !!}
        <hr>
        <input type="button" id="addBtn" value="Create" class="btn btn-primary">
        <input type="button" id="FormCloseBtn" value="Close" class="btn btn-warning">
        {!! Form::close() !!}
    </div>

    <button id="newBtn" type="button" class="btn btn-info">Add New Role</button>
    <hr>
    <div id="contentContainer">

        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>Sl</th>
                <th>Role Name</th>
                <th>Action</th>

            </tr>
            </thead>
            <tbody>
            <?php
            $n = 1;
            ?>
            @forelse ($roles as $role)
                <tr>
                    <td>{{$n++}}</td>
{{--                    <td>{{$role->branch->branch_name}}</td>--}}
                    <td>{{$role->role_name}}</td>
                    <td><button id="EditBtn" rid="#" type="button" class="btn btn-info">Edit</button>
                        <button id="deleteBtn" rid="{{$role->id}}" type="button" class="btn btn-info">Delete</button></td>
                </tr>
            @empty
                <h3>No post found from you. Create a new Role</h3>
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

            var url = "{{URL::to('/user_role')}}";
            // console.log(url);
            $("#addBtn").click(function(){
                // alert('form work');
                if($(this).val() == 'Create') {
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {
                            role_name: $("#role_name").val(),
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
                            staff_name: $("#staff_name").val(),
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
                $roleid = $(this).attr('rid');
                //console.log($roomid);
                $info_url = url + '/'+$roleid;
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
                $("#branch_name").val(data.branch_name);
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
