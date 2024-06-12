@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> User Setting</h4>
                    <div class="ermsg">
                    </div>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>

                <div class="container">
                    <br>
                    <!-- Nav pills -->
                     <!-- switch  -->
                <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
                    @if(auth()->user()->user_type == 11)
                    <li class="nav-item">
                        <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><b>Switch Branch</b></a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"><b>Change Password</b></a>
                    </li>
                </ul>
                <!-- switch end  -->
              

                    <!-- Tab panes -->
                    <div class="tab-content">

                        {{-- swich branch  --}}
                        @if(auth()->user()->user_type == 11)
                        <div class="tab-pane fade show" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6">

                                        {!! Form::open(['url' => 'setting/create','id'=>'createThisForm']) !!}

                                        <div class="form-group">
                                            <label for="branch_id">Branch</label>

                                            <select name="branch_id" id="branch_id" class="form-control">
                                                <option value="" selected>Please Select Branch</option>
                                                @foreach($branch as $branches)
                                                    <option value="{{$branches->id}}">{{ $branches->branch_name }}</option>
                                                @endforeach

                                            </select>
                                        </div>

                                        <input type="button" id="addBtn" value="Switch" class="btn btn-primary">
                                        {!! Form::close() !!}

                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- password change  --}}
                        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6">

                                        {!! Form::open(['url' => 'setting/create','id'=>'createThisForm']) !!}

                                        <div class="form-group">
                                            <label for="password">Password:</label>
                                            <input id="password" type="password" class="form-control" name="password" required >
                                        </div>

                                        <div class="form-group">
                                            <label for="confirmpassword">Confirm Password:</label>
                                            <input id="confirmpassword" type="password" class="form-control" name="confirmpassword" required >
                                        </div>

                                        <input type="button" id="passwordBtn" value="Change Password" class="btn btn-primary">
                                        {!! Form::close() !!}

                                    </div>
                                </div>
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
            //header for csrf-token is must in laravel
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });


            var url = "{{URL::to('/setting')}}";
            $("#addBtn").click(function(){
                if($(this).val() == 'Switch') {
                    var branch_id= $("#branch_id").val();
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {branch_id:branch_id},
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
            });


            var passwordurl = "{{URL::to('/changepassword')}}";
            $("#passwordBtn").click(function(){
                if($(this).val() == 'Change Password') {
                    var password= $("#password").val();
                    var confirmpassword= $("#confirmpassword").val();
                    $.ajax({
                        url: passwordurl,
                        method: "POST",
                        data: {password:password,confirmpassword:confirmpassword},
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
            });

        });

    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#setting").addClass('active');
        });

    </script>
@endsection
