@extends('layouts.master')
@section('content')


<div class="app-title">
    <div>
        <h1><i class="fa fa-dashboard"></i> Manage Branch</h1>
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

                                {!! Form::open(['url' => 'branch/create','id'=>'createBranchForm']) !!}
                                {!! Form::hidden('branchid','', ['id' => 'branchid']) !!}
                            <br>{!! Form::label('Title', 'Branch Name', ['class' => 'awesome']) !!}
                                {!! Form::text('branch_name','',['id'=>'branch_name','class'=>'form-control','placeholder'=>'Branch Name']) !!}
                            <br>{!! Form::label('Title', 'Branch Phone', ['class' => 'awesome']) !!}
                                {!! Form::text('branch_phone','',['id'=>'branch_phone','class'=>'form-control','placeholder'=>'Branch Phone']) !!}
                            <br>{!! Form::label('Description', 'Branch Address', ['class' => 'awesome']) !!}
                                {!! Form::textarea('branch_address','',['id'=>'branch_address','class'=>'form-control','rows'=>'5','placeholder'=>'Branch Address']) !!}
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

    <button id="NewBranchBtn" type="button" class="btn btn-info">Add New Branch</button>
<hr>
    <div id="BranchContainer">


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3> Branch Details</h3>  
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="container">


                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Branch Name</th>
                                        <th>Branch Phone</th>
                                        <th>Branch Address</th>
                                        @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                        <th>Action</th>
                                        @endif

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $n = 1;
                                    ?>
                                    @forelse ($branches as $branch)
                                    <tr>
                                        <td>{{$n++}}</td>
                                        <td>{{$branch->branch_name}}</td>
                                        <td>{{$branch->branch_phone}}</td>
                                        <td>{{$branch->branch_address}}</td>
                                        @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                        
                                        <td><a id="EditBranchBtn" rid="{{$branch->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                            <a id="deleteBtn" rid="{{$branch->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a></td>
                                        
                                        @endif
                                    </tr>
                                   @empty
                                        <h3>No post found from you. Create a new Branch</h3>
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
    <script>
        $(document).ready(function () {

            $("#addBranchFormContainer").hide();
            $("#NewBranchBtn").click(function(){
                clearform();
                $("#NewBranchBtn").hide(100);
                $("#addBranchFormContainer").show(300);

            });
            $("#FormCloseBtn").click(function(){
                $("#addBranchFormContainer").hide(200);
                $("#NewBranchBtn").show(100);
                clearform();
            });


            //header for csrf-token is must in laravel
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            //

            var url = "{{URL::to('/branch')}}";
            $("#AddBranchBtn").click(function(){
                 //alert('form work');
                if($(this).val() == 'Create') {
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {
                            branch_name: $("#branch_name").val(),
                            branch_phone: $("#branch_phone").val(),
                            branch_address: $("#branch_address").val()
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
                //create Branch end

                //Update Branch
                if($(this).val() == 'Update'){
                    $.ajax({
                        url:url+'/'+$("#branchid").val(),
                        method: "PUT",
                        type: "PUT",
                        data:{
                            branch_name: $("#branch_name").val(),
                            branch_phone: $("#branch_phone").val(),
                            branch_address: $("#branch_address").val()
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
            $("#BranchContainer").on('click','#EditBranchBtn', function(){
                $branchid = $(this).attr('rid');
                $info_url = url + '/'+$branchid+'/edit';
                console.log($info_url);
                $.get($info_url,{},function(d){
                    populateForm(d);
                    pagetop();
                });
            });
            //Edit Room end

            //Delete Branch
            $("#BranchContainer").on('click','#deleteBtn', function(){
                if(!confirm('Sure?')) return;
                $branchid = $(this).attr('rid');
                $info_url = url + '/'+$branchid;
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
                $("#branch_phone").val(data.branch_phone);
                $("#branch_address").val(data.branch_address);
                $("#branchid").val(data.id);
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
            $("#branch").addClass('active');
        });
    </script>
@endsection
