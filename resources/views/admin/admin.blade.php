@extends('layouts.master')
@section('content')


<div class="app-title">
    <div>
        <h1><i class="fa fa-dashboard"></i> Manage Admin</h1>
        <p>Start a beautiful journey here</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="#">Blank Page</a></li>
    </ul>
</div>

    <div id="addBranchFormContainer">


        <div class="row">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>New Branch</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="ermsg">
                              
                                
                            </div>
                            <div class="container">

                                {!! Form::open(['url' => 'manage-admin/create','id'=>'createBranchForm']) !!}
                                {!! Form::hidden('adminid','', ['id' => 'adminid']) !!}
                                <br>
                                <div class="form-group">
                                    <label for="branch_id">Branch</label>
                                <select name="branch_name" id="branch_name" class="form-control">
                                    <option value="" disabled selected>Please Select Branch</option>
                                    @foreach($branch as $branches)
                                        <option value="{{$branches->id}}">{{ $branches->branch_name }}</option>
                                    @endforeach

                                </select>
                                </div>
                            <br>{!! Form::label('Title', 'Full Name', ['class' => 'awesome']) !!}
                                {!! Form::text('name','',['id'=>'full_name','class'=>'form-control','placeholder'=>'Full Name']) !!}
                            <br>{!! Form::label('Title', 'Phone', ['class' => 'awesome']) !!}
                                {!! Form::text('phone','',['id'=>'phone','class'=>'form-control','placeholder'=>'Phone']) !!}

                                <br><div class="form-group">
                                    <label for="user_type">User Role</label>
                                    <select name="user_type" id="user_type" class="form-control">
                                        <option value="11">Super Admin</option>
                                        <option value="1">Read Only</option>
                                        <option value="2">Data Entry</option>
                                        <option value="3">Sales Entry</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email </label>
                               
                                        <input id="email" type="email" class="form-control" name="email" value=" " required autocomplete="email">    
                                   

                                </div>

                                
                                <div class="form-group">
                                    <label for="address">Address </label>
                                    <input type="text" name="address" class="form-control" value="{{old('address')}}" placeholder="Enter Address" id="address">
                                </div>
                                <div id="passdiv">
                                <div class="form-group">
                                    <label for="pwd">Password:</label>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">                                 
                                </div>

                                <div class="form-group">
                                    <label for="cpwd">Confirm Password:</label>
                       
                                        <input id="cpassword" type="password" class="form-control" name="cpassword" required autocomplete="new-password">
                                </div>
                            </div>
                               <hr>
                                    <input type="button" id="AddBranchBtn" value="Create" class="btn btn-primary">
                                    <input type="button" id="FormCloseBtn" value="Close" class="btn btn-warning">
                                {!! Form::close() !!}


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
            </div>
        </div>

    </div>
<button id="NewBranchBtn" type="button" class="btn btn-info">Add New User</button>
<hr>
<div id="AdminContainer">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> User Registration</h4>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>
                <div class="card-body">
<table class="table table-hover">
    <thead>
    <tr>
        <th>Name</th>
        <th>Phone</th>
        <th>Branch Name</th>
        <th>User Role</th>
        <th>Email</th>
        <th>Address</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @forelse($users as $user)
    <tr>
        <td>{{$user->name}}</td>
        <td>{{$user->phone}}</td>
        <td>{{$user->branch->branch_name}}</td>
        
        @if($user->user_type == 11)
        <td>Super Admin</td>
        @elseif($user->user_type == 1)
        <td>Read Only</td>
        @elseif($user->user_type == 2)
        <td>Data Entry</td>
        @elseif($user->user_type == 3)
        <td>Sales Entry</td>
        @endif
        
        
        <td>{{$user->email}}</td>
        <td>{{$user->address}}</td>
      
        @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                        
        <td><a id="EditAdminBtn" rid="{{$user->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
            <a id="deleteBtn" rid="{{$user->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a></td>
        
        @endif
    </tr>
                    @empty
                        <h3>No post found from you. Create a new User</h3>
                    @endforelse

                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')



<script>
    $(document).ready(function () {

        $("#addBranchFormContainer").hide();
        $("#NewBranchBtn").click(function(){
            clearform();
            $("#NewBranchBtn").hide(100);
            $("#addBranchFormContainer").show(300);
            $("#passdiv").show();

        });
        $("#FormCloseBtn").click(function(){
            $("#addBranchFormContainer").hide(200);
            $("#NewBranchBtn").show(100);
            clearform();
        });


        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        //

        var url = "{{URL::to('/manage-admin')}}";
        $("#AddBranchBtn").click(function(){
             //alert('form work');
            if($(this).val() == 'Create') {
                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        branch_name: $("#branch_name").val(),
                        name: $("#full_name").val(),
                        phone: $("#phone").val(),
                        user_type: $("#user_type").val(),
                        email: $("#email").val(),
                        address: $("#address").val(),
                        password: $("#password").val(),
                        cpassword: $("#cpassword").val()
                    },
                    success: function (d) {
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                            pagetop();
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
            //create Branch end

            //Update Branch
            if($(this).val() == 'Update'){
                $.ajax({
                    url:url+'/'+$("#branchid").val(),
                    method: "PUT",
                    type: "PUT",
                    data:{
                        adminid: $("#adminid").val(),
                        branch_name: $("#branch_name").val(),
                        name: $("#full_name").val(),
                        phone: $("#phone").val(),
                        user_type: $("#user_type").val(),
                        email: $("#email").val(),
                        address: $("#address").val(),
                        password: $("#password").val(),
                        cpassword: $("#cpassword").val()
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
        //Edit Room
        $("#AdminContainer").on('click','#EditAdminBtn', function(){
            $branchid = $(this).attr('rid');
            $info_url = url + '/'+$branchid+'/edit';
            $.get($info_url,{},function(d){
                populateForm(d);
                // $("#passdiv").hide();
                pagetop();
            });
        });
        //Edit Room end

        //Delete Branch
        $("#AdminContainer").on('click','#deleteBtn', function(){
            if(!confirm('Sure?')) return;
            branchid = $(this).attr('rid');
            info_url = url + '/'+branchid;
            $.ajax({
                url:info_url,
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
        //Delete admin

        function populateForm(data){
            $("#adminid").val(data.id);
            $("#branch_name").val(data.branch_id);
            $("#full_name").val(data.name);
            $("#phone").val(data.phone);
            $("#user_type").val(data.user_type);
            $("#email").val(data.email);
            $("#address").val(data.address);
            $("#AddBranchBtn").val('Update');
            $("#addBranchFormContainer").show(300);
            $("#NewBranchBtn").hide(100);
        }
        function clearform(){
            $('#createBranchForm')[0].reset();
            $("#AddBranchBtn").val('Create');
        }


    });
</script>


    <script type="text/javascript">
        $(document).ready(function() {
            $("#alluser").addClass('active');
            $("#alluser").addClass('is-expanded');
            $("#role-register").addClass('active');
        });
    </script>

@endsection
