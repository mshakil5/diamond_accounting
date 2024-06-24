@extends('layouts.master')



@section('content')
    <div id="addThisFormContainer">


        <div class="row">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>New Shareholder</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="ermsg">
                              
                                
                            </div>
                            <div class="container">


                            {!! Form::open(['url' => 'shareholder/create','id'=>'createThisForm']) !!}
                            {!! Form::hidden('codeid','', ['id' => 'codeid']) !!}
                            {!! Form::label('Title', 'Shareholder Name', ['class' => 'awesome']) !!}
                            {!! Form::text('title','',['id'=>'name','class'=>'form-control','placeholder'=>'Name']) !!}
                            {!! Form::label('Title', 'Email', ['class' => 'awesome']) !!}
                            {!! Form::text('title','',['id'=>'email','class'=>'form-control','placeholder'=>'Email']) !!}
                            {!! Form::label('Title', 'Phone', ['class' => 'awesome']) !!}
                            {!! Form::text('title','',['id'=>'phone','class'=>'form-control','placeholder'=>'Phone']) !!}


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
                        <h3> Shareholder Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="container">


                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                    <th>Action</th>
                                    @endif

                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $n = 1;
                                ?>
                                @forelse ($data as $data)
                                    <tr>
                                        <td>{{$n++}}</td>
                                        <td>{{$data->name}}</td>
                                        <td>{{$data->email}}</td>
                                        <td>{{$data->phone}}</td>
                                        <td>{{$data->address}}</td>
                                        @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                            
                                            <td><a id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                        <a id="deleteBtn" rid="{{$data->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a></td>
                                        @endif
                                    </tr>
                                @empty
                                    <h3>No post found.</h3>
                                @endforelse
                                </tbody>
                            </table>

                            {{$data->links()}}

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

            var url = "{{URL::to('/shareholder')}}";
            // console.log(url);
            $("#addBtn").click(function(){
                // alert('form work');
                if($(this).val() == 'Create') {
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {
                            name: $("#name").val(),
                            email: $("#email").val(),
                            phone: $("#phone").val(),
                            address: $("#address").val()
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
                        url:upurl,
                        method: "POST",
                        type: "POST",
                        data:{
                            name: $("#name").val(),
                            email: $("#email").val(),
                            phone: $("#phone").val(),
                            address: $("#address").val(),
                            codeid: $("#codeid").val()
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
                $sid = $(this).attr('rid');
                //console.log($roomid);
                $info_url = url + '/'+$sid+'/edit';
                //console.log($info_url);
                $.get($info_url,{},function(d){
                    populateForm(d);
                    pagetop();
                });
            });
            //Edit Room end

        //Delete
            $("#contentContainer").on('click','#deleteBtn', function(){
                if(!confirm('Sure?')) return;
                $shid = $(this).attr('rid');
                $info_url = url + '/'+$shid;
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
            //Delete End




            function populateForm(data){
                $("#name").val(data.name);
                $("#email").val(data.email);
                $("#phone").val(data.phone);
                $("#address").val(data.address);
                $("#codeid").val(data.id);
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
            $("#allownerequity").addClass('active');
            $("#allownerequity").addClass('is-expanded');
            $("#shareholder").addClass('active');
        });
    </script>
@endsection
